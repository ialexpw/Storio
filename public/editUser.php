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
	<!-- Second line -->
	<div class="row g-3">
		<!-- Is the user enabled -->
		<div class="col-md-4">
			<label for="inputEnab" class="form-label">User enabled</label>
			<select id="inputEnab" name="inputEnab" class="form-select">
				<option value="true" selected>true</option>
				<option value="false">false</option>
			</select>
		</div>

		<!-- Is the user enabled -->
		<div class="col-md-4">
			<label for="inputEnab" class="form-label">User enabled</label>
			<select id="inputEnab" name="inputEnab" class="form-select">
				<option value="true" selected>true</option>
				<option value="false">false</option>
			</select>
		</div>

		<!-- Can upload files -->
		<div class="col-md-4">
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