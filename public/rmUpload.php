<?php
	/**
	 * remoteUploadMultiple.php
	 *
	 * Upload mechanism
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.uk
	 */

	ini_set('display_startup_errors', 1);
	ini_set('display_errors', 1);
	error_reporting(-1);

	include 'app/storio.app.php';
	
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
	if($_POST['usrSes'] != $_SESSION['Username']) {
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
	$usrCfg = Storio::UserConfig($_SESSION['Username']);

	// Check directory and config file
	if(file_exists('../users/configs/' . $_SESSION['Username'] . '-cfg.json')) {
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

	// Get IP
	$usrIP = $_SERVER['REMOTE_ADDR'];

	// File counter
	$fileCount = 0;

	// Starter for file size
	$fileSize = 0;

	// Check the directory exists where you want to upload
	if(is_dir('../users/' . $_POST['usrSes'] . $_POST['uplFld'])) {
		// Save the upload dir
		$dirUpl = '../users/' . $_POST['usrSes'] . $_POST['uplFld'];

		// Get total file size
		$totalFileSize = array_sum($_FILES['file']['size']);

		// Work out if the file size is too big 
		$maxFileSize = $siteCfg['uploadMaxMB'] * 1024 * 1024;

		// If files exceed size, error
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
					Storio::AddShareLink($dirUpl, $_FILES["file"]["name"][$index], $_SESSION['Username']);
				}else{
					return false;
				}

				// Add up the file size
				$fileSize += $_FILES["file"]["size"][$index];

				// Bump counter
				$fileCount++;
			}
		}

		// Add to the log
		//Storio::AddLog(time(), "Files Uploaded", $_SESSION['Username'] . ' has uploaded ' . $fileCount . ' new file(s)');

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