<?php
	/**
	 * load_sizes.php
	 *
	 * Load the sizes on a timer to update without refreshing
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.aw0.uk
	 */

	include '../../app/storio.app.php';

	// Load the configuration
	$usrCfg = json_decode(file_get_contents('../../users/configs/' . $_SESSION['Username'] . '-cfg.json'), true);

	if($usrCfg['usedStorage'] > 0) {
		// Work out the percentage
		$percUsed = number_format($usrCfg['usedStorage'] * (100/$usrCfg['maxStorage']));
	}else{
		$percUsed = 0;
	}

	echo '<br /><hr><p class="text-center">Storage allocation</p><div class="progress" style="width:50%;">';
	echo '<div class="progress-bar" role="progressbar" style="color:black; width: ' . $percUsed . '%" aria-valuenow="' . $percUsed . '" aria-valuemin="0" aria-valuemax="100"></div>';
	echo '<small class="justify-content-center d-flex position-absolute w-50">' . $usrCfg['usedStorage'] . 'MB / ' . $usrCfg['maxStorage'] . 'MB</small>';
	echo '</div>';
?>