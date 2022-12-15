<?php
	/**
	 * rmUpload.php
	 *
	 * Upload mechanism
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2022 Storio
	 * @link       https://github.com/ialexpw/Storio
	 */

	include 'app/storio.app.php';

	// Include if available
	//if(file_exists('../vendor/autoload.php')) {
		include '../vendor/autoload.php';
	//}
	
	// POSTing check
	if($_SERVER['REQUEST_METHOD'] != 'POST') {
		$output = array(
			"success" => false,
			"message" => "invalid_request_method",
			"verbose" => "Incorrect method"
		);

		header("Content-Type: application/json; charset=utf-8");
	 	echo json_encode($output);
		exit();
	}

	// Check the logged in user with the hidden field
	if($_POST['usrSes'] != $_SESSION['Username'] || empty($_POST['usrSes']) || empty($_SESSION['Username'])) {
		$output = array(
			"success" => false,
			"message" => "failed_upload",
			"verbose" => "Authentication failure"
		);

		header("Content-Type: application/json; charset=utf-8");
	 	echo json_encode($output);
		exit();
	}

	// Load the site configuration
	$siteCfg = Storio::SiteConfig();

	// Load the user configuration
	$usrCfg = Storio::UserConfig($_POST['usrSes']);

	// Upload folder
	$usr_folder = str_replace("{user}", $_POST['usrSes'], $siteCfg['uploadFolder']);

	// Check directory and config file
	if(file_exists('../users/configs/' . $_POST['usrSes'] . '-cfg.json')) {
		// Check if user can upload
		if($usrCfg['canUpload'] != 'true') {
			$output = array(
				"success" => false,
				"message" => "upload_permissions",
				"verbose" => "User account does not have permission to upload files"
			);
	
			header("Content-Type: application/json; charset=utf-8");
			echo json_encode($output);
			exit();
		}

		// Check storage left
		if($usrCfg['usedStorage'] >= $usrCfg['maxStorage']) {
			$output = array(
				"success" => false,
				"message" => "storage_full",
				"verbose" => "Account storage is full, remove some files to free up some space"
			);
	
			header("Content-Type: application/json; charset=utf-8");
			echo json_encode($output);
			exit();
		}
	}

	// File counter
	$fileCount = 0;

	// Starter for file size
	$fileSize = 0;

	// Check the directory exists where you want to upload
	if(is_dir($usr_folder . $_POST['uplFld'])) {
		// Save the upload dir
		$dirUpl = $usr_folder . $_POST['uplFld'];

		// Get total file size
		$totalFileSize = array_sum($_FILES['file']['size']);

		// Work out if the file size is too big 
		$maxFileSize = $siteCfg['uploadMaxMB'] * 1024 * 1024;

		// If file total exceed static size limit, error
		if($totalFileSize > $maxFileSize) {
			$output = array(
				"success" => false,
				"message" => "file_size",
				"verbose" => "Size of files exceeds the max upload size, please check and try again"
			);
	
			header("Content-Type: application/json; charset=utf-8");
			echo json_encode($output);
			exit();
		}

		// Lastly check if we have enough space left for all of the files combined
		$spaceLeft = ($usrCfg['maxStorage'] - $usrCfg['usedStorage']) * 1024 * 1024;

		// If total size is more than the free space left
		if($totalFileSize > $spaceLeft) {
			$output = array(
				"success" => false,
				"message" => "file_size",
				"verbose" => "Not enough room for all of these files, remove some files to free up some space"
			);
	
			header("Content-Type: application/json; charset=utf-8");
			echo json_encode($output);
			exit();
		}

		// Loop the files
		foreach($_FILES['file']['tmp_name'] as $index => $tmpName ) {
			// Error occurred on this file
			if(!empty($_FILES['file']['error'][$index])) {
				return false;
			}

			// Check that it's an uploaded file and not empty
			if(!empty($tmpName) && is_uploaded_file($tmpName)) {
				// Move the file
				if(move_uploaded_file($tmpName, $dirUpl . '/' . $_FILES["file"]["name"][$index])) {
					// Add a share link
					$shareId = Storio::AddShareLink($dirUpl, $_FILES["file"]["name"][$index], $_SESSION['Username']);

					// Grab the mime type
					$mimeType = mime_content_type($dirUpl . '/' . $_FILES["file"]["name"][$index]);

					// If it is an image, create a thumbnail
					if(strpos($mimeType, 'image') !== false) {
						// Get the extension
						$path = $_FILES["file"]["name"][$index];
						$ext = pathinfo($path, PATHINFO_EXTENSION);

						// Only create thumbs for png/jpg/gif
						if($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif') {
							Storio::CreateThumb($dirUpl . '/' . $_FILES["file"]["name"][$index], '../users/configs/_thumbs/' . $_SESSION['Username'] . '/_thumb_' . $shareId . '_' . $_FILES["file"]["name"][$index], 320, 320);
						}
					}

					if($ext == 'mp4') {
						// Swap out the extension for the thumb
						$rep_ext = str_replace('.mp4', '.png', $_FILES["file"]["name"][$index]);
						$thumb = '../users/configs/_thumbs/' . $_SESSION['Username'] . '/_thumb_' . $shareId . '_' . $rep_ext;

						// Create the thumb
						$ffmpeg = FFMpeg\FFMpeg::create();
						$video = $ffmpeg->open($dirUpl . '/' . $_FILES["file"]["name"][$index]);
						$frame = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10));
						$frame->save($thumb);
					}
				}else{
					return false;
				}

				// Add up the file size
				$fileSize += $_FILES["file"]["size"][$index];

				// Bump counter
				$fileCount++;
			}
		}

		// Update folder sizes
		Storio::UpdateStorageSize($_SESSION['Username']);

		// Output results
		$output = array(
			"success" => true
		);

		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($output);
		exit();
	}