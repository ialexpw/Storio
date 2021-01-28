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

		public static function LoadSiteConfig() {
			return json_decode(file_get_contents('users/configs/site-settings.json'), true);
		}

		public static function DownloadFile($file) {
			return true;
		}

		public static function SimpleCrypt($string, $action = 'e') {
			// you may change these values to your own
			$secret_key = 'Storio';
			$secret_iv = 'ShareLinkGen';
		 
			$output = false;
			$encrypt_method = "AES-256-CBC";
			$key = hash( 'sha256', $secret_key );
			$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
		 
			if( $action == 'e' ) {
				$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
			}
			else if( $action == 'd' ){
				$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
			}
		 
			return $output;
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

		public static function GoBack($path) {
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

		public static function UpdateStorageSize($user, $size) {
			// Check directory and config file
			if(is_dir('users/' . $user) && file_exists('users/configs/' . $user . '-cfg.json')) {
				// Load the configuration
				$usrCfg = json_decode(file_get_contents('users/configs/' . $user . '-cfg.json'), true);

				// Add the usage
				$usrCfg['usedStorage'] = number_format($usrCfg['usedStorage'] + $size, 2);

				// Encode and resave the config
				$usrCfgEncode = json_encode($usrCfg);
				file_put_contents('users/configs/' . $user . '-cfg.json', $usrCfgEncode);
			}
		}

		public static function delTree($dir) {
			$files = array_diff(scandir($dir), array('.', '..'));

			foreach ($files as $file) {
				(is_dir("$dir/$file")) ? Storio::delTree("$dir/$file") : unlink("$dir/$file");
			}

			return rmdir($dir);
		}

		public static function GenerateFileIcon($file) {
			// Set out our icons

			// Arrays of file types
			// Document files
			$docArr = array(
				"doc",
				"docx"
			);

			// Video files
			$vidArr = array(
				"webm",
				"mp4"
			);

			// Image files
			$imgArr = array(
				"png",
				"jpeg",
				"jpg",
				"gif",
				"apng",
				"ico",
				"svg",
				"tiff",
				"webp"
			);

			// Text/source files
			$txtArr = array(
				"asm",
				"atom",
				"c",
				"cpp",
				"cs",
				"css",
				"d",
				"dart",
				"docker",
				"dockerfile",
				"go",
				"h",
				"htm",
				"html",
				"ini",
				"js",
				"javascript",
				"json",
				"less",
				"lua",
				"makefile",
				"markdown",
				"md",
				"nginx",
				"perl",
				"php",
				"py",
				"python",
				"rb",
				"rss",
				"ruby",
				"rust",
				"sass",
				"scss",
				"sh",
				"smarty",
				"sql",
				"twig",
				"txt",
				"vbnet",
				"vim",
				"xml",
				"yml",
				"yaml"
			);

			// Audio files
			$audArr = array(
				"mp3",
				"ogg"
			);

			// Code icon
			$cIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-code" viewBox="0 0 16 16">
			<path d="M6.646 5.646a.5.5 0 1 1 .708.708L5.707 8l1.647 1.646a.5.5 0 0 1-.708.708l-2-2a.5.5 0 0 1 0-.708l2-2zm2.708 0a.5.5 0 1 0-.708.708L10.293 8 8.646 9.646a.5.5 0 0 0 .708.708l2-2a.5.5 0 0 0 0-.708l-2-2z"/>
			<path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z"/>
			</svg>';

			// Text icon
			$txIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-text" viewBox="0 0 16 16">
			<path d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zM5 8a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1H5z"/>
			<path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z"/>
			</svg>';

			// Word icon
			$wIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-word" viewBox="0 0 16 16">
			<path d="M4.879 4.515a.5.5 0 0 1 .606.364l1.036 4.144.997-3.655a.5.5 0 0 1 .964 0l.997 3.655 1.036-4.144a.5.5 0 0 1 .97.242l-1.5 6a.5.5 0 0 1-.967.01L8 7.402l-1.018 3.73a.5.5 0 0 1-.967-.01l-1.5-6a.5.5 0 0 1 .364-.606z"/>
			<path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
			</svg>';

			// Powerpoint icon
			$pIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-ppt" viewBox="0 0 16 16">
			<path d="M6.5 4.5a.5.5 0 0 0-1 0V12a.5.5 0 0 0 1 0V9.236a3 3 0 1 0 0-4.472V4.5zm0 2.5a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
			<path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z"/>
			</svg>';

			// Excel icon
			$exIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-excel" viewBox="0 0 16 16">
			<path d="M5.18 4.616a.5.5 0 0 1 .704.064L8 7.219l2.116-2.54a.5.5 0 1 1 .768.641L8.651 8l2.233 2.68a.5.5 0 0 1-.768.64L8 8.781l-2.116 2.54a.5.5 0 0 1-.768-.641L7.349 8 5.116 5.32a.5.5 0 0 1 .064-.704z"/>
			<path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
			</svg>';

			// Image icon
			$iIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-image" viewBox="0 0 16 16">
			<path d="M8.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
			<path d="M12 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM3 2a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v8l-2.083-2.083a.5.5 0 0 0-.76.063L8 11 5.835 9.7a.5.5 0 0 0-.611.076L3 12V2z"/>
			</svg>';

			// Video icon
			$vIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-play" viewBox="0 0 16 16">
			<path d="M6 10.117V5.883a.5.5 0 0 1 .757-.429l3.528 2.117a.5.5 0 0 1 0 .858l-3.528 2.117a.5.5 0 0 1-.757-.43z"/>
			<path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
			</svg>';

			// Audio icon
			$aIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-music" viewBox="0 0 16 16">
			<path d="M10.304 3.13a1 1 0 0 1 1.196.98v1.8l-2.5.5v5.09c0 .495-.301.883-.662 1.123C7.974 12.866 7.499 13 7 13c-.5 0-.974-.134-1.338-.377-.36-.24-.662-.628-.662-1.123s.301-.883.662-1.123C6.026 10.134 6.501 10 7 10c.356 0 .7.068 1 .196V4.41a1 1 0 0 1 .804-.98l1.5-.3z"/>
			<path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
			</svg>';

			// PDF icon
			$pdIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-richtext" viewBox="0 0 16 16">
			<path d="M7 4.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0zm-.861 1.542l1.33.886 1.854-1.855a.25.25 0 0 1 .289-.047l1.888.974V7.5a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V7s1.54-1.274 1.639-1.208zM5 9a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1H5z"/>
			<path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z"/>
			</svg>';

			// Compressed icon
			$comIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-zip" viewBox="0 0 16 16">
			<path d="M6.5 7.5a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v.938l.4 1.599a1 1 0 0 1-.416 1.074l-.93.62a1 1 0 0 1-1.109 0l-.93-.62a1 1 0 0 1-.415-1.074l.4-1.599V7.5zm2 0h-1v.938a1 1 0 0 1-.03.243l-.4 1.598.93.62.93-.62-.4-1.598a1 1 0 0 1-.03-.243V7.5z"/>
			<path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm5.5-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9v1H8v1h1v1H8v1h1v1H7.5V5h-1V4h1V3h-1V2h1V1z"/>
			</svg>';

			// Default file
			$defIco = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file" viewBox="0 0 16 16">
			<path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
			</svg>';

			// Grab the extension
			$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

			// Check against the arrays
			if(in_array($ext, $docArr)) {
				return $wIco;
			}else if(in_array($ext, $vidArr)) {
				return $vIco;
			}else if(in_array($ext, $imgArr)) {
				return $iIco;
			}else if(in_array($ext, $txtArr)) {
				return $cIco;
			}else if(in_array($ext, $audArr)) {
				return $aIco;
			}else if($ext == 'pdf') {
				return $pdIco;
			}else{
				return $defIco;
			}
		}

		public static function AddLog($time, $type, $msg) {
			// Format the time
			$logData = date("H:i:s - d M Y : <", $time);

			// Append the type and message
			$logData .= $type . '> ' . $msg . "\n";

			// Insert the data
			file_put_contents('users/configs/site-logs.txt', $logData, FILE_APPEND);
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