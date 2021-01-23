<?php
	/**
	 * remoteUploadMultiple.php
	 *
	 * Upload mechanism that allows both single and multi-upload with zipping
	 *
	 * @package    Whispa
	 * @author     Alex White (https://github.com/ialexpw/Whispa)
	 * @copyright  2020 Whispa
	 * @link       https://whispa.app
	 */

	//ini_set('display_startup_errors', 1);
	//ini_set('display_errors', 1);
	//error_reporting(-1);

	include 'app/storio.app.php';
	
	// POSTing check
	if($_SERVER['REQUEST_METHOD'] != 'POST') {
		$output = array(
			"success" => false,
			"message" => "invalid_request_method"
		);

		header("Content-Type: application/json; charset=utf-8");
	 	echo json_encode($output);
		exit();
	}

	// Check the logged in user with the hidden field
	if($_POST['usrSes'] != $_SESSION['Username']) {
		$output = array(
			"success" => false,
			"message" => "failed_upload"
		);

		header("Content-Type: application/json; charset=utf-8");
	 	echo json_encode($output);
		exit();
	}

	// Get IP
	$usrIP = $_SERVER['REMOTE_ADDR'];

	// Check the directory exists where you want to upload
	if(is_dir('users/' . $_POST['usrSes'] . $_POST['uplFld'])) {
		// Save the upload dir
		$dirUpl = 'users/' . $_POST['usrSes'] . $_POST['uplFld'];

		// Loop the files
		foreach($_FILES['file']['tmp_name'] as $index => $tmpName ) {
			// Error occured on this file
			if(!empty( $_FILES['file']['error'][$index])) {
				return false;
			}

			// Check that it's an uploaded file and not empty
			if(!empty($tmpName) && is_uploaded_file($tmpName)) {
				// Move the file
				if(!move_uploaded_file($tmpName, $dirUpl . '/' . $_FILES["file"]["name"][$index])) {
					return false;
				}
			}
		}

		// Output results
		$output = array(
			"success" => true
		);

		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($output);
		exit();
	}
?>
