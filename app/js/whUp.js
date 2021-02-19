$(document).ready(function(){
	$('input[type=file]').change(function(){

		// Init area
		const url = 'remoteUploadMultiple.php';
		const form = document.querySelector('#upload');
		var totalSize = 0;

		// For plus
		var maxUploads = 10;
		var maxUploadSize = 2150000000; // 2GB

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
		}else if(files.length == 1){
			$('#filename').html(files[0].name);
		}

		// Reset progress
		$('#progress').html("");
		$('#progressBar').width(0);

		////////////////////
		// Loop files
		////////////////////

		// Loop through the files
		for (let i = 0; i < files.length; i++) {
			let file = files[i];

			// Add the total size of all files
			totalSize += file.size;
		}

		//formData.append();

		// Check file size - if high error (5GB)
		if(totalSize > maxUploadSize) {
			$('#filename').html("Maximum size has been exceeded");

			// Hide the progress bar
			if (x.style.display === "inherit") {
				x.style.display = "none";
			}

			// return out
			return;
		}

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
						if (percentComplete > 50) {
							var progFont = document.getElementById("progress");
							progFont.style.color = "#fff";
						}

						// Complete, wait
						if (percentComplete === 100) {
							$('#progress').html("Syncing with download server, please wait...");
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
					//upload failed
					//$('#progress').html("Upload failed");
					//$('#filename').html(data.verbose);
					$('#progress').html(data.verbose);
					return;
				}

				//upload successful
				$('#progress').html("Complete");

				window.location.reload();
				return false;
			}
		});
	});
});