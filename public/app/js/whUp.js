$(document).ready(function(){
	$('input[type=file]').change(function(){

		// Init area
		const url = 'rmUpload.php';
		const form = document.querySelector('#upload');

		// Max files at a time
		var maxUploads = 25;

		////////////////////
		// Get files
		////////////////////

		const files = document.querySelector('[type=file]').files;
		const formData = new FormData(form);

		////////////////////
		// Start script
		////////////////////

		// Max files per upload
		if(files.length > maxUploads) {
			$('#filename').html("Max "+maxUploads+" uploads at a time");
			return;
		}

		// Change label text
		$("#custom-file-label").text("Selected "+files.length+" file(s)");
		

		// Show progress bar
		var x = document.getElementById("progBar");
		if (x.style.display === "none") {
			x.style.display = "inherit";
		}

		// Upload started
		if(files.length > 1) {
			$('#filename').html("Multiple files");
		}else if(files.length === 1){
			$('#filename').html(files[0].name);
		}

		// Reset progress
		$('#progress').html("");
		$('#progressBar').width(0);

		$.ajax({
			xhr: function () {
				var xhr = new window.XMLHttpRequest();

				xhr.upload.addEventListener("progress", function (evt) {
					if (evt.lengthComputable) {
						var percentComplete = evt.loaded / evt.total;
						percentComplete = parseInt(percentComplete * 100);

						// Update progress
						$('#progress').html("Progress: " + Math.round(percentComplete) + "%");
						$('#progressBar').width(percentComplete + "%");

						// Over 50%, change font colour
						if (percentComplete > 60) {
							var progFont = document.getElementById("progress");
							progFont.style.color = "#fff";
						}

						// Complete, wait
						if (percentComplete === 100) {
							$('#progress').html("Syncing with storage folder, please wait...");
						}
					}
				}, false);

				return xhr;
			},
			url: url,
			type: "POST",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			success: function (data) {
				// If error with the upload
				if(!data.success) {
					// Upload failed
					$('#progress').html(data.verbose);
					return;
				}

				// Upload success
				$('#progress').html("Complete");

				// Reload the window to get the file listing
				window.location.reload();
				return false;
			}
		});
	});
});