<?php

session_start();

$_SESSION["login"] = null;
$_SESSION["user"] = null;

header('Location: login.php', true, 301);
exit();

?>