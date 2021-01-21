<?php
	/**
	 * ad-files.php
	 *
	 * The file management page for administrators
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.aw0.uk
	 */

	// Get the user list from the dir structure
	$dirs = array_filter(glob('users/*'), 'is_dir');
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Storio - File Management</title>

		<link rel="canonical" href="https://storio.aw0.uk">

		<!-- Bootstrap core CSS -->
		<link rel="stylesheet" href="app/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&display=swap">

		<style>
			* {
				font-family: 'Nunito', sans-serif;
			}

			.nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
				height:94px;
			}

			a {
				text-decoration:none;
			}
		</style>

		<!-- Custom styles -->
		<link rel="stylesheet" href="app/css/custom.css">
	</head>
	<body>
		<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
			<div class="container-fluid">
				<i class="bi bi-droplet" style="font-size: 2rem; margin-right:12px; margin-bottom:6px; color: cornflowerblue;"></i>
				<a class="navbar-brand" href="?page=ad-dashboard"> Storio File Management</a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbars" aria-controls="navbars" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
			</div>
		</nav>

		<main class="container">
			<div class="starter-template py-5 px-3">
				<div class="card">
					<div class="card-header text-center">
						<ul class="nav nav-tabs card-header-tabs">
							<li class="nav-item" style="width:12%;">
								<a class="nav-link" href="?page=ad-dashboard">
									<i class="bi bi-house" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Dashboard
								</a>
							</li>
							<li class="nav-item" style="width:12%;">
								<a class="nav-link active" aria-current="true" href="?page=ad-files">
									<i class="bi bi-folder" style="font-size: 2rem;"></i>
									<br />Files
								</a>
							</li>
							<li class="nav-item" style="width:12%;">
								<a class="nav-link" href="?page=ad-users">
									<i class="bi bi-people" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Users
								</a>
							</li>
							<li class="nav-item" style="width:12%;">
								<a class="nav-link" href="?page=ad-messages">
									<i class="bi bi-chat" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Messages
								</a>
							</li>
							<li class="nav-item" style="width:12%;">
								<a class="nav-link" href="?page=ad-logs">
									<i class="bi bi-archive" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Logs
								</a>
							</li>
							<li class="nav-item" style="width:12%;">
								<a class="nav-link" href="?page=ad-settings">
									<i class="bi bi-gear" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Settings
								</a>
							</li>
						</ul>
					</div>
					<div class="card-body">
						<!-- File management -->
						<br />
						<h4 class="card-title">File Management</h4>
						<p class="card-text" style="margin-top:15px;">
							<?php
								// Are there users?
								if(count($dirs) > 1) {
									// Attempting to browse a users files
									if(isset($_GET['browse']) && !empty($_GET['browse'])) {
										// Check the folder exists
										if(is_dir('users/' . $_GET['browse'])) {
											// Save the arrays
											$fldArr = dirlist('users/' . $_GET['browse']);

											// Loop the folders first
											foreach($fldArr as $dir) {
												echo $dir['folders'] . '<br />';
											}

											// Loop the files after
											foreach($fldArr as $file) {
												echo $file['files'] . '<br />';
											}

											echo '<pre>';
											print_r(dirlist('users/' . $_GET['browse']));
											echo '</pre>';
										}
									}else{
										// Gen the table start
										echo '<table class="table table-hover">';
										echo '<thead>';
										echo '<tr>';
										echo '<th scope="col">User</th>';
										echo '<th scope="col">Storage</th>';
										echo '<th scope="col">Controls</th>';
										echo '</tr>';
										echo '</thead>';
										echo '<tbody>';

										// Loop users
										foreach($dirs as $usr) {
											// Remove the users/ prefix
											$usr = str_replace("users/", "", $usr);

											// Skip the configs dir
											if($usr == 'configs') {
												continue;
											}

											// Try and get the config
											if(file_exists('users/configs/' . $usr . '-cfg.json')) {
												$usrCfg = json_decode(file_get_contents('users/configs/' . $usr . '-cfg.json'), true);
											}

											// Add table row
											echo '<tr>';
											echo '<td>' . $usr . '</td>';
											echo '<td>' . number_format($usrCfg['usedStorage']) . ' / ' . number_format($usrCfg['maxStorage']) . ' MB</td>';
											echo '<td><a href="?page=ad-files&browse=' . $usr . '/">Browse Files</a></td>';
											echo '</tr>';
										}

										echo '</tbody>';
										echo '</table>';
									}
								}else{
									echo 'Storio does not have any users, would you like to <a href="?page=ad-users">create one</a>?';
								}
							?>
						</p>
					</div>
				</div>
			</div>
		</main>

		<script src="app/js/bootstrap.bundle.min.js"></script>
	</body>
</html>
<?php
	function dirlist($dir){
		if(!file_exists($dir)){ return $dir.' does not exists'; }
		$list = array('path' => $dir, 'dirview' => array(), 'dirlist' => array(), 'files' => array(), 'folders' => array());
	
		$dirs = array($dir);
		while(null !== ($dir = array_pop($dirs))){
			if($dh = opendir($dir)){
				while(false !== ($file = readdir($dh))){
					if($file == '.' || $file == '..') continue;
					$path = $dir.DIRECTORY_SEPARATOR.$file;
					$list['dirlist_natural'][] = $path;
					if(is_dir($path)){
						$list['dirview'][$dir]['folders'][] = $path;
						// Bos klasorler while icerisine tekrar girmeyecektir. Klasorun oldugundan emin olalım.
						if(!isset($list['dirview'][$path])){ $list['dirview'][$path] = array(); }
						$dirs[] = $path;
					}
					else{
						$list['dirview'][$dir]['files'][] = $path;
					}
				}
				closedir($dh);
			}
		}
	
		if(!empty($list['dirview'])) ksort($list['dirview']);
	
		// Dosyaları dogru sıralama yaptırıyoruz. Deniz P. - info[at]netinial.com
		foreach($list['dirview'] as $path => $file){
			if(isset($file['files'])){
				$list['dirlist'][] = $path;
				$list['files'] = array_merge($list['files'], $file['files']);
				$list['dirlist'] = array_merge($list['dirlist'], $file['files']);
			}
			// Add empty folders to the list
			if(is_dir($path) && array_search($path, $list['dirlist']) === false){
				$list['dirlist'][] = $path;
			}
			if(isset($file['folders'])){
				$list['folders'] = array_merge($list['folders'], $file['folders']);
			}
		}
	
		return $list;
	}
?>