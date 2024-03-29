<?php
	/**
	 * login.php
	 *
	 * Log in page for users
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2022 Storio
	 * @link       https://github.com/ialexpw/Storio
	 */

	// No direct access
	if(!defined('INC_DATA')) {
		exit('error');
	}

	 // Auto direct if already logged in
	if(Storio::LoggedIn()) {
		if(Storio::LoggedIn('admin')) {
			// Go to admin dashboard
			header("Location: ?page=ad-dashboard");
		}else{
			// Go to user dashboard
			header("Location: ?page=us-dashboard");
		}
	}
	
	// Load the site configuration
	$siteCfg = Storio::SiteConfig();

	// Log in to Storio
	if(!empty($_POST)) {
		$logUsr = Storio::LoginUser($_POST);

		// If success
		if($logUsr) {
			// Set sessions
			$_SESSION['UserID'] = sha1($_POST['userInput'] . 'Storio');
			$_SESSION['Username'] = strtolower($_POST['userInput']);

			// For admin users
			if(isset($_SESSION['isAdmin'])) {
				// Go to the admin dashboard
				header("Location: ?page=ad-dashboard&li");
			}else{
				// Go to the dashboard
				header("Location: ?page=us-dashboard&li");
			}
		}else{
			// Log in error
			header("Location: ?page=login&le");
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
		<title>Storio - Log in</title>

		<link rel="canonical" href="">

		<!-- Bootstrap core CSS -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">

		<!-- Google fonts -->
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
				<a class="navbar-brand" href="?page=ad-dashboard"> <?php echo $siteCfg['siteName']; ?></a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
			</div>
		</nav>

		<main class="container">
			<div class="starter-template py-5 px-3">
				<div class="card">
					<div class="card-header text-center">
						<ul class="nav nav-tabs card-header-tabs">
							<li class="nav-item">
								<a class="nav-link active" aria-current="true" href="?page=login">
									<i class="bi bi-door-open" style="font-size: 2rem;"></i>
									<br />Log in
								</a>
							</li>
							<?php
								// Check whether registration is allowed
								if($siteCfg['allowRegistration'] == 'true') {
							?>
							<li class="nav-item">
								<a class="nav-link" href="?page=register">
									<i class="bi bi-door-closed" style="font-size: 2rem;"></i>
									<br />Register
								</a>
							</li>
							<?php
								}
							?>
						</ul>
					</div>
					<div class="card-body">
						<!-- Log in form -->
						<br />
						<h4 class="card-title">Log in to Storio</h4>
						<div class="card-text" style="margin-top:15px;">
							<form method="post" action="?page=login">
								<div class="mb-3">
									<label for="userInput" class="form-label">User name</label>
									<input type="text" class="form-control" id="userInput" name="userInput">
								</div>
								<div class="mb-3">
									<label for="passInput" class="form-label">Password</label>
									<input type="password" class="form-control" id="passInput" name="passInput">
								</div>
								<button type="submit" class="btn btn-primary">Log in</button>
							</form>
						</div>
					</div>
				</div>
				<p class="text-center" style="margin-top:5px;">Powered by <a href="https://github.com/ialexpw/Storio">Storio</a></p>
			</div>
		</main>

		<script type="text/javascript" src="app/js/jquery.min.js"></script>
		
		<!-- Bootstrap JS -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
		
		<?php
			// Updated details
			if(isset($_GET['le'])) {
				?>
                <script>
                    document.addEventListener("DOMContentLoaded", function(){
                        $('.toast').toast('show');
                    });
                </script>

                <!-- Toast notification for Share link -->
                <div class="toast-container position-absolute p-3 bottom-0 end-0" id="toastPlacement">
                    <div class="toast align-items-center bg-info bottom-0 end-0" style="background-color:#628EEB !important;" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                Log in failed, please check your credentials and try again.
                            </div>
                            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
				<?php
			}
		?>
	</body>
</html>