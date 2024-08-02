<?php
	/**
	 * stSearch.php
	 *
	 * File search utility for Storio
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2022 Storio
	 * @link       https://github.com/ialexpw/Storio
	 */

	include 'app/storio.app.php';
	include 'app/icons.class.php';

	// Redirect if not logged in
	if(!Storio::LoggedIn()) {
		header("Location: ?page=login");
	}

	// Load the site configuration
	$siteCfg = Storio::SiteConfig();

	// Users upload folder
	$usrDir = str_replace("{user}", $_SESSION['Username'], $siteCfg['uploadFolder']);

	// Check we are getting a search term
	if(!empty($_GET['sid'])) {
		// Get the username
		$stUser = $_SESSION['Username'];

		// Ensure there is a users folder
		if(is_dir('../users/' . $stUser)) {
			// Wildcard search on the file within the users dir
			$scResult = Storio::rglob($usrDir . '/*' . $_GET['sid'] . '*');

			// We have results
			if(!empty($scResult)) {
				// Set up the layout of the table
				echo '<div class="row">';

				echo '<div class="col-8 left-indent"><b>Search results</b></div>';
				echo '<div class="col-4 mobileCenter"><b>Location</b></div>';

				// End the row
				echo '</div>';
				echo '<hr>';

				echo '<div class="row">';

				// Loop the search results
				foreach($scResult as $res) {
					// Get file name
					$strExplode = explode('/', $res);
					$fileName = end($strExplode);

					// Get path to file
					$filePath = str_replace($fileName, "", $res);

					// Strip out the users/username structure
					$filePath = str_replace($usrDir, "", $filePath);

					// Work out the icon to use
					if(is_dir($res)) {
						$ico = StoIco::ShowIcon("folder");
						echo '<div class="col-8 left-indent" style="margin-bottom:2px;"><i style="font-size: 1.4rem; margin-right:6px;" class=""></i> ' . $ico . $fileName . '</div>';
					}else{
						$ico = StoIco::ShowIcon($fileName);
						echo '<div class="col-8 left-indent" style="margin-bottom:2px;"><i style="font-size: 1.4rem; margin-right:12px;" class=""></i> ' . $ico . $fileName . '</div>';
					}

					// Build the result view
					echo '<div class="col-4" style="margin-bottom:2px;">' . $filePath . '</div>';
				}

				echo '</div>';

				// Add spacing before normal file listing
			}else{
				// No results found from the search
				$ico = StoIco::ShowIcon("empty");
				echo '<p class="left-indent" style="margin-bottom:2px;"><i style="font-size: 1.4rem; margin-right:6px;" class=""></i>' . $ico . ' No results have been found</p>';
			}
			echo '<br />';
		}
	}