wp-luminance
============

calculate luminance and median color for wordpress attachments and saves it in their attachment metadata
usefull if you want to customize your theme based on what images are used in post

##Usage

1. Install the plugin
2. Regenerate thumbnails 
3. Use values in your theme

###Get the Data

    $attachment_metadata = wp_get_attachment_metadata($attachment_id);		
    $luminance = $attachment_metadata['image_meta']['luminance'];
    $median_color = $attachment_metadata['image_meta']['median_color'];

##Download
https://github.com/jcsrb/wp-luminance/archive/master.zip
