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
		 * Storio::AddUser($user, $password, $size_mb, $settings)
		 * Add a user to Storio, user information and permissions are passed through with the array
		 */
		public static function Install() {
			// Hash the admin password
			$usrPass = password_hash("AdminUser123", PASSWORD_DEFAULT);

			// Set up the default settings
			$siteCfg = array(
				"siteName" => "Storio File Management",
				"adminUser" => "admin",
				"adminPassword" => $usrPass,
				"allowRegistration" => false,
				"expireFiles" => false
			);

			// JSON encode the configuration
			$jsonCfg = json_encode($siteCfg);

			// Create the json configuration file and write the contents
			$siteFile = fopen('users/configs/site-settings.json','w+');
			fwrite($siteFile, $jsonCfg);
			fclose($siteFile);

			return true;
		}

		/**
		 * Storio::AddUser($user, $password, $size_mb, $settings)
		 * Add a user to Storio, user information and permissions are passed through with the array
		 */
		public static function AddUser($user, $password, $size_mb, $settings) {
			// Check if a user already exists
			if(file_exists('users/' . $user)) {
				return false;
			}

			// Hash the password
			$usrPass = password_hash($password, PASSWORD_DEFAULT);

			// Create the user directory
			if(mkdir('users/' . $user)) {
				// Create the user config
				$usrCfg = array(
					"userName" => $user,
					"passWord" => $usrPass,
					"maxStorage" => $size_mb,
					"canUpload" => $settings['upload'],
					"canShare" => $settings['share'],
					"canDelete" => $settings['delete'],
					"canEdit" => $settings['edit'],
					"isAdmin" => $settings['admin']
				);

				// JSON encode the configuration
				$jsonCfg = json_encode($usrCfg);

				// Create the json configuration file and write the contents
				$usrFile = fopen('users/configs/' . $user . '-cfg.json','w+');
				fwrite($usrFile, $jsonCfg);
				fclose($usrFile);

				return true;
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