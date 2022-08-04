<?php
	/**
	 * us-files-table.php
	 *
	 * The file management page for users (table view)
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
	if(!Storio::LoggedIn()) {
		header("Location: ?page=login");
	}

	// Load the site configuration
	$siteCfg = Storio::SiteConfig();
	
	// Load the share links configuration
	$shareCfg = Storio::ShareLinks();

	// Load the user configuration
	$usrCfg = Storio::UserConfig(USER);

	// Get the user dir structure
	if(is_dir('../users/' . $_SESSION['Username'])) {
		// Users upload folder
		$usrDir = str_replace("{user}", $_SESSION['Username'], $siteCfg['uploadFolder']);

		// Store the browse (if any)
		if(!empty($_GET['browse'])) {
			$getBrowse = '/' . $_GET['browse'];

			// Viewing an invalid folder
			if(!is_dir($usrDir . $getBrowse)) {
				header("Location: ?page=us-files-table");
			}
		}else{
			$getBrowse = '';
		}
	}else{
		// Something has gone wrong - user possibly deleted while logged in?
		header("Location: ?logout");
	}

	// Creating a new folder
	if(!empty($_POST['inpFolder']) && $_POST['usrSesr'] == USER) {
		// Validate folder here

		// If folder does not already exist
		if(!is_dir($usrDir . $_POST['uplFldr'] . '/' . $_POST['inpFolder'])) {
			if(mkdir($usrDir . $_POST['uplFldr'] . '/' . $_POST['inpFolder'])) {
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
					Storio::UpdateStorageSize(USER);

					// Reload
					if(!empty($_GET['browse'])) {
						header('Location: ?page=us-files-table&browse=' . $_GET['browse']);
					}else{
						header('Location: ?page=us-files-table');
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
					$shareId = sha1(USER . $rmFile);

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
					Storio::UpdateStorageSize(USER);

					// Reload
					if(!empty($_GET['browse'])) {
						header('Location: ?page=us-files-table&browse=' . $_GET['browse']);
					}else{
						header('Location: ?page=us-files-table');
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

		<link rel="canonical" href="">

		<!-- Bootstrap core CSS -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">

		<!-- Google fonts -->
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&display=swap">

		<!-- Featherlight lightbox -->
		<link href="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.css" type="text/css" rel="stylesheet" />

		<!-- Copy to clipboard -->
		<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/clipboard@2.0.6/dist/clipboard.min.js"></script>

		<!-- Custom styles -->
		<link rel="stylesheet" href="app/css/custom.css">

		<style>
			.selected {
				border: 1px solid #ccc;
				border-radius: 8px;
			}
		</style>
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
								<a class="nav-link" href="?page=us-dashboard">
									<i class="bi bi-house" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Home
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link active" aria-current="true" href="?page=us-files-table">
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

						<div class="row">
							<!-- Title area -->
							<div class="col-md-8">
								<h4 class="card-title">File Management (new <a href="#" data-bs-toggle="modal" data-bs-target="#fileModal">file</a> or <a href="#" data-bs-toggle="modal" data-bs-target="#folderModal">directory</a>)</h4>
							</div>

							<!-- Search bar -->
							<div class="col-md-4">
								<form class="form-inline" name="searchForm">
									<div class="input-group mb-3">
										<input type="text" class="form-control" placeholder="Search term..." id="sTerm" name="sTerm" aria-label="Search term...">
										<div class="input-group-append">
											<a class="btn btn-outline-secondary" id="searchClick" name="searchClick" href="javascript:;">Search</a>
										</div>
									</div>
								</form>
							</div>
						</div>

						<div class="searchItems">

						</div>

						<p class="card-text" id="dirLister" style="margin-top:15px;">
							<?php
								// Save the arrays
								$fldArr = Storio::DirList($usrDir . $getBrowse);

								echo '<div class="row">';

								// Show the back button if needed
								if(!empty($_GET['browse'])) {
									echo '<div class="col-md-12 left-indent" style="margin-bottom:8px;">';
									echo '<a href="?page=us-files-table' . Storio::GoBack($_GET['browse']) . '"><i class="bi bi-arrow-left"></i></a>';
									echo '</div>';
								}else{
									echo '<div class="col-md-12 left-indent" style="margin-bottom:8px; text-align:right;">';
									echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-list" viewBox="0 0 16 16">
									<path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
									<path d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8zm0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zM4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
								  </svg>';
									echo '<i class="bi bi-card-text"></i> <i class="bi bi-card-list"></i>';
									//echo '<a href="?page=us-files-table' . Storio::GoBack($_GET['browse']) . '"><i class="bi bi-arrow-left"></i></a>';
									echo '</div>';
								}

								

								echo '<div class="table-responsive">';
								echo '<table class="table align-middle">';
								echo '<thead>';
								echo '<tr>';
								echo '<th scope="col" style="width:5%"></th>';
								echo '<th scope="col" style="width:70%"></th>';
								echo '<th scope="col" style="width:5%"></th>';
								echo '<th scope="col" style="width:20%"></th>';
								echo '</tr>';
								echo '</thead>';
								echo '<tbody>';

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

										$folder = file_get_contents('../users/configs/_thumbs/folder.png');
										$fold_img = 'data:image/png;base64,' . base64_encode($folder);

										echo '<tr>';

										echo '<td class="text-center"><input type="checkbox" id="" class="multiSelect" name="checkBox" value="" disabled></td>';

										echo '<td><img width="50" height="50" src="' . $fold_img . '" class="rounded" alt="..." style="margin-right: 25px;"> <a href="?page=us-files-table&browse=' . ltrim($subLink, '/') . '">' . $dir . '</a></td>';

										echo '<td class="text-center">n/a</td>';

										echo '<td class="text-center"><a style="color:indianred;" href="?page=us-files-table&del=' . $encFile . '&type=folder" class="delete" data-confirm="Are you sure you would like to delete this folder?">Delete</a></td>';

										echo '</tr>';
									}
								}

								// Create a counter for file loop
								$fc = 0;

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

										// Get the files extension
										$ext = pathinfo($file, PATHINFO_EXTENSION);

										// Grab the mime type
										$mimeType = mime_content_type($usrDir . $getBrowse. '/' . $file);

										// Config the user icon
										$fileIco = $fIco;

										// Generate the download link
										$shareId = sha1(USER . $usrDir . $getBrowse. '/' . $file);

										// Convert to chars with md5
										$shareId = md5('STR' . $shareId);

										// Cut the length of the string down
										$shareId = substr($shareId, 0, 15);

										// Encrypt file path
										$encFile = Storio::SimpleCrypt($usrDir . $getBrowse. '/' . $file);

										// For multi share links
										$encMultiShare = Storio::SimpleCrypt($usrDir . $getBrowse. ':::' . $file);

										// For copy share url
										$webPath = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

										// If an image, check for a thumbnail
										if(strpos($mimeType, 'image') !== false) {
											if(file_exists('../users/configs/_thumbs/' . $_SESSION['Username'] . '/_thumb_' . $file)) {
												$type = pathinfo($path, PATHINFO_EXTENSION);
												$thumb = '../users/configs/_thumbs/' . $_SESSION['Username'] . '/_thumb_' . $file;
												$thumb = file_get_contents($thumb);
												$img = 'data:image/' . $type . ';base64,' . base64_encode($thumb);
											}else{
												$img = 'https://placeimg.com/25/25';
											}
										}else{
											$img = 'https://placeimg.com/25/25';
										}

										echo '<tr id="' . $encMultiShare . '-hide">';

										echo '<td class="text-center"><input type="checkbox" id="' . $encMultiShare . '" class="multiSelect" name="checkBox" value="' . $shareId . '"></td>';

										// Thumbnail & name
										if(strpos($mimeType, 'image') !== false) {
											echo '<td><img width="50" height="50" src="' . $img . '" class="rounded" alt="..." style="margin-right: 25px;"> <a class="noLink" href="#" data-featherlight="viewSource.php?u=' . $_SESSION['Username'] .'&p=' . $encFile .'">' . $file . '</a></td>';
										}else if(strpos($mimeType, 'video/mp4') !== false || $ext == 'mp4') {
											echo '<td><img width="50" height="50" src="https://placeimg.com/50/50" class="rounded" alt="..." style="margin-right: 25px;"> <a class="noLink reqBtn" name="' . USER . '+Sto+' . $encFile . '" href="javascript:;" data-bs-toggle="modal" data-bs-target="#reqModal">' . $file . '</a></td>';
										}else{
											echo '<td><img width="50" height="50" src="https://placeimg.com/50/50" class="rounded" alt="..." style="margin-right: 25px;"> ' . $file . '</td>';
										}

										// Size
										echo '<td class="text-center">' . Storio::ReadableSize(filesize($usrDir . $getBrowse. '/' . $file)) . '</td>';

										// Options
										echo '<td class="text-center">';
										echo '<div class="btn-group">';
										echo '<a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">';
										echo 'Options..';
										echo '</a>';
										echo '<ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start">';
										echo '<li><a class="dropdown-item" href="?dl=' . $shareId . '">Direct Download</a></li>';

										// Whether to have the direct download or the download page
										if($siteCfg['downloadPage']) {
											echo '<li><a alt="Copy link" class="dropdown-item copyText" id="copyTxt" onClick="showAlert()" data-clipboard-text="' . $webPath . '?id=' . $shareId . '" href="javascript:;">Copy Share Link</a></li>';
										}else{
											echo '<li><a alt="Copy link" class="dropdown-item copyText" id="copyTxt" onClick="showAlert()" data-clipboard-text="' . $webPath . '?dl=' . $shareId . '" href="javascript:;">Copy Share Link</a></li>';
										}
										
										echo '<li><a class="dropdown-item disabled" data-bs-toggle="modal" data-bs-target="#moveModal" href="#">Move File</a></li>';

										// When deleting a file, ensure we are redirected back
										if(!empty($_GET['browse'])) {
											echo '<li><a alt="Delete file" style="color:indianred;" class="dropdown-item delete" href="?page=us-files-table&browse=' . $_GET['browse'] . '&del=' . $encFile . '&type=file" data-confirm="Are you sure you would like to delete this file?">Delete File</a></li>';
										}else{
											echo '<li><a alt="Delete file" style="color:indianred;" class="dropdown-item delete" href="?page=us-files-table&del=' . $encFile . '&type=file" data-confirm="Are you sure you would like to delete this file?">Delete File</a></li>';
										}
										
										echo '</ul>';
										echo '</div>';
										echo '</td>';

										echo '</tr>';

										$fc++;
									}
								}

								echo '</tbody>';
								echo '</table>';
								echo '</div>';

								// Empty dir
								if(empty($fldArr['dirview'][$usrDir.$getBrowse]['folders']) && empty($fldArr['dirview'][$usrDir.$getBrowse]['files'])) {
									echo '<div class="col-md-12" style="text-align:center;">Seems this directory is empty!</div>';
								}

								// End the row
								echo '</div>';
							?>

							<div class="dropdown">
								<button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
									Group actions
								</button>
								<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
									<li><a class="dropdown-item" id="multiSelectCopy" href="javascript:;">Copy Share Link</a></li>
									<li><a class="dropdown-item" id="multiSelectDelete" href="javascript:;" style="color:indianred;">Delete Selected</a></li>
								</ul>
							</div>

						</p>
					</div>
				</div>

				<p class="text-center" style="margin-top:5px;">Powered by <a href="https://github.com/ialexpw/Storio">Storio</a></p>
			</div>
		</main>

		<!-- JQuery -->
		<script type="text/javascript" src="app/js/jquery.min.js"></script>

		<!-- Storio file upload script -->
		<script type="text/javascript" src="app/js/whUp.js"></script>

		<!-- Bootstrap JS -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>

		<!-- Keep sessions alive while browser is open -->
		<script type="text/javascript" src="app/js/session.js"></script>

		<!-- Lightbox script -->
		<script type="text/javascript" src="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.js"></script>

		<!-- Copy text to clipboard -->
		<script>
			new ClipboardJS(".copyText");
		</script>

		<!-- Modal for a moving files -->
		<div class="modal fade" id="moveModal" tabindex="-1" aria-labelledby="moveModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="moveModalLabel">Move file</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<!-- Form for moving files -->
						<form action="?page=us-files-table" method="post">
							<div class="mb-3">
								<label for="inpFolder" class="form-label">Moving file</label>
								<input type="text" class="form-control" id="inpFolder" name="inpFolder" required pattern="([A-z0-9À-ž\s]){2,}" maxlength="26" />
								<input type="text" id="uplFldr" name="uplFldr" value="<?php echo $getBrowse; ?>"/>
								<input type="hidden" id="usrSesr" name="usrSesr" value="<?php echo USER; ?>"/>
							</div>
							<button type="submit" class="btn btn-primary">Move</button>
						</form>
					</div>
				</div>
			</div>
		</div>		

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
						<form action="?page=us-files-table<?php echo $post; ?>" method="post">
							<div class="mb-3">
								<label for="inpFolder" class="form-label">Folder name</label>
								<input type="text" class="form-control" id="inpFolder" name="inpFolder" required pattern="([A-z0-9À-ž\s.]){2,}" maxlength="26" />
								<input type="hidden" id="uplFldr" name="uplFldr" value="<?php echo $getBrowse; ?>"/>
								<input type="hidden" id="usrSesr" name="usrSesr" value="<?php echo USER; ?>"/>
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

						<!-- Upload files -->
						<form method="post" id="upload" enctype="multipart/form-data" style="margin:0px; padding:0px; display:inline;">
							<div class="custom-file" ondragover="allowDrop(event)" ondragleave="leaveDrop(event)" style="margin-top:10px;">
								<div class="mb-3">
									<label for="fileInput" id="custom-file-label" class="form-label">Select up to 25 files</label>
									<input class="form-control" type="file" name="file[]" id="fileInput" multiple>
									<input type="hidden" id="uplFld" name="uplFld" value="<?php echo $getBrowse; ?>"/>
									<input type="hidden" id="usrSes" name="usrSes" value="<?php echo USER; ?>"/>
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

						<!-- Show the file name on upload -->
						<div id="filename" style="text-align:center; margin-top:5px;"></div>

						<!-- Max size label -->
						<p style="text-align:center; margin-top:15px;"><?php echo $siteCfg['uploadMaxMB'] . ' MB Max'; ?></p>

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
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body" style="margin-bottom:-10px;">
						<div id="showVid"></div>
					</div>
				</div>
			</div>
		</div>

		<!-- Multi share link -->
		<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="shareModalLabel">Share link</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body" style="margin-bottom:-10px;">
						<p>Copy the link below to share the selected files</p>
						<input type="text" onClick="this.setSelectionRange(0, this.value.length)" class="form-control" id="shareLinkML" name="shareLinkML" readonly/><br />
					</div>
				</div>
			</div>
		</div>

		<script>
			function showAlert(){
				$('.toast').toast('show');
			}

			// When document ready
			$(document).ready(function(){
				var selectedIds = "";

				// For multi-select
				$(".multiSelect").on("click", function() {
					$(this).toggleClass('selected');
					selectedIds = $('.selected').map(function() {
						return this.id;
					}).get();
					console.log(selectedIds);
				});

				// Multiselect to share
				$('#multiSelectCopy').click(function() {
					// Ensure html is empty first
					$('#shareLinkML').html("");

					// Request the share link
					$.ajax({
						type: 'GET',
						url: "multiShare.php?sid="+selectedIds,
						success:function(data){
							$('#shareLinkML').val(data);
						}
					});

					// Show modal
					$("#shareModal").modal('show');

					//selectedIds = "";
				});

				// Multiselect to share
				$('#multiSelectDelete').click(function() {
					// Request to delete the files
					$.ajax({
						type: 'GET',
						url: "multiDelete.php?sid="+selectedIds,
						success:function(data){
							var fileId = selectedIds.toString().split(',');

							// Loop the files to hide the divs
							for (let i = 0; i < fileId.length; i++) {
								//var tostr = "#"+fileId[i];
								var tdiv = document.getElementById(fileId[i]+"-hide");
								$(tdiv).fadeOut('slow');
							} 
						}
					});

					//selectedIds = "";
				});

				// When modal is closed, blank the input
				$('#shareModal').on('hidden.bs.modal', function () {
					// Delete the video
					$('#shareLinkML').val("");
				});

				// Pop up modal for the video player - Click the video to preview
				$('.reqBtn').click(function() {
					// Store the name
					var vidSplit = this.name;

					// Split the string
					vidSplit = vidSplit.split("+Sto+");

					// Generate the iframe link
					var ifContent = '<iframe style="width:100%; height:650px;" src="viewSource.php?u='+vidSplit[0]+'&p='+vidSplit[1]+'"></iframe>';

					// Ensure html is empty first
					$('#showVid').html("");

					// Show modal
					$('#reqModal').on('shown.bs.modal', function () {
						// Load the iframe html in
						$('#showVid').html(ifContent);

						// Cleanup
						delete vidSplit;
						delete ifContent;
					});
				});

				// When modal is closed, remove video player (stop sound)
				$('#reqModal').on('hidden.bs.modal', function () {
					// Delete the video
					$('#showVid').html("");
				});

				// Search button clicked
				$("#searchClick").click(function () {
					// Get the value of the search input
					var formPath = searchForm.sTerm.value;

					// If empty return
					if(formPath == "") {
						return false;
					}

					// Ensure the results div is empty before
					$('.searchItems').html("");

					// Search for results and display them
					$.ajax({
						type: 'GET',
						url: "stSearch.php?sid="+formPath,
						success:function(data){
							// Display the html and then clean up the vars
							$('.searchItems').html(data);
							delete formPath;
						}
					});
				});
			});

			// If the enter key is clicked on the search input
			const node = document.getElementById('sTerm');
			node.addEventListener('keydown', function onEvent(event) {
				if (event.key === "Enter") {
					event.preventDefault();

					document.getElementById("searchClick").click();
				}
			});

			// If deleting a file, track the file we are deleting
			var deleteLinks = document.querySelectorAll('.delete');

			for (var i = 0; i < deleteLinks.length; i++) {
				deleteLinks[i].addEventListener('click', function(event) {
					event.preventDefault();

					// Ask to confirm the choice of deleting
					var choice = confirm(this.getAttribute('data-confirm'));

					// If deleting, follow the link
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