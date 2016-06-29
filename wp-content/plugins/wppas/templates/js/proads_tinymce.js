(function() {
	
	/* TEXT EDITOR BUTTON */
    tinymce.create('tinymce.plugins.wpproads', {
        init : function(ed, url) {
			url = url.split('/templates/js');
			url = url[0];
            ed.addButton('wpproads_button', {
                title : 'WP Pro Advertising System Shortcode Editor',
                image : url+'/images/banner_icon_20.png',
                onclick : function() {
                     //ed.selection.setContent('[pre_ad adid="123"]' + ed.selection.getContent() + '[/pre_ad]');
					
					 var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
						W = W - 80;
						H = H - 84;
						tb_show( 'WP Pro Advertising System Shortcode Editor', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=wpproads-shortcode-editor-form' );
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('wpproads', tinymce.plugins.wpproads);
	
	
	// executes this when the DOM is ready
	jQuery(function(){
		
		jQuery.ajax({
		   type: "POST",
		   url: ajaxurl,
		   data: "action=load_wpproads_shortcodes"
		}).done(function( msg ) {
		   //success: function( msg ){
				
				var form = jQuery( msg );
				//var table = form.find('table');
				form.appendTo('body').hide();
				
				
				/* #Buttons
				================================================== */ 
				form.find('#adzone_submit').click(function(){
					var shortcode = '[pro_ad_display_adzone';
					
					if( jQuery('#adzone_id').val() != '' ){
						shortcode += ' id="' + jQuery('#adzone_id').val() + '"';
					}
					// Default Options
					if( jQuery('#ajax_load').val() != 0 ){
						shortcode += ' ajax_load="'+ jQuery('#ajax_load').val() +'"';
					}
					if( jQuery('#hide_if_loggedin').val() != 0 ){
						shortcode += ' hide_if_loggedin="'+ jQuery('#hide_if_loggedin').val() +'"';
					}
					if( jQuery('#adzone_class').val() != '' ){
						shortcode += ' class="'+ jQuery('#adzone_class').val() +'"';
					}
					if( jQuery('#adzone_align').val() != '' ){
						shortcode += ' align="'+ jQuery('#adzone_align').val() +'"';
					}
					if( jQuery('#adzone_info_text').val() != '' ){
						shortcode += ' info_text="'+ jQuery('#adzone_info_text').val() +'"';
					}
					if( jQuery('#adzone_info_text_img').val() != '' ){
						shortcode += ' info_text_img="'+ jQuery('#adzone_info_text_img').val() +'"';
					}
					if( jQuery('#adzone_info_text_url').val() != '' ){
						shortcode += ' info_text_url="'+ jQuery('#adzone_info_text_url').val() +'"';
					}
					if( jQuery('#adzone_info_text').val() != '' ){
						shortcode += ' info_text_position="'+ jQuery('#adzone_info_text_position').val() +'"';
					}
					if( jQuery('#adzone_info_text').val() != '' && jQuery('.adzone_info_text_color').val() != '' ){
						shortcode += ' font_color="'+ jQuery('.adzone_info_text_color').val() +'"';
					}
					if( jQuery('#adzone_info_text').val() != '' ){
						shortcode += ' font_size="'+ jQuery('#adzone_info_text_font_size').val() +'"';
					}
					if( jQuery('#adzone_info_text').val() != '' && jQuery('#adzone_info_text_decoration').val() != '' ){
						shortcode += ' text_decoration="'+ jQuery('#adzone_info_text_decoration').val() +'"';
					}
					if( jQuery('#adzone_padding').val() != '' ){
						shortcode += ' padding="'+ jQuery('#adzone_padding').val() +'"';
					}
					if( jQuery('.adzone_background_color').val() != '' ){
						shortcode += ' background_color="'+ jQuery('.adzone_background_color').val() +'"';
					}
					/*if( jQuery('.adzone_background_pattern').val() != '' ){
						shortcode += ' background_pattern="'+ jQuery('.adzone_background_pattern').val() +'"';
					}*/
					if( jQuery('#adzone_border_radius').val() != '' ){
						shortcode += ' border_radius="'+ jQuery('#adzone_border_radius').val() +'"';
					}
					if( jQuery('#adzone_border').val() != '' ){
						shortcode += ' border="'+ jQuery('#adzone_border').val() +'"';
					}
					if( jQuery('.adzone_border_color').val() != '' ){
						shortcode += ' border_color="'+ jQuery('.adzone_border_color').val() +'"';
					}
					// Popup
					if( jQuery('#adzone_is_popup').val() == 1 ){
						shortcode += ' popup="1"';
					}
					if( jQuery('#adzone_popup_cookie').val() == 1 ){
						shortcode += ' cookie="1"';
					}
					if( jQuery('#adzone_popup_close_btn').val() == 0 ){
						shortcode += ' popup_close_btn="0"';
					}
					if( jQuery('.adzone_popup_delay').val() != '' ){
						shortcode += ' delay="'+ jQuery('.adzone_popup_delay').val() +'"';
					}
					if( jQuery('.adzone_popup_auto_close_time').val() != '' ){
						shortcode += ' popup_auto_close_time="'+ jQuery('.adzone_popup_auto_close_time').val() +'"';
					}
					if( jQuery('#adzone_is_exit_popup').val() == 1 ){
						shortcode += ' exit_popup="1"';
					}
					if( jQuery('#adzone_popup_nobg').val() == 1 ){
						shortcode += ' nobg="1"';
					}
					if( jQuery('.adzone_popup_bg_color').val() != '' ){
						shortcode += ' popup_bg="'+ jQuery('.adzone_popup_bg_color').val() +'"';
					}
					if( jQuery('.adzone_popup_opacity').val() != '' ){
						shortcode += ' popup_opacity="'+ jQuery('.adzone_popup_opacity').val() +'"';
					}
					// Background
					if( jQuery('#adzone_is_background').val() == 1 ){
						shortcode += ' background="1"';
					}
					if( jQuery('.adzone_background_container').val() != '' ){
						shortcode += ' container="'+ jQuery('.adzone_background_container').val() +'"';
					}
					if( jQuery('#adzone_background_container_type').val() != '' ){
						shortcode += ' container_type="'+ jQuery('#adzone_background_container_type').val() +'"';
					}
					if( jQuery('.adzone_background_repeat').val() != '' ){
						shortcode += ' repeat="'+ jQuery('.adzone_background_repeat').val() +'"';
					}
					if( jQuery('.adzone_background_stretch').val() != '' ){
						shortcode += ' stretch="'+ jQuery('.adzone_background_stretch').val() +'"';
					}
					if( jQuery('.adzone_background_bg_color').val() != '' ){
						shortcode += ' bg_color="'+ jQuery('.adzone_background_bg_color').val() +'"';
					}
					// Corner Peel
					if( jQuery('#adzone_is_cornercurl').val() == 1 ){
						shortcode += ' corner_curl="'+ jQuery('#adzone_is_cornercurl').val() +'"';
					}
					if( jQuery('.adzone_cornercurl_small').val() != '' ){
						shortcode += ' corner_small="'+ jQuery('.adzone_cornercurl_small').val() +'"';
					}
					if( jQuery('.adzone_cornercurl_big').val() != '' ){
						shortcode += ' corner_big="'+ jQuery('.adzone_cornercurl_big').val() +'"';
					}
					if( jQuery('#adzone_is_cornercurl').val() == 1 && jQuery('.adzone_cornercurl_animate').val() != '' ){
						shortcode += ' corner_animate="'+ jQuery('.adzone_cornercurl_animate').val() +'"';
					}
					// Fly In
					if( jQuery('#adzone_is_flyin').val() == 1 ){
						shortcode += ' flyin="'+ jQuery('#adzone_is_flyin').val() +'"';
					}
					if( jQuery('#adzone_flyin_position').val() != '' ){
						shortcode += ' flyin_position="'+ jQuery('#adzone_flyin_position').val() +'"';
					}
					if( jQuery('.adzone_flyin_delay').val() != '' ){
						shortcode += ' flyin_delay="'+ jQuery('.adzone_flyin_delay').val() +'"';
					}
					// Advanced Options
					if( jQuery('#adzone_fixed').val() == 1 ){
						shortcode += ' fixed="'+ jQuery('#adzone_fixed').val() +'"';
					}
					if( jQuery('#adzone_fixed_till').val() != '' ){
						shortcode += ' fixed_till="'+ jQuery('#adzone_fixed_till').val() +'"';
					}
					
					shortcode += ']';
					
					// inserts the shortcode into the active editor
					tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
					
					// closes Thickbox
					tb_remove();
				});
				
				
				
			//} // end success
			
		});
		
	});
	
})();



/*function wpm_html_entities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, "'"); //'&quot;'
}*/