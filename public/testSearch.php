<?php
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
		$result = rglob('../users/alex' . '/*' . $_GET['sid'] . '*');

		echo '<pre>';
		print_r($result);
		echo '</pre>';
		//var_dump($result);
	}

	// usage: to find the test.zip file recursively
	//$result = rglob('../users/alex' . '/enVigil.zip');
	//var_dump($result);

	//echo '<br /><br />';

	
?>