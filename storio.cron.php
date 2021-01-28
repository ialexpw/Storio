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

	$dirs = array_filter(glob('users/*'), 'is_dir');
	print_r($dirs);

	//number_format(Storio::getDirectorySize('users/user') / 1048576, 2);
?>