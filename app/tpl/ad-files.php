<?php
	$dirs = array_filter(glob('users/*'), 'is_dir');
	print_r($dirs);
?>