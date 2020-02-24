<?php
include 'core/php/include.php';

if(isset($_SESSION['id'])) {
	header("Location: /dashboard");
	exit();
}

if(isset($_GET['code'])) {
	$token = $google->fetchAccessTokenWithAuthCode($_GET['code']);
	$google->setAccessToken($token['access_token']);
	$oauth = new Google_Service_Oauth2($google);
	$info = $oauth->userinfo->get();
	$name = $info->name;
	$email = $info->email;
	$org = $info->hd;
	$id = $info->id;
	$verified = $info->verifiedEmail;
	$picture = $info->picture;

	if(!$verified) {
		header("Location: /error");
		exit();
	}

	$q = mysqli_query($db, "SELECT * FROM `users` WHERE `guid` = '$id'");

	if(mysqli_num_rows($q) > 0) {
		$data = mysqli_fetch_assoc($q);
		$_SESSION['id'] = $data['id'];
		header("Location: /dashboard");
		exit();
	} else {
		mysqli_query($db, "INSERT INTO `users` (`guid`, `name`, `email`, `org`, `image`) VALUES ('$id', '$name', '$email', '$org', '$picture')");
		$_SESSION['id'] = mysqli_insert_id($db);
		header("Location: /dashboard");
		exit();
	}
} else {
	header("Location: " . $google->createAuthUrl());
	exit();
}
?>