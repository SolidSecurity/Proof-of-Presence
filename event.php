<?php
include 'core/php/include.php';

use ParagonIE\ConstantTime\Base32;

if(!isset($_SESSION['id'])) {
	header("Location: /");
	exit();
}

if(empty($_GET['id'])) {
	header("Location: /dashboard");
	exit();
}

$eid = mysqli_real_escape_string($db, $_GET['id']);
$q = mysqli_query($db, "SELECT * FROM `groups` WHERE `id` = '$eid' AND `owner` = '$id'");
if(mysqli_num_rows($q) == 0) {
	header("Location: /dashboard");
	exit();
} else {
	$edata = mysqli_fetch_assoc($q);
}

if(!empty($_GET['createEventName'])) {
	if($org != false && $user_data['approved']) {
		$name = mysqli_real_escape_string($db, $_GET['createEventName']);
		$key = $encoded = Base32::encode(openssl_random_pseudo_bytes(8));
		mysqli_query($db, "INSERT INTO `events` (`owner`, `name`, `key`, `group`) VALUES ('$id', '$name', '$key', '$eid')");
		header("Location: https://pop.solidsecurity.co/event/$eid");
		exit();
	}
}


if(empty($edata['image'])) {
	$image = "https://ssl.gstatic.com/calendar/images/eventillustrations/v1/img_code_2x.jpg";
} else {
	$image = $edata['image'];
}

$cap = 400;
if(strlen($edata['description']) > $cap) {
	$description = substr($edata['description'], 0, $cap) . "&hellip;";
} else {
	$description = $edata['description'];
}

?>
<!doctype html>
<html lang="en" class="h-100">
	<head>
		<?php require('core/php/metas.php'); ?>
		<title>Event | POP by Solid Security</title>
		<style>
			.message {
				background: #eee;
				border-radius: 15px;
				padding: 30px;
				width: 600px;
				max-width: 95%;
				margin: 0 auto;
				margin-top: 64px;
			}
			.bgimg {
				height: 240px !important;
				min-height: 240px !important;
				max-height: 240px !important;
				background-size: cover;
				background-position: center;
				padding: 32px;
				color: #FFF;
			}
			.bgimg h1 {
				margin-bottom: -4px;
			}
		</style>
	</head>
	<body class="d-flex flex-column h-100">
		<?php require('core/php/navbar.php'); ?>
		<main role="main" class="flex-shrink-0">
			<div class="container" style="margin-top: 32px;">
				<div class="bgimg" style="background-image: url('<?=$image?>');">
					<h1 class="display-5"><?=$edata['name']?></h1>
					<p><b><?=$edata['code']?></b></p>
					<p style="max-width: 50%; overflow-wrap: break-word;"><?=$description?></p>
				</div>
			</div>
			<div class="container">
				<?php
                $q = mysqli_query($db, "SELECT * FROM `events` WHERE `owner` = '$id' AND `group` = '$eid'");
				if(mysqli_num_rows($q) == 0) {
				?>
				<div class="message">
					<h4>There's nothing here yet.</h4>
					<p style="margin-bottom: 0;">You haven't created any events yet. Get started by creating your first event.</p>
					<br>
					<a href="#" class="btn btn-success btn-lg" data-toggle="modal" data-target="#new-event"><i class="fas fa-plus"></i>&nbsp; Create Event</a>
				</div>
				<?php
					} else {
				?>
				<h3 style="margin-top: 32px; margin-bottom: 8px;">Meetings &nbsp; 
				<a href="#" class="btn btn-success btn-sm" data-toggle="modal" data-target="#new-event"><i class="fas fa-plus"></i>&nbsp; Create Meeting</a></h3>
				<table class="table table-striped">
					<thead class="thead-dark">
						<tr>
							<th scope="col">EID</th>
							<th scope="col">Name</th>
							<th scope="col">Created</th>
							<th scope="col">Key</th>
							<th scope="col">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php
						while($data = mysqli_fetch_assoc($q)) {
						?>
						<tr>
							<th scope="row"><?=$data['id']?></th>
							<td><?=$data['name']?></td>
							<td><?=$data['time']?></td>
							<td><?=substr($data['key'], 0, 4)?>&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</td>
							<td>
								<a href="/collect/<?=$data['id']?>" class="btn btn-primary btn-sm"><i class="fas fa-clipboard-user"></i>&nbsp; Collect Attendance</a>
								<a href="/download/<?=$data['id']?>" class="btn btn-dark btn-sm"><i class="fas fa-download"></i>&nbsp; Download Attendance</a>
								<a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete-<?=$data['id']?>"><i class="fas fa-trash"></i>&nbsp; Delete Meeting</a>
							</td>
						</tr>
						<div class="modal fade" id="delete-<?=$data['id']?>" tabindex="-1" role="dialog" aria-labelledby="new-event-title" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="new-event-title">Delete Meeting</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<form action="" method="post">
										<input type="hidden" name="delete" value="<?=$data['id']?>">
										<div class="modal-body">
											<p><b>Are you sure you want to delete "<?=$data['name']?>"?</b></p>
											<p>You will not be able to collect or download attendance data for this meeting moving forward. This action is permanent and cannot be undone.</p>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
											<button type="submit" class="btn btn-danger">Delete</button>
										</div>
									</form>
								</div>
							</div>
						</div>
						<?php
						}
						?>
					</tbody>
				</table>
				<?php
					}
				?>
			</div>
		</main>
		<div class="modal fade" id="new-event" tabindex="-1" role="dialog" aria-labelledby="new-event-title" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="new-event-title">Create New Meeting</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<form action="https://pop.solidsecurity.co/event.php" method="get">
						<input type="hidden" name="id" value="<?=$eid?>">
						<div class="modal-body">
							<div class="form-group">
								<label for="name">Meeting Name</label>
								<input type="text" name="createEventName" class="form-control" id="name" placeholder="My Meeting" required>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary">Create</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<?php require('core/php/footer.php'); ?>
	</body>
</html>