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
	$cat = $edata['group'];
}
?>
<!doctype html>
<html lang="en">
	<head>
		<?php require('core/php/metas.php'); ?>
		<title>Collect Attendance | POP by Solid Security</title>
		<style>
			.left {
				position: fixed;
				left: 0;
				top: 0;
				bottom: 0;
				width: 66vw;
			}
			.image {
				position: relative;
				width: 115%;
				height: 115%;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				background-size: contain;
				background-position: center;
				background-repeat: no-repeat;
			}
			.right {
				position: fixed;
				right: 0;
				top: 0;
				bottom: 0;
				width: 33vw;
				background: #f5f5f5;
				padding: 32px;
				/* border-left: 1px solid rgba(235, 235, 235) !important; */
			}
			.footer {
				position: absolute;
				bottom: 0;
				left: 0;
				right: 0;
				background: rgba(235, 235, 235) !important;
			}
			.logins {
				position: absolute;
				left: 0;
				right: 0;
				bottom: 62px;
				height: 50vh;
			}
			#logins {
				position: absolute;
				left: 0;
				right: 0;
				top: 0;
				bottom: 0;
				height: 100%;
				width: 100%;
				padding: 32px;
				display: flex;
				justify-content: flex-end;
				flex-direction: column;
				overflow: hidden;
				z-index: 8;
			}
			.user {
				background: rgba(55,55,55) !important;
				height: 100px;
				min-height: 100px !important;
				border-radius: 16px;
				position: relative;
				margin-top: 16px;
			}
			.user .profile {
				height: 70px;
				width: 70px;
				background: #f5f5f5;
				border-radius: 50%;
				position: absolute;
				top: 50%;
				left: 24px;
				transform: translateY(-50%);
				background-size: contain;
				background-repeat: no-repeat;
				background-position: center;
			}
			.user .details {	
			    height: auto;
			    position: absolute;
			    top: 50%;
			    left: 118px;
			    transform: translateY(-50%);
			    color: #FFF;
    			right: 24px;
			}
			.user i {
				position: absolute;
				top: 50%;
				right: 0;
				font-size: 32px;
				transform: translateY(-50%);
				color: #FFF;
			}

			#exit {
				color: #444 !important;
				position: absolute;
				top: 20px;
				right: 25px;
				font-size: 18px;
			}
			#exit:hover {
				color: #888 !important;
			}
			.logins h4 {
				position: absolute;
				top: 64px;
				left: 32px;
			}
			.log-sm {
				display: none;
				font-weight: bold;
				margin-bottom: 28px;
			}
			.xssh {
				display: none;
			}
			@media(max-height: 900px) {
				.logins {
					display: none;
				}
				.log-sm {
					display: block;
				}
			}
			@media(max-height: 580px) {
				.xshd {
					display: none;
				}
				.xssh {
					display: block;
				}
			}
			@media(max-width: 1190px) {
				.left {
					width: 50vw;
				}
				.right {
					width: 50vw;
				}
			}
			@media(max-width: 750px) {
				.left {
					width: 100vw;
					height: 50vh;
					top: 0;
					left: 0;
					right: 0;
					bottom: unset;
				}
				.right {
					width: 100vw;
					height: 50vh;
					bottom: 0;
					right: 0;
					left: 0;
					top: unset;
				}
				.xshd {
					display: none;
				}
				.xssh {
					display: none;
				}
			}
			@media(max-height: 550px) {
				.log-sm {
					display: none;
				}
			}
			@media(max-width: 350px) {
				.log-sm {
					display: none;
				}
			}
			@media (max-height: 490px) and (max-width: 750px) {
				.hh1 {
					display: none;
				}
				.lead {
					display: none;
				}
				#exit {
					display: none;
				}
				hr {
					display: none;
				}
				.right {
					height: 62px;
				}
				.left {
					height: auto;
					bottom: 62px;
				}
			} 
		</style>
	</head>
	<body>
		<div class="left">
			<div class="image" id="code">
		</div>
		<div class="right">
			<a id="exit" href="https://pop.solidsecurity.co/event/<?=$cat?>">
	          <i class="fas fa-times"></i>
	        </a>

			<p class="lead"><img src="/core/img/logo.png" height="50" alt="pop" style="margin-bottom: -25px"> <span class="text-muted">| BETA</span> &nbsp;at <?=$org?></p>
			<hr style="margin-bottom: 25px">
			<h1 class="hh1" style="font-size: 48px;"><b><?=$edata['name']?></b></h1>
			<h2 class="log-sm">Recorded attendees: <span class="num">0</span></h2>

			<div class="xshd">
				<h4 style="margin-bottom: -16px; margin-top: 16px;" class="text-muted">To record your attendance:</h4>
						
				<h3 style="margin-top: 26px; margin-bottom: 0;">1. Using your phone, visit</h3>
				<h1><i class="fad fa-mobile-android"></i> &nbsp;go.illinois.edu/pop</h1>

				<h3 style="margin-top: 26px; margin-bottom: 0;">2. Sign in to POP</h3>
				<p>Make sure you use your <b>illinois.edu</b> account when signing in.</p>

				<h3 style="margin-top: 26px; margin-bottom: 6px;">3. Scan the QR code</h3>
				<p>Keep scanning until the page turns green and "Attendance recorded successfully" is displayed.</p>
			</div>
			<div class="xssh">
				<h3 style="margin-top: 26px; margin-bottom: 0;">To record your attendance, visit:</h3>
				<h1><i class="fad fa-mobile-android"></i> &nbsp;go.illinois.edu/pop</h1>
			</div>

			<div class="logins">
				<h4 class="text-muted">Recorded attendees (<span class="num">0</span>):</h4>
				<div id="logins">

				</div>
			</div>

			<footer class="footer text-center py-3">
			  <div class="container">
			    <img src="https://www.solidsecurity.co/wp-content/uploads/2019/04/logo-18.png" height="30px"><span style="color: #000;"> &nbsp; &mdash; &nbsp; Copyright &copy;<?=date('Y')?> Solid Security.</span>
			  </div>
			</footer>
		</div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script>
			var oldurl = '';
			var oldusers = {};

			$(() => {
				setInterval(() => {
					jQuery.getJSON('https://pop.solidsecurity.co/qr.php', {
						id: '<?=$edata['id']?>'
					}, function(data) {
						if(oldurl != data.url) {
							$('#code').css('background-image', 'url(' + data.url + ')');
							oldurl = data.url;
						}
						if(oldusers != data.uhs) {
							var users = '';

							if(data.users.length >= 3) {
								var user = data.users[data.users.length - 3];
								users += `
								<div class="user" style="opacity: 0.33">
									<div class="profile" style="background-image: url('` + user.image + `');"></div>
									<div class="details">
										<p class="title" style="margin: 0; font-size: 18px;"><b>` + user.name + `</b></p>
										<p class="email" style="margin: 0;">` + user.email + `</p>
										<i class="fas fa-check-circle"></i>
									</div>
								</div>`;
							}

							if(data.users.length >= 2) {
								var user = data.users[data.users.length - 2];
								users += `
								<div class="user" style="opacity: 0.66">
									<div class="profile" style="background-image: url('` + user.image + `');"></div>
									<div class="details">
										<p class="title" style="margin: 0; font-size: 18px;"><b>` + user.name + `</b></p>
										<p class="email" style="margin: 0;">` + user.email + `</p>
										<i class="fas fa-check-circle"></i>
									</div>
								</div>`;
							}

							if(data.users.length >= 1) {
								var user = data.users[data.users.length - 1];
								users += `
								<div class="user">
									<div class="profile" style="background-image: url('` + user.image + `');"></div>
									<div class="details">
										<p class="title" style="margin: 0; font-size: 18px;"><b>` + user.name + `</b></p>
										<p class="email" style="margin: 0;">` + user.email + `</p>
										<i class="fas fa-check-circle"></i>
									</div>
								</div>`;
							}

							$("#logins").html(users);
							oldusers = data.uhs;
							$(".num").text(data.num);
						}
					});
				}, 500);
			});
		</script>
	</body>
</html>