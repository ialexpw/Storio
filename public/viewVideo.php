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
	echo '<video id="player" class="center" playsinline controls>';
	echo '<source src="viewSource.php?u=' . $_SESSION['Username'] . '&p=' . $_GET['vid'] . '" type="video/mp4">';
	echo '</video>';



		//echo '<video class="center" controls>';
		//echo '<source src="viewSource.php?u=' . $_SESSION['Username'] . '&p=' . $_GET['vid'] . '" type="video/mp4">';
		//echo 'Your browser does not support the video tag.';
		//echo '</video>';
	}
?>