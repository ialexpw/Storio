<?php
	/**
	 * download.php
	 *
	 * Download page for individual files
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

	// Load the site configuration
	$siteCfg = Storio::SiteConfig();

	// Load the share links configuration
	$shareCfg = Storio::ShareLinks();

	if(isset($_GET['id'])) {
		$shareHash = $_GET['id'];

		if(empty($shareCfg['ShareLinks'][$shareHash]['File'])) {
			header("Location: /?page=404");
		}
	}else{
		header("Location: /?page=404");
	}
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Storio - Download</title>

		<link rel="canonical" href="">

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
				<a class="navbar-brand" href="?page=ad-dashboard"> <?php echo $siteCfg['siteName']; ?></a>
			</div>
		</nav>

		<main class="container">
			<div class="starter-template py-5 px-3">
				<div class="row">
					<div class="col-3"></div>

					<div class="col-6">
						<div class="card">
							<div class="card-body" style="text-align: center;">
								<!-- Download files -->
								<br />
								<h4 class="card-title">Download files</h4>

								<pre><?php echo $shareCfg['ShareLinks'][$shareHash]['File']; ?></pre>

								<a class="btn btn-outline-dark" href="/?dl=<?php echo $shareHash; ?>" role="button">Download File(s)</a>
								<br /><br />
							</div>
						</div>
					</div>

					<div class="col-3"></div>
				</div>
				<p class="text-center" style="margin-top:5px;">Powered by <a href="https://github.com/ialexpw/Storio">Storio</a> - <?php echo 'b. ' . shell_exec("git log -1 --pretty=format:'%h'"); ?></p>
			</div>
		</main>

		<script type="text/javascript" src="app/js/jquery.min.js"></script>
		<script type="text/javascript" src="app/js/bootstrap.bundle.min.js"></script>
		<script type="text/javascript" src="app/js/session.js"></script>
	</body>
</html>