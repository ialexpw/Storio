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

	// Changing the password
	if(isset($_POST)) {
		// Set an updated flag
		$strUpd = 0;

		// Load the user configuration
		$usrCfg = Storio::UserConfig($_SESSION['Username']);

		// Updating password
		if(!empty($_POST['currPass']) && !empty($_POST['newPass'])) {
			// Store variables
			$usrPass = $_POST['currPass'];
			$newPass = $_POST['newPass'];

			// Verify password
			if(password_verify($usrPass, $usrCfg['passWord'])) {
				// Alter the password hash
				$usrCfg['passWord'] = password_hash($newPass, PASSWORD_DEFAULT);

				$strUpd = 1;
			}
		}

		// Updating email
		if(!empty($_POST['usrMail']) && !empty($_POST['usrMail'])) {
			// Store variables
			$usrMail = $_POST['usrMail'];

			// Verify email
			if(filter_var($usrMail, FILTER_VALIDATE_EMAIL)) {
				// Alter the email
				$usrCfg['usrEmail'] = $usrMail;

				$strUpd = 1;
			}
		}

		// Config has been updated
		if($strUpd) {
			// Encode and resave the config
			$usrCfgEncode = json_encode($usrCfg);
			file_put_contents('../users/configs/' . $_SESSION['Username'] . '-cfg.json', $usrCfgEncode);

			// Redirect
			header("Location: ?page=us-settings&success");
		}
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

		<!-- Custom styles -->
		<link rel="stylesheet" href="app/css/custom.css">
	</head>
	<body>
		<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
			<div class="container-fluid">
				<i class="bi bi-droplet" style="font-size: 2rem; margin-right:12px; margin-bottom:6px; color: cornflowerblue;"></i>
				<a class="navbar-brand" href="?page=dashboard"> <?php echo $siteCfg['siteName']; ?></a>
			</div>
		</nav>

		<main class="container">
			<div class="starter-template py-5 px-3">
				<div class="card">
					<div class="card-header text-center">
						<ul class="nav nav-tabs card-header-tabs">
							<li class="nav-item">
								<a class="nav-link" href="?page=us-dashboard">
									<i class="bi bi-house" style="font-size: 2rem; color: cornflowerblue;"></i>
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

		<?php
			// Updated details
			if(isset($_GET['success'])) {
		?>
			<script>
				document.addEventListener("DOMContentLoaded", function(){
					$('.toast').toast('show');
				});
			</script>

			<!-- Toast notification for Share link -->
			<div class="toast-container position-absolute p-3 bottom-0 end-0" id="toastPlacement">
				<div class="toast align-items-center bg-info bottom-0 end-0" role="alert" aria-live="assertive" aria-atomic="true">
					<div class="d-flex">
						<div class="toast-body">
							Your details have been updated!
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