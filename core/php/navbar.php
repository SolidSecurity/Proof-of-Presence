<nav class="navbar navbar-expand-lg navbar-light bg-light">
	<div class="container">
		<?php
		if($org == false) {
		?>
		<a class="navbar-brand" href="#">
			<img src="/core/img/logo.png" height="50" alt="pop" style="margin-bottom: -25px">
		</a>
		<?php
		} else {
		?>
		<a class="navbar-brand" href="/dashboard">
			<img src="/core/img/logo.png" height="50" alt="pop" style="margin-bottom: -25px"> &nbsp;at <?=$org?>
		</a>
		<?php
		}
		?>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbar">
			<ul class="navbar-nav ml-auto">
				<?php
				if(isset($_SESSION['id'])) {
				?>
				<li class="nav-item">
					<a href="/logout">
						<button class="btn btn-primary my-2 my-sm-0" type="submit"><i class="fas fa-sign-out-alt"></i> &nbsp;Logout</button>
					</a>
				</li>
				<?php
				} else {
				?>
				<li class="nav-item">
					<a href="/login">
						<button class="btn btn-primary my-2 my-sm-0" type="submit"><i class="fas fa-sign-in-alt"></i> &nbsp;Login</button>
					</a>
				</li>
				<?php
				}
				?>
			</ul>
		</div>
	</div>
</nav>