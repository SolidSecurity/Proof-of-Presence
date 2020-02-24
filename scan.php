<?php
include 'core/php/include.php';
use OTPHP\TOTP;

$_SESSION['scan'] = true;

if(isset($_GET['code1']) && isset($_GET['code2'])) {
	$str1 = explode("/", $_GET['code1']);
	$str2 = explode("/", $_GET['code2']);
	
	if(empty($_GET['vote'])) {
	    $vote = "";
	} else {
    	$vote = mysqli_real_escape_string($db, substr($_GET['vote'], 0, 1));
	}

	if(count($str1) != 2 && count($str1) != 2) {
		die("FAILURE 00");
	}

	if(!isset($_SESSION['id'])) {
		die("FAILURE 99");
	}

	if($org != "Illinois") {
		die("FAILURE 98");
	}

	$org1 = $str1[0];
	$org2 = $str2[0];

	if($org1 == $org2) {
		$code1 = $str1[1];
		$code2 = $str2[1];

		if($code1 != $code2) {
			$org = mysqli_real_escape_string($db, $org1);
			$q = mysqli_query($db, "SELECT * FROM `events` WHERE `id` = '$org'");
			if(mysqli_num_rows($q) > 0) {
				$data = mysqli_fetch_assoc($q);

				$key = $data['key'];

				$totp = TOTP::create(
					$key,
					1,
					'sha512',
					10
				);

				$window = 4;
				if($totp->verify($code1, null, $window) && $totp->verify($code2, null, $window)) {
					$event = $data['id'];
					$user = $user_data['id'];
					$name = $user_data['name'];
					$ename = $data['name'];
					$email = $user_data['email'];
					$image = $user_data['image'];

					$q = mysqli_query($db, "SELECT * FROM `attendees` WHERE `event` = '$event' AND `user` = '$user'");

					if(mysqli_num_rows($q) != 0) {
						die("REPEAT $ename");
					} else {
						mysqli_query($db, "INSERT INTO `attendees` (`event`, `user`, `name`, `email`, `image`, `vote`) VALUES ('$event', '$user', '$name', '$email', '$image', '$vote')");
						die("SUCCESS $ename");
					}
				} else {
					die("FAILURE 04");
				}
			} else {
				die("FAILURE 03");
			}
		} else {
			die("FAILURE 02");
		}
	} else {
		die("FAILURE 01");
	}
}

