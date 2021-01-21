<?php
	/**
	 * us-files.php
	 *
	 * The file management page for users
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.aw0.uk
	 */

	// Redirect if not logged in
	if(!Storio::LoggedIn('admin')) {
		header("Location: ?page=login");
	}

	// Get the user list from the dir structure
	$dirs = array_filter(glob('users/*'), 'is_dir');
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Storio - File Management</title>

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
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbars" aria-controls="navbars" aria-expanded="false" aria-label="Toggle navigation">
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
								<a class="nav-link active" aria-current="true" href="?page=us-files">
									<i class="bi bi-folder" style="font-size: 2rem;"></i>
									<br />Files
								</a>
							</li>
							<li class="nav-item" style="width:12%;">
								<a class="nav-link" href="?page=us-settings">
									<i class="bi bi-gear" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Settings
								</a>
							</li>
						</ul>
					</div>
					<div class="card-body">
						<!-- File management -->
						<br />
						<h4 class="card-title">File Management</h4>
						<p class="card-text" style="margin-top:15px;">
							<?php
								// Are there users?
								if(count($dirs) > 1) {
									// Attempting to browse a users files
									if(isset($_GET['browse']) && !empty($_GET['browse'])) {
										// Check the folder exists
										if(is_dir('users/' . $_GET['browse'])) {
											// Use for the path
											$strPath = 'users/' . $_GET['browse'];

											// Use for str_replace
											$strRep = 'users/' . $_GET['browse'] . '/';

											// Save the arrays
											$fldArr = Storio::DirList('users/' . $_GET['browse']);

											// Start the row
											echo '<div class="row">';

											echo '<div class="col-md-4"><b>File name</b></div>';
											echo '<div class="col-md-4"><b>Size</b></div>';
											echo '<div class="col-md-4"><b>Actions</b></div>';
											echo '<hr>';

											// Check if there are subfolders first (avoid warnings)
											if(!empty($fldArr['dirview'][$strPath]['folders'])) {
												// Loop the folders first
												foreach($fldArr['dirview'][$strPath]['folders'] as $dir) {
													// Replace the beginning of the path
													$dir = str_replace($strRep, "", $dir);

													// Generate a link to subfolder
													$subLink = $_GET['browse'] . $dir . '/';

													// Folder icon
													$foldIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder" viewBox="0 0 16 16">
													<path d="M.54 3.87L.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z"/>
													</svg>';

													echo '<div class="col-md-4">' . $foldIco . ' <a href="?page=ad-files&browse=' . $subLink . '">' . $dir . '</a></div>';
													echo '<div class="col-md-4">n/a</div>';
													echo '<div class="col-md-4">n/a</div>';
												}
											}
											
											// Check if there are files first (avoid warnings)
											if(!empty($fldArr['dirview'][$strPath]['files'])) {
												// Loop the files after
												foreach($fldArr['dirview'][$strPath]['files'] as $file) {
													// Replace the beginning of the path
													$file = str_replace($strRep, "", $file);

													// File icon
													$fileIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-text" viewBox="0 0 16 16">
													<path d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zM5 8a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1H5z"/>
													<path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z"/>
													</svg>';

													echo '<div class="col-md-4">' . $fileIco . ' ' . $file . '</div>';
													echo '<div class="col-md-4">' . Storio::ReadableSize(filesize('users/' . $_GET['browse'] . $file)) . '</div>';
													echo '<div class="col-md-4">Download - Delete</div>';
												}
											}
											
											// End the row
											echo '</div>';

											//echo '<pre>';
											//print_r(dirlist('users/' . $_GET['browse']));
											//echo '</pre>';
										}
									}else{
										// Gen the table start
										echo '<table class="table table-hover">';
										echo '<thead>';
										echo '<tr>';
										echo '<th scope="col">User</th>';
										echo '<th scope="col">Storage</th>';
										echo '<th scope="col">Controls</th>';
										echo '</tr>';
										echo '</thead>';
										echo '<tbody>';

										// Loop users
										foreach($dirs as $usr) {
											// Remove the users/ prefix
											$usr = str_replace("users/", "", $usr);

											// Skip the configs dir
											if($usr == 'configs') {
												continue;
											}

											// Try and get the config
											if(file_exists('users/configs/' . $usr . '-cfg.json')) {
												$usrCfg = json_decode(file_get_contents('users/configs/' . $usr . '-cfg.json'), true);
											}

											// Add table row
											echo '<tr>';
											echo '<td>' . $usr . '</td>';
											echo '<td>' . number_format($usrCfg['usedStorage']) . ' / ' . number_format($usrCfg['maxStorage']) . ' MB</td>';
											echo '<td><a href="?page=ad-files&browse=' . $usr . '/">Browse Files</a></td>';
											echo '</tr>';
										}

										echo '</tbody>';
										echo '</table>';
									}
								}else{
									echo 'Storio does not have any users, would you like to <a href="?page=ad-users">create one</a>?';
								}
							?>
						</p>
					</div>
				</div>
			</div>
		</main>

		<script src="app/js/bootstrap.bundle.min.js"></script>
	</body>
</html>