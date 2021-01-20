<?php
echo 'a';
	$dirCheck = '';

	// Check for the install file
	if(!file_exists('users/configs/site-settings.json')) {
		// Check the users dir permissions
		if(!is_writable('users/')) {
			$dirCheck .= '<p>Please ensure the users/ folder is writable</p>';
		}

		// Check the configs dir permissions
		if(is_writable('users/configs/')) {
			$dirCheck .= '<p>Please ensure the users/configs/ folder is writable</p>';
		}
	}

	// If folders need changing, echo the messages
	if(!empty($dirCheck)) {
		echo $dirCheck;
		exit();
	}
?>