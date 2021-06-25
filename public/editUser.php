<?php
	/**
	 * editUser.php
	 *
	 * Remotely called file to edit user permissions
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.uk
	 */

	include 'app/storio.app.php';

	// Need admin to call this
	if(!Storio::LoggedIn('admin')) {
		exit("Permission denied");
	}

	// Check for the user
	if(!empty($_GET['uid']) && is_dir('../users/' . $_GET['uid'])) {
		// Store the user
		$usrEdit = $_GET['uid'];

		// Load the configuration
		$usrCfg = Storio::UserConfig($usrEdit);

		echo '<pre>';
		print_r($usrCfg);
		echo '</pre>';
	}
?>