<?php
	/**
	 * ad-users.php
	 *
	 * User management page for administrators
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

	// Load the site configuration
	$siteCfg = Storio::SiteConfig();

	// Get the user list from the dir structure
	$dirs = array_filter(glob('../users/*'), 'is_dir');

	// Validate and create a new user
	if(!empty($_POST)) {
		// If user validates/creates, reload page (avoid re-post)
		if(Storio::ValidateUserData($_POST)) {
			// Add to the log
			Storio::AddLog(time(), "User Added", $_POST['inputUser'] . ' has been created');

			// Reload
			header("Location: ?page=ad-users");
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
		<title>Storio - User Management</title>

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

			a {
				text-decoration:none;
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
								<a class="nav-link active" aria-current="true" href="?page=ad-users">
									<i class="bi bi-people" style="font-size: 2rem;"></i>
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
								<a class="nav-link" href="?page=ad-logs">
									<i class="bi bi-archive" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Logs
								</a>
							</li>
							-->
							<li class="nav-item">
								<a class="nav-link" href="?page=ad-settings">
									<i class="bi bi-gear" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Settings
								</a>
							</li>
						</ul>
					</div>
					<div class="card-body">
						<!-- File management -->
						<br />
						<h4 class="card-title">User Management (<a href="#" data-bs-toggle="modal" data-bs-target="#userModal">new</a>)</h4>
						<p class="card-text" style="margin-top:15px;">
							<?php
								// Are there users?
								if(count($dirs) > 1) {
									// Gen the table start
									echo '<table class="table table-responsive">';
									echo '<thead>';
									echo '<tr>';
									echo '<th scope="col">User</th>';
									echo '<th scope="col">Storage</th>';
									echo '<th scope="col">Enabled</th>';
									echo '<th scope="col">Upload</th>';
									echo '<th scope="col">Admin</th>';
									echo '</tr>';
									echo '</thead>';
									echo '<tbody>';

									// Loop users
									foreach($dirs as $usr) {
										// Remove the users/ prefix
										$usr = str_replace("../users/", "", $usr);

										// Skip the configs dir
										if($usr == 'configs') {
											continue;
										}

										// Try and get the config
										if(file_exists('../users/configs/' . $usr . '-cfg.json')) {
											$usrCfg = json_decode(file_get_contents('../users/configs/' . $usr . '-cfg.json'), true);
										}

										// Add table row
										echo '<tr>';
										echo '<td style="width:26%;">' . $usr . '</td>';

										// If admin user, storage is not used
										if($usrCfg['isAdmin'] == 'true') {
											echo '<td style="width:20%;">n/a</td>';
										}else{
											echo '<td style="width:20%;">' . number_format($usrCfg['usedStorage'], 2) . ' / ' . number_format($usrCfg['maxStorage']) . ' MB</td>';
										}
										
										echo '<td style="width:18%;">' . $usrCfg['isEnabled'] . '</td>';
										echo '<td style="width:18%;">' . $usrCfg['canUpload'] . '</td>';
										echo '<td style="width:18%;">' . $usrCfg['isAdmin'] . '</td>';
										echo '</tr>';
									}

									echo '</tbody>';
									echo '</table>';
								}else{
									echo 'There are no users, you can create one with the link above.';
								}
							?>
						</p>
					</div>
				</div>
				<p class="text-center" style="margin-top:5px;">Powered by <a href="https://storio.uk">Storio</a> - <?php echo 'b. ' . shell_exec("git log -1 --pretty=format:'%h'"); ?></p>
			</div>
		</main>

		<script type="text/javascript" src="app/js/jquery.min.js"></script>
		<script type="text/javascript" src="app/js/bootstrap.bundle.min.js"></script>
		<script type="text/javascript" src="app/js/session.js"></script>

		<!-- Modal -->
		<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="userModalLabel">Add new user</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form class="row g-3" method="post" action="?page=ad-users">
							<div class="row g-3">
								<!-- Username -->
								<div class="col-md">
									<label for="inputUser" class="form-label">Username</label>
									<input type="text" class="form-control" id="inputUser" name="inputUser" required>
								</div>

								<!-- Email address -->
								<div class="col-md">
									<label for="inputEmail" class="form-label">Email (optional)</label>
									<input type="email" class="form-control" id="inputEmail" name="inputEmail">
								</div>

								<!-- Password -->
								<div class="col-md">
									<label for="inputPass" class="form-label">Password</label>
									<input type="password" class="form-control" id="inputPass" name="inputPass" required>
								</div>

								<!-- Admin user -->
								<div class="col-md-2">
									<label for="inputAdmin" class="form-label">Admin user</label>
									<select id="inputAdmin" name="inputAdmin" class="form-select">
										<option value="false" selected>false</option>
										<option value="true">true</option>
									</select>
								</div>
							</div>

							<!-- Second line -->
							<div class="row g-3">
								<!-- Storage allowance -->
								<div class="col-md-3">
									<label for="inputStorage" class="form-label">Storage (MB)</label>
									<input type="number" class="form-control" id="inputStorage" name="inputStorage" value="<?php echo $siteCfg['defaultAllowance']; ?>">
								</div>

								<!-- Is the user enabled -->
								<div class="col-md">
									<label for="inputEnab" class="form-label">User enabled</label>
									<select id="inputEnab" name="inputEnab" class="form-select">
										<option value="true" selected>true</option>
										<option value="false">false</option>
									</select>
								</div>

								<!-- Can upload files -->
								<div class="col-md">
									<label for="inputUpload" class="form-label">Can upload</label>
									<select id="inputUpload" name="inputUpload" class="form-select">
										<option value="true" selected>true</option>
										<option value="false">false</option>
									</select>
								</div>
							</div>

							<!-- Footer and submit -->
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
								<button type="submit" class="btn btn-primary">Create</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>