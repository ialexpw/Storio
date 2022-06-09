<?php
	/**
	 * install.php
	 *
	 * Storio installer page, check permissions before installing
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2022 Storio
	 * @link       https://github.com/ialexpw/Storio
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