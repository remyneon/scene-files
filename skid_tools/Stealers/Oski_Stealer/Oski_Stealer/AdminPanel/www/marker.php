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
if($_SESSION["user"]["markers"] ? false : true)
{
	header('Location: dashboard.php', true, 301);
	exit();
}

$actions = $_GET["action"];
$UID = $_GET["UID"];

$marker_name = $_GET["marker_name"];
$marker_color = $_GET["marker_color"];
$marker_hosts = $_GET["marker_hosts"];

switch($actions)
{
	case "add":
		addRule($db, $marker_name, $marker_color, $marker_hosts);
		break;
	
	case "delete":
		deleteRule($db, $UID);
		break;
		
	case "modal":
		viewRules($db);
		break;
		
	default:
		break;
}

function addRule($db, $marker_name, $marker_color, $marker_hosts)
{
	$db->query("INSERT INTO `markers`(`Name`, `URLs`, `Color`) VALUES ('$marker_name','$marker_hosts','$marker_color')");
	
	echo "success";
	
	exit();
}

function deleteRule($db, $UID)
{
	$db->query("DELETE FROM `markers` WHERE `UID`='$UID'");
	echo "success";
	
	exit();
}


function viewRules($db)
{
	$markers = $db->query("SELECT * FROM `markers` ORDER BY `UID` DESC LIMIT 100;");
?>
<table class="table">
	<thead>
		<tr>
			<th scope="col">ID</th>
			<th scope="col">Rule Name</th>
			<th scope="col">URLs</th>
			<th scope="col">Color</th>
			<th scope="col">Actions</th>
		</tr>
	</thead>
	<tbody>
	<? while ($log = $markers->fetch_assoc()) { ?>
		<tr>
			<th style="text-align: left;padding-top: 18px;"><? echo $log["UID"]; ?></th>
			<td style="text-align: left;padding-top: 18px;"><? echo $log["Name"]; ?></td>
			<td style="text-align: left;padding-top: 18px;"><? echo $log["URLs"]; ?></td>
			<td style="text-align: left;padding-top: 18px; color: #<? echo $log["Color"]; ?>;"><? echo $log["Color"]; ?></td>
			<td style="text-align: left;">
				<div class="dropdown">
					<button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
					<div class="dropdown-menu">
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

$pageID = "Marker";

include_once 'Models/Header.php';

?>
<div class="content content-fixed">
	<div class="container pd-x-0 pd-lg-x-10 pd-xl-x-0">
		<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
			<div>
				<h4 class="mg-b-0 tx-spacing--1">Marker Rules</h4>
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
	<div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 50%;">
		<div class="modal-content tx-14">
			<div class="modal-header">
				<h6 class="modal-title" id="exampleModalLabel6">Create marker</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			
				<div id="wizard1">
					<h3>General</h3>
					<section>
			
						<div class="form-group">
							<label for="marker_name" class="d-block">Rule name</label>
							<div class="input-group mg-b-10">
								<input type="text" class="form-control" placeholder="First rule" id="marker_name">
							</div>
						</div>
						
						<div class="form-group">
							<label for="marker_color" class="d-block">Color</label>
							<select class="custom-select" id="marker_color">
								<option value="b6c2c9" style="background-color: #b6c2c9;" selected>Grey</option>
								<option value="727cb6" style="background-color: #727cb6;">Purple</option>
								<option value="348fe2" style="background-color: #348fe2;">Primary</option>
								<option value="49b6d6" style="background-color: #49b6d6;">Aqua</option>
								<option value="00acac" style="background-color: #00acac;">Green</option>
								<option value="90ca4b" style="background-color: #90ca4b;">Lime</option>
								<option value="f59c1a" style="background-color: #f59c1a;">Orange</option>
								<option value="ffd900" style="background-color: #ffd900;">Yellow</option>
								<option value="ff5b57" style="background-color: #ff5b57;">Red</option>
							</select>
                        </div>
						
						
					</section>
					
					<h3>Hosts</h3>
					<section>
			
						<div class="form-group">
							<label for="marker_hosts" class="d-block">Hosts list</label>
							<div class="input-group mg-b-10">
								<textarea id="marker_hosts" class="form-control" rows="2" placeholder="blockchain.com,kraken.com"></textarea>
							</div>
						</div>
					</section>
				
				</div>
				
			</div>
		</div>
	</div>
</div>

<script>
$('.viewTasks').load("marker.php?action=modal");

function createRuleUI()
{
	$('#createRule').modal({show:true});
};

function createRule()
{
	var marker_name = document.getElementById("marker_name").value;
	var marker_color = document.getElementById("marker_color").value;
	var marker_hosts = document.getElementById("marker_hosts").value;
		
	var xhr = new XMLHttpRequest();
	
	xhr.open('GET', `marker.php?action=add&marker_name=${marker_name}&marker_color=${marker_color}&marker_hosts=${marker_hosts}`);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.onload = function() 
	{
		if (xhr.status === 200 && xhr.responseText == "success")
		{
			new Noty({
				timeout: 3500,
				text: 'Success: marker created',
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
				text: 'Error: fill marker parameters',
				type: 'error',
				layout: 'topRight',
				theme: 'bootstrap-v4',
				progressBar: true
				
			}).show();
		}
		
		$('.viewTasks').load("marker.php?action=modal");
	};
	xhr.send(encodeURI('name'));
};

function deleteRule(id)
{
	var xhr = new XMLHttpRequest();

	xhr.open('GET', `marker.php?action=delete&UID=${id}`);
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
		
		$('.viewTasks').load("marker.php?action=modal");
	};
	xhr.send(encodeURI('name'));
};

function updateRuleList()
{
	$('.viewTasks').load("marker.php?action=modal");
	
	new Noty({
		timeout: 3500,
		text: 'Success: rules list updated',
		type: 'success',
		layout: 'topRight',
		theme: 'bootstrap-v4',
		progressBar: true
	}).show();
};

$('#wizard1').steps(
{
	headerTag: 'h3',
	bodyTag: 'section',
	autoFocus: true,
	titleTemplate: '<span class="number">#index#</span> <span class="title">#title#</span>'
});

$(document).ready(function() {
    $('input#color').simpleColorPicker();
});

$('.actions a[href=\\#finish]').attr('onclick', 'createRule();');

</script>	

<? include_once 'Models/Footer.php'; ?>
</body>
</html>