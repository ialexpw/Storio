<?php
	/**
	 * cli.php
	 *
	 * Quick way to update some parts of Storio from the command line
	 * Currently a WIP
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2022 Storio
	 * @link       https://github.com/ialexpw/Storio
	 */

	include 'public/app/storio.app.php';

	if(php_sapi_name() === 'cli') {
		if(!empty($argv[1])) {
			// Create a new user
			if(strtolower($argv[1]) == 'create-user') {
				// Check the user exists already
				if(!file_exists('users/' . $argv[2])) {
					// Password supplied
					if(isset($argv[3])) {
						// Hash the password
						$usrPass = password_hash($argv[3], PASSWORD_DEFAULT);

						// Default settings
						$usrAr = array(
							"enabled" => "true",
							"upload" => "true",
							"admin" => "false"
						);

						// Create with default perms
						if(Storio::AddUser(strtolower($argv[2]), $usrPass, 1000, $usrAr)) {
							exit("User created");
						}else{
							exit("Failed to create user");
						}
					}else{
						exit("Supply a password");
					}
				}else{
					exit("User exists");
				}
			}

			// Set a password
			if(strtolower($argv[1]) == 'set-password') {
				// Check the user exists
				if(file_exists('users/' . $argv[2])) {
					// Password supplied
					if(isset($argv[3])) {
						// Load the user configuration
						$usrCfg = Storio::UserConfig($argv[2]);

						// Hash the new password
						$usrCfg['passWord'] = password_hash($argv[3], PASSWORD_DEFAULT);

						// Encode and resave the config
						$usrCfgEncode = json_encode($usrCfg);
						file_put_contents('users/configs/' . $argv[2] . '-cfg.json', $usrCfgEncode);

						exit('Password updated');
					}else{
						exit('Supply a password');
					}
				}else{
					exit('User not found');
				}
			}

			// Create video thumbnails FFMPEG required
			if(strtolower($argv[1]) == 'video-thumbs') {
				exit();
			}
		}else{
			exit('No params');
		}
	}
?>