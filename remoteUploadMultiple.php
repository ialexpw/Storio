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

	// Get IP
	$usrIP = $_SERVER['REMOTE_ADDR'];

	/*
	// Anti spam measure >30 uploads p/hour
	if(Whispa::SpamDetected($db, $usrIP)) {
		$output = array(
			"success" => false,
			"message" => "upload_overflow",
			"verbose" => "Uploading limit hit, please wait a few minutes and try again"
		);
		
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($output);
		exit();
	}
	*/

	print_r($_FILES);

	print_r($_POST);

	//echo count($_FILES);

	//foreach($_FILES['file']['name'] as $id => $file) {
	//foreach($_FILES['file']['tmp_name'] as $id => $file) {
	//	echo 'file ';
	//}
		//
	//echo '<pre>';
	//print_r($_POST);
	//echo '</pre>';
	exit();
/*
	// Over 10 files
	if(count($_FILES['file']['name']) > 10) {
		$output = array(
			"success" => false,
			"message" => "max_files_exceeded",
			"verbose" => "Maximum 10 files at a time"
		);
		
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($output);
		exit();
	}

	// Multiple file upload
	if(count($_FILES['file']['name']) > 1) {
		// Set the multiple flag
		$muUp = 1;

		// Set the archive flag
		$archive = 1;

		// Set original file name as "multiple"
		//$orFile = 'multiple files';
		$orFile = $_FILES['file']['name'][0] . ' & ' . (count($_FILES['file']['name'])-1) . ' others';

		// Create a parent folder name
		$prntFolder = substr(sha1(time() . microtime()), 0, 1) . Whispa::generateRandomString(6) . substr(sha1(time() . microtime()), 2, 3);

		// Create a hash used for removing the file(s)
		$fHash = $prntFolder . '-whisprm-' . md5($prntFolder . 'whispa-app-1005');

		// Save archive path
		$arcPth = 'tmp/archive_' . $prntFolder . '.zip';

		// Create the zip
		$zip = new ZipArchive;
		$zip->open('tmp/archive_' . $prntFolder . '.zip', ZipArchive::CREATE);

		// Loop the files
		foreach($_FILES['file']['name'] as $id => $file) {
			// Grab the extension to the file
			$path = $_FILES['file']['name'][$id];
			$ext = pathinfo($path, PATHINFO_EXTENSION);

			// Remove the extension (to clean the file name)
			$fileName = str_replace("." . $ext, "", basename($_FILES['file']['name'][$id]));

			// Check/replace file characters
			$fileName = Whispa::GenCleanUrl($fileName);

			// Re-add the extension
			$fileName .= "." . $ext;

			// Add file to the zip and set to no compression
			$zip->addFile($_FILES['file']['tmp_name'][$id], $fileName);
			$zip->setCompressionIndex($id, ZipArchive::CM_STORE);

			// Add file name to string
			$multiFiles .= $_FILES['file']['name'][$id] . ' ';
		}

		// Close the zip
		$zip->close();

		// Remove last ";"
		$multiFiles = substr($multiFiles, 0, -1);

		// Grab the file size
		$trgSize = filesize($arcPth);
		$trgSize = round($trgSize / 1024);

		// Over 2GB file size
		if($trgSize > 2200000) {
			$output = array(
				"success" => false,
				"message" => "size_exhausted",
				"verbose" => "File size exceeded"
			);
			
			header("Content-Type: application/json; charset=utf-8");
			echo json_encode($output);
			exit();
		}

		// Make the directory
		if(mkdir("/mnt/whsfn/" . $prntFolder)) {
			// New local upload
			if(rename($arcPth, "/mnt/whsfn/" . $prntFolder . '/archive_' . $prntFolder . '.zip')) {
				$res['success'] = true;
			}else{
				$res['success'] = false;
				$res['message'] = "upload_fail";
			}
		}else{
			$res['success'] = false;
			$res['message'] = "folder_creation_fail";
		}

	// Uploading a single file
	}else if(count($_FILES['file']['name']) == 1){
		// Set the multiple flag off
		$muUp = 0;

		// Set original file name
		$orFile = basename($_FILES["file"]["name"][0]);

		// Create a parent folder name
		$prntFolder = substr(sha1(time() . microtime()), 0, 1) . Whispa::generateRandomString(6) . substr(sha1(time() . microtime()), 2, 3);

		// Create a hash used for removing the file(s)
		$fHash = $prntFolder . '-whisprm-' . md5($prntFolder . 'whispa-app-1005');

		// Grab the extension to the file
		$path = $_FILES["file"]["name"][0];
		$ext = pathinfo($path, PATHINFO_EXTENSION);

		// Store file name
		$multiFiles = $_FILES["file"]["name"][0];

		// If zip file, use the archive name (allows previewing after upload)
		if($ext == 'zip') {
			$fileName = 'archive_' . $prntFolder . '.zip';

			// Set the archive flag
			$archive = 1;
		}else{
			// Remove the extension (to clean the file name)
			$fileName = str_replace("." . $ext, "", basename($_FILES["file"]["name"][0]));

			// Check/replace file characters
			$fileName = Whispa::GenCleanUrl($fileName);

			// Re-add the extension
			$fileName .= "." . $ext;
		}

		// Grab the file size
		$trgSize = round($_FILES["file"]["size"][0] / 1024);

		// Over 2GB file size
		if($trgSize > 2200000) {
			$output = array(
				"success" => false,
				"message" => "size_exhausted",
				"verbose" => "File size exceeded"
			);
			
			header("Content-Type: application/json; charset=utf-8");
			echo json_encode($output);
			exit();
		}

		// Make the directory
		if(mkdir("/mnt/whsfn/" . $prntFolder)) {
			// New local upload
			if(move_uploaded_file($_FILES["file"]["tmp_name"][0], "/mnt/whsfn/" . $prntFolder . "/" . $fileName)) {
				$res['success'] = true;
			}else{
				$res['success'] = false;
				$res['message'] = "upload_fail";
			}
		}else{
			$res['success'] = false;
			$res['message'] = "folder_creation_fail";
		}

	}else{
		$output = array(
			"success" => false,
			"message" => "empty_files",
			"verbose" => "Select a file"
		);
		
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($output);
		exit();
	}
	
	// Check if the file being uploaded is a requested type
	if(isset($_SESSION['TmpUserId']) && !empty($_SESSION['TmpUserId']) && !empty($_SESSION['ReqId'])) {
		$usrID = $_SESSION['TmpUserId'];

		// Blank the session after using
		$_SESSION['TmpUserId'] = "";

		// Current time
		$cuTime = time();

		// Set the state to 2 (requested)
		$setState = 2;

		// Update the request URL with the time
		$updateReq = $db->prepare('UPDATE wh_request SET req_time = :req_time WHERE req_id = :req_id');
		$updateReq->bindValue(':req_time', $cuTime);
		$updateReq->bindValue(':req_id', $_SESSION['ReqId']);
		$updateReqRes = $updateReq->execute();
	}else{
		// Grab the user ID (if any)
		if(isset($_SESSION['UsrId']) && !empty($_SESSION['UsrId'])) {
			$usrID = $_SESSION['UsrId'];
		}else{
			// Not logged in, default
			$usrID = 0;
		}
	}

	// Failure to upload
	if(!$res['success']) {
		$output = array(
			"success" => false,
			"message" => $res['message']
		);
		
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($output);
		exit();
	}
	
	// The uploaded Url (depending on multi upload)
	if($muUp) {
		$s3Url = 'https://dl.whispa.app/' . $prntFolder . '/archive_' . $prntFolder . '.zip';
	}else{
		$s3Url = 'https://dl.whispa.app/' . $prntFolder . '/' . $fileName;
	}

	// Are we uploading through the app?
	if(isset($_POST['api_key'])) {
		$usrID = Whispa::GetIdFromApi($db, $_POST['api_key']);
	}

	// Insert into the DB
	$data = array( 'or_filename' => $orFile, 'up_filename' => $prntFolder, 'stored_files' => $multiFiles, 's3_link' => $s3Url, 'size' => $trgSize, 'ip_addr' => $usrIP, 'timestamp' => time(), 'usr_id' => $usrID, 'state' => $setState, 'archive' => $archive );
	$stmt = $db->prepare("INSERT INTO wh_files (or_filename, up_filename, stored_files, s3_link, size, ip_addr, timestamp, usr_id, state, archive) VALUES (:or_filename, :up_filename, :stored_files, :s3_link, :size, :ip_addr, :timestamp, :usr_id, :state, :archive)");
	$stmt->execute($data);

	// Output results
	$output = array(
		"success" => true,
		"message" => "upload_success",
		"filename" => $prntFolder,
		"hash" => $fHash
	);

	// Get month/year format e.g. Nov:2019
	$curDate = date('F:Y');

	// Update our stats
	$stmt = $db->prepare("SELECT * FROM wh_stats WHERE cur_date = :cur_date");
	$stmt->bindParam(':cur_date', $curDate);
	$stmt->execute();
	$getMonthStat = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// If empty, create and insert the row
	if(empty($getMonthStat)) {
		$data = array( 'cur_date' => $curDate, 'sizeUpload' => $trgSize, 'sizeDownload' => "0", 'totalDownloads' => "0" );
		$stmt = $db->prepare("INSERT INTO wh_stats (cur_date, sizeUpload, sizeDownload, totalDownloads) VALUES (:cur_date, :sizeUpload, :sizeDownload, :totalDownloads)");
		$stmt->execute($data);
	}else{
		// Otherwise update it by looping (will just be one)
		foreach($getMonthStat as $stat) {
			$updateMonth = $db->prepare('UPDATE wh_stats SET sizeUpload = sizeUpload+:uploadsize WHERE cur_date = :cur_date');
			$updateMonth->bindValue(':uploadsize', $trgSize);
			$updateMonth->bindValue(':cur_date', $curDate);
			$updateMonthRes = $updateMonth->execute();
		}
	}

	header("Content-Type: application/json; charset=utf-8");
	echo json_encode($output);
*/
?>
