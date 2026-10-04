<?php

include_once 'Configs/Main.Config.php';
include 'App/Engine.php';

session_start();

if($_SESSION["login"] == true)
{
	header( 'Location: dashboard.php', true, 301 );
	exit();
}

$login = checkParam($_POST['login']);
$password = checkParam($_POST['password']);

if ($login != null & $password != null)
{
	$_login = mysqli_real_escape_string($db, $login);
	$_passw = mysqli_real_escape_string($db, $password);
	
	$passwordHash = md5($_passw);
	
	$checkUser = $db->query("SELECT * FROM `users` WHERE `login`='$_login' and `password`='$passwordHash'")->fetch_array();
	
	if($checkUser["id"] != null)
	{
		$_SESSION["login"] = true;
		$_SESSION["user"] = $checkUser;
		
		header( 'Location: dashboard.php', true, 301 );
		exit();
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Login</title>

    <link href="Template/lib/fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="Template/lib/ionicons/css/ionicons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="Template/assets/css/dashforge.css">
    <link rel="stylesheet" href="Template/assets/css/dashforge.dashboard.css">
  </head>
  <body>



    <div class="content content-fixed content-auth">
      <div class="container">
        <div class="media align-items-stretch justify-content-center ht-100p pos-relative">
          <div class="media-body align-items-center d-none d-lg-flex">
            <div class="mx-wd-600">
              <img src="Template/img/img15.png" class="img-fluid" alt="">
            </div>
          </div>
          <div class="sign-wrapper mg-lg-l-50 mg-xl-l-60">
		  <form method="POST">
            <div class="wd-100p">
              <h3 class="tx-color-01 mg-b-5">Sign In</h3>
              <p class="tx-color-03 tx-16 mg-b-40">Welcome back! Please signin to continue.</p>

              <div class="form-group">
                <label>Login</label>
                <input name="login" type="text" class="form-control" placeholder="Enter your login">
              </div>
              <div class="form-group">
                <div class="d-flex justify-content-between mg-b-5">
                  <label class="mg-b-0-f">Password</label>
                </div>
                <input name="password" type="password" class="form-control" placeholder="Enter your password">
              </div>
              <button type="submit" class="btn btn-brand-02 btn-block">Sign In</button>
            </div>
			</form>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>