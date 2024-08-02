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

	const INC_DATA = true;

	if(Storio::LoggedIn()) {
		define('USER', $_SESSION['Username']);
	}
	
	// Check for the installation file
	if(!file_exists('../users/configs/site-settings.json')) {
		exit(Storio::LoadView('install'));
	}

	// Logout
	if(Storio::LoggedIn() && isset($_GET['logout'])) {
		session_destroy();
		session_start();
	}

	// Show the download page
	if(!empty($_GET['id'])) {
		exit(Storio::LoadView('download'));
	}

	// Download a file
	if(!empty($_GET['dl'])) {
		// Store the hash
		$shareHash = $_GET['dl'];

		// Load the share links configuration
		$shareCfg = Storio::ShareLinks();

		// Check if there is a hash
		if(!empty($_GET['hash'])) {
			$dlFile = Storio::SimpleCrypt($_GET['hash'], 'd');
		}else if(isset($shareCfg['ShareLinks'][$shareHash]['Path'])){
			$dlFile = $shareCfg['ShareLinks'][$shareHash]['Path'];
		}else{
			// No value - send to 404
			exit(Storio::LoadView('404'));
		}

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
	}

	// Simple templating
	if(empty($_GET['page'])) {
		Storio::LoadView('login');
	}else{
		Storio::LoadView($_GET['page']);
	}