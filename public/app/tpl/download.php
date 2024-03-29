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

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

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

		if(empty($shareCfg['ShareLinks'][$shareHash]['User'])) {
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
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">

		<!-- Google fonts -->
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&display=swap">

		<!-- Custom styles -->
		<link rel="stylesheet" href="app/css/custom.css">

		<!-- Featherlight lightbox -->
		<link href="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.css" type="text/css" rel="stylesheet" />
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
					<div class="col-1 col-sm-3"></div>

					<div class="col-10 col-sm-6">
						<div class="card">
							<div class="card-body" style="text-align: center;">
								<!-- Download files -->
								<br />
								<h4 class="card-title">Download files</h4><hr>

								<?php
									if($shareCfg['ShareLinks'][$shareHash]['Multi'] == 1) {
										// Counter
										$fc = 0;

										foreach($shareCfg['ShareLinks'][$shareHash] as $file) {
											// To avoid an error with file listing due to user at the bottom of array
											if(!isset($file['File'])) {
												continue;
											}

											// Add spacing
											if($fc > 0) {
												echo '<br /><br />';
											}

											// Get the files extension
											$ext = pathinfo($file['File'], PATHINFO_EXTENSION);

											// Grab the mime type
											$mimeType = mime_content_type($file['Path']);

											// Encrypt file name
											$encFile = Storio::SimpleCrypt($file['Path']);

											// Lightbox use
											if(strpos($mimeType, 'image') !== false) {
												echo '<p><a class="noLink" href="#" data-featherlight="viewSource.php?u=' . $shareCfg['ShareLinks'][$shareHash]['User'] .'&p=' . $encFile .'">' . $file['File'] . '</a></p>';
											}else if(strpos($mimeType, 'video/mp4') !== false || $ext == 'mp4') {
												echo '<p><a class="noLink reqBtn" name="' . $shareCfg['ShareLinks'][$shareHash]['User'] . '+Sto+' . $encFile . '" href="javascript:;" data-bs-toggle="modal" data-bs-target="#reqModal">' . $file['File'] . '</a></p>';
											}else{
												echo '<p>' . $file['File'] . '</p>';
											}

											echo '<a class="btn btn-outline-dark" href="/?dl=' . $shareHash . '&hash=' . $encFile . '" role="button">Download</a>';

											$fc++;
										}
									}else if($shareCfg['ShareLinks'][$shareHash]['Multi'] == 0) {
										// Get the files extension
										$ext = pathinfo($shareCfg['ShareLinks'][$shareHash]['File'], PATHINFO_EXTENSION);

										// Grab the mime type
										$mimeType = mime_content_type($shareCfg['ShareLinks'][$shareHash]['Path']);

										// Encrypt file name
										$encFile = Storio::SimpleCrypt($shareCfg['ShareLinks'][$shareHash]['Path']);

										// Lightbox use
										if(strpos($mimeType, 'image') !== false) {
											echo '<p><a class="noLink" href="#" data-featherlight="viewSource.php?u=' . $shareCfg['ShareLinks'][$shareHash]['User'] .'&p=' . $encFile .'">' . $shareCfg['ShareLinks'][$shareHash]['File'] . '</a></p>';
										}else if(strpos($mimeType, 'video/mp4') !== false || $ext == 'mp4') {
											echo '<p><a class="noLink reqBtn" name="' . $shareCfg['ShareLinks'][$shareHash]['User'] . '+Sto+' . $encFile . '" href="javascript:;" data-bs-toggle="modal" data-bs-target="#reqModal">' . $shareCfg['ShareLinks'][$shareHash]['File'] . '</a></p>';
										}else{
											echo '<p>' . $shareCfg['ShareLinks'][$shareHash]['File'] . '</p>';
										}

										echo '<a class="btn btn-outline-dark" href="/?dl=' . $shareHash . '" role="button">Download</a>';
									}
								?>
								
								<br /><br />
							</div>
						</div>
					</div>

					<div class="col-1 col-sm-3"></div>
				</div>
				<p class="text-center" style="margin-top:5px;">Powered by <a href="https://github.com/ialexpw/Storio">Storio</a></p>
			</div>
		</main>

		<script type="text/javascript" src="app/js/jquery.min.js"></script>
		
		<!-- Bootstrap JS -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>

		<script type="text/javascript" src="app/js/session.js"></script>

		<!-- Lightbox script -->
		<script type="text/javascript" src="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.js"></script>

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

		<script>
			// Pop up modal for the video player
			$(document).ready(function(){
				// Click the video to preview
				$('.reqBtn').click(function() {
					// Store the name
					var vidSplit = this.name;

					// Split the string
					vidSplit = vidSplit.split("+Sto+");

					// Generate the iframe link
					var ifContent = '<iframe style="width:100%; height:650px;" src="viewSource.php?u='+vidSplit[0]+'&p='+vidSplit[1]+'"></iframe>';

					// Ensure html is empty first
					$('#showVid').html("");
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
			});
		</script>
	</body>
</html>