(function(jQuery){
	"use strict";
    
	var file_frame;
	
	jQuery(document).on('click', '.upload_image_button', function( event ){

		event.preventDefault();

		jQuery(this).addClass('active');

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
		  file_frame.open();
		  return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
		  title: jQuery( this ).data( 'uploader_title' ),
		  button: {
			text: jQuery( this ).data( 'uploader_button_text' ),
		  },
		  multiple: false  // Set to true to allow multiple files to be selected
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
		  // We set multiple to false so only get one image from the uploader
		  var attachment = file_frame.state().get('selection').first().toJSON();

		  // Do something with attachment.id and/or attachment.url here
		  jQuery('.upload_image_button.active').siblings('.image-id').eq(0).val(attachment.id);
		  jQuery('.upload_image_button.active').siblings('.image-id').eq(0).change();	//trigger change event
		  jQuery('.upload_image_button').removeClass('active');

		});

		// Finally, open the modal
		file_frame.open();
	});
	
})(jQuery);




