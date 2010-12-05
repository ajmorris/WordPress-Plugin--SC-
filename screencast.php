<?
/*
Plugin Name: Screencast Video Embedder
Plugin URI: http://wordpress.org/extend/plugins/screencast-video-embedder
Description: Allows users of Screencast.com to easily embed their video/images by the use of a WordPress shortcode.
Version: 0.1
Author: TechSmith Corporation
Author URI: http://techsmith.com
License: GPL2 
*/

function screencast_handler($attributes)
{
include_once('simple_html_dom.php');

	extract(shortcode_atts(array(
		'url' => '',
		'height' => '',
		'width' => '',
	), $attributes));

$embed = '';

// if it looks like a Screencast.com tinyurl, get the embed code for the file
if ( preg_match('/screencast.com\/t\//', $url) > 0 ) {
   $html = file_get_html($url);

   if ( $height > 0 ) {
      $html->find('object#scPlayer',0)->height = $height;
   }

   if ( $width > 0 ) {
      $html->find('object#scPlayer',0)->width = $width;
   }

   $embed = $html->find('div#mediaDisplayArea', 0)->innertext;

}

return ($embed);

}

add_shortcode('screencast', 'screencast_handler');

add_filter( 'contextual_help', 'custom_post_help', 10, 2 );	

function custom_post_help($help, $screen)
{
	global $post_type; //required in 3.0 to differentiate posts from pages and other content types
	
	if ( $screen = 'post' && $post_type == 'post' ) 
	{
		$help .= '
			<p><strong>Screencast Video Embedder</strong> - You can use this to display your video in a post or on a page.<p>
			<p><strong>Example:</strong> [screencast url="http://screencast.com/tinyurl" width="400" height="300"]<p>
		';
	}
	
	return $help;
}

// create custom plugin settings menu
add_action('admin_menu', 'sve_create_menu');

function sve_create_menu() {

	//create new top-level menu
	add_menu_page('SVE Plugin Settings', 'SVE Settings', 'administrator', __FILE__, 'sve_settings_page');

	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
	//register our settings
	register_setting( 'sve-settings-group', 'default_width' );
	register_setting( 'sve-settings-group', 'default_height' );
}

function sve_settings_page()
{
?>
<div class="wrap">
<h2>Screencast Video Embedder Options</h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<table class="form-table">

<tr valign="top">
<th scope="row">Default Width</th>
<td><input type="text" name="default_width" value="<?php echo get_option('default_width'); ?>" /></td>
</tr>
 
<tr valign="top">
<th scope="row">Default Height</th>
<td><input type="text" name="default_height" value="<?php echo get_option('default_height'); ?>" /></td>
</tr>

</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="new_option_name,some_other_option,option_etc" />

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>

<?php

}


?>
