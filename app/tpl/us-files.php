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

	// Creating a new folder
	if(!empty($_POST['inpFolder']) && $_POST['usrSes'] == $_SESSION['Username']) {
		// Validate folder here

		// If folder does not already exist
		if(!is_dir($usrDir . $_POST['uplFld'] . '/' . $_POST['inpFolder'])) {
			if(mkdir($usrDir . $_POST['uplFld'] . '/' . $_POST['inpFolder'])) {
				// Add to the log
				Storio::AddLog(time(), "Folder Created", $_SESSION['Username'] . ' has created a new folder named ' . $_POST['inpFolder']);

				// Reload
				header('Location: ' . $_SERVER['REQUEST_URI']);
			}
		}
	}

	//Storio::delTree('users/user/testing directory');
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
						<h4 class="card-title">File Management (new <a href="#" data-bs-toggle="modal" data-bs-target="#fileModal">file</a> or <a href="#" data-bs-toggle="modal" data-bs-target="#folderModal">directory</a>)</h4>
						<p class="card-text" style="margin-top:15px;">
							<?php
								// Save the arrays
								$fldArr = Storio::DirList($usrDir . $getBrowse);

								// Start the row
								echo '<div class="row">';

								echo '<div class="col-md-6"><b>File name</b></div>';
								echo '<div class="col-md-2" style="text-align:center;"><b>Type</b></div>';
								echo '<div class="col-md-2" style="text-align:center;"><b>Size</b></div>';
								echo '<div class="col-md-2" style="text-align:center;"><b>Actions</b></div>';
								echo '<hr>';

								// Show the back button if needed
								if(!empty($_GET['browse'])) {
									// Arrow icon
									$arrIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
									<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
								  </svg>';

									echo '<div class="col-md-12">';
									echo '<a href="?page=us-files' . Storio::GoBack($_GET['browse']) . '">' . $arrIco . '</a>';
									echo '</div>';
								}
								

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

										echo '<div class="col-md-6">' . $foldIco . ' <a href="?page=us-files&browse=' . ltrim($subLink, '/') . '">' . $dir . '</a></div>';
										echo '<div class="col-md-2" style="text-align:center;">directory</div>';
										echo '<div class="col-md-2" style="text-align:center;">n/a</div>';
										echo '<div class="col-md-2" style="text-align:center;"><span style="color:red;">Delete</span></div>';
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

										// Download icon
										$dlIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
										<path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
										<path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
										</svg>';

										$copyIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard-check" viewBox="0 0 16 16">
										<path fill-rule="evenodd" d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
										<path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
										<path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
										</svg>';

										$delIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square" viewBox="0 0 16 16">
										<path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
										<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
										</svg>';

										echo '<div class="col-md-6">' . $fileIco . ' ' . $file . '</div>';
										echo '<div class="col-md-2" style="text-align:center;">' . mime_content_type($usrDir . $getBrowse. '/' . $file) . '</div>';
										echo '<div class="col-md-2" style="text-align:center;">' . Storio::ReadableSize(filesize($usrDir . $getBrowse. '/' . $file)) . '</div>';
										echo '<div class="col-md-2" style="text-align:center;"><span style="padding-right:22px;>' . $dlIco . '</span> <span style="padding-right:22px;>' . $copyIco . '</span> <span style="">' . $copyIco . '</span></div>';
									}
								}

								// Empty dir
								if(empty($fldArr['dirview'][$usrDir.$getBrowse]['folders']) && empty($fldArr['dirview'][$usrDir.$getBrowse]['files'])) {
									echo '<div class="col-md-12" style="text-align:center;">Seems this directory is empty!</div>';
								}

								// End the row
								echo '</div>';
							?>
						</p>
					</div>
				</div>
			</div>
		</main>

		<script type="text/javascript" src="app/js/jquery.min.js"></script>
		<script type="text/javascript" src="app/js/whUp.js"></script>
		<script type="text/javascript" src="app/js/bootstrap.bundle.min.js"></script>

		<!-- Modal -->
		<div class="modal fade" id="folderModal" tabindex="-1" aria-labelledby="folderModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="folderModalLabel">Create a new directory</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<p>
							<?php
								// Showing where the folder will go
								if(isset($_GET['browse']) && !empty($_GET['browse'])) {
									echo 'Folder will be created under /' . $_GET['browse'] . '/';
									
									// Also generate a POST link
									$post = '&browse=' . ltrim($getBrowse, '/');
								}else{
									echo 'Folder will be created under /';

									// Create empty if no browsing
									$post = '';
								}
							?>
						</p>

						<form action="?page=us-files<?php echo $post; ?>" method="post">
							<div class="mb-3">
								<label for="inpFolder" class="form-label">Folder name</label>
								<input type="text" class="form-control" id="inpFolder" name="inpFolder" required pattern="([A-z0-9À-ž\s]){2,}" />
								<input type="hidden" id="uplFld" name="uplFld" value="<?php echo $getBrowse; ?>"/>
								<input type="hidden" id="usrSes" name="usrSes" value="<?php echo $_SESSION['Username']; ?>"/>
							</div>
							<button type="submit" class="btn btn-primary">Create</button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal -->
		<div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="fileModalLabel">Upload new files</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<p>
							<?php
								// Showing where uploads will go
								if(isset($_GET['browse']) && !empty($_GET['browse'])) {
									echo 'Uploads will be stored under /' . $_GET['browse'] . '/';
								}else{
									echo 'Uploads will be stored under /';
								}
							?>
						</p>

						<form method="post" id="upload" enctype="multipart/form-data" style="margin:0px; padding:0px; display:inline;">
							<div class="custom-file" ondragover="allowDrop(event)" ondragleave="leaveDrop(event)" id="customFile" style="margin-top:10px;">
								<input type="file" name="file[]" class="custom-file-input" id="fileInput" aria-describedby="fileHelp" multiple>
								<label class="custom-file-label" id="custom-file-label" for="fileInput" style="text-align:left;">
									Select up to 10 files
								</label>
								<input type="hidden" id="uplFld" name="uplFld" value="<?php echo $getBrowse; ?>"/>
								<input type="hidden" id="usrSes" name="usrSes" value="<?php echo $_SESSION['Username']; ?>"/>
							</div>
						</form>

						<br />

						<div class="progress" id="progBar" style="border: 1px solid #000; margin-top:10px; display:none; height:20px;">
							<div class="progress-bar bg-info" id="progressBar" role="progressbar" style="background-color:#759CC9 !important;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
								<div id="progress" align="center" style="color:black;"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script>
			function allowDrop(event) {
				event.preventDefault();
				document.getElementById("custom-file-label").innerHTML = "Drop files to upload";
			}

			function leaveDrop(event) {
				event.preventDefault();
				document.getElementById("custom-file-label").innerHTML = "Select up to 50 files";
			}
		</script>
	</body>
</html>