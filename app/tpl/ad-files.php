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
										echo '<td>Browse Files</td>';
										echo '</tr>';
									}

									echo '</tbody>';
									echo '</table>';
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