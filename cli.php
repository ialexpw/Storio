<?php
	/**
	 * cli.php
	 *
	 * Quick way to update some parts of Storio from the command line
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.uk
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

						// Create with default perms
						if(Storio::AddUser(strtolower($argv[2]), $usrPass, 1000, $usrAr, $post['inputEmail'])) {

						}
					}else{
						// Random password
					}
				}
			}

			// Set a password
			if(strtolower($argv[1]) == 'set-password') {
				// Check the user exists
				if(file_exists('users/' . $argv[2])) {
					// Password supplied
					if(isset($argv[3])) {
						// Load the user configuration
						$usrCfg = Storio::UserConfig($argv[2], 1);

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
		}else{
			exit('No params');
		}
	}
?>