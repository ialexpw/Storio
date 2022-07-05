<?php
	/**
	 * install.php
	 *
	 * Storio installer page, check permissions before installing
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2022 Storio
	 * @link       https://github.com/ialexpw/Storio
	 */

	$dirCheck = '';

	if(isset($_POST['uploadPath']) && !empty($_POST['uploadPath'])) {
		// Remove the user tag while checking permissions
		$tempUpload = str_replace("{user}", "", $_POST['uploadPath']);

		// Check permissions and install
		if(is_writable($tempUpload) && is_writable('../users/') && is_writable('../users/configs/')) {
			Storio::Install($_POST['uploadPath']);
		}
	}
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Storio - Install</title>

		<link rel="canonical" href="">

		<!-- Bootstrap core CSS -->
		<link rel="stylesheet" href="app/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&display=swap">

		<!-- Custom styles -->
		<link rel="stylesheet" href="app/css/custom.css">
	</head>
	<body>
		<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
			<div class="container-fluid">
				<i class="bi bi-droplet" style="font-size: 2rem; margin-right:12px; margin-bottom:6px; color: cornflowerblue;"></i>
				<a class="navbar-brand" href="#"> Installer</a>
			</div>
		</nav>

		<main class="container">
			<div class="starter-template py-5 px-3">
				<div class="row">
					<div class="col-1 col-sm-3"></div>

					<div class="col-10 col-sm-6">
						<div class="card">
							<div class="card-body" style="text-align: center;">
								<!-- 404 Page -->
								<br />
								<h4 class="card-title">Storio Installer</h4><hr>

								<?php
									// Check for the install file
									if(!file_exists('../users/configs/site-settings.json')) {
										// Check the users dir permissions
										if(!is_writable('../users')) {
											$dirCheck .= '<p>Please ensure the <b>users</b> folder is writable</p>';
										}else{
											$dirCheck .= '<p>Permissions for the <b>users</b> folder are correct</p>';
										}

										// Check the configs dir permissions
										if(!is_writable('../users/configs')) {
											$dirCheck .= '<p>Please ensure the <b>users/configs</b> folder is writable</p>';
										}else{
											$dirCheck .= '<p>Permissions for the <b>users/configs</b> folder are correct</p>';
										}

										$dirCheck .= '<hr><form method="post" action="?page=install">';
										$dirCheck .= '<div class="mb-3">';
										$dirCheck .= '<label for="uploadPath" class="form-label">Upload path ({user} gets replaced by each user)</label>';
										$dirCheck .= '<input type="text" class="form-control" id="uploadPath" name="uploadPath" value="../users/{user}">';
										$dirCheck .= '</div>';

										$dirCheck .= '<button type="submit" class="btn btn-primary">Install</button>';
										$dirCheck .= '</form>';
									}else{
										$dirCheck .= 'Storio has been installed! Trying <a href="/?page=login">logging in</a>.';
									}

									// If folders need changing, echo the messages
									if(!empty($dirCheck)) {
										echo $dirCheck;
									}
								?>
								<br /><br />
							</div>
						</div>
					</div>

					<div class="col-1 col-sm-3"></div>
				</div>
				<p class="text-center" style="margin-top:5px;">Powered by <a href="https://github.com/ialexpw/Storio">Storio</a></p>
			</div>
		</main>

		<script type="text/javascript" src="app/js/jquery.min.js"></script>
		<script type="text/javascript" src="app/js/bootstrap.bundle.min.js"></script>
		<script type="text/javascript" src="app/js/session.js"></script>
	</body>
</html>