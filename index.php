<?php
	/**
	 * index.php
	 *
	 * Homepage
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

	// Logout
	if(Storio::LoggedIn() && isset($_GET['logout'])) {
		session_destroy();
		session_start();
	}

	// Simple templating
	if(!isset($_GET['page']) || empty($_GET['page'])) {
		Storio::LoadView('index');
	}else{
		Storio::LoadView($_GET['page']);
	}

	$ex = array(
		"upload" => true,
		"share" => true,
		"delete" => false,
		"edit" => true,
		"admin" => false
	);

	Storio::AddUser("alex", "Al3xWhit3", "5000", $ex);
?>