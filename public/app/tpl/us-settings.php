<?php
	/**
	 * us-settings.php
	 *
	 * The user settings
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.uk
	 */

	// Redirect if not logged in
	if(!Storio::LoggedIn()) {
		header("Location: ?page=login");
	}
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
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
			</div>
		</nav>

		<main class="container">
			<div class="starter-template py-5 px-3">
				<div class="card">
					<div class="card-header text-center">
						<ul class="nav nav-tabs card-header-tabs">
							<li class="nav-item" style="width:12%;">
								<a class="nav-link" href="?page=us-dashboard">
									<i class="bi bi-house" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Dashboard
								</a>
							</li>
							<li class="nav-item" style="width:12%;">
								<a class="nav-link" href="?page=us-files">
									<i class="bi bi-folder" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Files
								</a>
							</li>
							<li class="nav-item" style="width:12%;">
								<a class="nav-link active" aria-current="true" href="?page=us-settings">
									<i class="bi bi-gear" style="font-size: 2rem;"></i>
									<br />Settings
								</a>
							</li>
						</ul>
					</div>
					<div class="card-body">
						<!-- User Settings -->
						<br />
						<h4 class="card-title">User Settings</h4>
						<p class="card-text" style="margin-top:15px;">Change password</p>
					</div>
				</div>
			</div>
		</main>

		<script src="app/js/bootstrap.bundle.min.js"></script>
	</body>
</html>