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

	// Check if we have ID's
	if(isset($_GET['sid']) && !empty($_GET['sid'])) {
		// Explode the ID's
		$file_ids = explode(",", $_GET['sid']);

		// File counter
		$fc = 0;

		// Loop the file to create an array
		foreach($file_ids as $id) {
			$file_path = Storio::SimpleCrypt($id, 'd');

			echo $file_path;
		}
	}
?>