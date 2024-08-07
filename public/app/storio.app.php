<?php
	/**
	 * storio.app.php
	 *
	 * Functions file for Storio
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2024 Storio
	 * @link       https://github.com/ialexpw/Storio
	 */

	$sessTimeout = 7200;
	session_start();
	setcookie(session_name(), session_id(), time() + $sessTimeout);

	/**
	 * Storio Class
	 */
	class Storio {
		/**
		 * Storio::Install()
		 * Install Storio
		 */
		public static function Install($dataPath = '') {
			// Check the data path
			if(empty($dataPath)) {
				$dataPath = '../users/{user}';
			}

			// Set up the default settings
			$siteCfg = array(
				"siteName" => "Storio",
				"allowRegistration" => false,
				"defaultAllowance" => 1000,				// in MB
				"uploadMaxMB" => 100,					// in MB
				"uploadFolder" => "$dataPath",
				"downloadPage" => true
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

			// Hash the admin password
			$usrPass = password_hash("AdminUser123", PASSWORD_DEFAULT);

			// Create the array for admin
			$usrAr = array(
				"enabled" => 'true',
				"upload" => 'true',
				"admin" => 'true'
			);

			// Add the admin user
			if(!Storio::AddUser('admin', $usrPass, 0, $usrAr, "admin@storio")) {
				exit('Error creating admin user.');
			}

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
		public static function UserConfig($user) {
			// Lower case
			$user = strtolower($user);

			// Attempt both paths to include the json file
			if(file_exists('../users/configs/' . $user . '-cfg.json')) {
				return json_decode(file_get_contents('../users/configs/' . $user . '-cfg.json'), true);
			}else{
				if(file_exists('users/configs/' . $user . '-cfg.json')) {
					return json_decode(file_get_contents('users/configs/' . $user . '-cfg.json'), true);
				}
			}

			return 'error';
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
		public static function AddUser($user, $password, $size_mb, $settings, $email="") {
			// Load the site config
			$site_cfg = Storio::SiteConfig();

			// Users upload folder
			$usrDir = str_replace("{user}", strtolower($user), $site_cfg['uploadFolder']);

			// Check if a user already exists
			if(file_exists($usrDir)) {
				return false;
			}

			// Create directories needed
			@mkdir($usrDir);
			@mkdir('../users/' . strtolower($user));
			@mkdir('../users/configs/_thumbs/' . strtolower($user));

			// Create the user directory
			if(is_dir($usrDir) && is_dir('../users/' . strtolower($user))) {
				// Create the user config
				$usrCfg = array(
					"userName" => strtolower($user),
					"usrEmail" => $email,
					"passWord" => $password,
					"usedStorage" => 0,
					"maxStorage" => $size_mb,
					"isEnabled" => $settings['enabled'],
					"canUpload" => $settings['upload'],
					"isAdmin" => $settings['admin']
				);

				// JSON encode the configuration
				$jsonCfg = json_encode($usrCfg);

				// Create the json configuration file and write the contents
				$usrFile = fopen('../users/configs/' . strtolower($user) . '-cfg.json','w+');
				fwrite($usrFile, $jsonCfg);
				fclose($usrFile);

				return true;
			}

			return false;
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
			}

			return false;
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
		public static function LoginUser($post) {
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
					// Check if user is enabled
					if($usrCfg['isEnabled'] == 'true') {
						// Admin user?
						if($usrCfg['isAdmin'] == "true") {
							// Set the session
							$_SESSION['isAdmin'] = $user;
							return true;
						}else{
							return true;
						}
					}
				}
			}

			return false;
		}
		
		/**
		 * Storio::ReadableSize()
		 * Converts bytes into a readable value
		 * @param $bytes
		 * @return string
		 */
		public static function ReadableSize($bytes) {
			// Avoid / by 0 if the file is empty
			if($bytes == 0) {
				return '0B';
			}
			
			$i = floor(log($bytes, 1024));
			return round($bytes / pow(1024, $i), [0,0,2,2,3][$i]).['B','kB','MB','GB','TB'][$i];
		}
		
		/**
		 * Storio::DirList()
		 * Lists all the files/folders from a specific area
		 * @author https://stackoverflow.com/questions/24783862/list-all-the-files-and-folders-in-a-directory-with-php-recursive-function
		 * @param $dir
		 * @return array|string
		 */
		public static function DirList($dir) {
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
							// Bos klasorler while icerisine tekrar girmeyecektir. Klasorun oldugundan emin olalım
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
		
		/**
		 * Storio::UpdateStorageSize()
		 * Used to update the storage used from each user, done after removing/adding files and also on the cron
		 * @param $user
		 */
		public static function UpdateStorageSize($user, $cron = 0) {
			// Check directory and config file
			if(is_dir('../users/' . $user) && file_exists('../users/configs/' . $user . '-cfg.json')) {
				// Load the configuration based on cron
				$usrCfg = Storio::UserConfig($user);

				// Get the site configuration
				$site_cfg = Storio::SiteConfig();

				// Users upload folder
				$usr_folder = str_replace("{user}", $user, $site_cfg['uploadFolder']);

				// Get dir size
				$dirSize = Storio::getDirectorySize($usr_folder);

				// Add the usage
				if($dirSize > 0) {
					$usrCfg['usedStorage'] = number_format($dirSize / 1048576, 2);
				}else{
					// Empty
					$usrCfg['usedStorage'] = 0;
				}

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
		public static function getDirectorySize($path) {
			// Not a directory
			if(!is_dir($path)) {
				return 0;
			}

			$path = strval($path);
			$io = popen("ls -ltrR {$path} |awk '{print \$5}'|awk 'BEGIN{sum=0} {sum=sum+\$1} END {print sum}'", 'r');
			$size = intval(fgets($io, 80));
			pclose($io);

			return $size;
		}
		
		/**
		 * Storio::delTree()
		 * Deletes a folder, including sub-dirs and files
		 * @param $dir
		 * @return bool
		 */
		public static function delTree($dir) {
			$files = array_diff(scandir($dir), array('.', '..'));

			foreach ($files as $file) {
				(is_dir("$dir/$file")) ? Storio::delTree("$dir/$file") : unlink("$dir/$file");
			}

			return rmdir($dir);
		}

		public static function rglob($pattern, $flags = 0) {
			$files = glob($pattern, $flags);

			foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
				$files = array_merge($files, Storio::rglob($dir . '/' . basename($pattern), $flags));
			}

			return $files;
		}
		
		/**
		 * Storio::AddShareLink()
		 * Generate a share link
		 * @param $path
		 * @param $file
		 * @param $user
		 * @param int $len
		 */
		public static function AddShareLink($path, $file, $user) {
			// Build the path
			$fullPath = $path . '/' . $file;
			
			// Check the path
			if(file_exists($fullPath)) {
				// Decode the share links file
				$shareCfg = json_decode(file_get_contents('../users/configs/share-links.json'), true);

				// Generate string for the share link
				$shareId = sha1($user . $fullPath);

				// Convert to chars with md5
				$shareId = md5('STR' . $shareId);

				// Cut the length of the string down
				$shareId = substr($shareId, 0, 15);
				
				// Add the required strings
				$shareCfg['ShareLinks'][$shareId]['File'] = $file;
				$shareCfg['ShareLinks'][$shareId]['Path'] = $fullPath;
				$shareCfg['ShareLinks'][$shareId]['User'] = $user;
				$shareCfg['ShareLinks'][$shareId]['Multi'] = 0;
				
				// Encode and resave the config
				$shareCfgEncode = json_encode($shareCfg);
				file_put_contents('../users/configs/share-links.json', $shareCfgEncode);

				return $shareId;
			}
		}

		public static function AddMultiShareLink($files, $user) {
			// Decode the share links file
			$shareCfg = json_decode(file_get_contents('../users/configs/share-links.json'), true);

			// Generate string for the share link
			$shareId = sha1(microtime() . 'STOR');

			// Convert to chars with md5
			$shareId = md5('STR' . $shareId);

			// Cut the length of the string down
			$shareId = substr($shareId, 0, 16);

			// Keep track of the loop
			$f = 0;

			// Loop through the files
			foreach($files as $file) {
				// Add the required strings
				$shareCfg['ShareLinks'][$shareId][$f]['File'] = $file['name'];
				$shareCfg['ShareLinks'][$shareId][$f]['Path'] = $file['path'];

				$f++;
			}

			// Add after the loop
			$shareCfg['ShareLinks'][$shareId]['User'] = $user;
			$shareCfg['ShareLinks'][$shareId]['Multi'] = 1;

			// Encode and resave the config
			$shareCfgEncode = json_encode($shareCfg);
			file_put_contents('../users/configs/share-links.json', $shareCfgEncode);

			return $shareId;
		}

		/**
		 * Storio::MoveFile()
		 * Moves a file from one folder to another
		 */
		public static function MoveFile($source, $destination, $file,  $user) {
			// Check the file exists
			if(file_exists('../users/' . $user . '/' . $source)) {
				// Check the destination folder exists
				if(is_dir('../users/' . $user . '/' . $destination)) {
					if(rename('../users/' . $user . '/' . $source, '../users/' . $user . '/' . $destination . '/' . $file)) {
						return true;
					}
				}
			}

			return false;
		}

		public static function CreateThumb($filepath, $thumbpath, $thumbnail_width, $thumbnail_height, $background='transparent') {
			list($original_width, $original_height, $original_type) = getimagesize($filepath);
			if ($original_width > $original_height) {
				$new_width = $thumbnail_width;
				$new_height = intval($original_height * $new_width / $original_width);
			} else {
				$new_height = $thumbnail_height;
				$new_width = intval($original_width * $new_height / $original_height);
			}
			$dest_x = intval(($thumbnail_width - $new_width) / 2);
			$dest_y = intval(($thumbnail_height - $new_height) / 2);
		
			if ($original_type === 1) {
				$imgt = "ImageGIF";
				$imgcreatefrom = "ImageCreateFromGIF";
			} else if ($original_type === 2) {
				$imgt = "ImageJPEG";
				$imgcreatefrom = "ImageCreateFromJPEG";
			} else if ($original_type === 3) {
				$imgt = "ImagePNG";
				$imgcreatefrom = "ImageCreateFromPNG";
			} else {
				return false;
			}
		
			$old_image = $imgcreatefrom($filepath);
			$new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height); // creates new image, but with a black background
		
			// figuring out the color for the background
			if(is_array($background) && count($background) === 3) {
			  list($red, $green, $blue) = $background;
			  $color = imagecolorallocate($new_image, $red, $green, $blue);
			  imagefill($new_image, 0, 0, $color);
			// apply transparent background only if is a png image
			} else if($background === 'transparent' && $original_type === 3) {
			  imagesavealpha($new_image, TRUE);
			  $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
			  imagefill($new_image, 0, 0, $color);
			}
		
			imagecopyresampled($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
			$imgt($new_image, $thumbpath);
			return file_exists($thumbpath);
		}
	}
?>