if(!isset($_GET['org']) || empty($_GET['org'])) {
	header("Location: /scan/illinois");
	exit();
}
?>
<!doctype html>
<html lang="en" class="h-100">
	<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
		<?php require('core/php/metas.php'); ?>
		<title>Scan | POP by Solid Security</title>
		<style>
			body {
				background: #f5f5f5;
			}
			footer {
				background: #ebebeb !important;
			}
			#loadingMessage {
				text-align: center;
				padding: 40px;
				background-color: #eee;
			}

			#canvas {
				width: 100%;
			}

			#output {
				margin-top: 20px;
				background: #eee;
				padding: 10px;
				padding-bottom: 0;
			}

			#output div {
				padding-bottom: 10px;
				word-wrap: break-word;
			}

			#noQRFound {
				text-align: center;
			}

			#success {
				background: #0F9D58;
				position: fixed;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				z-index: 100;
				color: #FFF;
				height: 0%;
				overflow: hidden;
			}
			#vote {
				background: #2196F3;
				position: fixed;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				z-index: 100;
				color: #FFF;
				height: 100%;
				overflow: hidden;
			}
			.success-checkmark {
			  width: 80px;
			  height: 115px;
			  margin: 0 auto;
			}
			.success-checkmark .check-icon {
			  width: 80px;
			  height: 80px;
			  position: relative;
			  border-radius: 50%;
			  box-sizing: content-box;
			  border: 4px solid #FFF;
			}
			.success-checkmark .check-icon::before {
			  top: 3px;
			  left: -2px;
			  width: 30px;
			  transform-origin: 100% 50%;
			  border-radius: 100px 0 0 100px;
			}
			.success-checkmark .check-icon::after {
			  top: 0;
			  left: 30px;
			  width: 60px;
			  transform-origin: 0 50%;
			  border-radius: 0 100px 100px 0;
			  animation: rotate-circle 4.25s ease-in;
			}
			.success-checkmark .check-icon::before, .success-checkmark .check-icon::after {
			  content: '';
			  height: 100px;
			  position: absolute;
			  background: #0F9D58;
			  transform: rotate(-45deg);
			}
			.success-checkmark .check-icon .icon-line {
			  height: 5px;
			  background-color: #FFF;
			  display: block;
			  border-radius: 2px;
			  position: absolute;
			  z-index: 10;
			}
			.success-checkmark .check-icon .icon-line.line-tip {
			  top: 46px;
			  left: 14px;
			  width: 25px;
			  transform: rotate(45deg);
			  animation: icon-line-tip 0.75s;
			}
			.success-checkmark .check-icon .icon-line.line-long {
			  top: 38px;
			  right: 8px;
			  width: 47px;
			  transform: rotate(-45deg);
			  animation: icon-line-long 0.75s;
			}
			.success-checkmark .check-icon .icon-circle {
			  top: -4px;
			  left: -4px;
			  z-index: 10;
			  width: 80px;
			  height: 80px;
			  border-radius: 50%;
			  position: absolute;
			  box-sizing: content-box;
			  border: 4px solid rgba(255, 255, 255, 0.5);
			}
			.success-checkmark .check-icon .icon-fix {
			  top: 8px;
			  width: 5px;
			  left: 26px;
			  z-index: 1;
			  height: 85px;
			  position: absolute;
			  transform: rotate(-45deg);
			  background-color: #0F9D58;
			}

			@keyframes rotate-circle {
			  0% {
			    transform: rotate(-45deg);
			  }
			  5% {
			    transform: rotate(-45deg);
			  }
			  12% {
			    transform: rotate(-405deg);
			  }
			  100% {
			    transform: rotate(-405deg);
			  }
			}
			@keyframes icon-line-tip {
			  0% {
			    width: 0;
			    left: 1px;
			    top: 19px;
			  }
			  54% {
			    width: 0;
			    left: 1px;
			    top: 19px;
			  }
			  70% {
			    width: 50px;
			    left: -8px;
			    top: 37px;
			  }
			  84% {
			    width: 17px;
			    left: 21px;
			    top: 48px;
			  }
			  100% {
			    width: 25px;
			    left: 14px;
			    top: 45px;
			  }
			}
			@keyframes icon-line-long {
			  0% {
			    width: 0;
			    right: 46px;
			    top: 54px;
			  }
			  65% {
			    width: 0;
			    right: 46px;
			    top: 54px;
			  }
			  84% {
			    width: 55px;
			    right: 0px;
			    top: 35px;
			  }
			  100% {
			    width: 47px;
			    right: 8px;
			    top: 38px;
			  }
			}
			.check-icon {
				display: none;
			}
			.success-checkmark {
				transform: scale(2.0);
			}
			.center {
				position: fixed;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
			}
		</style>
	</head>
	<body class="d-flex flex-column h-100">
		<div id="success">
			<div class="container" style="padding: 32px;">
				<p class="lead"><img src="/core/img/logo.png" height="50" alt="pop" style="margin-bottom: -25px; filter: brightness(100);"> <span class="text-muted" style="color: #FFF !important;">| BETA</span> &nbsp;at Illinois</p>
				<hr style="margin-bottom: 25px; background: #FFF;">
				<h1 id="title"></h1>
				<p style="margin-top: -10px" id="status">Attendance recorded successfully.</p>
				<div class="center">
					<div class="success-checkmark">
					  <div class="check-icon">
					    <span class="icon-line line-tip"></span>
					    <span class="icon-line line-long"></span>
					    <div class="icon-circle"></div>
					    <div class="icon-fix"></div>
					  </div>
					</div>
				</div>
			</div>
			<footer class="footer mt-auto py-3 text-center" style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(255,255,255,0.5) !important;">
			  <div class="container">
			    <img src="https://www.solidsecurity.co/wp-content/uploads/2019/04/logo-18.png" height="30px"><span style="color: #000;"> &nbsp; &mdash; &nbsp; Copyright &copy;<?=date('Y')?> Solid Security.</span>
			  </div>
			</footer>
		</div>
		<main role="main" class="flex-shrink-0">
			<div class="container" style="padding: 32px;">
				<p class="lead"><img src="/core/img/logo.png" height="50" alt="pop" style="margin-bottom: -25px"> <span class="text-muted">| BETA</span> &nbsp;at Illinois</p>
				<hr style="margin-bottom: 25px">
				<?php if(isset($_SESSION['id'])) { ?>
					<?php if($org == "Illinois") { ?>
                		<div id="vote">
                			<div class="container" style="padding: 32px;">
                				<p class="lead"><img src="/core/img/logo.png" height="50" alt="pop" style="margin-bottom: -25px; filter: brightness(100);"> <span class="text-muted" style="color: #FFF !important;">| BETA</span> &nbsp;at Illinois</p>
                				<hr style="margin-bottom: 25px; background: #FFF;">
                				<h1 id="title"></h1>
                				<p style="margin-top: -10px" id="status">Please select your vote:</p>
                				<a href="#" id="vote-a" class="btn btn-outline-light btn-lg btn-block">A</a>
                				<a href="#" id="vote-b" class="btn btn-outline-light btn-lg btn-block">B</a>
                				<a href="#" id="vote-c" class="btn btn-outline-light btn-lg btn-block">C</a>
                				<a href="#" id="vote-d" class="btn btn-outline-light btn-lg btn-block">D</a>
                			</div>
                			<footer class="footer mt-auto py-3 text-center" style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(255,255,255,0.5) !important;">
                			  <div class="container">
                			    <img src="https://www.solidsecurity.co/wp-content/uploads/2019/04/logo-18.png" height="30px"><span style="color: #000;"> &nbsp; &mdash; &nbsp; Copyright &copy;<?=date('Y')?> Solid Security.</span>
                			  </div>
                			</footer>
                		</div>
						<h4><i class="fas fa-user"></i> &nbsp;<?=$user_data['name']?></h4>
						<p>Scan the QR code on the screen to record your attendance for the event.</p>
						<div id="loadingMessage">🎥 Unable to access video stream. Please enable camera access to continue.</div>
						<canvas id="canvas" hidden></canvas>
						<script src="https://gitcdn.xyz/repo/cozmo/jsQR/master/dist/jsQR.js"></script>
						<script>
							var video = document.createElement("video");
						    var canvasElement = document.getElementById("canvas");
						    var canvas = canvasElement.getContext("2d");
						    var loadingMessage = document.getElementById("loadingMessage");

						    var codes = [];

						    function drawLine(begin, end, color) {
						      canvas.beginPath();
						      canvas.moveTo(begin.x, begin.y);
						      canvas.lineTo(end.x, end.y);
						      canvas.lineWidth = 4;
						      canvas.strokeStyle = color;
						      canvas.stroke();
						    }

						    var success = false;

						    // Use facingMode: environment to attemt to get the front camera on phones
						    navigator.mediaDevices.getUserMedia({ audio: false, video: { facingMode: "environment" } }).then(function(stream) {
								video.setAttribute('autoplay', true);
								video.setAttribute('muted', true);
								video.setAttribute('playsinline', true);
						      video.srcObject = stream;
						      video.play();
						      requestAnimationFrame(tick);
						    });

						    function sendCodes(codes) {
						    	$.get('https://pop.solidsecurity.co/scan.php', {
						    		code1: codes[codes.length - 1],
									code2: codes[codes.length - 2],
									vote: vote
						    	}, function(data) {
						    		if(data.indexOf("SUCCESS") == 0) {
						    			success = true;
						    			var name = data.substr(8);
						    			$("#title").text(name);
						    			$("#success").animate({height: '100%'}, 300);
						    			setTimeout(function(){
						    				$(".check-icon").show();
						    			}, 300);
						    		} else if(data.indexOf("REPEAT") == 0) {
						    			success = true;
						    			var name = data.substr(7);
						    			$("#title").text(name);
						    			$("#status").text("Attendance already recorded.");
						    			$("#success").animate({height: '100%'}, 300);
						    			setTimeout(function(){
						    				$(".check-icon").show();
						    			}, 300);
						    		}
						    	});
						    }
						    
						    var vote = 'x';
						    
						    document.getElementById("vote-a").onclick = function(){
						        vote = 'A';
						    	$("#vote").animate({height: '0%'}, 300);
						    }
						    
						    document.getElementById("vote-b").onclick = function(){
						        vote = 'B';
						    	$("#vote").animate({height: '0%'}, 300);
						    }
						    
						    document.getElementById("vote-c").onclick = function(){
						        vote = 'C';
						    	$("#vote").animate({height: '0%'}, 300);
						    }
						    
						    document.getElementById("vote-d").onclick = function(){
						        vote = 'D';
						    	$("#vote").animate({height: '0%'}, 300);
						    }

						    function tick() {
						      if(!success) {
							      loadingMessage.innerText = "⌛ Loading video...";
							      if (video.readyState === video.HAVE_ENOUGH_DATA) {
							        loadingMessage.hidden = true;
							        canvasElement.hidden = false;

							        canvasElement.height = video.videoHeight;
							        canvasElement.width = video.videoWidth;
							        canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
							        var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
							        var code = jsQR(imageData.data, imageData.width, imageData.height, {
							          inversionAttempts: "dontInvert",
							        });
							        if (code) {
							          drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
							          drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
							          drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
							          drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");

							          if(codes.length == 0 || code.data != codes[codes.length - 1]) {
							          	codes.push(code.data);

								        if(codes.length >= 2) {
								          sendCodes(codes);
								        }
							          }
							        }
							      }
							      requestAnimationFrame(tick);
						      }
						    }
						</script>
					<?php } else { ?>
						<?php unset($_SESSION['id']); ?>
						<p>Accounts from <b><?=$domain?></b> are not allowed to participate in this event.</p>
						<p>Sign in with your <b>illinois.edu</b> account to continue.</p>
						<a href="/login">
							<button class="btn btn-primary btn-lg" type="submit"><i class="fas fa-sign-in-alt"></i> &nbsp;Login</button>
						</a>
					<?php } ?>
				<?php } else { ?>
					<p>Proof of Presence (POP) is an experimental technology used by organizations like yours to monitor event attendance and participation.</p>
					<p>Sign in with your illinois.edu account to continue.</p>
					<a href="/login">
						<button class="btn btn-primary btn-lg" type="submit"><i class="fas fa-sign-in-alt"></i> &nbsp;Login</button>
					</a>
				<?php } ?>
			</div>
		</main>
		<footer class="footer mt-auto py-3 text-center">
		  <div class="container">
		    <img src="https://www.solidsecurity.co/wp-content/uploads/2019/04/logo-18.png" height="30px"><span class="text-muted"> &nbsp; &mdash; &nbsp; Copyright &copy;<?=date('Y')?> Solid Security.</span>
		  </div>
		</footer>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	</body>
</html>