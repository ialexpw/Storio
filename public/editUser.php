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
	}else{
		exit("Permission denied");
	}
?>
<!-- Form for editing the user -->
<form class="row g-3" method="post" action="?page=ad-users&usr=<?php echo $usrEdit; ?>">
	<div class="row g-3">
		<!-- Editing user -->
		<div class="col-md-8">
			<label for="editUsrName" class="form-label">Username</label>
			<input type="text" class="form-control" id="editUsrName" name="editUsrName" value="<?php echo $usrEdit; ?>" readonly />
		</div>

		<!-- Storage for user -->
		<div class="col-md-4">
			<label for="editStorage" class="form-label">Storage (MB)</label>
			<input type="number" class="form-control" id="editStorage" name="editStorage" value="<?php echo $usrCfg['maxStorage']; ?>" />
		</div>

		<!-- Is the user enabled -->
		<div class="col-md-4">
			<label for="editEnab" class="form-label">Enabled</label>
			<select id="editEnab" name="editEnab" class="form-select">
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
			<label for="editUpload" class="form-label">Upload</label>
			<select id="editUpload" name="editUpload" class="form-select">
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
			<label for="editAdmin" class="form-label">Admin</label>
			<select id="editAdmin" name="editAdmin" class="form-select">
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
		<button type="submit" class="btn btn-primary">Save</button>
	</div>
</form>