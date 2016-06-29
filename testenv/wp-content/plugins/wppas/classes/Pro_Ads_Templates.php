<?php
class Pro_Ads_Templates {	

	public function __construct() 
	{
		
	}


	/*
	 * Create Adzone Code Popup screen
	 *
	 * NOTE: <p> tags in the HTML output are needed to show the content!!
	 *
	 * @access public
	 * @param int $id, int $i
	 * @return html
	*/
	public function pro_ad_adzone_popup_screen( $id )
	{
		global $pro_ads_adzones;

		/* ----------------------------------------------------------------
		 * Create AdZones Export Codes
		 * ---------------------------------------------------------------- */
		if( isset( $id ) )
		{
			$size = get_post_meta( $id, '_adzone_size', true );
			$custom = get_post_meta( $id, '_adzone_custom_size', true );
			$responsive = get_post_meta( $id, '_adzone_responsive', true );
			$adzone_is_popup = get_post_meta( $id, 'adzone_is_popup', true );
			
			$adzone_shortcode = $adzone_is_popup ? '[pro_ad_display_adzone id="'.$id.'" popup="1"]' : '[pro_ad_display_adzone id="'.$id.'"]';
			?>
			<div id="pro_ads_adzone_popup_<?php echo $id; ?>"> <!--  style="display:none;" -->
            	
				<p style="padding:0; margin:0;"></p>
                
                <div class="tuna_meta">
					<table class="form-table">
                    	<tbody>
                        
                        	<tr valign="top">
                            	<th scope="row">
									<?php _e('Post tag [shortcode]', 'wpproads'); ?>
                                    <span class="description"><?php _e('If you want to show this ad zone into a single post/page you can use this <em>Post Tag</em>. Just copy the shortcode into your post\'s textfield', 'wpproads'); ?></span>
                                </th>
                            	<td>
                                	<textarea id="sc_<?php echo $id; ?>" class="input" style="width:100%; height:50px; font-size:10px;"><?php echo $adzone_shortcode; ?></textarea>
                                    <!--<span class="description"><?php _e('<strong>Shortcode attributes:</strong> id = int (the adzone id), popup = boolean (open the adzone as a popup)', 'wpproads'); ?></span>-->
                                    <a class="wpproads_button open_sc_editor_<?php echo $id; ?>" form_id="<?php echo $id; ?>" ><?php _e('Open Shortcode Editor','wpproads'); ?></a>
                                </td>
                            </tr>
                            <tr id="sc_editor_<?php echo $id; ?>" style="display:none;">
                            	<td colspan="2">
                                	<?php 
									$this->pro_ads_shortcode_creator($id, $id); ?>
                                </td>
                            </tr>
                            
                            <tr valign="top">
                            	<th scope="row">
									<?php _e('Template tag', 'wpproads'); ?>
                                    <span class="description"><?php _e('If you want to use this ad zone on a fixed place inside your website, you can use this <em>Template tag</em>. Just copy the function into your website template, there where you want to show the banners.', 'wpproads'); ?></span>
                                </th>
                            	<td>
                                	<textarea id="tt_<?php echo $id; ?>" class="input" style="width:100%; height:50px; font-size:10px;"><?php echo htmlentities( '<?php echo do_shortcode("[pro_ad_display_adzone id='.$id.']"); ?>' ); ?></textarea>
                                    <span class="description"></span>
                                </td>
                            </tr>
                            
                            <tr valign="top">
                            	<th scope="row">
									<?php _e('Iframe tag', 'wpproads'); ?>
                                    <span class="description"><?php _e('The Iframe tag allows you to export adzones to other websites. This way you can manage adzones on 1 site but have them displayed on multiple websites at the same time.', 'wpproads'); ?></span>
                                </th>
                            	<td>
                                	<?php 
									if( !$responsive )
									{
										$sz = explode('x', $size);
									}
									else
									{
										$sz[0] = '100%';
										$sz[1] = 'auto';
									}
									?>
									<textarea class="input" style="width:100%; height:50px; font-size:10px;"><?php echo htmlentities( '<iframe id="wp_pro_ad_system_ad_zone" frameborder="0" src="'.get_bloginfo('url').'/?wpproadszoneid='.$id.'" width="'.$sz[0].'" height="'.$sz[1].'" scrolling="no"></iframe>' ); ?></textarea>
                                    <span class="description"></span>
                                </td>
                            </tr>
                            
                            <tr valign="top">
                            	<th scope="row">
									<?php _e('RSS feed', 'wpproads'); ?>
                                    <span class="description"><?php _e('The RSS feed of this adzone. Ready to use with MailChimp newsletters.', 'wpproads'); ?></span>
                                </th>
                            	<td>
                                	<input type="text" value="<?php echo get_bloginfo('url'); ?>/?wpproads-rss=<?php echo $id; ?>" />
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
                
			</div>
            <script type="text/javascript">
			jQuery(document).ready(function($){
				$('.open_sc_editor_<?php echo $id; ?>').on('click', function(){
					$('#sc_editor_'+ $(this).attr('form_id')).slideToggle();
				});
			});
			</script>
			<?php
		}
		else
		{
			echo _e('Woops! we cannot find the adzone your looking for!', 'wpproads');	
		}		
	}
	
	
	
	
	
	
	
