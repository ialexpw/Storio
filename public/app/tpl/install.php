<?php
	/**
	 * install.php
	 *
	 * Basic installer
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.uk
	 */
	
	$dirCheck = '';

	// Check for the install file
	if(!file_exists('../users/configs/site-settings.json')) {
		// Check the users dir permissions
		if(!is_writable('../users')) {
			$dirCheck .= '<p>Please ensure the users/ folder is writable</p>';
		}

		// Check the configs dir permissions
		if(!is_writable('../users/configs')) {
			$dirCheck .= '<p>Please ensure the users/configs/ folder is writable</p>';
		}
	}

	// If folders need changing, echo the messages
	if(!empty($dirCheck)) {
		exit($dirCheck);
	}
?>