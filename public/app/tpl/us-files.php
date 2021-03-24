<?php
	/**
	 * us-files.php
	 *
	 * The file management page for users
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

	// Get the user dir structure
	if(is_dir('../users/' . $_SESSION['Username'])) {
		$dirs = array_filter(glob('../users/' . $_SESSION['Username'] . '/*'), 'is_dir');

		// Set the static path (e.g. users/username)
		$usrDir = '../users/' . $_SESSION['Username'];

		// Store the browse (if any)
		if(!empty($_GET['browse'])) {
			$getBrowse = '/' . $_GET['browse'];

			// Viewing an invalid folder
			if(!is_dir($usrDir . $getBrowse)) {
				header("Location: ?page=us-files");
			}
		}else{
			$getBrowse = '';
		}
	}else{
		// Something has gone wrong - user possibly deleted while logged in?
		header("Location: ?logout");
	}

	// Load the site configuration
	$siteCfg = Storio::SiteConfig();
	
	// Load the share links configuration
	$shareCfg = Storio::ShareLinks();

	// Load the user configuration
	$usrCfg = Storio::UserConfig($_SESSION['Username']);

	// Creating a new folder
	if(!empty($_POST['inpFolder']) && $_POST['usrSesr'] == $_SESSION['Username']) {
		// Validate folder here

		// If folder does not already exist
		if(!is_dir($usrDir . $_POST['uplFldr'] . '/' . $_POST['inpFolder'])) {
			if(mkdir($usrDir . $_POST['uplFldr'] . '/' . $_POST['inpFolder'])) {
				// Add to the log
				Storio::AddLog(time(), "Folder Created", $_SESSION['Username'] . ' has created a new folder named ' . $_POST['inpFolder']);

				// Reload
				header('Location: ' . $_SERVER['REQUEST_URI']);
			}
		}
	}

	// Deleting files/folders
	if(!empty($_GET['del']) && !empty($_GET['type'])) {
		if($_GET['type'] == 'folder') {			// Deleting folder
			// Decrypt it
			$rmFolder = Storio::SimpleCrypt($_GET['del'], 'd');

			// Do a quick user check (does the full path contain the user/folder string)
			if(strpos($rmFolder, $usrDir) !== false) {
				// Check the folder exists
				if(is_dir($rmFolder)) {
					Storio::delTree($rmFolder);

					// Update folder sizes
					Storio::UpdateStorageSize($_SESSION['Username']);

					// Reload
					if(!empty($_GET['browse'])) {
						header('Location: ?page=us-files&browse=' . $_GET['browse']);
					}else{
						header('Location: ?page=us-files');
					}
				}
			}
		}else if($_GET['type'] == 'file') {		// Deleting file
			$rmFile = Storio::SimpleCrypt($_GET['del'], 'd');

			// Do a quick user check (does the full path contain the user/folder string)
			if(strpos($rmFile, $usrDir) !== false) {
				// Check the file exists
				if(file_exists($rmFile)) {
					unlink($rmFile);

					// Remove the share link entry - Generate string for the share link
					$shareId = sha1($_SESSION['Username'] . $rmFile);

					// Cut the length of the string down
					$shareId = substr($shareId, 0, 15);

					// Unset the array entry
					if(isset($shareCfg['ShareLinks'][$shareId])) {
						unset($shareCfg['ShareLinks'][$shareId]);

						// Encode and resave the config
						$shareCfgEncode = json_encode($shareCfg);
						file_put_contents('../users/configs/share-links.json', $shareCfgEncode);
					}

					// Update folder sizes
					Storio::UpdateStorageSize($_SESSION['Username']);

					// Reload
					if(!empty($_GET['browse'])) {
						header('Location: ?page=us-files&browse=' . $_GET['browse']);
					}else{
						header('Location: ?page=us-files');
					}
				}
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
		<title>Storio - File Management</title>

		<link rel="canonical" href="https://storio.uk">

		<!-- Bootstrap core CSS -->
		<link rel="stylesheet" href="app/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

		<!-- Google fonts -->
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&display=swap">

		<!-- Featherlight lightbox -->
		<link href="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.css" type="text/css" rel="stylesheet" />

		<!-- Font awesome -->
		<link rel="stylesheet" href="app/css/all.css">

		<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/clipboard@2.0.6/dist/clipboard.min.js"></script>

		<style>
			a {
				text-decoration:none;
			}
			.progress {
				margin-left: auto; margin-right:auto;
			}

			.noLink {
				color: black;
				text-decoration: none;
			}

			html.with-featherlight {
				/* disable global scrolling when featherlights are visible */
				overflow: inherit;
			}

			.center {
				margin: 0 auto;
				max-height: 80%;
				display: block;
			}
		</style>

		<!-- Custom styles -->
		<link rel="stylesheet" href="app/css/custom.css">
	</head>
	<body>
		<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
			<div class="container-fluid">
				<i class="bi bi-droplet" style="font-size: 2rem; margin-right:12px; margin-bottom:6px; color: cornflowerblue;"></i>
				<a class="navbar-brand" href="?page=us-dashboard"> Storio File Management</a>
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
							<li class="nav-item">
								<a class="nav-link" href="?page=us-dashboard">
									<i class="bi bi-house" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Dashboard
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link active" aria-current="true" href="?page=us-files">
									<i class="bi bi-folder" style="font-size: 2rem;"></i>
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
						<!-- File management -->
						<br />
						<h4 class="card-title">File Management (new <a href="#" data-bs-toggle="modal" data-bs-target="#fileModal">file</a> or <a href="#" data-bs-toggle="modal" data-bs-target="#folderModal">directory</a>)</h4>
						<p class="card-text" style="margin-top:15px;">
							<?php
								// Save the arrays
								$fldArr = Storio::DirList($usrDir . $getBrowse);

								// Start the row
								echo '<div class="row">';

								echo '<div class="col-8 col-md-6"><b>File name</b></div>';
								echo '<div class="col-md-2 d-none d-sm-block"><b>Type</b></div>';
								echo '<div class="col-md-2 d-none d-sm-block"><b>Size</b></div>';
								echo '<div class="col-4 col-md-2" style="text-align:center;"><b>Actions</b></div>';

								// End the row
								echo '</div>';
								echo '<hr>';

								echo '<div class="row">';

								// Show the back button if needed
								if(!empty($_GET['browse'])) {
									echo '<div class="col-md-1" style="margin-bottom:8px;">';
									echo '<a href="?page=us-files' . Storio::GoBack($_GET['browse']) . '"><i class="fas fa-arrow-left"></i></a>';
									echo '</div>';

									// Breadcrumbs, remove the first /
									$makeBread = ltrim($getBrowse, '/');
									$makeBread = explode("/", $makeBread);

									echo '<div class="col-md-11">';
									// Store initial variables
									$hrLink = '';
									$fullBread = '';

/*
<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="#">Home</a></li>
<li class="breadcrumb-item active" aria-current="page">Library</li>
</ol>
</nav>
*/

									// Loop and build the breadcrumbs
									foreach($makeBread as $breadCrumb) {
										$rmSlash = str_replace("/", "", $breadCrumb);

										$hrLink .= $breadCrumb;
										$fullBread .= '<a style="margin-right:10px; margin-left:10px;" href="?page=us-files&browse=' . $hrLink . '">' . $breadCrumb . '</a> >';

										// Add the slash onto the link (for the next go around)
										$hrLink .= '/';
									}

									// Remove trailing >
									$fullBread = substr($fullBread, 0, -1);

									// Echo the breadcrumbs
									echo $fullBread;
									echo '</div>';
								}

								// Check & sort
								if(isset($fldArr['dirview'][$usrDir.$getBrowse]['folders'])) {
									// Sort folders
									sort($fldArr['dirview'][$usrDir.$getBrowse]['folders']);
								}

								// Check if there are subfolders first (avoid warnings)
								if(!empty($fldArr['dirview'][$usrDir.$getBrowse]['folders'])) {
									foreach($fldArr['dirview'][$usrDir.$getBrowse]['folders'] as $dir) {
										// Replace the beginning of the path
										$dir = str_replace($usrDir.$getBrowse.'/', "", $dir);

										// Generate a link to subfolder
										$subLink = $getBrowse . '/' . $dir;

										// Encrypt file name
										$encFile = Storio::SimpleCrypt($usrDir . $getBrowse. '/' . $dir);

										// Folder icon/name
										echo '<div class="col-8 col-md-6" style="margin-bottom:2px;"><i style="font-size: 1.4rem; margin-right:6px;" class="far fa-folder"></i> <a href="?page=us-files&browse=' . ltrim($subLink, '/') . '">' . $dir . '</a></div>';
										
										// Set type to "directory"
										echo '<div class="col-md-2 d-none d-sm-block" style="margin-bottom:2px;">directory</div>';

										// No size shown for directories
										echo '<div class="col-md-2 d-none d-sm-block" style="margin-bottom:2px;">n/a</div>';

										// Delete folder option
										echo '<div class="col-4 col-md-2" style="text-align:center;" style="margin-bottom:2px;"><a href="?page=us-files&del=' . $encFile . '&type=folder" class="delete" data-confirm="Are you sure you would like to delete this folder?"><span style="color:red; margin-left:78px;"><i class="far fa-trash-alt"></i></span></a></div>';
									}
								}

								// Check & sort
								if(isset($fldArr['dirview'][$usrDir.$getBrowse]['files'])) {
									// Sort files
									sort($fldArr['dirview'][$usrDir.$getBrowse]['files']);
								}

								// Check if there are files first (avoid warnings)
								if(!empty($fldArr['dirview'][$usrDir.$getBrowse]['files'])) {
									// Loop the files after
									foreach($fldArr['dirview'][$usrDir.$getBrowse]['files'] as $file) {
										// Replace the beginning of the path
										$file = str_replace($usrDir.$getBrowse.'/', "", $file);

										// Get the correct file icon
										$fIco = StoIco::ShowIcon($file);

										// Grab the mime type
										$mimeType = mime_content_type($usrDir . $getBrowse. '/' . $file);

										// Config the user icon
										$fileIco = '<i style="font-size: 1.4rem; margin-right:12px;" class="' . $fIco . '"></i>';

										// Generate the download link
										$shareId = sha1($_SESSION['Username'] . $usrDir . $getBrowse. '/' . $file);

										// Cut the length of the string down
										$shareId = substr($shareId, 0, 15);

										// Encrypt file name
										$encFile = Storio::SimpleCrypt($usrDir . $getBrowse. '/' . $file);

										// For copy share url
										$webPath = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

										// Lightbox use
										if(strpos($mimeType, 'image') !== false) {
											echo '<div class="col-8 col-md-6" style="margin-bottom:2px;"><a class="noLink" href="#" data-featherlight="viewSource.php?u=' . $_SESSION['Username'] .'&p=' . $encFile .'">' . $fileIco . ' ' . $file . '</a></div>';
										}else if(strpos($mimeType, 'video/mp4') !== false) {
											echo '<div class="col-8 col-md-6" style="margin-bottom:2px;"><a class="noLink reqBtn" name="' . $encFile . '" href="javascript:;" data-bs-toggle="modal" data-bs-target="#reqModal">' . $fileIco . ' ' . $file . '</a></div>';
										}else{
											echo '<div class="col-8 col-md-6" style="margin-bottom:2px;">' . $fileIco . ' ' . $file . '</div>';
										}

										// Show mime type
										echo '<div class="col-md-2 d-none d-sm-block" style="margin-bottom:2px;">' . $mimeType . '</div>';

										// Show file size
										echo '<div class="col-md-2 d-none d-sm-block" style="margin-bottom:2px;">' . Storio::ReadableSize(filesize($usrDir . $getBrowse. '/' . $file)) . '</div>';

										// Show actions (download, copy and delete)
										echo '<div class="col-4 col-md-2" style="text-align:center;" style="margin-bottom:2px;">';
										//echo '<a alt="Download file" href="?dl=' . $encFile . '"><span style="color:green; margin-right:22px;"><i class="fas fa-angle-double-down"></i></span></a> ';
										echo '<a alt="Download file" href="?dl=' . $shareId . '"><span style="color:#A2D0C0; margin-right:22px;"><i class="fas fa-angle-double-down"></i></span></a> ';
										echo '<a alt="Copy link" class="copyText" id="copyTxt" onClick="showAlert()" data-clipboard-text="' . $webPath . '?dl=' . $shareId . '" href="javascript:;"><span style="color:#A4B6DD; margin-right:22px;"><i class="fas fa-link"></i></span></a> ';
										echo '<a alt="Delete file" href="?page=us-files&del=' . $encFile . '&type=file" class="delete" data-confirm="Are you sure you would like to delete this file?"><span style="color:#D09292;"><i class="far fa-trash-alt"></i></span></a>';
										echo '</div>';
									}
								}

								// Empty dir
								if(empty($fldArr['dirview'][$usrDir.$getBrowse]['folders']) && empty($fldArr['dirview'][$usrDir.$getBrowse]['files'])) {
									echo '<div class="col-md-12" style="text-align:center;">Seems this directory is empty!</div>';
								}

								// End the row
								echo '</div>';

								// Work out the percentage of used space
								if($usrCfg['usedStorage'] > 0) {
									// Work out the percentage
									$percUsed = number_format($usrCfg['usedStorage'] * (100/$usrCfg['maxStorage']));

									// Round down if over
									if($percUsed > 100) {
										$percUsed = 100;
									}
								}else{
									$percUsed = 0;
								}

								// Used storage bar
								echo '<br /><hr><p class="text-center">Storage allocation</p><div class="progress" style="border: 1px solid #000; width:75%;">';
								echo '<div class="progress-bar" role="progressbar" style="color:black; width: ' . $percUsed . '%" aria-valuenow="' . $percUsed . '" aria-valuemin="0" aria-valuemax="100"></div>';
								echo '<small class="justify-content-center d-flex position-absolute w-75">' . number_format($usrCfg['usedStorage'], 2) . 'MB / ' . number_format($usrCfg['maxStorage']) . 'MB</small>';
								echo '</div>';
							?>
						</p>
					</div>
				</div>
			</div>
		</main>

		<!-- JQuery -->
		<script type="text/javascript" src="app/js/jquery.min.js"></script>

		<!-- Storio file upload script -->
		<script type="text/javascript" src="app/js/whUp.js"></script>

		<!-- Bootstrap JS -->
		<script type="text/javascript" src="app/js/bootstrap.bundle.min.js"></script>

		<!-- Keep sessions alive while browser is open -->
		<script type="text/javascript" src="app/js/session.js"></script>

		<!-- Lightbox script -->
		<script type="text/javascript" src="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.js"></script>

		<!-- Copy text to clipboard -->
		<script>
			new ClipboardJS(".copyText");
		</script>		

		<!-- Modal for a new folder -->
		<div class="modal fade" id="folderModal" tabindex="-1" aria-labelledby="folderModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="folderModalLabel">Create a new directory</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<?php
							// Permission to upload
							if($usrCfg['canUpload'] == 'true') {
						?>
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

						<!-- Form for creating a new folder -->
						<form action="?page=us-files<?php echo $post; ?>" method="post">
							<div class="mb-3">
								<label for="inpFolder" class="form-label">Folder name</label>
								<input type="text" class="form-control" id="inpFolder" name="inpFolder" required pattern="([A-z0-9À-ž\s]){2,}" maxlength="26" />
								<input type="hidden" id="uplFldr" name="uplFldr" value="<?php echo $getBrowse; ?>"/>
								<input type="hidden" id="usrSesr" name="usrSesr" value="<?php echo $_SESSION['Username']; ?>"/>
							</div>
							<button type="submit" class="btn btn-primary">Create</button>
						</form>

						<?php
							}else{
								// Disabled uploading
								echo 'Uploading has been disabled for this account';
							}
						?>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal for new files -->
		<div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="fileModalLabel">Upload new files</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<?php
							// Permission to upload
							if($usrCfg['canUpload'] == 'true') {
						?>
						<p>
							Uploading new files to Storio is easy, use the form below to select up to 25 files at a time. Files will be stored within the folder you are currently looking at!
							<?php
								/*
								// Showing where uploads will go
								if(isset($_GET['browse']) && !empty($_GET['browse'])) {
									echo 'Uploads will be stored under /' . $_GET['browse'] . '/';
								}else{
									echo 'Uploads will be stored under /';
								}
								*/
							?>
						</p>

						<div id="filename"></div>

						<br /><br />

						<h5>Upload form</h5>

						<hr>

						<!-- Upload files -->
						<form method="post" id="upload" enctype="multipart/form-data" style="margin:0px; padding:0px; display:inline;">
							<div class="custom-file" ondragover="allowDrop(event)" ondragleave="leaveDrop(event)" style="margin-top:10px;">
								<div class="mb-3">
									<label for="fileInput" id="custom-file-label" class="form-label">Select up to 25 files</label>
									<input class="form-control" type="file" name="file[]" id="fileInput" multiple>
									<input type="hidden" id="uplFld" name="uplFld" value="<?php echo $getBrowse; ?>"/>
									<input type="hidden" id="usrSes" name="usrSes" value="<?php echo $_SESSION['Username']; ?>"/>
								</div>
							</div>
						</form>

						<!-- Progress bar -->
						<div id="progBar" style="display:none;">
							<div class="progress" style="border: 1px solid #000;">
								<div class="progress-bar" id="progressBar" role="progressbar" style="color:black;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
								<small class="justify-content-center d-flex position-absolute w-100" id="progress"></small>
							</div>
						</div>

						<!-- Max size label -->
						<p style="text-align:center; margin-top:15px;"><?php echo $siteCfg['uploadMaxMB'] . 'MB Max'; ?></p>

						<?php
							// Disabled uploading
							}else{
								echo 'Uploading has been disabled for this account';
							}
						?>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal for video -->
		<div class="modal fade" id="reqModal" tabindex="-1" aria-labelledby="reqModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-xl" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="reqModalLabel">Video Preview</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" style="margin-bottom:-10px;">
						<div id="showVid"></div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

		<script>
			function showAlert(){
				$('.toast').toast('show');
			}

			// Pop up modal with request files dialog
			$(document).ready(function(){
				$('.reqBtn').click(function() {
					console.log("clicked");
					usrPath = this.name;
					$('#showVid').html("");
					$('#reqModal').on('shown.bs.modal', function () {
						$.ajax({
							type: 'GET',
							url: "viewVideo.php?vid="+usrPath,
							success:function(data){
								$('#showVid').html(data);
								delete usrPath;
							}
						});
					});
				});
			});

			var deleteLinks = document.querySelectorAll('.delete');

			for (var i = 0; i < deleteLinks.length; i++) {
				deleteLinks[i].addEventListener('click', function(event) {
					event.preventDefault();

					var choice = confirm(this.getAttribute('data-confirm'));

					if (choice) {
						window.location.href = this.getAttribute('href');
					}
				});
			}

			// Hover over file upload
			function allowDrop(event) {
				event.preventDefault();
				document.getElementById("custom-file-label").innerHTML = "Drop files to upload";
			}

			// Leave the hover area
			function leaveDrop(event) {
				event.preventDefault();
				document.getElementById("custom-file-label").innerHTML = "Select up to 50 files";
			}

			// Focus on the input
			var myModal = document.getElementById('folderModal')
			var myInput = document.getElementById('inpFolder')

			myModal.addEventListener('shown.bs.modal', function () {
				myInput.focus()
			})
		</script>

		<!-- Toast notification for Share link -->
		<div class="toast-container position-absolute p-3 bottom-0 end-0" id="toastPlacement">
			<div class="toast align-items-center bg-info bottom-0 end-0" role="alert" aria-live="assertive" aria-atomic="true">
				<div class="d-flex">
					<div class="toast-body">
						Share link has been copied to your clipboard.
					</div>
					<button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
				</div>
			</div>
		</div>
	</body>
</html>