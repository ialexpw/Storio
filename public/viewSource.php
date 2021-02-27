<?php
	/**
	 * viewSource.php
	 *
	 * Use for showing images/video files directly within a lightbox or media player
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
	
	// Grab the path and user
	if(isset($_GET['p']) && !empty($_GET['p']) && isset($_GET['u']) && !empty($_GET['u'])) {
		// Users directory
		if(is_dir('../users/' . $_GET['u'])) {
			// Decrypt the string
			$usrFile = Storio::SimpleCrypt($_GET['p'], 'd');

			// Check the file exists
			if(file_exists($usrFile)) {
				// Is it an image?
				if(strpos(mime_content_type($usrFile), 'image') !== false) {
					// Grab the image contents
					$getImg = file_get_contents($usrFile);

					// Base64 encode it
					$base64 = 'data:' . mime_content_type($usrFile) . ';base64,' . base64_encode($getImg);

					// Echo the image out
					echo '<img src="' . $base64 . '" />';
				}
			}
		}
	}
?>