<?php
	/**
	 * cron.php
	 *
	 * Cron to update file/folder sizes
	 * Storio adds the file sizes on upload, the cron is here to update when files/folders get removed
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.uk
	 */

	include 'public/app/storio.app.php';

	if(php_sapi_name() === 'cli') {
		// Get a listing of directories
		$dirs = array_diff(scandir('users/'), array('.', '..'));

		// Loop the users
		foreach($dirs as $usr) {
			// Skip files, only for directories
			if(!is_dir('users/' . $usr)) {
				continue;
			}

			// Update storage sizes for each user
			Storio::UpdateStorageSize($usr, 1);
		}

		// Get site stats (total files/folders/users)
		$usrDirs = Storio::getDirectorySize('users');

		if($usrDirs > 0) {
			$usrDirs = Storio::ReadableSize($usrDirs);
			//$usrDirs = number_format($usrDirs / 1048576, 2);
		}else{
			$usrDirs = 0;
		}

		print_r($usrDirs);
	}
?>