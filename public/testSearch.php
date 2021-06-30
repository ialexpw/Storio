<?php
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);

	// Does not support flag GLOB_BRACE
	function rglob($pattern, $flags = 0) {
		$files = glob($pattern, $flags);
		foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
			$files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
		}
		return $files;
	}

	if(isset($_GET['sid']) && !empty($_GET['sid'])) {
		// to find the all files that names ends with test.zip
		$scResult = rglob('../users/alex' . '/*' . $_GET['sid'] . '*');

		// We have results
		if(!empty($scResult)) {
			// Set up the layout of the table
			echo '<div class="row">';

			echo '<div class="col-8 col-md-8 left-indent"><b>File name</b></div>';
			echo '<div class="col-4 col-md-4" style="text-align:center;"><b>Location</b></div>';

			// End the row
			echo '</div>';
			echo '<hr>';

			echo '<div class="row">';

			foreach($scResult as $res) {
				// Get file name
				$strExplode = explode('/', $res);
				$fileName = end($strExplode);

				// Get path to file
				$filePath = str_replace($fileName, "", $res);

				echo '<div class="col-8 col-md-8 left-indent" style="margin-bottom:2px;"><i style="font-size: 1.4rem; margin-right:6px;" class="far fa-folder"></i> <a href="?page=us-files&browse=">' . $fileName . '</a></div>';
				echo '<div class="col-4 col-md-4" style="text-align:center;" style="margin-bottom:2px;"><a href="" class="">' . $filePath . '</a></div>';

			}

			echo '</div>';
		}

		//echo '<pre>';
		//print_r($result);
		//echo '</pre>';
		//var_dump($result);
	}

	// usage: to find the test.zip file recursively
	//$result = rglob('../users/alex' . '/enVigil.zip');
	//var_dump($result);

	//echo '<br /><br />';

	
?>