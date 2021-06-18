<?php
	/**
	 * us-dashboard.php
	 *
	 * The user dashboard
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
	if(!Storio::LoggedIn()) {
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

		<link rel="canonical" href="https://storio.aw0.uk">

		<!-- Bootstrap core CSS -->
		<link rel="stylesheet" href="app/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&display=swap">

		<!-- Custom styles -->
		<link rel="stylesheet" href="app/css/custom.css">
	</head>
	<body>
		<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
			<div class="container-fluid">
				<i class="bi bi-droplet" style="font-size: 2rem; margin-right:12px; margin-bottom:6px; color: cornflowerblue;"></i>
				<a class="navbar-brand" href="?page=us-dashboard"> <?php echo $siteCfg['siteName']; ?></a>
			</div>
		</nav>

		<main class="container">
			<div class="starter-template py-5 px-3">
				<div class="card">
					<div class="card-header text-center">
						<ul class="nav nav-tabs card-header-tabs">
							<li class="nav-item">
								<a class="nav-link active" aria-current="true" href="?page=us-dashboard">
									<i class="bi bi-house" style="font-size: 2rem;"></i>
									<br />Home
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="?page=us-files">
									<i class="bi bi-folder" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Files
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="?page=us-settings">
									<i class="bi bi-gear" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Settings
								</a>
							</li>
						</ul>
					</div>
					<div class="card-body">
						<!-- Intro to the dashboard -->
						<br />
						<h4 class="card-title">Welcome to Storio!</h4>
						<p class="card-text">Storio makes it easy to Upload, Store and Share your files across the web. View media files directly from your account and organise all of them into their appropriate folders and sub-folders.</p>
						
						<h5 class="card-title">Upload</h5>
						<p class="card-text" style="margin-top:15px;">With Storio you can upload multiple files at the same time into your account, either choose them from your standard file-picker or drag and drop them from your computer directly into your webpage.</p>

						<h5 class="card-title">Store</h5>
						<p class="card-text" style="margin-top:15px;">Create folders within Storio to sort your content into different categories and keep track of files with unique file-type icons throughout the application.</p>

						<h5 class="card-title">Share</h5>
						<p class="card-text" style="margin-top:15px;">Unique sharing links can be created for each individual file. Storio has the ability to remove or regenerate sharing links to void access to people at any time.</p>

						<p class="card-text"></p>
					</div>
				</div>
			</div>
		</main>

		<script type="text/javascript" src="app/js/jquery.min.js"></script>
		<script type="text/javascript" src="app/js/bootstrap.bundle.min.js"></script>
		<script type="text/javascript" src="app/js/session.js"></script>

		<?php
			// Just logged in
			if(isset($_GET['li'])) {
		?>
			<script>
				document.addEventListener("DOMContentLoaded", function(){
					$('.toast').toast('show');
				});
			</script>

			<!-- Toast notification -->
			<div class="toast-container position-absolute p-3 bottom-0 end-0" id="toastPlacement">
				<div class="toast align-items-center text-white bg-success bottom-0 end-0" role="alert" aria-live="assertive" aria-atomic="true">
					<div class="d-flex">
						<div class="toast-body">
							Logged in successfully! Welcome to Storio.
						</div>
						<button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
					</div>
				</div>
			</div>
		<?php
			}
		?>
	</body>
</html>