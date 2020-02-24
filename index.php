<?php
include 'core/php/include.php';

if(isset($_SESSION['id'])) {
	header("Location: /dashboard");
	exit();
}
?>
<!doctype html>
<html lang="en" class="h-100">
	<head>
		<?php require('core/php/metas.php'); ?>
		<title>Home | POP by Solid Security</title>
	</head>
	<body class="d-flex flex-column h-100">
		<?php require('core/php/navbar.php'); ?>
		<main role="main" class="flex-shrink-0">
			<div class="container">
				<h1 class="mt-5">Proof of Presence (POP)</h1>
				<p class="lead">by Solid Security &nbsp;|&nbsp; <span class="text-muted">BETA</span></p>
				<hr>
				<p>Interested in deploying POP at your organization? Contact pop@solidsecurity.co to learn more.</p>
			</div>
		</main>
		<?php require('core/php/footer.php'); ?>
	</body>
</html>