	/*
	 * Create Adzone Order Popup screen
	 *
	 * NOTE: <p> tags in the HTML output are needed to show the content!!
	 *
	 * @access public
	 * @param int $id, int $i
	 * @return html
	*/
	public function pro_ad_adzone_order_popup_screen( $id )
	{
		global $pro_ads_adzones, $pro_ads_banners;
		
		if( isset( $id ) )
		{
			$linked_banner_ids = get_post_meta( $id, '_linked_banners', true );
			$banners = !empty($linked_banner_ids) ? $pro_ads_banners->get_banners( array('post__in' => $linked_banner_ids, 'orderby'=>'post__in') ) : '';
			?>
            <div id="pro_ads_adzone_order_popup_<?php echo $id; ?>" style="display:none;">
				<p>
                	<?php _e('Drag the banners to change the order of appearance.', 'wpproads'); ?>
                </p>
                
                <ul class="order_banners order_banners_<?php echo $id; ?>" id="adzone_order_sortable" aid="<?php echo $id; ?>">
                	<li class="loading"><?php _e('Updating', 'wpproads'); ?></li>
                    <?php
					if( !empty($banners) )
					{ 
						foreach( $banners as $i => $banner )
						{
							$preview = $pro_ads_banners->get_banner_preview( $banner->ID, '', 0, 'background', array('width' => 40, 'height' => 40) );
							$name = !empty( $banner->post_title ) ? $banner->post_title : $banner->post_name;
							?>
							<li id="order-item-<?php echo $i; ?>" bid="<?php echo $banner->ID; ?>">
								<div class="btn"><?php _e('Drag', 'wpproads'); ?></div>
								<div class="preview info_item"><?php echo $preview; ?></div>
								<div class="info_item">
									<div class="banner_name"><?php echo $name; ?></div>
									<div class="banner_info"><small>ID: <?php echo $banner->ID; ?></small></div>
								</div>
								<div class="clearFix"></div>
							</li>
							<?php
						}
					}
					else
					{
						echo '<li>'.__('No linked banners found.', 'wpproads').'</li>';
					}
                    ?>
                </ul>
                
            </div>
            <?php
		}
		else
		{
			echo _e('Woops! we cannot find the adzone your looking for!', 'wpproads');	
		}
		
	}
	
	
	
	
	/*
	 * Create Stats user info Popup screen
	 *
	 * NOTE: <p> tags in the HTML output are needed to show the content!!
	 *
	 * @access public
	 * @param int $id, int $i
	 * @return html
	*/
	public function stats_user_info_popup_screen( $item )
	{
		if( !empty( $item->id ) )
		{
			?>
            <div id="stats_user_info_popup_<?php echo $item->id; ?>" style="display:none;">
				<p></p>
                <div class="tuna_meta">
					<table class="form-table">
                    	<tbody>
                        
                        	<tr valign="top">
                            	<th scope="row">
									<?php _e('Browser','wpproads'); ?>:
                                    <span class="description"><?php _e('', 'wpproads'); ?></span>
                                </th>
                            	<td>
                                	<?php echo !empty($item->browser) ? $item->browser.' <img src="'.WP_ADS_URL.'images/browser/'.$item->browser.'.png" />' : __('n/a','wpproads'); ?>
                                    <span class="description"></span>
                                </td>
                            </tr>
                            <tr valign="top">
                            	<th scope="row">
									<?php _e('Platform','wpproads'); ?>:
                                    <span class="description"><?php _e('', 'wpproads'); ?></span>
                                </th>
                            	<td>
                                	<?php echo !empty($item->platform) ? $item->platform.' <img src="'.WP_ADS_URL.'images/platform/'.$item->platform.'.png" />' : __('n/a','wpproads'); ?>
                                    <span class="description"></span>
                                </td>
                            </tr>
                            
                            
                            <tr valign="top">
                            	<th scope="row">
									<?php _e('Country','wpproads'); ?>:
                                    <span class="description"><?php _e('', 'wpproads'); ?></span>
                                </th>
                            	<td>
                                	<?php echo !empty($item->country) ? $item->country.' ('.$item->country_cd.')' : __('n/a','wpproads'); ?>
                                    <span class="description"></span>
                                </td>
                            </tr>
                            <tr valign="top">
                            	<th scope="row">
									<?php _e('City','wpproads'); ?>:
                                    <span class="description"><?php _e('', 'wpproads'); ?></span>
                                </th>
                            	<td>
                                	<?php echo !empty($item->city) ? $item->city : __('n/a','wpproads'); ?>
                                    <span class="description"></span>
                                </td>
                            </tr>
                            <tr valign="top">
                            	<th scope="row">
									<?php _e('IP adress','wpproads'); ?>:
                                    <span class="description"><?php _e('', 'wpproads'); ?></span>
                                </th>
                            	<td>
                                	<?php echo !empty($item->ip_address) ? $item->ip_address : __('n/a','wpproads'); ?>
                                    <span class="description"></span>
                                </td>
                            </tr>
                            <tr valign="top">
                            	<th scope="row">
									<?php _e('Adzone','wpproads'); ?>:
                                    <span class="description"><?php _e('', 'wpproads'); ?></span>
                                </th>
                            	<td>
                                	<?php echo !empty($item->adzone_id) ? get_the_title($item->adzone_id).' ('.$item->adzone_id.')' : __('n/a','wpproads'); ?>
                                    <span class="description"></span>
                                </td>
                            </tr>
                            <tr valign="top">
                            	<th scope="row">
									<?php _e('Campaign','wpproads'); ?>:
                                    <span class="description"><?php _e('', 'wpproads'); ?></span>
                                </th>
                            	<td>
                                	<?php echo !empty($item->campaign_id) ? get_the_title($item->campaign_id).' ('.$item->campaign_id.')' : __('n/a','wpproads'); ?>
                                    <span class="description"></span>
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
                
            </div>
            <?php	
		}
		else
		{
			echo _e('Woops! we cannot find the adzone your looking for!', 'wpproads');	
		}
	}
	
	
	
	
	
	
	
	
	
	
	/*
	 * Tiny Mce Editor Wpproads shortcode editor.
	 *
	 * @access public
	 * @return html
	*/
	public function get_shortcode_editor_form() 
	{
		global $pro_ads_adzones;
		
		$adzones = $pro_ads_adzones->get_adzones();
		?>
        <link rel="stylesheet" id="proad-admin_style-css"  href="<?php echo WP_ADS_TPL_URL; ?>/css/admin.css" type="text/css" media="all" />
        <link rel="stylesheet" id="tuna_admin_style-css"  href="<?php echo WP_ADS_TPL_URL; ?>/css/tuna-admin.css" type="text/css" media="all" />
        
		<div class="wrap theme_settings" id="wpproads-shortcode-editor-form" style="display:none;">
        
            <div id="icon-themes" class="icon32 wpproads_shortcode_editor"><br /></div>
			<h2>WP Pro Advertising System - <?php _e('Shortcode Generator', 'wpproads'); ?></h2>
            <p>
            	<?php _e('Select the adzone you want to use. To add the shortcode click <em>Insert Shortdode</em>.', 'wpproads'); ?>
            </p>
                
            <div class="tuna_meta tuna_theme_options metabox-holder">
            
            
            	<!-- ADZONE -->
                <div class="adpostbox"><!-- closed -->
                    <div class="handlediv" title="<?php _e('Click to change', 'wpproads'); ?>">-</div>
                    <h3 class="hndle"><span><?php _e( "Adzones", 'wpproads'); ?></span></h3>
                    <div class="inside">
                    <table class="form-table">
                            <tbody>
                            	<tr valign="top">
                                    <th scope="row">
                                        <?php _e( "Select an adzone", 'wpproads'); ?>
                                        <span class="description"><?php _e('','wpproads'); ?></span>
                                    </th>
                                    <td>
                                        <select id="adzone_id">
                                        	<option value=""><?php _e( "Select an adzone", 'wpproads'); ?></option>
                                            <?php
											foreach( $adzones as $adzone )
											{
												?>
                                            	<option value="<?php echo $adzone->ID; ?>"><?php echo get_the_title($adzone->ID).' ('.$adzone->ID.')'; ?></option>
                                                <?php
											}
											?>
                                        </select>
                                        <span class="description"><?php _e('','wpproads'); ?></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!--<p><input type="submit" id="adzone_submit" value="<?php _e('Insert Shortcode', 'wpproads'); ?>" class="button-primary" /></p>-->
                    </div>
                </div>
                <!-- end .postbox - Buttons --> 
                
                <?php
				$this->adzone_default_options( $adzone->ID ); 
				$this->adzone_popup_options( $adzone->ID ); 
				$this->adzone_background_ads_options( $adzone->ID );
				$this->adzone_cornercurl_options( $adzone->ID );
				$this->adzone_flyin_options( $adzone->ID );
				$this->adzone_advanced_options( $adzone->ID );
				?> 
                
                <p><input type="submit" id="adzone_submit" value="<?php _e('Insert Shortcode', 'wpproads'); ?>" class="button-primary" /></p>
                
                
            </div>
        </div>
        
        <script type="text/javascript">
            jQuery(document).ready(function($){
                
                
                //postboxes.add_postbox_toggles('wpproads-shortcode-editor-form');
				
					jQuery('.adpostbox h3').click( function() {
						jQuery(jQuery(this).parent().get(0)).toggleClass('closed');
						if( jQuery(jQuery(this).parent().get(0)).hasClass('closed') ){
							jQuery(this).prev('.handlediv').html('+');
						}else{
							jQuery(this).prev('.handlediv').html('-');
						}
					});
				
				jQuery('.my-color-field').wpColorPicker();
            });
        </script>
    <?php
	}
	
	
	
	
	
