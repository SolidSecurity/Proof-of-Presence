<?php
require_once 'vendor/autoload.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$google = new Google_Client();
$google->setClientId('XXXXX.apps.googleusercontent.com');
$google->setClientSecret('XXXXX');
$google->setRedirectUri('https://XXXXX/login');
$google->addScope('email');
$google->addScope('profile');

$db = mysqli_connect("XXXXX", "XXXXX", "XXXXX", "XXXXX");

$org = false;
if(isset($_SESSION['id'])) {
	$id = $_SESSION['id'];
	$q = mysqli_query($db, "SELECT * FROM `users` WHERE `id` = '$id'");
	$user_data = mysqli_fetch_assoc($q);

	if(isset($_SESSION['id'])) {
		$domain = substr($user_data['email'], strpos($user_data['email'], '@') + 1);

		if(empty($user_data['org'])) {
		} else if($domain != $user_data['org']) {
		} else {
			$torg = $user_data['org'];
			$q = mysqli_query($db, "SELECT * FROM `orgs` WHERE `domain` = '$torg'");
			if(mysqli_num_rows($q) != 0) {
				$data = mysqli_fetch_assoc($q);
				$org = $data['name'];
			}
		}
	}
}