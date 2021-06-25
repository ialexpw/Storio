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
	}
?>
<p>Editing user: <?php echo $usrCfg['userName']; ?></p>

<form class="row g-3" method="post" action="?page=ad-users">
	<div class="row g-3">
		<!-- Is the user enabled -->
		<div class="col-md-4">
			<label for="inputEnab" class="form-label">Enabled</label>
			<select id="inputEnab" name="inputEnab" class="form-select">
				<?php
					// Enabled/disabled options
					if($usrCfg['isEnabled'] == 'true') {
						echo '<option value="true" selected>true</option>';
						echo '<option value="false">false</option>';
					}else{
						echo '<option value="true">true</option>';
						echo '<option value="false" selected>false</option>';
					}
				?>
			</select>
		</div>

		<!-- Can the user upload files? -->
		<div class="col-md-4">
			<label for="inputUpload" class="form-label">Upload</label>
			<select id="inputUpload" name="inputUpload" class="form-select">
				<?php
					// Enabled/disabled options
					if($usrCfg['canUpload'] == 'true') {
						echo '<option value="true" selected>true</option>';
						echo '<option value="false">false</option>';
					}else{
						echo '<option value="true">true</option>';
						echo '<option value="false" selected>false</option>';
					}
				?>
			</select>
		</div>

		<!-- Is the user an admin? -->
		<div class="col-md-4">
			<label for="inputAdmin" class="form-label">Admin</label>
			<select id="inputAdmin" name="inputAdmin" class="form-select">
				<?php
					// Enabled/disabled options
					if($usrCfg['isAdmin'] == 'true') {
						echo '<option value="true" selected>true</option>';
						echo '<option value="false">false</option>';
					}else{
						echo '<option value="true">true</option>';
						echo '<option value="false" selected>false</option>';
					}
				?>
			</select>
		</div>
	</div>

	<!-- Footer and submit -->
	<div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
		<button type="submit" class="btn btn-primary">Create</button>
	</div>
</form>