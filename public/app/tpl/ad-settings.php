<?php
	/**
	 * ad-settings.php
	 *
	 * The settings for this Storio instance
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.uk
	 */

	// No direct access
	if(!defined('INC_DATA')) {
		exit('error');
	}

	// Redirect if not logged in
	if(!Storio::LoggedIn('admin')) {
		header("Location: ?page=login");
	}

	// Load the user configuration
	$usrCfg = Storio::UserConfig($_SESSION['Username']);
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Storio - Settings</title>

		<link rel="canonical" href="https://storio.aw0.uk">

		<!-- Bootstrap core CSS -->
		<link rel="stylesheet" href="app/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&display=swap">

		<style>
			* {
				font-family: 'Nunito', sans-serif;
			}

			.nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
				height:94px;
			}
		</style>

		<!-- Custom styles -->
		<link rel="stylesheet" href="app/css/custom.css">
	</head>
	<body>
		<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
			<div class="container-fluid">
				<i class="bi bi-droplet" style="font-size: 2rem; margin-right:12px; margin-bottom:6px; color: cornflowerblue;"></i>
				<a class="navbar-brand" href="?page=ad-dashboard"> Storio File Management</a>
			</div>
		</nav>

		<main class="container">
			<div class="starter-template py-5 px-3">
				<div class="card">
					<div class="card-header text-center">
						<ul class="nav nav-tabs card-header-tabs">
							<li class="nav-item">
								<a class="nav-link" href="?page=ad-dashboard">
									<i class="bi bi-house" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Home
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="?page=ad-users">
									<i class="bi bi-people" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Users
								</a>
							</li>
							<!--
							<li class="nav-item">
								<a class="nav-link" href="?page=ad-messages">
									<i class="bi bi-chat" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Messages
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link"  href="?page=ad-logs">
									<i class="bi bi-archive" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Logs
								</a>
							</li>
							-->
							<li class="nav-item">
								<a class="nav-link active" aria-current="true" href="?page=ad-settings">
									<i class="bi bi-gear" style="font-size: 2rem;"></i>
									<br />Settings
								</a>
							</li>
						</ul>
					</div>
					<div class="card-body">
						<!-- System Settings -->
						<br />
						<h4 class="card-title">System Settings</h4>
						<br />
						<form method="post">
							<!-- Original password -->
							<div class="mb-3">
								<label for="currPass" class="form-label">Site Name</label>
								<input type="text" class="form-control" id="currPass" name="currPass" aria-describedby="currPass">
							</div>

							<!-- New password -->
							<div class="mb-3">
								<label for="newPass" class="form-label">New password</label>
								<input type="password" class="form-control" id="newPass" name="newPass" aria-describedby="newPass">
								<div id="passHelp" class="form-text">Make it a good one!</div>
							</div>

							<!-- Email -->
							<div class="mb-3">
								<label for="usrMail" class="form-label">Email</label>
								<input type="email" class="form-control" id="usrMail" name="usrMail" value="<?php echo $usrCfg['usrEmail']; ?>" aria-describedby="usrMail">
								<div id="passHelp" class="form-text">Optional</div>
							</div>
							<button type="submit" class="btn btn-primary">Update</button>
						</form>

						<br />
						<hr />
						<br />

						<h4 class="card-title">User Settings</h4>
						<br />
						<form method="post">
							<!-- Original password -->
							<div class="mb-3">
								<label for="currPass" class="form-label">Current password</label>
								<input type="password" class="form-control" id="currPass" name="currPass" aria-describedby="currPass">
							</div>

							<!-- New password -->
							<div class="mb-3">
								<label for="newPass" class="form-label">New password</label>
								<input type="password" class="form-control" id="newPass" name="newPass" aria-describedby="newPass">
								<div id="passHelp" class="form-text">Make it a good one!</div>
							</div>

							<!-- Email -->
							<div class="mb-3">
								<label for="usrMail" class="form-label">Email</label>
								<input type="email" class="form-control" id="usrMail" name="usrMail" value="<?php echo $usrCfg['usrEmail']; ?>" aria-describedby="usrMail">
								<div id="passHelp" class="form-text">Optional</div>
							</div>
							<button type="submit" class="btn btn-primary">Update</button>
						</form>
					</div>
				</div>
			</div>
		</main>

		<script type="text/javascript" src="app/js/jquery.min.js"></script>
		<script type="text/javascript" src="app/js/bootstrap.bundle.min.js"></script>
		<script type="text/javascript" src="app/js/session.js"></script>
	</body>
</html>