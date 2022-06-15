<?php
	/**
	 * index.php
	 *
	 * Homepage
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2022 Storio
	 * @link       https://github.com/ialexpw/Storio
	 */

	include 'app/storio.app.php';
	include 'app/icons.class.php';

	define('INC_DATA', true);

	if(Storio::LoggedIn()) {
		define('USER', $_SESSION['Username']);
	}
	

	// Check for the install file
	if(!file_exists('../users/configs/site-settings.json')) {
		// Check the users dir permissions
		if(is_writable('../users/')) {
			// Check the configs dir permissions
			if(is_writable('../users/configs/')) {
				Storio::Install();
			}else{
				exit(Storio::LoadView('install'));
			}
		}else{
			exit(Storio::LoadView('install'));
		}
	}

	// Logout
	if(Storio::LoggedIn() && isset($_GET['logout'])) {
		session_destroy();
		session_start();
	}

	// Download a file
	if(isset($_GET['id']) && !empty($_GET['id'])) {
		exit(Storio::LoadView('download'));
	}

	// Download a file
	if(isset($_GET['dl']) && !empty($_GET['dl'])) {
		// Store the hash
		$shareHash = $_GET['dl'];

		// Load the share links configuration
		$shareCfg = Storio::ShareLinks();

		// Check there is a value
		if(isset($shareCfg['ShareLinks'][$shareHash]['Path'])) {
			// Store the path
			$dlFile = $shareCfg['ShareLinks'][$shareHash]['Path'];

			// Check if the file exists
			if(file_exists($dlFile)) {
				// Download it
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.basename($dlFile).'"');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($dlFile));
				readfile($dlFile);
				exit();
			}
		}else{
			// No value - send to 404
			exit(Storio::LoadView('404'));
		}
	}

	// Simple templating
	if(!isset($_GET['page']) || empty($_GET['page'])) {
		Storio::LoadView('login');
	}else{
		Storio::LoadView($_GET['page']);
	}
?>