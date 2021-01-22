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
	if(!Storio::LoggedIn()) {
		header("Location: ?page=login");
	}

	// Get the user dir structure
	if(is_dir('users/' . $_SESSION['Username'])) {
		$dirs = array_filter(glob('users/' . $_SESSION['Username'] . '/*'), 'is_dir');

		// Set the static path (e.g. users/username)
		$usrDir = 'users/' . $_SESSION['Username'];

		// Store the browse (if any)
		if(!empty($_GET['browse'])) {
			$getBrowse = '/' . $_GET['browse'];
		}else{
			$getBrowse = '';
		}
	}else{
		// Something has gone wrong - user possibly deleted while logged in?
		header("Location: ?logout");
	}
	
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
						<h4 class="card-title">File Management (new <a href="#" data-bs-toggle="modal" data-bs-target="#userModal">file</a> or <a href="#" data-bs-toggle="modal" data-bs-target="#userModal">directory</a>)</h4>
						<p class="card-text" style="margin-top:15px;">
							<?php
								// Save the arrays
								$fldArr = Storio::DirList($usrDir . $getBrowse);

								// Start the row
								echo '<div class="row">';

								echo '<div class="col-md-4"><b>File name</b></div>';
								echo '<div class="col-md-4"><b>Size</b></div>';
								echo '<div class="col-md-4"><b>Actions</b></div>';
								echo '<hr>';

								// Check if there are subfolders first (avoid warnings)
								if(!empty($fldArr['dirview'][$usrDir.$getBrowse]['folders'])) {
									foreach($fldArr['dirview'][$usrDir.$getBrowse]['folders'] as $dir) {
										// Replace the beginning of the path
										$dir = str_replace($usrDir.$getBrowse.'/', "", $dir);

										// Generate a link to subfolder
										$subLink = $getBrowse . '/' . $dir;

										// Folder icon
										$foldIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder" viewBox="0 0 16 16">
										<path d="M.54 3.87L.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z"/>
										</svg>';

										echo '<div class="col-md-4">' . $foldIco . ' <a href="?page=us-files&browse=' . ltrim($subLink, '/') . '">' . $dir . '</a></div>';
										echo '<div class="col-md-4">n/a</div>';
										echo '<div class="col-md-4">n/a</div>';
									}
								}

								// Check if there are files first (avoid warnings)
								if(!empty($fldArr['dirview'][$usrDir.$getBrowse]['files'])) {
									// Loop the files after
									foreach($fldArr['dirview'][$usrDir.$getBrowse]['files'] as $file) {
										// Replace the beginning of the path
										$file = str_replace($usrDir.$getBrowse.'/', "", $file);

										// File icon
										$fileIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-text" viewBox="0 0 16 16">
										<path d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zM5 8a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1H5z"/>
										<path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z"/>
										</svg>';

										echo '<div class="col-md-4">' . $fileIco . ' ' . $file . '</div>';
										echo '<div class="col-md-4">' . Storio::ReadableSize(filesize($usrDir . $getBrowse. '/' . $file)) . '</div>';
										echo '<div class="col-md-4">Download - Delete</div>';
									}
								}

								// End the row
								echo '</div>';
							?>
						</p>
					</div>
				</div>
			</div>
		</main>

		<script src="app/js/bootstrap.bundle.min.js"></script>

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
							<!-- Username -->
							<div class="col-md-2">
								<label for="inputUser" class="form-label">Username</label>
								<input type="text" class="form-control" id="inputUser" name="inputUser" required>
							</div>

							<!-- Email address -->
							<div class="col-md-3">
								<label for="inputEmail" class="form-label">Email (optional)</label>
								<input type="email" class="form-control" id="inputEmail" name="inputEmail">
							</div>

							<!-- Password -->
							<div class="col-md-3">
								<label for="inputPass" class="form-label">Password</label>
								<input type="password" class="form-control" id="inputPass" name="inputPass" required>
							</div>

							<!-- Welcome email -->
							<div class="col-md-2">
								<label for="inputWelcome" class="form-label">Welcome email</label>
								<select id="inputWelcome" name="inputWelcome" class="form-select">
									<option value="true" selected>true</option>
									<option value="false">false</option>
								</select>
							</div>

							<!-- Admin user -->
							<div class="col-md-2">
								<label for="inputAdmin" class="form-label">Admin user</label>
								<select id="inputAdmin" name="inputAdmin" class="form-select">
									<option value="false" selected>false</option>
									<option value="true">true</option>
								</select>
							</div>

							<!-- Second line -->

							<!-- Storage allowance -->
							<div class="col-md-2">
								<label for="inputStorage" class="form-label">Storage (MB)</label>
								<input type="number" class="form-control" id="inputStorage" name="inputStorage" value="1000">
							</div>

							<!-- Can view files -->
							<div class="col-md-2">
								<label for="inputView" class="form-label">Can view</label>
								<select id="inputView" name="inputView" class="form-select">
									<option value="true" selected>true</option>
									<option value="false">false</option>
								</select>
							</div>

							<!-- Can upload files -->
							<div class="col-md-2">
								<label for="inputUpload" class="form-label">Can upload</label>
								<select id="inputUpload" name="inputUpload" class="form-select">
									<option value="true" selected>true</option>
									<option value="false">false</option>
								</select>
							</div>

							<!-- Can edit files -->
							<div class="col-md-2">
								<label for="inputEdit" class="form-label">Can edit</label>
								<select id="inputEdit" name="inputEdit" class="form-select">
									<option value="true" selected>true</option>
									<option value="false">false</option>
								</select>
							</div>

							<!-- Can share files -->
							<div class="col-md-2">
								<label for="inputShare" class="form-label">Can share</label>
								<select id="inputShare" name="inputShare" class="form-select">
									<option value="true" selected>true</option>
									<option value="false">false</option>
								</select>
							</div>

							<!-- Can delete files -->
							<div class="col-md-2">
								<label for="inputDelete" class="form-label">Can delete</label>
								<select id="inputDelete" name="inputDelete" class="form-select">
									<option value="true" selected>true</option>
									<option value="false">false</option>
								</select>
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