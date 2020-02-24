<?php
include 'core/php/include.php';

use OTPHP\TOTP;

if(!isset($_SESSION['id'])) {
	exit();
}

if(empty($_GET['id'])) {
	exit();
}

$eid = mysqli_real_escape_string($db, $_GET['id']);
$q = mysqli_query($db, "SELECT * FROM `events` WHERE `id` = '$eid' AND `owner` = '$id'");
if(mysqli_num_rows($q) == 0) {
	exit();
} else {
	$edata = mysqli_fetch_assoc($q);
}

$q = mysqli_query($db, "SELECT * FROM `attendees` WHERE `event` = '$eid'");
$users = [];

$num = mysqli_num_rows($q);

while($data = mysqli_fetch_assoc($q)) {
	$users[] = [
		"email" => $data['email'],
		"name" => $data['name'],
		"image" => $data['image']
	];
}

$uhs = md5(json_encode($users));


$key = $edata['key'];

$totp = TOTP::create(
	$key,
	1,
	'sha512',
	10
);

$code = $totp->now();

$data = $edata['id'] . "/" . $code;

$url = "https://chart.googleapis.com/chart?cht=qr&chs=540x540&chl=$data";

header("Cache-Control: no-cache");

die(json_encode([
	"url" => $url,
	"users" => $users,
	"uhs" => $uhs,
	"num" => $num
]));