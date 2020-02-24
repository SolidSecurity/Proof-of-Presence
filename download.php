<?php
include 'core/php/include.php';

if(!isset($_SESSION['id'])) {
	header("Location: /");
	exit();
}

if(empty($_GET['id'])) {
	header("Location: /dashboard");
	exit();
}

$eid = mysqli_real_escape_string($db, $_GET['id']);
$q = mysqli_query($db, "SELECT * FROM `events` WHERE `id` = '$eid' AND `owner` = '$id'");
if(mysqli_num_rows($q) == 0) {
	header("Location: /dashboard");
	exit();
} else {
	$edata = mysqli_fetch_assoc($q);
	$eid = $edata['id'];
	$q = mysqli_query($db, "SELECT * FROM `attendees` WHERE `event` = '$eid'");
	$num = mysqli_num_rows($q);
}

if(isset($_GET['download'])) {
	echo "aid,uid,eid,time,netid,name,email,vote\n";
	while($data = mysqli_fetch_assoc($q)) {
		echo $data['id'] . "," . $data['event'] . "," . $data['user'] . "," . $data['time'] . "," . explode("@", $data['email'])[0] . "," . $data['name'] . "," . $data['email'] . "," . $data['vote'] . "\n";
	}
	exit();
}
?>
<!doctype html>
<html lang="en" class="h-100">
	<head>
		<?php require('core/php/metas.php'); ?>
		<title>Download | POP by Solid Security</title>
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
		</style>
	</head>
	<body class="d-flex flex-column h-100">
		<?php require('core/php/navbar.php'); ?>
		<main role="main" class="flex-shrink-0">
			<div class="container">
				<h3 style="margin-top: 32px; margin-bottom: 8px;">Attendees (<?=$num?>) &nbsp; 
				<a href="https://pop.solidsecurity.co/download.php?id=<?=$eid?>&download=true" class="btn btn-success btn-sm" download="<?=$edata['name']?>.csv"><i class="fas fa-download"></i>&nbsp; Download .csv</a></h3>
				<table class="table table-striped">
					<thead class="thead-dark">
						<tr>
							<th scope="col">AID</th>
							<th scope="col">UID</th>
							<th scope="col">EID</th>
							<th scope="col">Time</th>
							<th scope="col">NetID</th>
							<th scope="col">Name</th>
							<th scope="col">Email</th>
							<th scope="col">Vote</th>
						</tr>
					</thead>
					<tbody>
						<?php
						while($data = mysqli_fetch_assoc($q)) {
						?>
						<tr>
							<th scope="row"><?=$data['id']?></th>
							<td><?=$data['event']?></td>
							<td><?=$data['user']?></td>
							<td><?=$data['time']?></td>
							<td><?=explode("@", $data['email'])[0]?></td>
							<td><?=$data['name']?></td>
							<td><?=$data['email']?></td>
							<td><?=$data['vote']?></td>							
						</tr>
						<?php
						}
						?>
					</tbody>
				</table>
			</div>
		</main>
		<?php require('core/php/footer.php'); ?>
	</body>
</html>