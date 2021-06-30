<?php
	/**
	 * us-files.php
	 *
	 * The file management page for users
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.uk
	 */

	ini_set('display_startup_errors', 1);
	ini_set('display_errors', 1);
	error_reporting(-1);

	include 'app/storio.app.php';
	include 'app/icons.class.php';

	// Redirect if not logged in
	if(!Storio::LoggedIn()) {
		header("Location: ?page=login");
	}

	// Check we are getting a search term
	if(isset($_GET['sid']) && !empty($_GET['sid'])) {
		// Get the username
		$stUser = $_SESSION['Username'];

		// Wildcard search on the file within the users dir
		$scResult = Storio::rglob('../users/' . $stUser . '/*' . $_GET['sid'] . '*');

		// We have results
		if(!empty($scResult)) {
			// Set up the layout of the table
			echo '<div class="row">';

			echo '<div class="col-8 left-indent"><b>Search results</b></div>';
			echo '<div class="col-4" style="text-align:center;"><b>Location</b></div>';

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
				$filePath = str_replace("../users/" . $stUser, "", $filePath);

				// Work out the icon to use
				if(is_dir($res)) {
					$ico = 'far fa-folder';
				}else{
					$ico = StoIco::ShowIcon($fileName);
				}

				// Build the result view
				echo '<div class="col-8 left-indent" style="margin-bottom:2px;"><i style="font-size: 1.4rem; margin-right:6px;" class="' . $ico . '"></i> ' . $fileName . '</div>';
				echo '<div class="col-4" style="text-align:center;" style="margin-bottom:2px;">' . $filePath . '</div>';
			}

			echo '</div>';

			// Add spacing before normal file listing
			echo '<br />';
		}else{
			// No results found from the search
			echo '<p class="left-indent">No results have been found</p>';

			// Add spacing before normal file listing
			echo '<br />';
		}
	}
?>