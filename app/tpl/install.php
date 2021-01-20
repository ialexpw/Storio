<?php
echo 'a';
	$dirCheck = '';

	// Check for the install file
	if(!file_exists('users/configs/site-settings.json')) {
		echo 'b';
		// Check the users dir permissions
		if(!is_writable('users')) {
			$dirCheck .= '<p>Please ensure the users/ folder is writable</p>';
			echo 'c';
		}

		// Check the configs dir permissions
		if(!is_writable('users/configs')) {
			$dirCheck .= '<p>Please ensure the users/configs/ folder is writable</p>';
			echo 'd';
		}
	}

	// If folders need changing, echo the messages
	if(!empty($dirCheck)) {
		echo 'e';
		echo $dirCheck;
		exit();
	}
?>