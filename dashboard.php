<?php
include 'core/php/include.php';

if(!isset($_SESSION['id'])) {
	header("Location: /");
	exit();
}

if(isset($_SESSION['scan']) && $_SESSION['scan']) {
	header("Location: /scan");
	exit();
}

$errors = [];

if(!empty($_POST['action']) && $_POST['action'] == "create_event") {
	if(!isset($_POST['event_code']) || empty($_POST['event_code'])) {
		$errors[] = "Please enter an event code.";
	} else if(strlen($_POST['event_code']) < 3 || strlen($_POST['event_code']) > 8) {
		$errors[] = "The event code must be between 3 and 8 characters long.";
	}
	if(!isset($_POST['event_name']) || empty($_POST['event_name'])) {
		$errors[] = "Please enter an event name.";
	} else if(strlen($_POST['event_name']) < 3 || strlen($_POST['event_name']) > 60) {
		$errors[] = "The event name must be between 3 and 60 characters long.";
	}
	if(!isset($_POST['event_location']) || empty($_POST['event_location'])) {
		$errors[] = "Please enter an event location.";
	} else if(strlen($_POST['event_location']) < 3 || strlen($_POST['event_location']) > 60) {
		$errors[] = "The event location must be between 3 and 60 characters long.";
	}
	if(!isset($_POST['event_description']) || empty($_POST['event_description'])) {
		$errors[] = "Please enter an event event description.";
	}

	if(empty($errors)) {
		$event_code = mysqli_real_escape_string($db, htmlentities($_POST['event_code']));
		$event_name = mysqli_real_escape_string($db, htmlentities($_POST['event_name']));
		$event_location = mysqli_real_escape_string($db, htmlentities($_POST['event_location']));
		$event_description = mysqli_real_escape_string($db, htmlentities($_POST['event_description']));
		$owner = $user_data['id'];

		$q = mysqli_query($db, "INSERT INTO `groups` (`owner`, `code`, `name`, `location`, `description`) VALUES ('$owner', '$event_code', '$event_name', '$event_location', '$event_description')");

		$iid = mysqli_insert_id($db);
		header("Location: https://pop.solidsecurity.co/event/$iid");
		exit();
	}
}
?>
<!doctype html>
<html lang="en" class="h-100">
	<head>
		<?php require('core/php/metas.php'); ?>
		<title>Dashboard | POP by Solid Security</title>
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
			.link-unstyled, .link-unstyled:link, .link-unstyled:hover {
				color: inherit;
				text-decoration: inherit;
			}
		</style>
	</head>
	<body class="d-flex flex-column h-100">
		<?php require('core/php/navbar.php'); ?>
		<main role="main" class="flex-shrink-0">
			<div class="container">
				<?php
				if($org == false) {
				?>
				<div class="message">
					<h4>Unfortunately, <b><?=$domain?></b> is not supported yet.</h4>
					<p style="margin-bottom: 0;">Looking to use POP for <?=$domain?>? Email pop@solidsecurity.co to learn how to get approval for this domain.</p>
				</div>
				<?php
				} else if (!$user_data['approved']) {
				?>
				<div class="message">
					<h4>Your account is not authorized to use POP.</h4>
					<p style="margin-bottom: 0;">Your organization (<?=$domain?>) has not authorized this account to use POP as an administrator. Please contact your primary account holder to request access.</p>
				</div>
				<?php
				} else {
					$q = mysqli_query($db, "SELECT * FROM `groups` WHERE `owner` = '$id'");
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
				<h3 style="margin-top: 32px; margin-bottom: 8px;">Events</h3>
				<div class="row">
					<style>
					.bgimg {
						height: 120px !important; min-height: 120px !important; max-height: 120px !important;
						 background-size: cover; background-position: center;
					}
					@media(max-width: 767px) {
						.bgimg {
							min-height: 140px !important;
							height: 140px !important;
							max-height: 140px !important;
						}
						.card {
							height: auto !important;
						}
					}
					.card {
						box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
						transition: all 0.3s cubic-bezier(.25,.8,.25,1);
					}
					.card:hover {
						box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);
					}
					</style>
					<?php
					while($data = mysqli_fetch_assoc($q)) {
						if(empty($data['image'])) {
							$image = "https://ssl.gstatic.com/calendar/images/eventillustrations/v1/img_code_2x.jpg";
						} else {
							$image = $data['image'];
						}

						$cap = 90;
						if(strlen($data['description']) > $cap) {
							$description = substr($data['description'], 0, $cap) . "&hellip;";
						} else {
							$description = $data['description'];
						}
					?>
					<div class="col col-xl-3 col-lg-4 col-md-6 col-12">
						<a href="https://pop.solidsecurity.co/event/<?=$data['id']?>" class="link-unstyled">
							<div class="card" style="width: 100%; height: 275px; margin-bottom: 30px;">
								<div class="bgimg card-img-top" style="background-image: url('<?=$image?>');"></div>
								<div class="card-body">
									<h5 class="card-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?=$data['name']?></h5>
									<h6 class="card-subtitle mb-2 text-muted"><?=$data['code']?></h6>
									<p class="card-text"><?=$description?></p>
								</div>
							</div>
						</a>
					</div>
					<?php
					}
					?>
					<div class="col col-xl-3 col-lg-4 col-md-6 col-12">
						<a href="#" data-toggle="modal" data-target="#new-event" class="link-unstyled">
							<div class="card" style="width: 100%; height: 275px !important; margin-bottom: 30px; position: relative;">
								<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 32px; opacity: 0.5; text-align: center;">
									<i class="fas fa-plus-circle"></i>
									<p style="font-size: 16px; text-transform: uppercase; font-weight: bold; margin-bottom: 0;">Create Event</p>
								</div>
							</div>
						</a>
					</div>
				</div>
				<?php
					}
				}
				?>
			</div>
		</main>
		<div class="modal fade" id="new-event" tabindex="-1" role="dialog" aria-labelledby="new-event-title" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="new-event-title">Create New Event</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<?php if(!empty($errors)) { ?>
						<div class="alert alert-danger" style="border-radius: 0;">
							<ul style="margin-bottom: 0;">
								<?php foreach($errors as $error) { ?>
									<li><?=$error?></li>
								<?php } ?>
							</ul>
						</div>
					<?php } ?>
					<form action="" method="post">
						<div class="modal-body">
							<input type="hidden" name="action" value="create_event">
							<div class="form-group">
								<label for="name">Owner</label>
								<input type="text" name="owner" class="form-control" id="owner" value="<?=$user_data['name']?> <<?=explode("@", $user_data['email'])[0]?>>" disabled>
							</div>
							<div class="form-group">
								<label for="name">Event Code</label>
								<input required type="text" name="event_code" class="form-control" id="event_code" placeholder="Eg. XX999AAA" min="3" max="8" value="<?=htmlentities((isset($_POST['event_code']) ? $_POST['event_code'] : ""))?>">
							</div>
							<div class="form-group">
								<label for="name">Event Name</label>
								<input required type="text" name="event_name" class="form-control" id="event_name" placeholder="Eg. My Event Name" min="3" max="60" value="<?=htmlentities((isset($_POST['event_name']) ? $_POST['event_name'] : ""))?>">
							</div>
							<div class="form-group">
								<label for="name">Event Location</label>
								<input required type="text" name="event_location" class="form-control" id="event_location" placeholder="Eg. CSL 1232" min="3" max="60" value="<?=htmlentities((isset($_POST['event_location']) ? $_POST['event_location'] : ""))?>">
							</div>
							<div class="form-group">
								<label for="name">Event Description</label>
								<textarea required type="text" name="event_description" class="form-control" id="event_description" placeholder="Eg. My Event Description"><?=htmlentities((isset($_POST['event_description']) ? $_POST['event_description'] : ""))?></textarea>
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
		<?php if(!empty($errors)) { ?>
		<script>
			$(function(){
				$("#new-event").modal('show');
			});
		</script>
		<?php } ?>
	</body>
</html>