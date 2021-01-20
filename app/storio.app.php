<?php
	/**
	 * storio.app.php
	 *
	 * Functions file for Storio
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.aw0.uk
	 */

	$sessTimeout = 7200;
	session_start();
	setcookie(session_name(), session_id(), time() + $sessTimeout);

	/**
	 * Storio Class
	 */
	class Storio {

		/**
		 * Storio::Connect()
		 * Connect to the SQL database and return the database query
		 */
		public static function Connect($host, $database, $user, $password) {
			try {
				$db = new PDO("mysql:host=$host;dbname=$database", $user, $password);
				// set the PDO error mode to exception
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				return $db;
			}
			catch(PDOException $e) {
				echo "Connection failed: " . $e->getMessage();
			}
		}

		/**
		 * Storio::LoadView()
		 * Simple template system, check for a template file otherwise return 404
		 */
		public static function LoadView($view) {
			// Check if the page exists otherwise 404
			if(file_exists('app/tpl/' . $view . '.php')) {
				include 'app/tpl/' . $view . '.php';
			}else{
				include 'app/tpl/404.php';
			}
		}

		/**
		 * Storio::LoggedIn()
		 * Check logged in status by looking at the sessions
		 */
		public static function LoggedIn() {
			if(!isset($_SESSION['UserID']) || !isset($_SESSION['Username'])) {
				return 0;
			}else{
				return 1;
			}
		}
	}
?>