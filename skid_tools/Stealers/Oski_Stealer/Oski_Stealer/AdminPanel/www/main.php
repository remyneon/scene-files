<?php

require("Configs/Main.Config.php");

$db = mysqli_connect($config["main"]["mysql_server"], $config["main"]["mysql_user"], $config["main"]["mysql_password"], $config["main"]["mysql_dbname"]);

if ($db->connect_errno)
{
	exit;
}

$grab = $db->query("SELECT * FROM `grab` ORDER BY `UID` DESC LIMIT 20;");

while ($task = $grab->fetch_assoc())
{
	echo $task["Name"].";".$task["StartPath"].";".$task["FileMasks"].";";
}

?>