<?php
	/**
	 * ad-settings.php
	 *
	 * The settings for this Storio instance
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

	// Load the user configuration
	$usrCfg = Storio::UserConfig(USER);

	if($siteCfg['allowRegistration'] == 'true') {
		$checkStat = 'checked';
	}else{
		$checkStat = '';
	}

	if($siteCfg['downloadPage'] == 'true') {
		$checkDown = 'checked';
	}else{
		$checkDown = '';
	}

	// Changing the password
	if(isset($_POST)) {
		// Set an updated flag
		$strUpd = 0;

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
			header("Location: ?page=ad-settings&success");
		}
	}

	// Changing site settings
	if(isset($_POST) && (!empty($_POST['siteName']) && !empty($_POST['defStore']) && !empty($_POST['maxUpload']))) {
		// Check numeric values
		if(is_numeric($_POST['defStore']) && is_numeric($_POST['maxUpload'])) {
			// Validate the site name
			if(preg_match('/^[a-z0-9 .\-]+$/i', $_POST['siteName'])) {
				// Set the new site name
				$siteCfg['siteName'] = $_POST['siteName'];

				// Set default storage
				$siteCfg['defaultAllowance'] = $_POST['defStore'];

				// Set max upload
				$siteCfg['uploadMaxMB'] = $_POST['maxUpload'];

				// Check whether to allow registration
				if(isset($_POST['userRegCheck']) && $_POST['userRegCheck'] == 'AllowReg') {
					$siteCfg['allowRegistration'] = true;
				}else{
					$siteCfg['allowRegistration'] = false;
				}

				// Whether to show the download page or not
				if(isset($_POST['userDlPage']) && $_POST['userDlPage'] == 'DownloadPage') {
					$siteCfg['downloadPage'] = true;
				}else{
					$siteCfg['downloadPage'] = false;
				}

				// Encode and resave the config
				$siteCfgEncode = json_encode($siteCfg);
				file_put_contents('../users/configs/site-settings.json', $siteCfgEncode);

				// Redirect
				header("Location: ?page=ad-settings&success");
			}
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
								<a class="nav-link" href="?page=ad-editor">
									<i class="bi bi-pen" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Editor
								</a>
							</li>
							-->
							<li class="nav-item">
								<a class="nav-link active" aria-current="true" href="?page=ad-settings">
									<i class="bi bi-gear" style="font-size: 2rem;"></i>
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
						<!-- System Settings -->
						<br />
						<h4 class="card-title">System Settings</h4>
						<br />
						<form method="post">
							<!-- Site name -->
							<div class="mb-3">

								<div class="row">
									<div class="col-6">
										<label for="siteName" class="form-label">Site Name</label>
										<input type="text" class="form-control" id="siteName" name="siteName" value="<?php echo $siteCfg['siteName']; ?>" aria-describedby="siteName">
									</div>

									<div class="col">
										<label class="form-check-label" for="userRegCheck">User registration</label><br />
										<input type="checkbox" class="form-check-input" id="userRegCheck" id="userRegCheck" name="userRegCheck" value="AllowReg" <?php echo $checkStat; ?>>
									</div>

									<div class="col">
										<label class="form-check-label" for="userDlPage">Download page</label><br />
										<input type="checkbox" class="form-check-input" id="userDlPage" id="userDlPage" name="userDlPage" value="DownloadPage" <?php echo $checkDown; ?>>
									</div>
								</div>
							</div>

							<div class="mb-3">
								<div class="row">
									<!-- Default storage size -->
									<div class="col">
										<label for="defStore" class="form-label">Default Storage</label>
										<div class="input-group">
											<input type="number" class="form-control" id="defStore" name="defStore" value="<?php echo $siteCfg['defaultAllowance']; ?>">
											<span class="input-group-text">MB</span>
										</div>
									</div>

									<!-- Max upload size -->
									<div class="col">
										<label for="maxUpload" class="form-label">Max Upload</label>
										<div class="input-group">
											<input type="number" class="form-control" id="maxUpload" name="maxUpload" value="<?php echo $siteCfg['uploadMaxMB']; ?>">
											<span class="input-group-text">MB</span>
										</div>
									</div>
								</div>
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
							</div>
							<button type="submit" class="btn btn-primary">Update</button>
						</form>
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