<?php
	/**
	 * editUser.php
	 *
	 * Remotely called file to edit user permissions
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2021 Storio
	 * @link       https://storio.uk
	 */

	include 'app/storio.app.php';

	// Need admin to call this
	if(!Storio::LoggedIn('admin')) {
		exit("Permission denied");
	}

	// Check for the user
	if(!empty($_GET['uid']) && is_dir('../users/' . $_GET['uid'])) {
		// Store the user
		$usrEdit = $_GET['uid'];

		// Load the configuration
		$usrCfg = Storio::UserConfig($usrEdit);

		echo '<pre>';
		print_r($usrCfg);
		echo '</pre>';
	}
?>

<form class="row g-3" method="post" action="?page=ad-users">
	<div class="row g-3">
		<!-- Username -->
		<div class="col-md">
			<label for="inputUser" class="form-label">Username</label>
			<input type="text" class="form-control" id="inputUser" name="inputUser" required>
		</div>

		<!-- Email address -->
		<div class="col-md">
			<label for="inputEmail" class="form-label">Email (optional)</label>
			<input type="email" class="form-control" id="inputEmail" name="inputEmail">
		</div>

		<!-- Password -->
		<div class="col-md">
			<label for="inputPass" class="form-label">Password</label>
			<input type="password" class="form-control" id="inputPass" name="inputPass" required>
		</div>

		<!-- Admin user -->
		<div class="col-md-2">
			<label for="inputAdmin" class="form-label">Admin user</label>
			<select id="inputAdmin" name="inputAdmin" class="form-select">
				<option value="false" selected>false</option>
				<option value="true">true</option>
			</select>
		</div>
	</div>

	<!-- Second line -->
	<div class="row g-3">
		<!-- Storage allowance -->
		<div class="col-md-3">
			<label for="inputStorage" class="form-label">Storage (MB)</label>
			<input type="number" class="form-control" id="inputStorage" name="inputStorage" value="<?php echo $siteCfg['defaultAllowance']; ?>">
		</div>

		<!-- Is the user enabled -->
		<div class="col-md">
			<label for="inputEnab" class="form-label">User enabled</label>
			<select id="inputEnab" name="inputEnab" class="form-select">
				<option value="true" selected>true</option>
				<option value="false">false</option>
			</select>
		</div>

		<!-- Can upload files -->
		<div class="col-md">
			<label for="inputUpload" class="form-label">Can upload</label>
			<select id="inputUpload" name="inputUpload" class="form-select">
				<option value="true" selected>true</option>
				<option value="false">false</option>
			</select>
		</div>
	</div>

	<!-- Footer and submit -->
	<div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
		<button type="submit" class="btn btn-primary">Create</button>
	</div>
</form>