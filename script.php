<?php
header('Content-type: text/javascript');
?>
function convert(lg){
	if(lg == 1) return true;
	return false;
}
jQuery(document).ready(function(){
	jQuery('p').each(function(i,element){
		if(!$(element).html()) $(element).remove();
	});

	jQuery('.hmd_link').click(function(e){
		e.preventDefault();
		var url = encodeURIComponent(jQuery(this).attr('href'));
		url = WP_PLUGIN_URL + '/getdata.php?url=' + url;
		var index = jQuery(this).index();
		var loading = $(this).prev();
		loading.show();
		var h = $(this).parent().height();
		var w = $(this).parent().width();
		loading.height(h);
		loading.width(w);
		if(SLIDESHOW_THEME.indexOf('default') < 0) {
			loading.css('background','#fff url(wp-content/plugins/hmd-picasa/images/prettyPhoto/' + SLIDESHOW_THEME + '/loader.gif) no-repeat center center');
		}
		jQuery.ajax({
			'url': url,
			'cache':true,
			'dataType': 'json',
			'success':function(data){
				var html = [];
				var thumbs = data[0];
				var fulls = data[1];
				
				for(var i = 0, len = thumbs.length; i < len; i++){
					html.push('<a href="'+ fulls[i] +'" rel="prettyPhoto[gallery' +index+']" style="display:none;"><img src="' + thumbs[i] + '"\/></a>');
				}
				html = html.join('');
				jQuery('#hmd_image_wrapper').html(html);				
				jQuery('#hmd_image_wrapper').find('a[rel^="prettyPhoto"]').prettyPhoto({
					autoplay_slideshow: convert(HMD_AUTOPLAY),
					show_title:false,
					theme: SLIDESHOW_THEME
				});
				jQuery.prettyPhoto.open(fulls);				
				jQuery('.hmd_loading').hide();
			}
		});
	});
});

