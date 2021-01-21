<?php
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
		<title>Storio - User Management</title>

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
								<a class="nav-link" href="?page=ad-files">
									<i class="bi bi-folder" style="font-size: 2rem; color: cornflowerblue;"></i>
									<br />Files
								</a>
							</li>
							<li class="nav-item" style="width:12%;">
								<a class="nav-link active" aria-current="true" href="?page=ad-users">
									<i class="bi bi-people" style="font-size: 2rem;"></i>
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
						<h4 class="card-title">User Management (<a href="#" data-bs-toggle="modal" data-bs-target="#userModal">new</a>)</h4>
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
									echo '<th scope="col">View</th>';
									echo '<th scope="col">Upload</th>';
									echo '<th scope="col">Edit</th>';
									echo '<th scope="col">Share</th>';
									echo '<th scope="col">Delete</th>';
									echo '<th scope="col">Admin</th>';
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
										echo '<td style="width:14%;">' . $usr . '</td>';
										echo '<td style="width:14%;">' . $usrCfg['usedStorage'] . ' / ' . $usrCfg['maxStorage'] . ' MB</td>';
										echo '<td style="width:12%;">' . ($usrCfg['canView'] ? 'true' : 'false') . '</td>';
										echo '<td style="width:12%;">' . ($usrCfg['canUpload'] ? 'true' : 'false') . '</td>';
										echo '<td style="width:12%;">' . ($usrCfg['canEdit'] ? 'true' : 'false') . '</td>';
										echo '<td style="width:12%;">' . ($usrCfg['canShare'] ? 'true' : 'false') . '</td>';
										echo '<td style="width:12%;">' . ($usrCfg['canDelete'] ? 'true' : 'false') . '</td>';
										echo '<td style="width:12%;">' . ($usrCfg['isAdmin'] ? 'true' : 'false') . '</td>';
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

		<!-- Modal -->
		<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="userModalLabel">Add new user</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
<form class="row g-3">
<div class="col-md-3">
<label for="inputUser" class="form-label">Username</label>
<input type="text" class="form-control" id="inputUser">
</div>
<div class="col-md-3">
<label for="inputEmail" class="form-label">Email (optional)</label>
<input type="email" class="form-control" id="inputEmail">
</div>
<div class="col-md-3">
<label for="inputPass" class="form-label">Password</label>
<input type="password" class="form-control" id="inputPass">
</div>
<div class="col-md-3">
<div class="form-check" style="margin-top:12px;">
<input class="form-check-input" type="checkbox" id="gridCheck">
<label class="form-check-label" for="gridCheck">
Send welcome email
</label>
</div>
</div>
<div class="col-12">
<label for="inputAddress" class="form-label">Address</label>
<input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St">
</div>
<div class="col-12">
<label for="inputAddress2" class="form-label">Address 2</label>
<input type="text" class="form-control" id="inputAddress2" placeholder="Apartment, studio, or floor">
</div>
<div class="col-md-6">
<label for="inputCity" class="form-label">City</label>
<input type="text" class="form-control" id="inputCity">
</div>
<div class="col-md-4">
<label for="inputState" class="form-label">State</label>
<select id="inputState" class="form-select">
<option selected>Choose...</option>
<option>...</option>
</select>
</div>
<div class="col-md-2">
<label for="inputZip" class="form-label">Zip</label>
<input type="text" class="form-control" id="inputZip">
</div>
<div class="col-12">
<div class="form-check">
<input class="form-check-input" type="checkbox" id="gridCheck">
<label class="form-check-label" for="gridCheck">
Check me out
</label>
</div>
</div>
<div class="col-12">
<button type="submit" class="btn btn-primary">Sign in</button>
</div>
</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary">Create</button>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>