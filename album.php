<?php
 /*
   Plugin Name: HMD Picasa photos album
   Version: 1.1
   Description: Get all photos for your public picasa album so easily.
   Author: Hoang Manh Dung
   Author URI: http://www.hoangmanhdung.info
   Plugin URI: http://www.hoangmanhdung.info
   */
  
  /*
   Copyright 2008  Hoang Manh Dung  (email : dung14000@gmail.com)
   
   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.
   
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
$plugin_url = WP_PLUGIN_URL .'/hmd-picasa';
add_action('wp_head', 'insert_asset');
add_shortcode('hmd_album','album_filter');
function get_content($userId){
	global $plugin_url;
	$doc = new DOMDocument();
	$doc->load('http://picasaweb.google.com/data/feed/api/user/'. $userId .'?kind=album');
	$entrys = $doc->getElementsByTagName("entry");
	$content = '<div class="hmd_album_container">';
	foreach( $entrys as $entry)  {
		$thumbnail = $entry->getElementsByTagNameNS('http://search.yahoo.com/mrss/','thumbnail');
		$thumbUrl = $thumbnail->item(0)->getAttribute('url');
		$albumFeed = $entry->getElementsByTagName('link')->item(0)->getAttribute('href');
		$content .= '<div class="hmd_album">'; 
		$content .=  '	<div class="hmd_loading"></div>';
		$content .=  '	<a href="'. $albumFeed .'" class="hmd_link"><img src="'.$thumbUrl.'"/></a>';
		$content .=  '</div>';
	}
	$content .=  '</div>';
	unset($doc);
	return $content;
}

function get_id($atts){
	extract( shortcode_atts( array(
			'id' => 'dung14000'
		), $atts ) );
		return $id
}

function album_filter($atts, $content){
	global $plugin_url;	
	$decoration = get_option('decoration');
	$css = ($decoration)?$decoration : file_get_contents($plugin_url . '/css/style.css');
	$css = '<style type="text/css">'. $css . '</style>';
	$new_content = get_content(get_id($atts));
	$content = str_replace($value, $new_content, $content);
	
	$content .= '<div id="hmd_image_wrapper"></div>';
	$content .= '<script src="'.$plugin_url.'/script.php"  type="text/javascript"></script>';
	$content = $css . $content;
	return $content;
}

function insert_asset(){
	global $plugin_url;
	echo '<script src="'.$plugin_url.'/js/jquery-1.4.4.min.js" type="text/javascript" charset="utf-8"></script>';
	echo '<link rel="stylesheet" href="'.$plugin_url.'/css/prettyPhoto.css" type="text/css" />'; 
//	echo '<link rel="stylesheet" href="'.$plugin_url.'/css/style.css" type="text/css" />';
	echo '<script src="'.$plugin_url.'/js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>';
	echo '<script type="text/javascript">';
	echo 'var WP_PLUGIN_URL = "'. $plugin_url .'";';
	if(get_option('autoplay')){
		echo 'var HMD_AUTOPLAY ='. get_option('autoplay').';';		
	} 
	else {
		echo 'var HMD_AUTOPLAY = 0;';		
	}
	if(get_option('slideshow_theme')){
		echo 'var SLIDESHOW_THEME = "'. get_option('slideshow_theme').'";';		
	} 
	else {
		echo 'var SLIDESHOW_THEME = "pp_default";';		
	}
	echo '</script>';
}

add_action('admin_menu', 'hmd_plugin_menu');

function hmd_plugin_menu() {
	add_options_page('HMD Picasa Album', 'HMD Picasa Album', 'manage_options', 'hmd-album', 'hmd_plugin_options');
	add_action('admin_init', 'register_hmd_setting');
}
function register_hmd_setting(){
	register_setting('hmd_options','autoplay');
	register_setting('hmd_options','slideshow_theme');
	register_setting('hmd_options','decoration');
}
function hmd_plugin_options() {
	global $plugin_url;
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}	
?>
<script src="<?php echo $plugin_url; ?>/js/utils.js" type="text/javascript"></script>
<style type="text/css">
.wrap h2{
	border-radius: 8px;
	background: #F2EDED;
	padding-left: 10px;
	margin: 10px 100px 10px 0; 	
}

.display_setting{
	border-radius: 8px;
	background: #F2EDED;
	padding: 10px;
	margin-right: 100px;
}
</style>
<div class="wrap">
<h2>HMD Picasa Album Setting</h2>
<form action="options.php" method="post">
<?php
	settings_fields( 'hmd_options' ); 
	do_settings_fields( 'hmd-album','hmd_options' );
	$autoplay = get_option('autoplay');
	$theme = get_option('slideshow_theme');	
	$decoration = get_option('decoration');
	if($decoration == '')  $decoration = file_get_contents($plugin_url.'/css/style.css');
?>
	<div class="display_setting">
		<h3>Slideshow theme</h3>
		<select name="slideshow_theme">
			<option value="pp_default" <?php selected($theme,'pp_default'); ?>>Default</option>
			<option value="light_rounded" <?php selected($theme,'light_rounded'); ?>>Light rounded</option>
			<option value="dark_rounded" <?php selected($theme,'dark_rounded'); ?>>Dark rounded</option>
			<option value="light_square" <?php selected($theme,'light_square'); ?>>Light square</option>
			<option value="dark_square" <?php selected($theme,'dark_square'); ?>>Dark square</option>
			<option value="facebook" <?php selected($theme,'facebook'); ?>>Facebook</option>
		</select>
		<p>
		<h3>Autoplay setting</h3>
		<label for="full_size">Enable</label><input type="checkbox" name="autoplay" value="<?php echo $autoplay; ?>" <?php checked( $autoplay, 1 );?>  onclick='switcher(this);'/>
		</p>
		<h3>Frame style </h3>
		<textarea name="decoration" style="width: 100%;height: 300px;"><?php echo $decoration; ?></textarea>
	</div>
	<p>
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
</form>
</div>
<?php } ?>