	/*
	 * Shortcode creator for NON Tiny Mce use.
	 *
	 * @access public
	 * @return html
	*/
	public function pro_ads_shortcode_creator( $adzone_id = 0, $form_id = 0 ) 
	{
		global $pro_ads_adzones;
		
		// $form_id = !$adzone_id ? rand() : $adzone_id;
		$adzones = !$adzone_id ? $pro_ads_adzones->get_adzones() : '';
		?>
        <link rel="stylesheet" id="tuna_admin_style-css"  href="<?php echo WP_ADS_TPL_URL; ?>/css/admin.css" type="text/css" media="all" />
        <link rel="stylesheet" id="admin_standard_style-css"  href="<?php echo WP_ADS_TPL_URL; ?>/css/admin_standard.css" type="text/css" media="all" />
        
		<div class="wrap theme_settings" id="wpproads-shortcode-editor-form" style="border: solid 1px #C0C3C5; background:#EEE; padding:10px;">
        
            <div id="icon-themes" class="icon32 wpproads_shortcode_editor"><br /></div>
			<h2><?php _e('Adzone Shortcode Editor', 'wpproads'); ?></h2>
            <p>
            	<?php _e('', 'wpproads'); ?>
            </p>
                
            <div id="<?php echo $form_id; ?>" class="tuna_meta tuna_theme_options metabox-holder">
            
            
            	<!-- ADZONE -->
                <div class="adpostbox adpostbox_<?php echo $form_id; ?> <?php echo $adzone_id ? 'closed' : ''; ?>"><!-- closed -->
                    <div class="handlediv" title="<?php _e('Click to change', 'wpproads'); ?>"><?php echo $adzone_id ? '' : '-'; ?></div>
                    <?php echo $adzone_id ? '<h4 class="hndle"><span>'. sprintf(__( "Selected Adzone: %s", 'wpproads'), '<strong>'.get_the_title($adzone_id).'</strong> <em>(ID:'.$adzone_id.')</em>').'</span></h4>' : '<h3 class="hndle"><span>'. __( "Adzones", 'wpproads').'</span></h3>'; ?>
                    <div class="inside">
                    <table class="form-table">
                            <tbody>
                            	<tr valign="top">
                                    <th scope="row">
                                        <?php echo $adzone_id ? __( "Selected adzone", 'wpproads') : __( "Select an adzone", 'wpproads'); ?>
                                        <span class="description"><?php _e('','wpproads'); ?></span>
                                    </th>
                                    <td>
                                    	<?php
										if( !empty($adzones))
										{
											?>
                                            <select class="adzone_id">
                                                <option value=""><?php _e( "Select an adzone", 'wpproads'); ?></option>
                                                <?php
                                                    foreach( $adzones as $adzone )
                                                    {
                                                        ?>
                                                        <option value="<?php echo $adzone->ID; ?>"><?php echo get_the_title($adzone->ID).' ('.$adzone->ID.')'; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                            </select>
                                        	<?php
										}
										else
										{
											?>
                                            <input type="text" class="adzone_id" name="adzone_id" readonly="readonly" value="<?php echo $adzone_id; ?>">
                                            <?php	
										}
										?>
                                        <span class="description"><?php _e('','wpproads'); ?></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!--<p><input type="submit" id="adzone_submit" value="<?php _e('Insert Shortcode', 'wpproads'); ?>" class="button-primary" /></p>-->
                    </div>
                </div>
                <!-- end .postbox - Buttons --> 
                
                <?php 
				$this->adzone_default_options( $form_id );
				$this->adzone_popup_options( $form_id ); 
				$this->adzone_background_ads_options( $form_id );
				$this->adzone_cornercurl_options( $form_id );
				$this->adzone_flyin_options( $form_id );
				$this->adzone_advanced_options( $form_id );
				?> 
                
                <p>
                	<input type="submit" id="create_shortcode" value="<?php _e('Create Shortcode', 'wpproads'); ?>" class="button-primary" />
                    <input type="button" id="close_shortcode_editor" value="<?php _e('Close', 'wpproads'); ?>" class="button-secondary" />
                </p>
                
                
            </div>
        </div>
        
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('.my-color-field').wpColorPicker();
                
				$('#<?php echo $form_id; ?> #create_shortcode').on('click', function(){
					
					var is_popup = $('#<?php echo $form_id; ?> #adzone_is_popup').val();
					var shortcode = '[pro_ad_display_adzone';
					
					if( $('#adzone_id').val() != '' ){
						shortcode += ' id="' + $('#<?php echo $form_id; ?> .adzone_id').val() + '"';
					}
					// Default options
					if( $('#<?php echo $form_id; ?> #ajax_load').val() != 0 ){
						shortcode += ' ajax_load="'+ $('#<?php echo $form_id; ?> #ajax_load').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #hide_if_loggedin').val() != 0 ){
						shortcode += ' hide_if_loggedin="'+ $('#<?php echo $form_id; ?> #hide_if_loggedin').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_class').val() != '' ){
						shortcode += ' class="'+ $('#<?php echo $form_id; ?> #adzone_class').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_align').val() != '' ){
						shortcode += ' align="'+ $('#<?php echo $form_id; ?> #adzone_align').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_info_text').val() != '' ){
						shortcode += ' info_text="'+ $('#<?php echo $form_id; ?> #adzone_info_text').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_info_text_img').val() != '' ){
						shortcode += ' info_text_img="'+ $('#<?php echo $form_id; ?> #adzone_info_text_img').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_info_text_url').val() != '' ){
						shortcode += ' info_text_url="'+ $('#<?php echo $form_id; ?> #adzone_info_text_url').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_info_text').val() != '' ){
						shortcode += ' info_text_position="'+ $('#<?php echo $form_id; ?> #adzone_info_text_position').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_info_text').val() != '' && $('#<?php echo $form_id; ?> #adzone_info_text_color').val() != '' ){
						shortcode += ' font_color="'+ $('#<?php echo $form_id; ?> #adzone_info_text_color').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_info_text').val() != '' ){
						shortcode += ' font_size="'+ $('#<?php echo $form_id; ?> #adzone_info_text_font_size').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_info_text').val() != '' && $('#<?php echo $form_id; ?> #adzone_info_text_decoration').val() != '' ){
						shortcode += ' text_decoration="'+ $('#<?php echo $form_id; ?> #adzone_info_text_decoration').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_padding').val() != '' ){
						shortcode += ' padding="'+ $('#<?php echo $form_id; ?> #adzone_padding').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> .adzone_background_color').val() != '' ){
						shortcode += ' background_color="'+ $('#<?php echo $form_id; ?> .adzone_background_color').val() +'"';
					}
					/*if( $('#<?php echo $form_id; ?> .adzone_background_pattern').val() != '' ){
						shortcode += ' background_pattern="'+ $('#<?php echo $form_id; ?> .adzone_background_pattern').val() +'"';
					}*/
					if( $('#<?php echo $form_id; ?> #adzone_border_radius').val() != '' ){
						shortcode += ' border_radius="'+ $('#<?php echo $form_id; ?> #adzone_border_radius').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_border').val() != '' ){
						shortcode += ' border="'+ $('#<?php echo $form_id; ?> #adzone_border').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> .adzone_border_color').val() != '' ){
						shortcode += ' border_color="'+ $('#<?php echo $form_id; ?> .adzone_border_color').val() +'"';
					}
					// Popup
					if( $('#<?php echo $form_id; ?> #adzone_is_popup').val() == 1 ){
						shortcode += ' popup="1"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_is_exit_popup').val() == 1 ){
						shortcode += ' exit_popup="1"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_popup_cookie').val() == 1 ){
						shortcode += ' cookie="1"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_popup_close_btn').val() == 0 ){
						shortcode += ' popup_close_btn="0"';
					}
					if( $('#<?php echo $form_id; ?> .adzone_popup_delay').val() != '' ){
						shortcode += ' delay="'+ $('#<?php echo $form_id; ?> .adzone_popup_delay').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> .adzone_popup_auto_close_time').val() != '' ){
						shortcode += ' popup_auto_close_time="'+ $('#<?php echo $form_id; ?> .adzone_popup_auto_close_time').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_popup_nobg').val() == 1 ){
						shortcode += ' nobg="1"';
					}
					if( $('#<?php echo $form_id; ?> .adzone_popup_bg_color').val() != '' ){
						shortcode += ' popup_bg="'+ $('#<?php echo $form_id; ?> .adzone_popup_bg_color').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> .adzone_popup_opacity').val() != '' ){
						shortcode += ' popup_opacity="'+ $('#<?php echo $form_id; ?> .adzone_popup_opacity').val() +'"';
					}
					// Background
					if( $('#<?php echo $form_id; ?> #adzone_is_background').val() == 1 ){
						shortcode += ' background="1"';
					}
					if( $('#<?php echo $form_id; ?> .adzone_background_container').val() != '' ){
						shortcode += ' container="'+ $('#<?php echo $form_id; ?> .adzone_background_container').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_background_container_type').val() != '' ){
						shortcode += ' container_type="'+ $('#<?php echo $form_id; ?> #adzone_background_container_type').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> .adzone_background_repeat').val() != '' ){
						shortcode += ' repeat="'+ $('#<?php echo $form_id; ?> .adzone_background_repeat').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> .adzone_background_stretch').val() != '' ){
						shortcode += ' stretch="'+ $('#<?php echo $form_id; ?> .adzone_background_stretch').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> .adzone_background_bg_color').val() != '' ){
						shortcode += ' bg_color="'+ $('#<?php echo $form_id; ?> .adzone_background_bg_color').val() +'"';
					}
					// Corner peel
					if( $('#<?php echo $form_id; ?> #adzone_is_cornercurl').val() == 1 ){
						shortcode += ' corner_curl="'+ $('#<?php echo $form_id; ?> #adzone_is_cornercurl').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> .adzone_cornercurl_small').val() != '' ){
						shortcode += ' corner_small="'+ $('#<?php echo $form_id; ?> .adzone_cornercurl_small').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> .adzone_cornercurl_big').val() != '' ){
						shortcode += ' corner_big="'+ $('#<?php echo $form_id; ?> .adzone_cornercurl_big').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_is_cornercurl').val() == 1 && $('#<?php echo $form_id; ?> .adzone_cornercurl_animate').val() != '' ){
						shortcode += ' corner_animate="'+ $('#<?php echo $form_id; ?> .adzone_cornercurl_animate').val() +'"';
					}
					// Fly In
					if( $('#<?php echo $form_id; ?> #adzone_is_flyin').val() == 1 ){
						shortcode += ' flyin="'+ $('#<?php echo $form_id; ?> #adzone_is_flyin').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_flyin_position').val() != '' ){
						shortcode += ' flyin_position="'+ $('#<?php echo $form_id; ?> #adzone_flyin_position').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> .adzone_flyin_delay').val() != '' ){
						shortcode += ' flyin_delay="'+ $('#<?php echo $form_id; ?> .adzone_flyin_delay').val() +'"';
					}
					// Advanced Options
					if( $('#<?php echo $form_id; ?> #adzone_fixed').val() == 1 ){
						shortcode += ' fixed="'+ $('#<?php echo $form_id; ?> #adzone_fixed').val() +'"';
					}
					if( $('#<?php echo $form_id; ?> #adzone_fixed_till').val() != '' ){
						shortcode += ' fixed_till="'+ $('#<?php echo $form_id; ?> #adzone_fixed_till').val() +'"';
					}
					
					shortcode += ']';
					
					// Update shortcode textarea.
					$('#sc_<?php echo $form_id; ?>').val(shortcode);
					// Update template tag textarea.
					var tt = $('#tt_<?php echo $form_id; ?>').val(),
						sc = shortcode.replace(/"/g, "'");
					tt = tt.replace(/"([^"]+)"/g, '"'+sc+'"');
					$('#tt_<?php echo $form_id; ?>').val(tt);
					
					$('#sc_editor_<?php echo $form_id; ?>').hide();
				});
				
				$('#<?php echo $form_id; ?> #close_shortcode_editor').on('click', function(){
					$('#sc_editor_<?php echo $form_id; ?>').hide();
				});
                
				
				jQuery('.adpostbox_<?php echo $form_id; ?> h3').on('click', function() {
					
					jQuery(jQuery(this).parent().get(0)).toggleClass('closed');
					if( jQuery(jQuery(this).parent().get(0)).hasClass('closed') ){
						jQuery(this).prev('.handlediv').html('+');
					}else{
						//jQuery(this).prepend('<a class="togbox">-</a> ');
						jQuery(this).prev('.handlediv').html('-');
					}
				});
				
            });
        </script>
    <?php
	}
	
	
	
	
	
	
	public function adzone_default_options( $form_id )
	{
		?>
        <!-- ADZONE DEFAULT OPTIONS -->
        <div class="adpostbox adpostbox_<?php echo $form_id; ?> closed"><!--  -->
            <div class="handlediv" title="<?php _e('Click to change', 'wpproads'); ?>">+</div>
            <h3 class="hndle"><span><?php _e( "Default options", 'wpproads'); ?></span></h3>
            <div class="inside">
            <table class="form-table">
                    <tbody>
                    	<tr valign="top">
                            <th scope="row">
                                <?php _e( "Ajax Load", 'wpproads'); ?>
                                <span class="description"><?php _e('Load banners using ajax.','wpproads'); ?></span>
                            </th>
                            <td>
                            	<select id="ajax_load">
                                    <option value="0"><?php _e('No','wpproads'); ?></option>
                                    <option value="1"><?php _e('Yes','wpproads'); ?></option>
                                </select>
                                <span class="description"><?php _e('recommended if you are using a catching plugin','wpproads'); ?></span>
                            </td>
                        </tr>
                    	<tr valign="top">
                            <th scope="row">
                                <?php _e( "Hide if logged in", 'wpproads'); ?>
                                <span class="description"><?php _e('Hide this adzone for logged in members.','wpproads'); ?></span>
                            </th>
                            <td>
                            	<select id="hide_if_loggedin">
                                    <option value="0"><?php _e('No','wpproads'); ?></option>
                                    <option value="1"><?php _e('Yes','wpproads'); ?></option>
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "CSS Class", 'wpproads'); ?>
                                <span class="description"><?php _e('Add a class to the wrapping Adzone element.','wpproads'); ?></span>
                            </th>
                            <td>
                            	<input type="text" id="adzone_class" value="" />
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Adzone align", 'wpproads'); ?>
                                <span class="description"><?php _e('Selecting Left or Right will wrap content around your advertisement.','wpproads'); ?></span>
                            </th>
                            <td>
                                <select id="adzone_align" name="adzone_align">
                                    <option value=""><?php _e('Default','wpproads'); ?></option>
                                    <option value="left"><?php _e('Left','wpproads'); ?></option>
                                    <option value="right"><?php _e('Right','wpproads'); ?></option>
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="2" style="background:#EFEFEF;">
                            	<h3 style="margin:0; padding:0;"><?php _e('Adzone info text.','wpproads'); ?></h3>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Adzone Text", 'wpproads'); ?>
                                <span class="description"><?php _e('Show a small text above, below or on top of the adzone.','wpproads'); ?></span>
                            </th>
                            <td>
                            	<input type="text" id="adzone_info_text" value="" />
                                <span class="description"><?php _e('ex.: Advertisement','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Adzone Text Image", 'wpproads'); ?>
                                <span class="description"><?php _e('Show an image above, below or on top of the adzone.','wpproads'); ?></span>
                            </th>
                            <td>
                            	<input type="text" id="adzone_info_text_img" value="" placeholder="<?php _e('Image Url','wpproads'); ?>" />
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Adzone Text Link", 'wpproads'); ?>
                                <span class="description"><?php _e('Add a link to the adzone info text.','wpproads'); ?></span>
                            </th>
                            <td>
                            	<input type="text" id="adzone_info_text_url" value="" placeholder="http://" />
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Adzone Text Color", 'wpproads'); ?>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </th>
                            <td>
                                <input type="text" value="" class="my-color-field adzone_info_text_color" />
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Adzone Text Font Size", 'wpproads'); ?>
                                <span class="description"><?php _e('Select the font size for the adzone text.','wpproads'); ?></span>
                            </th>
                            <td>
                            	<select id="adzone_info_text_font_size" name="adzone_info_text_font_size">
                                    <?php
									for($i = 7; $i <= 20; $i++ )
									{
										?>
                                    	<option value="<?php echo $i; ?>" <?php echo $i == 11 ? 'selected="selected"' : ''; ?>><?php echo $i; ?>px</option>
                                        <?php
									}
									?>
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Adzone Text Decoration", 'wpproads'); ?>
                                <span class="description"><?php _e('Select the text decoration for the info text.','wpproads'); ?></span>
                            </th>
                            <td>
                                <select id="adzone_info_text_decoration" name="adzone_info_text_decoration">
                                    <option value=""><?php _e('None','wpproads'); ?></option>
                                    <option value="underline"><?php _e('Underline','wpproads'); ?></option>
                                    <option value="overline"><?php _e('Overline','wpproads'); ?></option>
                                    <option value="line-through"><?php _e('Line Through','wpproads'); ?></option>
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Adzone Text Position", 'wpproads'); ?>
                                <span class="description"><?php _e('Show the info text above, below or on top of the adzone.','wpproads'); ?></span>
                            </th>
                            <td>
                            	<select id="adzone_info_text_position" name="adzone_info_text_position">
                                	<option value=""><?php _e('Select Position','wpproads'); ?></option>
                                    <option value="above"><?php _e('Above adzone','wpproads'); ?></option>
                                    <option value="below"><?php _e('Below adzone','wpproads'); ?></option>
                                    <option value="top-right"><?php _e('On top - right ','wpproads'); ?></option>
                                    <option value="top-left"><?php _e('On top - left ','wpproads'); ?></option>
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="2" style="background:#EFEFEF;">
                            	<h3 style="margin:0; padding:0;"><?php _e('Adzone Style.','wpproads'); ?></h3>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Adzone padding", 'wpproads'); ?>
                                <span class="description"><?php _e('Select the padding for the adzone.','wpproads'); ?></span>
                            </th>
                            <td>
                            	<select id="adzone_padding" name="adzone_padding">
                                    <option value=""><?php _e('No Padding','wpproads'); ?></option>
                                    <?php
									for($i = 1; $i <= 20; $i++ )
									{
										?>
                                    	<option value="<?php echo $i; ?>"><?php echo $i; ?>px</option>
                                        <?php
									}
									?>
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Adzone Background Color", 'wpproads'); ?>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </th>
                            <td>
                                <input type="text" value="" class="my-color-field adzone_background_color" />
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <?php /*<tr valign="top">
                            <th scope="row">
                                <?php _e( "Adzone Background Pattern", 'wpproads'); ?>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </th>
                            <td>
                            	<input type="hidden" value="" class="adzone_background_pattern" />
                                <a class="pas_pattern_btn" style="border:solid 1px #EFEFEF;" pattern=""></a>
                            	<a class="pas_pattern_btn pas_pattern_1" pattern="pas_pattern_1"></a>
                                <a class="pas_pattern_btn pas_pattern_2" pattern="pas_pattern_2"></a>
                                <a class="pas_pattern_btn pas_pattern_4" pattern="pas_pattern_4"></a>
                                <a class="pas_pattern_btn pas_pattern_5" pattern="pas_pattern_5"></a>
                                <a class="pas_pattern_btn pas_pattern_6" pattern="pas_pattern_6"></a>
                                <a class="pas_pattern_btn pas_pattern_7" pattern="pas_pattern_7"></a>
                                <a class="pas_pattern_btn pas_pattern_8" pattern="pas_pattern_8"></a>
                                <a class="pas_pattern_btn pas_pattern_9" pattern="pas_pattern_9"></a>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>*/
						?>
                        
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Adzone border radius", 'wpproads'); ?>
                                <span class="description"><?php _e('Select a border radius if you want to add rounded corners to your adzone border.','wpproads'); ?></span>
                            </th>
                            <td>
                            	<select id="adzone_border_radius" name="adzone_border_radius">
                                    <option value=""><?php _e('No Border Radius','wpproads'); ?></option>
                                    <?php
									for($i = 1; $i <= 10; $i++ )
									{
										?>
                                    	<option value="<?php echo $i; ?>"><?php echo $i; ?>px</option>
                                        <?php
									}
									?>
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Adzone border", 'wpproads'); ?>
                                <span class="description"><?php _e('Select the border size for the adzone.','wpproads'); ?></span>
                            </th>
                            <td>
                            	<select id="adzone_border" name="adzone_border">
                                    <option value=""><?php _e('No Border','wpproads'); ?></option>
                                    <?php
									for($i = 1; $i <= 20; $i++ )
									{
										?>
                                    	<option value="<?php echo $i; ?>"><?php echo $i; ?>px</option>
                                        <?php
									}
									?>
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Adzone Border Color", 'wpproads'); ?>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </th>
                            <td>
                                <input type="text" value="" class="my-color-field adzone_border_color" />
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
                
            </div>
        </div>
        <!-- end .postbox - default options -->
        <?php	
	}
	
	
	
	
	
	
	
	public function adzone_advanced_options( $form_id )
	{
		?>
        <!-- ADZONE ADVANCED OPTIONS -->
        <div class="adpostbox adpostbox_<?php echo $form_id; ?> closed"><!--  -->
            <div class="handlediv" title="<?php _e('Click to change', 'wpproads'); ?>">+</div>
            <h3 class="hndle"><span><?php _e( "Advanced Options", 'wpproads'); ?></span></h3>
            <div class="inside">
            <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Fixed Adzone", 'wpproads'); ?>
                                <span class="description"><?php _e('Make the adzone fixed when it reaches the top of the page. <strong>Note:</strong> This will only work for 1 adzone per page.','wpproads'); ?></span>
                            </th>
                            <td>
                            	<select id="adzone_fixed" name="adzone_fixed">
                                    <option value="0"><?php _e('No','wpproads'); ?></option>
                                    <option value="1"><?php _e('Yes','wpproads'); ?></option>
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Fixe Adzone End", 'wpproads'); ?>
                                <span class="description"><?php _e('Add the ID or class from the div when the fixed adzone should stop.','wpproads'); ?></span>
                            </th>
                            <td>
                            	<input type="text" value="" id="adzone_fixed_till" name="adzone_fixed_till" placeholder="#footer" />
                                <span class="description"><?php _e('Make sure to add . or # before the class/ID name','wpproads'); ?></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
            </div>
        </div>
        <!-- end .postbox - advanced options -->
        <?php	
	}
	
	
	
	
	
	
	
	public function adzone_popup_options( $form_id )
	{
		?>
        <!-- ADZONE POPUP -->
        <div class="adpostbox adpostbox_<?php echo $form_id; ?> closed"><!--  -->
            <div class="handlediv" title="<?php _e('Click to change', 'wpproads'); ?>">+</div>
            <h3 class="hndle"><span><?php _e( "Popup options", 'wpproads'); ?></span></h3>
            <div class="inside">
            <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Open Adzone as a Popup", 'wpproads'); ?>
                                <span class="description"><?php _e('Do you want the adzone to open as a popup?','wpproads'); ?></span>
                            </th>
                            <td>
                                <select id="adzone_is_popup">
                                    <option value="0"><?php _e( "No", 'wpproads'); ?></option>
                                    <option value="1"><?php _e( "Yes", 'wpproads'); ?></option>   
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Exit Popup", 'wpproads'); ?>
                                <span class="description"><?php _e('Opens the popup when users exit the page.','wpproads'); ?></span>
                            </th>
                            <td>
                                <select id="adzone_is_exit_popup">
                                    <option value="0"><?php _e( "No", 'wpproads'); ?></option>
                                    <option value="1"><?php _e( "Yes", 'wpproads'); ?></option>   
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Popup Close Button", 'wpproads'); ?>
                                <span class="description"><?php _e('Do you want to show a close button for the popup?','wpproads'); ?></span>
                            </th>
                            <td>
                                <select id="adzone_popup_close_btn">
                                    <option value="1"><?php _e( "Yes", 'wpproads'); ?></option>
                                    <option value="0"><?php _e( "No", 'wpproads'); ?></option>   
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Popup Cookie", 'wpproads'); ?>
                                <span class="description"><?php _e('Do you want to limit the popup ad by cookie?','wpproads'); ?></span>
                            </th>
                            <td>
                                <select id="adzone_popup_cookie">
                                    <option value="0"><?php _e( "No", 'wpproads'); ?></option>
                                    <option value="1"><?php _e( "Yes", 'wpproads'); ?></option>   
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Popup Delay", 'wpproads'); ?>
                                <span class="description"><?php _e('Set the delay (in seconds) before the popup ad should appear.','wpproads'); ?></span>
                            </th>
                            <td>
                                <input type="text" class="adzone_popup_delay" value="" placeholder="2" /> <?php _e('sec.','wpproads'); ?>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Popup Auto Close Seconds", 'wpproads'); ?>
                                <span class="description"><?php _e('The amount of time (in seconds) to show the popup.','wpproads'); ?></span>
                            </th>
                            <td>
                                <input type="text" value="" placeholder="5" class="adzone_popup_auto_close_time" /> <?php _e('sec.', 'wpproads'); ?>
                                <span class="description"><?php _e('This will only work for popups without a closing button.','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Hide Content Background", 'wpproads'); ?>
                                <span class="description"><?php _e('Do you want to hide the background for the popup content?','wpproads'); ?></span>
                            </th>
                            <td>
                                <select id="adzone_popup_nobg">
                                    <option value="0"><?php _e( "No", 'wpproads'); ?></option>
                                    <option value="1"><?php _e( "Yes", 'wpproads'); ?></option>   
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Popup Background Color", 'wpproads'); ?>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </th>
                            <td>
                                <input type="text" value="" class="my-color-field adzone_popup_bg_color" />
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Popup Opacity", 'wpproads'); ?>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </th>
                            <td>
                                <select class="adzone_popup_opacity">
                                    <option value=""></option>
                                    <?php
                                    for($i = 0; $i < 10; $i++)
                                    {
                                        ?>
                                        <option value="<?php echo !$i ? '0' : '0.'.$i; ?>"><?php echo !$i ? '0' : '0.'.$i; ?></option> 
                                        <?php
                                    }
                                    ?>
                                    <option value="1">1</option> 
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
                
            </div>
        </div>
        <!-- end .postbox - popup -->
        <?php	
	}
	
	
	
	
	
	
	
	public function adzone_background_ads_options( $form_id )
	{
		?>
        <!-- ADZONE BACKGROUND ADS -->
        <div class="adpostbox adpostbox_<?php echo $form_id; ?> closed"><!--  -->
            <div class="handlediv" title="<?php _e('Click to change', 'wpproads'); ?>">+</div>
            <h3 class="hndle"><span><?php _e( "Background ad options", 'wpproads'); ?></span></h3>
            <div class="inside">
            <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Load Adzone as Background", 'wpproads'); ?>
                                <span class="description"><?php _e('Do you want the adzone to load as the page background?','wpproads'); ?></span>
                            </th>
                            <td>
                                <select id="adzone_is_background">
                                    <option value="0"><?php _e( "No", 'wpproads'); ?></option>
                                    <option value="1"><?php _e( "Yes", 'wpproads'); ?></option>   
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Background Container", 'wpproads'); ?>
                                <span class="description"><?php _e('Select the main website container. Default <strong>body</strong>','wpproads'); ?></span>
                            </th>
                            <td>
                                <input type="text" value="" placeholder="body" class="adzone_background_container" />
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Background Container Type", 'wpproads'); ?>
                                <span class="description"><?php _e('Select the type of the website container.','wpproads'); ?></span>
                            </th>
                            <td>
                                <select id="adzone_background_container_type">
                                    <option value=""><?php _e( "", 'wpproads'); ?></option>
                                    <option value="#"><?php _e( "ID", 'wpproads'); ?></option>   
                                    <option value="."><?php _e( "Class", 'wpproads'); ?></option>   
                                </select>
                                <span class="description"><?php _e('leave empty if you are using the default body option.','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Background Color", 'wpproads'); ?>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </th>
                            <td>
                                <input type="text" value="" class="my-color-field adzone_background_bg_color" />
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Repeat", 'wpproads'); ?>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </th>
                            <td>
                                <select class="adzone_background_repeat">
                                    <option value=""><?php _e( "No repeat", 'wpproads'); ?></option>
                                    <option value="1"><?php _e( "Repeat", 'wpproads'); ?></option>
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Stretch", 'wpproads'); ?>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </th>
                            <td>
                                <select class="adzone_background_stretch">
                                    <option value=""><?php _e( "No stretch", 'wpproads'); ?></option>
                                    <option value="1"><?php _e( "Stretch", 'wpproads'); ?></option>
                                </select>
                                <span class="description"><?php _e('Stretch the background image to the full width of the page.','wpproads'); ?></span>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
                
            </div>
        </div>
        <!-- end .postbox - background ads -->
        <?php	
	}
	
	
	
	
	
	
	
	public function adzone_cornercurl_options( $form_id )
	{
		?>
        <!-- ADZONE CORNER CURL -->
        <div class="adpostbox adpostbox_<?php echo $form_id; ?> closed"><!--  -->
            <div class="handlediv" title="<?php _e('Click to change', 'wpproads'); ?>">+</div>
            <h3 class="hndle"><span><?php _e( "Corner Peel options", 'wpproads'); ?></span></h3>
            <div class="inside">
            <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Open Adzone as a Corner Peel", 'wpproads'); ?>
                                <span class="description"><?php _e('Do you want the adzone to open as a corner curl?','wpproads'); ?></span>
                            </th>
                            <td>
                                <select id="adzone_is_cornercurl">
                                    <option value="0"><?php _e( "No", 'wpproads'); ?></option>
                                    <option value="1"><?php _e( "Yes", 'wpproads'); ?></option>   
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Corner size Closed", 'wpproads'); ?>
                                <span class="description"><?php _e('Set the size (in percentage % 1 - 100) for the corner when its closed.','wpproads'); ?></span>
                            </th>
                            <td>
                                <input type="text" class="adzone_cornercurl_small" value="" placeholder="26" />%
                                <span class="description"><?php _e('100% equals 300px','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Corner size Open", 'wpproads'); ?>
                                <span class="description"><?php _e('Set the size (in percentage % 1 - 100) for the corner when its open.','wpproads'); ?></span>
                            </th>
                            <td>
                                <input type="text" class="adzone_cornercurl_big" value="" placeholder="100" />%
                                <span class="description"><?php _e('100% equals 300px','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Corner Animation", 'wpproads'); ?>
                                <span class="description"><?php _e('Do you want the corner to be waving?','wpproads'); ?></span>
                            </th>
                            <td>
                                <select class="adzone_cornercurl_animate">
                                    <option value="1"><?php _e( "Yes", 'wpproads'); ?></option>
                                    <option value="0"><?php _e( "No", 'wpproads'); ?></option>
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
                
            </div>
        </div>
        <!-- end .postbox - cornercurl -->
        <?php	
	}
	
	
	
	
	
	
	public function adzone_flyin_options( $form_id )
	{
		?>
        <!-- FLY IN OPTIONS -->
        <div class="adpostbox adpostbox_<?php echo $form_id; ?> closed"><!--  -->
            <div class="handlediv" title="<?php _e('Click to change', 'wpproads'); ?>">+</div>
            <h3 class="hndle"><span><?php _e( "Fly In options", 'wpproads'); ?></span></h3>
            <div class="inside">
            <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Open Adzone as a Fly In", 'wpproads'); ?>
                                <span class="description"><?php _e('Do you want the adzone to open as a fly in advertisement?','wpproads'); ?></span>
                            </th>
                            <td>
                                <select id="adzone_is_flyin">
                                    <option value="0"><?php _e( "No", 'wpproads'); ?></option>
                                    <option value="1"><?php _e( "Yes", 'wpproads'); ?></option>   
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Fly In Position", 'wpproads'); ?>
                                <span class="description"><?php _e('Select the position of the fly in ad.','wpproads'); ?></span>
                            </th>
                            <td>
                                <select id="adzone_flyin_position">
                                	<option value=""><?php _e('Select Position','wpproads'); ?></option>
                                	<option value="bottom-left"><?php _e( "Bottom Left", 'wpproads'); ?></option>
                                    <option value="bottom-center"><?php _e( "Bottom Center", 'wpproads'); ?></option> 
                                    <option value="bottom-right"><?php _e( "Bottom Right", 'wpproads'); ?></option>   
                                    <option value="top-left"><?php _e( "Top Left", 'wpproads'); ?></option>
                                    <option value="top-center"><?php _e( "Top Center", 'wpproads'); ?></option> 
                                    <option value="top-right"><?php _e( "Top Right", 'wpproads'); ?></option>   
                                </select>
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Delay", 'wpproads'); ?>
                                <span class="description"><?php _e('Set the delay (in seconds) before the fly in ad should appear.','wpproads'); ?></span>
                            </th>
                            <td>
                                <input type="text" class="adzone_flyin_delay" value="" placeholder="3" /> sec.
                                <span class="description"><?php _e('','wpproads'); ?></span>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
                
            </div>
        </div>
        <!-- end .postbox - fly in -->
        <?php	
	}
	
	
	
	
	
	
	/*
	 * ADZONE POPUP SCREEN
	 *
	 * @access public
	 * @return html
	*/
	public function pro_ad_popup_screen( $arr = array() )
	{
		global $pro_ads_main, $pro_ads_adzones, $pro_ads_codex, $pro_ads_responsive;
		
		$exit_popup = $arr['exit_popup'];
		$popup_bg = !empty($arr['popup_bg']) ? ' background-color:'.$arr['popup_bg'].'; ' : '';
		$popup_opacity = !empty($arr['popup_opacity']) ? ' opacity:'.$arr['popup_opacity'].'; ' : '';
		$popup_close_btn = $arr['popup_close_btn'];
		$popup_auto_close_time = $arr['popup_auto_close_time']*1000;
		$popup_auto_close_time_attr = !$popup_close_btn ? ' closesec="'.$popup_auto_close_time.'"' : '';
		$popup_auto_close_class = !$popup_close_btn ? ' class="autoclose"' : '';
		$cookie = !empty($arr['atts']['cookie']) ? $arr['atts']['cookie'] : 0;
		$delay = !empty($arr['atts']['delay']) ? $arr['atts']['delay'] : 0;
		$nobg = !empty($arr['atts']['nobg']) ? ' nobg' : '';
		$hide_if_loggedin = !empty($arr['atts']['hide_if_loggedin']) ? 1 : 0;
		
		$html = '';
		$show = WPPAS_Adzone_Data::show_adzone(array('adzone_id' => $arr['adzone_id'], 'hide_if_loggedin' => $hide_if_loggedin));
		$show = !$arr['active_banners'] ? 0 : $show;
		
		if( $show )
		{
			$html.= '<div id="backgroundPasPopup" '.$popup_auto_close_class.$popup_auto_close_time_attr.' style="'.$popup_bg.$popup_opacity.'"></div>';
			$html.= '<div class="PasPopupCont" style="visibility: hidden; opacity:0;">';
				$html.= '<div class="paspopup_content'.$nobg.'">';
					$html.= $popup_close_btn ? '<div class="close_paspopup"><span>x</span></div>' : '';
					$html.= $arr['html'];
				$html.= '</div>';
			$html.= '</div>';
			
			$html.= empty($exit_popup) ? '<script>jQuery(document).ready(function($){loadPASPopup( 0, "success", "'.admin_url('admin-ajax.php').'", '.$cookie.', '.$delay.');});</script>' : '';
			
			if( $exit_popup )
			{
				$html.= '<script type="text/javascript">';
					$html.= 'jQuery(document).ready(function($){';
						$html.= '$(document).mouseleave(function(){';
							$html.= 'loadPASPopup( 0, "success", "'.admin_url('admin-ajax.php').'", '.$cookie.', '.$delay.');';
						$html.= '});';
					$html.= '});';
				$html.= '</script>';
			}
		}
        
		return $html;
	}
	
	
	
	
	
	/*
	 * ADZONE FLY IN
	 *
	 * @access public
	 * @return html
	*/
	public function pro_ad_fly_in( $arr = array() )
	{
		$delay = !empty($arr['delay']) ? $arr['delay'] : 3;
		$position = !empty($arr['position']) ? $arr['position'] : 'bottom-right';
		$cookie = !empty($arr['atts']['cookie']) ? $arr['atts']['cookie'] : 0;
		$hide_if_loggedin = !empty($arr['atts']['hide_if_loggedin']) ? 1 : 0;
		
		$html = '';
		$show = 1;
		
		$show = WPPAS_Adzone_Data::show_adzone(array('adzone_id' => $arr['adzone_id'], 'hide_if_loggedin' => $hide_if_loggedin, 'adzone_type' => 'flyin'));
		
		if( $show )
		{
			$html.= '<div class="pas_fly_in '.$position.'" style="visibility: hidden;">'; // display:none;
				$html.= '<div class="pasflyin_content">';
					$html.= '<div class="close_pasflyin"></div>';
					$html.= $arr['html'];
				$html.= '</div>';
			$html.= '</div>';
			$html.= '<script>jQuery(document).ready(function($){loadPASFlyIn( '.$arr['adzone_id'].', '.$delay.', "'.admin_url('admin-ajax.php').'", '.$cookie.');});</script>';
		}
		
		return $html;
	}
	
	
	
	
	
	
	
	/*
	 * ADZONE INFO TEXT
	 *
	 * @access public
	 * @return html
	*/
	public function adzone_info_text( $info_text, $position = array('above') )
	{
		$html = '';
		
		// Check if the info text has to be shown in this position.
		if( in_array($info_text['position'], $position) && !empty($info_text) )
		{
			// Check if text is image.
			$text = !empty($info_text['info_text_img']) ? '<img src="'.$info_text['info_text_img'].'" alt="'.$info_text['info_text'].'" />' : $info_text['info_text'];
			
			$html.= '<div class="pasinfotxt '.$info_text['position'].'">';
				$html.= !empty($info_text['info_text_url']) ? '<a href="'.$info_text['info_text_url'].'">' : '';
					$html.= '<small style="font-size:'.$info_text['font_size'].'px; color:'.$info_text['font_color'].'; text-decoration:'.$info_text['text_decoration'].';">'.$text.'</small>';
				$html.= !empty($info_text['info_text_url']) ? '</a>' : '';
			$html.= '</div>';
		}
		
		return $html;
	}
	
}
?>