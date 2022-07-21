<?php
	/**
	 * multiShare.php
	 *
	 * Create share links for multiple files
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2022 Storio
	 * @link       https://github.com/ialexpw/Storio
	 */

	include 'app/storio.app.php';

	// Redirect if not logged in
	if(!Storio::LoggedIn()) {
		header("Location: ?page=login");
	}

	// Check if we have ID's
	if(isset($_GET['sid']) && !empty($_GET['sid'])) {
		// Explode the ID's
		$file_ids = explode(",", $_GET['sid']);

		// File counter
		$fc = 0;

		// Store the array
		$fileArr = array();

		// Loop the file to create an array
		foreach($file_ids as $id) {
			$file_dec = Storio::SimpleCrypt($id, 'd');

			// Separate the path and file
			$file_info = explode(":::", $file_dec);

			// Store just the name
			$file = $file_info[1];

			// Store the location with file name
			$file_loc = $file_info[0] . '/' . $file;

			// Build an array
			$fileArr[$fc]['name'] = $file;
			$fileArr[$fc]['path'] = $file_loc;
			
			print_r($fileArr);
		}
	}
?>