<?php
	/**
	 * ad-users.php
	 *
	 * User management page for administrators
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

	// Get the user list from the dir structure
	$dirs = array_filter(glob('../users/*'), 'is_dir');

	// Validate and create a new user
	if(!empty($_POST)) {
		// If user validates/creates, reload page (avoid re-post)
		if(Storio::ValidateUserData($_POST)) {
			// Reload
			header("Location: ?page=ad-users");
		}
	}

	// Altering user settings
	if(isset($_POST['editUsrName']) && isset($_GET['usr'])) {
		// Check the user
		if(!empty($_GET['usr']) && is_dir('../users/' . $_GET['usr']) && $_POST['editUsrName'] == $_GET['usr']) {
			// Save the user
			$usrEdit = $_GET['usr'];

			// Load the configuration
			$usrCfg = Storio::UserConfig($usrEdit);

			// Validate storage
			if(!is_numeric($_POST['editStorage'])) {
				$usrCfg['maxStorage'] = 1000;
			}else{
				$usrCfg['maxStorage'] = $_POST['editStorage'];
			}

			// Alter the settings
			
			$usrCfg['isEnabled'] = $_POST['editEnab'];
			$usrCfg['canUpload'] = $_POST['editUpload'];
			$usrCfg['isAdmin'] = $_POST['editAdmin'];

			// Encode and resave the config
			$usrCfgEncode = json_encode($usrCfg);
			file_put_contents('../users/configs/' . $usrEdit . '-cfg.json', $usrCfgEncode);

			// Redirect back to page after change
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
						<!-- File management -->
						<br />
						<h4 class="card-title">User Management (<a href="#" data-bs-toggle="modal" data-bs-target="#userModal">new</a>)</h4>
						<p class="card-text" style="margin-top:15px;">
							<?php
								// Are there users?
								if(count($dirs) > 1) {
									// Gen the table start
									echo '<table class="table table-responsive table-bordered">';
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

										// If admin show icon
										if($usrCfg['isAdmin'] == 'true') {
											echo '<td style="width:26%;">';
											
											// Shield icon
											echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield" viewBox="0 0 16 16">
													<path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 7.159 7.159 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7.158 7.158 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z"/>
													</svg> ';
											echo $usr;
											
											// Do not allow editing yourself
											if($_SESSION['Username'] != $usr) {
												echo '<small><a class="noLink float-end editUsr" name="' . $usr . '" href="javascript:;" data-bs-toggle="modal" data-bs-target="#editUser">';

												// Edit icon
												echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
														<path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
														<path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
														</svg>';
										
												echo '</a></small>';
											}
											
											echo '</td>';
										}else{
											echo '<td style="width:26%;">';
											
											// User icon
											echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
													<path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
													</svg> ';
											echo $usr;
											echo '<small><a class="noLink float-end editUsr" name="' . $usr . '" href="javascript:;" data-bs-toggle="modal" data-bs-target="#editUser">';

											// Edit icon
											echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
													<path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
													<path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
													</svg>';
											
											echo '</a></small>';
											echo '</td>';
										}

										// If admin user, storage is not used
										if($usrCfg['isAdmin'] == 'true') {
											echo '<td style="width:20%;">';
											
											// Disabled icon
											echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square" viewBox="0 0 16 16">
													<path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
													<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
													</svg>';

											echo '</td>';
										}else{
											echo '<td style="width:20%;">' . number_format($usrCfg['usedStorage'], 2) . ' / ' . number_format($usrCfg['maxStorage']) . ' MB</td>';
										}
										
										// Enable/disable user
										if($usrCfg['isEnabled'] == 'true') {
											echo '<td style="width:18%;">';

											// Enabled icon
											echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square" viewBox="0 0 16 16">
													<path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
													<path d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.235.235 0 0 1 .02-.022z"/>
													</svg>';
											
											echo '</td>';
										}else{
											echo '<td style="width:18%;">';

											// Disabled icon
											echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square" viewBox="0 0 16 16">
													<path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
													<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
													</svg>';
											
											echo '</td>';
										}

										// If admin, upload disabled
										if($usrCfg['isAdmin'] != 'true') {
											// Enable/disable upload
											if($usrCfg['canUpload'] == 'true') {
												echo '<td style="width:18%;">';

												// Enabled icon
												echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square" viewBox="0 0 16 16">
														<path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
														<path d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.235.235 0 0 1 .02-.022z"/>
														</svg>';
												
												echo '</td>';
											}else{
												echo '<td style="width:18%;">';

												// Disabled icon
												echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square" viewBox="0 0 16 16">
														<path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
														<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
														</svg>';
												
												echo '</td>';
											}
										}else{
											echo '<td style="width:18%;">';

											// Disabled icon
											echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square" viewBox="0 0 16 16">
													<path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
													<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
													</svg>';
											
											echo '</td>';
										}

										// Enable/disable admin user
										if($usrCfg['isAdmin'] == 'true') {
											echo '<td style="width:18%;">';

											// Enabled icon
											echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square" viewBox="0 0 16 16">
													<path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
													<path d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.235.235 0 0 1 .02-.022z"/>
													</svg>';
											
											echo '</td>';
										}else{
											echo '<td style="width:18%;">';

											// Disabled icon
											echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square" viewBox="0 0 16 16">
													<path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
													<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
													</svg>';
											
											echo '</td>';
										}

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
				<p class="text-center" style="margin-top:5px;">Powered by <a href="https://github.com/ialexpw/Storio">Storio</a></p>
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

		<!-- Modal for user edit -->
		<div class="modal fade" id="editUser" tabindex="-1" aria-labelledby="editUserLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="editUserLabel">User configuration</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div id="usrDetails"></div>
					</div>
				</div>
			</div>
		</div>

		<script>
			// Pop up modal with request files dialog
			$(document).ready(function(){
				$('.editUsr').click(function() {
					console.log("clicked");
					usrPath = this.name;
					$('#usrDetails').html("");
					$('#editUser').on('shown.bs.modal', function () {
						$.ajax({
							type: 'GET',
							url: "editUser.php?uid="+usrPath,
							success:function(data){
								$('#usrDetails').html(data);
								delete usrPath;
							}
						});
					});
				});
			});
		</script>
	</body>
</html>