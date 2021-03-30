<?php
	/**
	 * viewVideo.php
	 *
	 * Show the video player
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.uk
	 */

	include 'app/storio.app.php';

	// Grab the path and user
	if(isset($_GET['vid']) && !empty($_GET['vid'])) {
		echo '<video class="center" controls>';
		echo '<source src="viewSource.php?u=' . $_SESSION['Username'] . '&p=' . $_GET['vid'] . '" type="video/mp4">';
		echo 'Your browser does not support the video tag.';
		echo '</video>';
	}
?>