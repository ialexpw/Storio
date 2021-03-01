<?php
	/**
	 * viewVideo.php
	 *
	 * TBA
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

	// Grab the path and user
	if(isset($_GET['vid']) && !empty($_GET['vid'])) {
		echo '<script src="https://cdn.plyr.io/3.6.4/plyr.polyfilled.js"></script>';
		echo '<link rel="stylesheet" href="https://cdn.plyr.io/3.6.4/plyr.css" />';

	echo '<video id="player" playsinline controls>';
	//echo '<source src="/path/to/video.mp4" type="video/mp4" />';
	echo '<source src="viewSource.php?u=' . $_SESSION['Username'] . '&p=' . $_GET['vid'] . '" type="video/mp4">';
	echo '</video>';



		//echo '<video class="center" controls>';
		//echo '<source src="viewSource.php?u=' . $_SESSION['Username'] . '&p=' . $_GET['vid'] . '" type="video/mp4">';
		//echo 'Your browser does not support the video tag.';
		//echo '</video>';
	}
?>