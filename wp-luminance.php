<?php
/*
Plugin Name: Luminance
Plugin URI: https://github.com/jcsrb/wp-luminance
Description: calculate luminance and median color for wordpress attachments
Version: 0.1
Author: Jakob Cosoroaba
Author URI: http://jakob.cosoroaba.ro/
License: MIT
*/


/*Calculate luminance and median color also save it to the attachments metadata*/
function luminance_generate_attachment_metadata( $attachment_metadata ) {

	/*Get thumbnail */
	$file = wp_upload_dir();
	$file = trailingslashit( $file['path'] ).$attachment_metadata['sizes']['thumbnail']['file'];

	/* Resample it to 1x1px*/
	list( $orig_w, $orig_h, $orig_type ) = @getimagesize( $file );
	$img = imagecreatefromstring( file_get_contents( $file ) );
	$tmp_img = imagecreatetruecolor( 1, 1 );
	imagecopyresampled( $tmp_img, $img, 0, 0, 0, 0, 1, 1, $orig_w, $orig_h ); // or ImageCopyResized


	/*Get the color */
	$rgb = imagecolorat( $tmp_img, 0, 0 );
	$r = ( $rgb >> 16 ) & 0xFF;
	$g = ( $rgb >> 8 ) & 0xFF;
	$b = $rgb & 0xFF;


	/*Calculate Color Hex*/
	$attachment_metadata['image_meta']['median_color'] = "#".dechex( $r ).dechex( $g ).dechex( $b );
	/*Calculate luminance*/
	$attachment_metadata['image_meta']['luminance'] = ( 0.2126*$r ) + ( 0.7152*$g ) + ( 0.0722*$b );
	return $attachment_metadata;
}

/*Add field to Attachment Edit Screen*/
function luminance_attachment_fields_to_edit( $form_fields, $post ) {
	$attachment_metadata = wp_get_attachment_metadata( $post->ID );
	$luminance = $attachment_metadata['image_meta']['luminance'];

	$form_fields["luminance"] = array(
		"label" => __( "Luminance" ),		
		"value" => $luminance
	);

	return $form_fields;
}


function luminance_attachment_fields_to_save( $post, $attachment ) {
	if ( isset( $attachment['luminance'] ) ) {
		if ( trim( $attachment['luminance'] ) == '' ) {			
			$post['errors']['luminance']['errors'][] = __( 'Not a valid luminance' );
		}else {
			$attachment_metadata = wp_get_attachment_metadata( $post->ID );
			$attachment_metadata['image_meta']['luminance'] = floatval( $attachment['luminance'] );
			wp_update_attachment_metadata( $post->ID, $attachment_metadata );
		}
	}
	return $post;
}


/*Add Hooks */
add_filter( "wp_generate_attachment_metadata", "luminance_generate_attachment_metadata", null, 1 );
add_filter( "attachment_fields_to_edit", "luminance_attachment_fields_to_edit", null, 2 );
add_filter( "attachment_fields_to_save", "luminance_attachment_fields_to_save", null, 2 );


?>
