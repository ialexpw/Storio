<?php
	/**
	 * cli.php
	 *
	 * Quick way to update some parts of Storio from the command line
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.uk
	 */

	include 'app/storio.app.php';

	if(php_sapi_name() === 'cli') {
		// Set a password
		if(strtolower($argv[1]) == 'password') {

		}
	}
?>