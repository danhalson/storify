
	$(document).ready(function() {
		function showStatus(level, message) {
			// Remove any existing alert level classes.
			var classes = $('#status').attr('class').split(/\s+/);

			for (var i in classes) {
				if (classes[i].indexOf('alert-') > -1) {
					$('#status').removeClass(classes[i]);
				}
			}

			$('#status').addClass('alert-' + level);
			$('#status').html(message);
		}

		function processImage(file) {
		  var reader = new FileReader();

		  // Add th eimage to the form.
		  reader.onload = function(ev) {
				debugger;
				showStatus('success', 'Image uploaded.');
				var image = ev.target.result;
				image = image.replace(/^data:image\/(png|jpg|jpeg|gif);base64,/, '');

		    // Add it to the form.
		    $('#image').val(image);
		  }

		  // Reads the file.
		  reader.readAsDataURL(file);
		}

		$("#image_upload").change(function() {
		  // We're not supporting multi upload.
		  processImage(this.files[0]);
		});

		$(".drop_area").on('drag dragstart dragend dragover dragenter dragleave drop', function(ev) {
	    ev.preventDefault();
	    ev.stopPropagation();
	  })
	  .on('dragover dragenter', function() {
	    $(".drop_area").addClass('is-dragover');
	  })
	  .on('dragleave dragend drop', function() {
	    $(".drop_area").removeClass('is-dragover');
	  })
	  .on('drop', function(ev) {
			var allowed_types = $('#image_upload').attr('accept').split(',');
			var file = ev.originalEvent.dataTransfer.files[0];

			if (allowed_types.indexOf(file.type) === -1) {
				showStatus('danger', 'Filetype not supported: ' + (file.type ? file.type : 'unrecognised'));
				return false;
			}

			processImage(file);
			$('.drop_area .panel-body').text(file.name);
	  });
	});
