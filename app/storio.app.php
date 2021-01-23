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
				"defaultAllowance" => 5000,
				"expireFiles" => false,
				"expiryDays" => 31,
				"uploadMaxMB" => 500,
				"mailProc" => "mail", // "mail" or "smtp"
				"smtpServer" => "",
				"smtpPort" => "587",
				"smtpSec" => "tls",
				"smtpAuth" => true,
				"smtpUsername" => "",
				"smtpPassword" => "",
				"smtpFromAddr" => ""
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
		public static function AddUser($user, $email="", $password, $size_mb, $settings) {
			// Check if a user already exists
			if(file_exists('users/' . $user)) {
				return false;
			}

			// Create the user directory
			if(mkdir('users/' . $user)) {
				// Create the user config
				$usrCfg = array(
					"userName" => $user,
					"usrEmail" => $email,
					"passWord" => $password,
					"usedStorage" => 0,
					"maxStorage" => $size_mb,
					"canView" => $settings['view'],
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
			}else{
				return false;
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
		public static function LoggedIn($type='') {
			// Standard user log in
			if(empty($type)) {
				if(!isset($_SESSION['UserID']) || !isset($_SESSION['Username'])) {
					return 0;
				}else{
					return 1;
				}
			// Admin log in
			}else if($type == 'admin') {
				if(!isset($_SESSION['UserID']) || !isset($_SESSION['Username']) || !isset($_SESSION['isAdmin'])) {
					return 0;
				}else{
					return 1;
				}
			}
		}

		/**
		 * Storio::ValidateUserData()
		 * Validate the user data when adding a user
		 */
		public static function ValidateUserData($post) {
			// Check the user
			if(!ctype_alnum($post['inputUser'])) {
				return -1;
			}

			// Hash the password
			$usrPass = password_hash($post['inputPass'], PASSWORD_DEFAULT);

			// Check the storage value
			if(!is_numeric($post['inputStorage'])) {
				return -2;
			}

			// Create the array
			$usrAr = array(
				"view" => $post['inputView'],
				"upload" => $post['inputUpload'],
				"share" => $post['inputShare'],
				"delete" => $post['inputDelete'],
				"edit" => $post['inputEdit'],
				"admin" => $post['inputAdmin']
			);

			// Add the user
			if(Storio::AddUser(strtolower($post['inputUser']), $post['inputEmail'], $usrPass, $post['inputStorage'], $usrAr)) {
				return true;
			}
		}

		public static function LoginUser($post) {
			// Store the data
			$user = strtolower($post['userInput']);
			$pass = $post['passInput'];

			// Check the user
			if(!ctype_alnum($user)) {
				return false;
			}

			// Check directory and config file
			if(is_dir('users/' . $user) && file_exists('users/configs/' . $user . '-cfg.json')) {
				// Load the configuration
				$usrCfg = json_decode(file_get_contents('users/configs/' . $user . '-cfg.json'), true);

				// Verify password
				if(password_verify($pass, $usrCfg['passWord'])) {
					// Admin user?
					if($usrCfg['isAdmin'] == "true") {
						// Set the session
						$_SESSION['isAdmin'] = $user;
						return true;
					}else{
						return true;
					}
				}else{
					return false;
				}
			}else{
				return false;
			}
		}

		public static function ReadableSize($bytes) {
			if($bytes == 0) {
				return '0B';
			}
			
			$i = floor(log($bytes, 1024));
			return round($bytes / pow(1024, $i), [0,0,2,2,3][$i]).['B','kB','MB','GB','TB'][$i];
		}

		public static function DirList($dir){
			if(!file_exists($dir)){ return $dir.' does not exists'; }
			$list = array('path' => $dir, 'dirview' => array(), 'dirlist' => array(), 'files' => array(), 'folders' => array());
		
			$dirs = array($dir);
			while(null !== ($dir = array_pop($dirs))){
				if($dh = opendir($dir)){
					while(false !== ($file = readdir($dh))){
						if($file == '.' || $file == '..') continue;
						$path = $dir.DIRECTORY_SEPARATOR.$file;
						$list['dirlist_natural'][] = $path;
						if(is_dir($path)){
							$list['dirview'][$dir]['folders'][] = $path;
							// Bos klasorler while icerisine tekrar girmeyecektir. Klasorun oldugundan emin olalım.
							if(!isset($list['dirview'][$path])){ $list['dirview'][$path] = array(); }
							$dirs[] = $path;
						}
						else{
							$list['dirview'][$dir]['files'][] = $path;
						}
					}
					closedir($dh);
				}
			}
		
			if(!empty($list['dirview'])) ksort($list['dirview']);
		
			// Dosyaları dogru sıralama yaptırıyoruz. Deniz P. - info[at]netinial.com
			foreach($list['dirview'] as $path => $file){
				if(isset($file['files'])){
					$list['dirlist'][] = $path;
					$list['files'] = array_merge($list['files'], $file['files']);
					$list['dirlist'] = array_merge($list['dirlist'], $file['files']);
				}
				// Add empty folders to the list
				if(is_dir($path) && array_search($path, $list['dirlist']) === false){
					$list['dirlist'][] = $path;
				}
				if(isset($file['folders'])){
					$list['folders'] = array_merge($list['folders'], $file['folders']);
				}
			}
		
			return $list;
		}

		/**
		 * Storio::CheckLicence()
		 * Function to check the licence code
		 */
		public static function CheckLicence($user, $code) {
			return true;
		}
	}
?>