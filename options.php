<?php
$plugin_url = WP_PLUGIN_URL .'/hmd-picasa';
$autoplay = $_POST['autoplay'];
$theme = $_POST['slideshow_theme'];
$decoration = $_POST['decoration'];
update_option('autoplay',$autoplay);
update_option('slideshow_theme',$theme);
update_option('decoration',$decoration);
?>
