<?php
	/**
	 * storio.app.php
	 *
	 * Functions file for Storio
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.uk
	 */

	$sessTimeout = 7200;
	session_start();
	setcookie(session_name(), session_id(), time() + $sessTimeout);

	/**
	 * Storio Class
	 */
	class Storio {
		/**
		 * Storio::AddUser()
		 * Add a user to Storio, user information and permissions are passed through with the array
		 */
		public static function Install(): bool
		{
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
			$siteFile = fopen('../users/configs/site-settings.json','w+');
			fwrite($siteFile, $jsonCfg);
			fclose($siteFile);
			
			// Create the share links json
			$shareCfg = array(
				"ShareLinks" => array(
					
				)
			);
			
			// JSON encode the configuration
			$jsonShareCfg = json_encode($shareCfg);
			
			// Create the json configuration file and write the contents
			$shareFile = fopen('../users/configs/share-links.json','w+');
			fwrite($shareFile, $jsonShareCfg);
			fclose($shareFile);

			return true;
		}

		/**
		 * Storio::SiteConfig()
		 * Returns the site configuration after decoding the JSON
		 */
		public static function SiteConfig() {
			return json_decode(file_get_contents('../users/configs/site-settings.json'), true);
		}
		
		/**
		 * Storio::ShareLinks()
		 * Returns the share links after decoding the JSON
		 */
		public static function ShareLinks() {
			return json_decode(file_get_contents('../users/configs/share-links.json'), true);
		}
		
		/**
		 * Storio::UserConfig()
		 * Returns the user configuration after decoding the JSON
		 * @param $user
		 * @return array
		 */
		public static function UserConfig($user): array
		{
			if(file_exists('../users/configs/' . $user . '-cfg.json')) {
				return json_decode(file_get_contents('../users/configs/' . $user . '-cfg.json'), true);
			}else{
				return array(
					"error" => "user_not_exist"
				);
			}
		}
		
		/**
		 * Storio::SimpleCrypt()
		 * Simple encryption/decryption function used for share and download links
		 * @param $string
		 * @param string $action
		 * @return false|string
		 */
		public static function SimpleCrypt($string, $action = 'e') {
			// Change these to encrypt links - changing these will void all previous share links
			$secret_key = 'Storio';
			$secret_iv = 'ShareLinkGen';

			$output = false;
			$encrypt_method = "AES-256-CBC";
			$key = hash('sha256', $secret_key);
			$iv = substr(hash('sha256', $secret_iv), 0, 16);
		 
			// If encrypting
			if($action == 'e') {
				$output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
			}
			// If decrypting
			else if($action == 'd'){
				$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
			}
		 
			return $output;
		}
		
		/**
		 * Storio::AddUser()
		 * Add a user to Storio, user information and permissions are passed through with the array
		 * @param $user
		 * @param $password
		 * @param $size_mb
		 * @param $settings
		 * @param string $email
		 * @return bool
		 */
		public static function AddUser($user, $password, $size_mb, $settings, $email=""): bool
		{
			// Check if a user already exists
			if(file_exists('../users/' . $user)) {
				return false;
			}

			// Create the user directory
			if(mkdir('../users/' . $user)) {
				// Create the user config
				$usrCfg = array(
					"userName" => $user,
					"usrEmail" => $email,
					"passWord" => $password,
					"usedStorage" => 0,
					"maxStorage" => $size_mb,
					"isEnabled" => $settings['enabled'],
					"canUpload" => $settings['upload'],
					"canShare" => $settings['share'],
					"canDelete" => $settings['delete'],
					"isAdmin" => $settings['admin']
				);

				// JSON encode the configuration
				$jsonCfg = json_encode($usrCfg);

				// Create the json configuration file and write the contents
				$usrFile = fopen('../users/configs/' . $user . '-cfg.json','w+');
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
		 * @param $view
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
		 * @param string $type
		 * @return int
		 */
		public static function LoggedIn($type='') {
			// Standard user log in
			if(empty($type)) {
				if(!isset($_SESSION['UserID']) || !isset($_SESSION['Username'])) {
					return false;
				}else{
					return true;
				}
			// Admin log in
			}else if($type == 'admin') {
				if(!isset($_SESSION['UserID']) || !isset($_SESSION['Username']) || !isset($_SESSION['isAdmin'])) {
					return false;
				}else{
					return true;
				}
			}else{
				return false;
			}
		}
		
		/**
		 * Storio::ValidateUserData()
		 * Validate the user data when adding a user
		 * @param $post
		 * @return bool|int
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
				"enabled" => $post['inputEnab'],
				"upload" => $post['inputUpload'],
				"share" => $post['inputShare'],
				"delete" => $post['inputDelete'],
				"admin" => $post['inputAdmin']
			);

			// Add the user
			if(Storio::AddUser(strtolower($post['inputUser']), $usrPass, $post['inputStorage'], $usrAr, $post['inputEmail'])) {
				return true;
			}else{
				return false;
			}
		}
		
		/**
		 * Storio::LoginUser()
		 * Function to validate the user credentials and proceed to log in the user to the correct place
		 * @param $post
		 * @return bool
		 */
		public static function LoginUser($post): bool
		{
			// Store the data
			$user = strtolower($post['userInput']);
			$pass = $post['passInput'];

			// Check the user
			if(!ctype_alnum($user)) {
				return false;
			}

			// Check directory and config file
			if(is_dir('../users/' . $user) && file_exists('../users/configs/' . $user . '-cfg.json')) {
				// Load the configuration
				$usrCfg = Storio::UserConfig($user);

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
			}else if($user == 'admin') {
				// Load the site configuration
				$usrCfg = Storio::SiteConfig();

				// Verify password
				if(password_verify($pass, $usrCfg['adminPassword'])) {
					// Set the session
					$_SESSION['isAdmin'] = $user;
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}
		
		/**
		 * Storio::ReadableSize()
		 * Converts bytes into a readable value
		 * @param $bytes
		 * @return string
		 */
		public static function ReadableSize($bytes): string
		{
			if($bytes == 0) {
				return '0B';
			}
			
			$i = floor(log($bytes, 1024));
			return round($bytes / pow(1024, $i), [0,0,2,2,3][$i]).['B','kB','MB','GB','TB'][$i];
		}
		
		/**
		 * Storio::DirList()
		 * Lists all the files/folders from a specific area
		 * @param $dir
		 * @return array|string
		 */
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
		
			// Dosyaları dogru sıralama yaptırıyoruz.
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
		 * Storio::GoBack()
		 * Simple function to work out one path back (used from sub-dirs)
		 * @param $path
		 * @return string
		 */
		public static function GoBack($path): string
		{
			// Check if there is a "/" in the path
			if(strpos($path, '/') !== false) {
				$exp = explode("/", $path);

				$str = '&browse=';

				for($i = 0; $i < count($exp)-1; $i++) {
					if($i==0) {
						$str .= $exp[$i];
					}else{
						$str .= '/' . $exp[$i];
					}
				}

				return $str;
			}else{
				// Return nothing so back to the "home"
				return '';
			}
		}
		
		/**
		 * Storio::UpdateStorageSize()
		 * Used to update the storage used from each user, done after removing/adding files and also on the cron
		 * @param $user
		 */
		public static function UpdateStorageSize($user) {
			// Check directory and config file
			if(is_dir('../users/' . $user) && file_exists('../users/configs/' . $user . '-cfg.json')) {
				// Load the configuration
				$usrCfg = Storio::UserConfig($user);

				// Get size of directory
				//$usrDir = Storio::getDirectorySize('../users/' . $user);

				// Add the usage
				$usrCfg['usedStorage'] = number_format(Storio::getDirectorySize('../users/' . $user) / 1048576, 2);

				// Encode and resave the config
				$usrCfgEncode = json_encode($usrCfg);
				file_put_contents('../users/configs/' . $user . '-cfg.json', $usrCfgEncode);
			}
		}
		
		/**
		 * Storio::getDirectorySize()
		 * Works out a directory (plus sub-dir sizes) - used from UpdateStorageSize()
		 * @param $path
		 * @return int
		 */
		public static function getDirectorySize($path): int
		{
			if(!is_dir( $path )) {
				return 0;
			}

			$path   = strval( $path );
			$io     = popen( "ls -ltrR {$path} |awk '{print \$5}'|awk 'BEGIN{sum=0} {sum=sum+\$1} END {print sum}'", 'r' );
			$size   = intval( fgets( $io, 80 ) );
			pclose( $io );

			return $size;
		}
		
		/**
		 * Storio::delTree()
		 * Deletes a folder, including sub-dirs and files
		 * @param $dir
		 * @return bool
		 */
		public static function delTree($dir): bool
		{
			$files = array_diff(scandir($dir), array('.', '..'));

			foreach ($files as $file) {
				(is_dir("$dir/$file")) ? Storio::delTree("$dir/$file") : unlink("$dir/$file");
			}

			return rmdir($dir);
		}
		
		/**
		 * Storio::AddLog()
		 * Used to add logs to the system of events
		 * @param $time
		 * @param $type
		 * @param $msg
		 */
		public static function AddLog($time, $type, $msg) {
			// Format the time
			$logData = date("H:i:s - d M Y : <", $time);

			// Append the type and message
			$logData .= $type . '> ' . $msg . "\n";

			// Insert the data
			file_put_contents('../users/configs/site-logs.txt', $logData, FILE_APPEND);
		}
		
		/**
		 * Storio::AddShareLink()
		 * Generate a share link
		 * @param $path
		 * @param $file
		 * @param $user
		 * @param int $len
		 * @return void
		 * @throws Exception
		 */
		public static function AddShareLink($path, $file, $user): void
		{
			// Build the path
			$fullPath = $path . '/' . $file;
			
			// Check the path
			if(file_exists($fullPath)) {	
				// Decode the share links file
				$shareCfg = json_decode(file_get_contents('../users/configs/share-links.json'), true);

				// Generate string for the share link
				$shareId = sha1($user . $fullPath);

				// Cut the length of the string down
				$shareId = substr($shareId, 0, 15);
				
				// Add the required strings
				$shareCfg['ShareLinks'][$shareId]['File'] = $file;
				$shareCfg['ShareLinks'][$shareId]['Path'] = $fullPath;
				$shareCfg['ShareLinks'][$shareId]['User'] = $user;
				
				// Encode and resave the config
				$shareCfgEncode = json_encode($shareCfg);
				file_put_contents('../users/configs/share-links.json', $shareCfgEncode);
			}
		}
		
		/**
		 * Storio::CheckLicence()
		 * Function to check the licence code - I'd rather you did not edit this, but do what you have to!
		 * @return bool
		 */
		public static function CheckLicence(): bool
		{
			return true;
		}
	}