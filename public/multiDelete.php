<?php
	/**
	 * multiDelete.php
	 *
	 * Delete multiple files using the checkboxes
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

			if(file_exists($file_loc)) {
				unlink($file_loc);

				// Remove the share link entry - Generate string for the share link
				$shareId = sha1($_SESSION['Username'] . $file_loc);

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
			}
			$fc++;
		}

		return true;
	}
?>