<?php
	/**
	 * viewSource.php
	 *
	 * Use for showing images/video files directly within a lightbox or media player
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.uk
	 */

	include 'app/storio.app.php';
	
	// Grab the path and user
	if(isset($_GET['p']) && !empty($_GET['p']) && isset($_GET['u']) && !empty($_GET['u'])) {
		// Users directory
		if(is_dir('../users/' . $_GET['u'])) {
			// Decrypt the string
			$usrFile = Storio::SimpleCrypt($_GET['p'], 'd');

			// Check the file exists
			if(file_exists($usrFile)) {
				// Is it an image?
				if(strpos(mime_content_type($usrFile), 'image') !== false) {
					// Grab the image contents
					$getImg = file_get_contents($usrFile);

					// Base64 encode it
					$base64 = 'data:' . mime_content_type($usrFile) . ';base64,' . base64_encode($getImg);

					// Echo the image out
					echo '<img style="width:100%; height:100%;" src="' . $base64 . '" />';
				}

				// Is it a video?
				if(strpos(mime_content_type($usrFile), 'video') !== false) {
					// Set the path
					$path = $usrFile;

					// Note file type
					$ftype = mime_content_type($usrFile);

					$file = $path;
					$fp = @fopen($file, 'rb');

					$size   = filesize($file); // File size
					$length = $size;           // Content length
					$start  = 0;               // Start byte
					$end    = $size - 1;       // End byte

					header('Content-type: ' . $ftype);
					header("Accept-Ranges: 0-$length");
					if (isset($_SERVER['HTTP_RANGE'])) {

						$c_start = $start;
						$c_end   = $end;

						list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
						if (strpos($range, ',') !== false) {
							header('HTTP/1.1 416 Requested Range Not Satisfiable');
							header("Content-Range: bytes $start-$end/$size");
							exit;
						}
						if ($range == '-') {
							$c_start = $size - substr($range, 1);
						}else{
							$range  = explode('-', $range);
							$c_start = $range[0];
							$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
						}
						$c_end = ($c_end > $end) ? $end : $c_end;
						if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
							header('HTTP/1.1 416 Requested Range Not Satisfiable');
							header("Content-Range: bytes $start-$end/$size");
							exit;
						}
						$start  = $c_start;
						$end    = $c_end;
						$length = $end - $start + 1;
						fseek($fp, $start);
						header('HTTP/1.1 206 Partial Content');
					}

					header("Content-Range: bytes $start-$end/$size");
					header("Content-Length: ".$length);


					$buffer = 1024 * 8;
					while(!feof($fp) && ($p = ftell($fp)) <= $end) {

						if ($p + $buffer > $end) {
							$buffer = $end - $p + 1;
						}
						set_time_limit(0);
						echo fread($fp, $buffer);
						flush();
					}

					fclose($fp);
					exit();
				}
			}
		}
	}

	// Grab the path and user for the video player
	if(isset($_GET['vid']) && !empty($_GET['vid'])) {
		echo '<video style="width:100%;" controls>';
		echo '<source src="viewSource.php?u=' . $_SESSION['Username'] . '&p=' . $_GET['vid'] . '" type="video/mp4">';
		echo 'Your browser does not support the video tag.';
		echo '</video>';
	}
?>