<?php
	/**
	 * storio.cron.php
	 *
	 * Cron to update file/folder sizes
	 * Storio adds the file sizes on upload, the cron is here to update when files/folders get removed
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.aw0.uk
	 */


	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);

	include 'app/storio.app.php';

	// Get a listing of directories
	$dirs = array_filter(glob('/var/www/aw0/Storio/users/*'), 'is_dir');
	print_r($dirs);

	// Loop
	foreach($dirs as $dir) {
		// Explode the username out
		$dir = explode("/", $dir);
		$usr = $dir[1];

		// Check a config file exists (checks the user)
		if(file_exists('users/configs/' . $usr . '-cfg.json')) {
			// Calculate the size in mb
			$getSize = number_format(Storio::getDirectorySize($dir) / 1048576, 2);

			// Load the configuration
			$usrCfg = json_decode(file_get_contents('users/configs/' . $usr . '-cfg.json'), true);

			// Add the usage
			$usrCfg['usedStorage'] = $getSize;

			// Encode and resave the config
			$usrCfgEncode = json_encode($usrCfg);
			file_put_contents('users/configs/' . $usr . '-cfg.json', $usrCfgEncode);
		}
	}
?>