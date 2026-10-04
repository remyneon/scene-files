<?php

// -------------------------------------------------------------------------
// Include config
include_once 'Configs/Main.Config.php';
include_once 'App/Engine.php';

// -------------------------------------------------------------------------
// auth checker
if($_SESSION["login"] != true) {
	header('Location: login.php', true, 301);
	exit();
}

// -------------------------------------------------------------------------
// rights checker
if($_SESSION["user"]["settings"] ? false : true) {
	header('Location: dashboard.php', true, 301);
	exit();
}

// -------------------------------------------------------------------------
// gets
$action				= $_GET["action"];
$allow_duplicates 	= $_GET["allow_duplicates"];


// -------------------------------------------------------------------------
// actions checker
if($action != null) {
	switch($action) {
		case "clear_stats":
			clearStats($db);
			break;
			
		case "update":
			updateConfig($db, $allow_duplicates);
			break;
	}
}

function updateConfig($db, $allow_duplicates) {
	$db->query("UPDATE `settings` SET `Value`='$allow_duplicates' WHERE `Name`='allow_duplicates';");
	echo "success";
	exit(0);
}

// -------------------------------------------------------------------------
// clear stats func
function clearStats($db) {
	$db->query("UPDATE `statistics` SET `Logs`=0;");
	$db->query("UPDATE `statistics` SET `Passwords`=0;");
	$db->query("UPDATE `statistics` SET `Chromium`=0;");
	$db->query("UPDATE `statistics` SET `Firefox`=0;");
	$db->query("UPDATE `statistics` SET `IE`=0;");
	$db->query("UPDATE `statistics` SET `Edge`=0;");
	$db->query("UPDATE `statistics` SET `Opera`=0;");
	$db->query("TRUNCATE TABLE `topsites`");
}

// -------------------------------------------------------------------------
// info from db
$allow_duplicates = $db->query("SELECT * FROM `settings` WHERE `Name`='allow_duplicates'")->fetch_array();
$pageID = "Settings";

include_once 'Models/Header.php';

?>
<div class="content content-fixed container">
	<div class="card ht-100p">
		<div class="card-header d-flex align-items-center justify-content-between" style="background: #2d353e;">
			<h6 class="mg-b-0" style="color: white;">
				Settings
			</h6>
		</div>
		
		<br>
		<div class="row row-xs">
			<div class="col mg-t-10">
			
				<div data-label="Duplicates" class="df-example demo-forms">
					<div class="d-flex">
						<div class="custom-control custom-switch">
							<input type="checkbox" class="custom-control-input" id="allow_duplicates" <? if($allow_duplicates["Value"] == "1"){ echo "checked"; } ?>>
							<label class="custom-control-label" for="allow_duplicates">
								Allow duplicates
							</label>
						</div>					
					</div>					
				</div>
				
				<div class="d-flex justify-content-end" style="margin: auto; padding: 10px; margin-bottom: -22px">
				<button type="button" onclick="updateSettings();" class="btn btn-primary">Save</button>
				</div>
				<br>
			
			</div>
		</div>
		
<script>

function updateSettings() {
	var allow_duplicates = document.getElementById("allow_duplicates");

	if (allow_duplicates.checked == true) {
		allow_duplicates = "1";
	}
	else {
		allow_duplicates = "0";
	}

	var xhr = new XMLHttpRequest();
		
	xhr.open('GET', `settings.php?action=update&allow_duplicates=${allow_duplicates}`);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		
	xhr.onload = function() {
		if (xhr.status === 200 && xhr.responseText == "success") {
			new Noty({
				timeout: 3500,
				text: 'Success: settings updated',
				type: 'success',
				layout: 'topRight',
				theme: 'bootstrap-v4',
				progressBar: true
				
			}).show();
			$('#createRule').modal('hide');
		}
		else {
			new Noty({
				timeout: 3500,
				text: 'Error: unk error',
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