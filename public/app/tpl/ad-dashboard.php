<?php
	/**
	 * ad-dashboard.php
	 *
	 * The admin dashboard
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2022 Storio
	 * @link       https://github.com/ialexpw/Storio
	 */

	// No direct access
	if(!defined('INC_DATA')) {
		exit('error');
	}

	// Redirect if not logged in
	if(!Storio::LoggedIn('admin')) {
		header("Location: ?page=login");
	}

	// Load the site configuration
	$siteCfg = Storio::SiteConfig();
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Storio - Dashboard</title>

		<link rel="canonical" href="">

		<!-- Bootstrap core CSS -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">

		<!-- Google fonts -->
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&display=swap">

		<!-- Custom styles -->
		<link rel="stylesheet" href="app/css/custom.css">
	</head>
	<body>
		<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
			<div class="container-fluid">
				<i class="bi bi-droplet" style="font-size: 2rem; margin-right:12px; margin-bottom:6px; color: cornflowerblue;"></i>
				<a class="navbar-brand" href="?page=ad-dashboard"> <?php echo $siteCfg['siteName']; ?></a>
			</div>
		</nav>

		<main class="container">
			<div class="starter-template py-5 px-3">
				<div class="card">
					<div class="card-header text-center">
						<ul class="nav nav-tabs card-header-tabs">
							<li class="nav-item">
								<a class="nav-link active" aria-current="true" href="?page=ad-dashboard">
									<i class="bi bi-house" style="font-size: 2rem;"></i>
									<br />Home
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="?page=ad-users">
									<i class="bi bi-people" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Users
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="?page=ad-settings">
									<i class="bi bi-gear" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Settings
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="?logout" style="color: indianred;">
									<i class="bi bi-x-octagon" style="font-size: 2rem; color: indianred;"></i>
									<br />Logout
								</a>
							</li>
						</ul>
					</div>
					<div class="card-body">
						<!-- Intro to the dashboard -->
						<br />
						<h4 class="card-title">Welcome to your Dashboard</h4>
						
						<p class="card-text">Welcome to the Admin Dashboard for Storio! Manage your website easily with the built-in controls on this panel and monitor the activity and usage of your users through the different tabs.</p>
						
						<h5 class="card-title">Users</h5>
						<p class="card-text" style="margin-top:15px;">Create new and edit existing users directly from your dashboard. Limit the actions they can do while logged in and the storage size they have available to them. Update permissions at any time.</p>

						<h5 class="card-title">Settings</h5>
						<p class="card-text" style="margin-top:15px;">Edit information such as the site name shown in the top banner, whether registrations are enabled and options such as direct downloads or via a download page. Limit the max upload size for files and the default storage for new users.</p>

						<p class="card-text"></p>
					</div>
				</div>
				<p class="text-center" style="margin-top:5px;">Powered by <a href="https://github.com/ialexpw/Storio">Storio</a></p>
			</div>
		</main>

		<script type="text/javascript" src="app/js/jquery.min.js"></script>

		<!-- Bootstrap JS -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
		
		<script type="text/javascript" src="app/js/session.js"></script>
	</body>
</html>