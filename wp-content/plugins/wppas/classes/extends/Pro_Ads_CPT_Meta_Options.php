<?php
class Pro_Ads_CPT_Meta_Options {
	
	
	
	
	
	
	/*
	 * Adds a box to the main column on the Post and Page edit screens.
	 *
	 * @access public
	*/
	public function wp_pro_ads_advertisers_meta_options() 
	{
		$screens = array( 
			array('slug' => 'advertisers', 'name' => __('Advertiser','wpproads')), 
			array('slug' => 'campaigns', 'name' => __('Campaign','wpproads')),
			array('slug' => 'banners', 'name' => __('Banner','wpproads')),
			array('slug' => 'adzones', 'name' => __('Adzone','wpproads'))
		);
	
		foreach ( $screens as $screen ) 
		{	
			if( $screen['slug'] == 'adzones' )
			{
				add_meta_box( 'wp_pro_ads_adzones_meta_sizes_id', __( 'Adzone Size:', 'wpproads' ), array($this, 'wp_pro_ads_adzones_meta_sizes_custom_box'), $screen['slug'], 'normal', 'high' );
			}
			
			add_meta_box( 'wp_pro_ads_'.$screen['slug'].'_meta_options_id', sprintf(__( '%s Options:', 'wpproads' ), $screen['name']), array($this, 'wp_pro_ads_'.$screen['slug'].'_meta_options_custom_box'), $screen['slug'], 'normal', 'high' );
			
			if( $screen['slug'] == 'banners' )
			{
				add_meta_box( 'wp_pro_ads_banners_meta_upload_id', __( 'Banner type:', 'wpproads' ), array($this, 'wp_pro_ads_banners_meta_upload_custom_box'), $screen['slug'], 'normal', 'default' );
				add_meta_box( 'wp_pro_ads_banners_meta_link_adzones_id', __( 'Link banner to Adzone:', 'wpproads' ), array($this, 'wp_pro_ads_banners_meta_link_adzones_box'), $screen['slug'], 'normal', 'default' );	
				add_meta_box( 'wp_pro_ads_banners_meta_optional_settings_id', __( 'Optional Settings:', 'wpproads' ), array($this, 'wp_pro_ads_banners_meta_optional_settings_box'), $screen['slug'], 'normal', 'default' );	
				add_meta_box( 'wp_pro_ads_banners_meta_side_stats_id', __( 'Banner Stats:', 'wpproads' ), array($this, 'wp_pro_ads_banners_meta_side_stats_box'), $screen['slug'], 'side', 'default' );	
			}
		}
	}
	
	
	
	function wp_pro_ads_advertisers_meta_options_custom_box( $post ) 
	{
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wp_pro_ads_advertisers_meta_options_inner_custom_box', 'wp_pro_ads_advertisers_meta_options_inner_custom_box_nonce' );
	
		/*	
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		*/
		$advertiser_email       = get_post_meta( $post->ID, '_proad_advertiser_email', true );
		$wpuser_id              = get_post_meta( $post->ID, '_proad_advertiser_wpuser', true );
		?>
		<div class="tuna_meta">
			<table class="form-table">
				<tbody>
		  			<tr valign="top">
                        <th scope="row">
                            <?php _e( "Email", 'wpproads' ); ?>
                            <span class="description"><?php _e('If the email address matches an existing Wordpress user account this advertiser will be linked to the WP account.', 'wpproads'); ?></span>
                        </th>
                        <td>
                            <input type="text" name="proad_advertiser_email" value="<?php echo !empty( $advertiser_email ) ? $advertiser_email : ''; ?>" placeholder="<?php _e('Email', 'wpproads'); ?>" />
                            <span class="description"></span>
                        </td>
                    </tr>
                    
                    <?php
					if( !empty( $wpuser_id ))
					{
						$wpuser = get_user_by( 'id', $wpuser_id );
						?>
                        <tr valign="top">
                            <th scope="row">
                                <?php _e( "Wordpress user", 'wpproads' ); ?>
                                <span class="description"><?php _e('This email is linked to an existing Wordpress user.', 'wpproads'); ?></span>
                            </th>
                            <td>
                            	<table>
                                	<tbody>
                                    	<tr>
                                        	<td><?php _e('Username:','wpproads'); ?> </td>
                                            <td><?php echo $wpuser->user_login; ?></td>
                                        </tr>
                                        <tr>
                                        	<td><?php _e('Name:','wpproads'); ?> </td>
                                            <td><?php echo !empty($wpuser->first_name) && !empty($wpuser->last_name) ? $wpuser->first_name.' '.$wpuser->last_name : __('n/a', 'wpproads'); ?></td>
                                        </tr>
                                        <tr>
                                			<td><?php _e('ID:','wpproads'); ?></td>
                                            <td><?php echo $wpuser_id; ?></td>
                                        </tr>
                                        <tr>
                                			<td><?php _e('Registered:','wpproads'); ?></td>
                                            <td><?php echo $wpuser->user_registered; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <span class="description"></span>
                            </td>
                        </tr>
                        <?php
					}
					?>
                    
                </tbody>
            </table>
        </div>
        <?php
	}
	
	
	
	
	function wp_pro_ads_advertisers_meta_options_save_postdata( $post_id ) 
	{
	  /*
	   * We need to verify this came from the our screen and with proper authorization,
	   * because save_post can be triggered at other times.
	   */
	
	  // Check if our nonce is set.
	  if ( ! isset( $_POST['wp_pro_ads_advertisers_meta_options_inner_custom_box_nonce'] ) )
		return $post_id;
	
	  $nonce = $_POST['wp_pro_ads_advertisers_meta_options_inner_custom_box_nonce'];
	
	  // Verify that the nonce is valid.
	  if ( ! wp_verify_nonce( $nonce, 'wp_pro_ads_advertisers_meta_options_inner_custom_box' ) )
		  return $post_id;
	
	  // If this is an autosave, our form has not been submitted, so we don't want to do anything.
	  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		  return $post_id;
	
	  // Check the user's permissions.
	  if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;
	  } else {
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;
	  }
	  /* OK, its safe for us to save the data now. */
	  
	  // Check if email exists in our user database.
	  $wpuser = get_user_by( 'email', $_POST['proad_advertiser_email'] );
	  $wpuid = !empty($wpuser) ? $wpuser->ID : '';
	
	  // Sanitize user input.
	  $advertiser_email  = sanitize_text_field( $_POST['proad_advertiser_email'] );
	
	  // Update the meta field in the database.
	  update_post_meta( $post_id, '_proad_advertiser_email', $advertiser_email );
	  update_post_meta( $post_id, '_proad_advertiser_wpuser', $wpuid );
	
	}
	
	
	
	
	
	
	
	
	
	function wp_pro_ads_campaigns_meta_options_custom_box( $post ) 
	{
		global $pro_ads_advertisers;

		wp_nonce_field( 'wp_pro_ads_campaigns_meta_options_inner_custom_box', 'wp_pro_ads_campaigns_meta_options_inner_custom_box_nonce' );
	
		$start_date         = get_post_meta( $post->ID, '_campaign_start_date', true );
		$end_date           = get_post_meta( $post->ID, '_campaign_end_date', true );
		$timing_start       = get_post_meta( $post->ID, '_campaign_timing_start', true );
		$timing_end         = get_post_meta( $post->ID, '_campaign_timing_end', true );
		$local_start_date   = get_post_meta( $post->ID, '_campaign_local_start_date', true );
		$local_end_date     = get_post_meta( $post->ID, '_campaign_local_end_date', true );
		$advertiser_id      = get_post_meta( $post->ID, '_campaign_advertiser_id', true );
		$advertisers        = $pro_ads_advertisers->get_advertisers();
		
		if( empty($local_start_date) )
		{
			$local_start_date = !empty($start_date) ? date_i18n('m.d.Y', $start_date) : '';
		}
		if( empty($local_end_date))
		{
			$local_end_date = !empty($end_date) ? date_i18n('m.d.Y', $end_date) : '';
		}
		?>
		<div class="tuna_meta">
			<table class="form-table">
				<tbody>
		  			<tr valign="top">
                        <th scope="row">
                            <?php _e( "Campaign for:", 'wpproads' ); ?>
                            <span class="description"><?php _e('Select an advertiser for this campaign.', 'wpproads'); ?></span>
                        </th>
                        <td>
                        	<select name="campaign_advertiser_id" class="chosen-select" required="required">
                            	<option value=""><?php _e('Select an advertiser', 'wpproads'); ?></option>
                                <?php
								foreach( $advertisers as $advertiser )
								{
									$select = $advertiser_id == $advertiser->ID ? 'selected' : '';
                            		echo '<option value="'.$advertiser->ID.'" '.$select.'>'.$advertiser->post_title.'</option>';
								}
								?>
                          	</select>
                            <span class="description"></span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e( "Campaign period:", 'wpproads' ); ?>
                            <span class="description"><?php _e('Add a start and end date por this campaign.', 'wpproads'); ?></span>
                        </th>
                        <td>
                        	<input type="hidden" name="start_date" id="start_date" value="<?php echo !empty($start_date) ? date_i18n('m.d.Y', $start_date) : ''; ?>" />
                            <input type="hidden" name="end_date" id="end_date" value="<?php echo !empty($end_date) ? date_i18n('m.d.Y', $end_date) : ''; ?>" />
                        	<input id="local_start_date"  name="local_start_date" placeholder="<?php _e('Start date', 'wpproads' ); ?>" value="<?php echo !empty($local_start_date) ? $local_start_date : ''; ?>" class="input" style="width:150px;">
                            <input id="local_end_date" name="local_end_date" placeholder="<?php _e('End date', 'wpproads' ); ?>" value="<?php echo !empty($local_end_date) ? $local_end_date : ''; ?>" class="input" style="width:150px;">
                            <span class="description"><?php _e('Leave empty to keep campaign active.', 'wpproads'); ?></span>
                        </td>
                    </tr>
                    <?php 
					// http://jonthornton.github.io/jquery-timepicker/  date_i18n('g:ia', current_time('timestamp'))
					/*$now = current_time('timestamp');
					if(str_replace(':','',date_i18n('G:i', $now)) < str_replace(':','',$timing_start) || str_replace(':','',date_i18n('G:i', $now)) > str_replace(':','',$timing_end))
					{
						//echo 'on hold';	
					}*/
					?>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e( "Campaign timing:", 'wpproads' ); ?>
                            <span class="description"><?php _e('When should this campaign be running.', 'wpproads'); ?></span>
                        </th>
                        <td id="campaign_timing">
                        	<input name="campaign_timing_start" type="text" class="time start input" style="width:150px;" value="<?php echo !empty($timing_start) ? $timing_start : '';?>" placeholder="<?php _e('From','wpproads'); ?>" />
                            <input name="campaign_timing_end" type="text" class="time end input" style="width:150px;" value="<?php echo !empty($timing_end) ? $timing_end : ''; ?>" placeholder="<?php _e('Till','wpproads'); ?>" />
                            <span class="description"><?php _e('Leave empty to keep campaign running all the time.', 'wpproads'); ?></span>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
        <script>
			jQuery(function($) {
				$('#campaign_timing .time').timepicker({
					//'minTime': '2:00pm',
					//'maxTime': '11:30pm',
					'timeFormat': 'G:i',
					'showDuration': true
				});
				
				$('#campaign_timing').datepair();
			});
		</script>
        <?php
	}
	function wp_pro_ads_campaigns_meta_options_save_postdata( $post_id ) 
	{
		global $pro_ads_main;
		
		// Check if our nonce is set.
		if ( ! isset( $_POST['wp_pro_ads_campaigns_meta_options_inner_custom_box_nonce'] ) )
		return $post_id;
		$nonce = $_POST['wp_pro_ads_campaigns_meta_options_inner_custom_box_nonce'];
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'wp_pro_ads_campaigns_meta_options_inner_custom_box' ) )
		  return $post_id;
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		  return $post_id;
		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;
		} else {
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;
		}
		/* OK, its safe for us to save the data now. */
		
		// Create startdate
		if( !empty( $_POST['start_date'] ))
		{
			$dt = explode('.', $_POST['start_date']);
			$sdate = mktime(0,0,0,$dt[0],$dt[1],$dt[2]);
		}
		else
		{
			$sdate = $pro_ads_main->time_by_timezone();
		}
		// Create enddate
		if( !empty( $_POST['end_date'] ))
		{
			$dt = explode('.', $_POST['end_date']);
			$edate = mktime(0,0,0,$dt[0],$dt[1],$dt[2]);
		}
		else
		{
			$edate = '';
		}
		
		/* 
		 * Check/ update status
		 * 0 = draft, 1 = running, 2 = finished
		*/
		if( !empty($edate) && $pro_ads_main->time_by_timezone() > $edate )
		{
			$status = 2;
		}
		elseif( !empty($sdate) && $pro_ads_main->time_by_timezone() < $sdate )
		{
			$status = 0;
		}
		else
		{
			$status = 1;
		}
		
		// Sanitize user input.
		$advertiser_id  = sanitize_text_field( $_POST['campaign_advertiser_id'] );
		$start_date  = sanitize_text_field( $sdate );
		$end_date  = sanitize_text_field( $edate );
		$timing_start = sanitize_text_field( $_POST['campaign_timing_start'] );
		$timing_end = sanitize_text_field( $_POST['campaign_timing_end'] );
		$local_start_date  = sanitize_text_field( $_POST['local_start_date'] );
		$local_end_date  = sanitize_text_field( $_POST['local_end_date'] );
		// Update the meta field in the database.
		update_post_meta( $post_id, '_campaign_advertiser_id', $advertiser_id );
		update_post_meta( $post_id, '_campaign_start_date', $start_date );
		update_post_meta( $post_id, '_campaign_end_date', $end_date );
		update_post_meta( $post_id, '_campaign_timing_start', $timing_start );
		update_post_meta( $post_id, '_campaign_timing_end', $timing_end );
		update_post_meta( $post_id, '_campaign_local_start_date', $local_start_date );
		update_post_meta( $post_id, '_campaign_local_end_date', $local_end_date );
		update_post_meta( $post_id, '_campaign_status', $status );
	}
	
	
	
	
	
	// BANNER
	function wp_pro_ads_banners_meta_options_custom_box( $post ) 
	{
		global $pro_ads_advertisers, $pro_ads_campaigns;
		
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wp_pro_ads_banners_meta_options_inner_custom_box', 'wp_pro_ads_banners_meta_options_inner_custom_box_nonce' );
		
		$advertiser_id       = get_post_meta( $post->ID, '_banner_advertiser_id', true );
		$campaign_id         = get_post_meta( $post->ID, '_banner_campaign_id', true );
		$banner_url          = get_post_meta( $post->ID, '_banner_url', true );
		$banner_link         = get_post_meta( $post->ID, '_banner_link', true );
		$banner_target       = get_post_meta( $post->ID, '_banner_target', true );
		$banner_status       = get_post_meta( $post->ID, '_banner_status', true );
		$remove_link_masking = get_post_meta( $post->ID, '_banner_remove_link_masking', true );
		
		$advertisers        = $pro_ads_advertisers->get_advertisers();
		$campaigns          = $pro_ads_campaigns->get_campaigns( array('meta_key' => '_campaign_advertiser_id', 'meta_value' => $advertiser_id) );
		?>
		<div class="tuna_meta">
			<table class="form-table">
				<tbody>
                	<tr valign="top">
                        <th scope="row">
                            <?php _e( "Banner for:", 'wpproads' ); ?>
                            <span class="description"><?php _e('Select an advertiser for this banner.', 'wpproads'); ?></span>
                        </th>
                        <td class="select_advertiser_td">
                        	<select name="banner_advertiser_id" class="chosen-select select_banner_advertiser" required="required">
                            	<option value=""><?php _e('Select an advertiser', 'wpproads'); ?></option>
                                <?php
								foreach( $advertisers as $advertiser )
								{
									$select = $advertiser_id == $advertiser->ID ? 'selected' : '';
                            		echo '<option value="'.$advertiser->ID.'" '.$select.'>'.$advertiser->post_title.'</option>';
								}
								?>
                          	</select>
                            <span class="description select_advertiser_required" style="display:none;"><?php _e('No Advertiser Selected!','wpproads'); ?></span>
                        </td>
                    </tr>
                    <tr valign="top" class="<?php echo empty($campaign_id) ? 'hidden_row' : ''; ?> hide_row">
                        <th scope="row">
                            <?php _e( "Banner campaign:", 'wpproads' ); ?>
                            <span class="description"><?php _e('Select a campaign for this banner.', 'wpproads'); ?></span>
                        </th>
                        <td class="select_campaign_td">
                        	<!-- Campaign select gets loaded here by ajax -->
                        	<div id="select_cont">
                            	<select name="banner_campaign_id" class="chosen-select select_banner_campaign" required="required">
                                	<option value=""><?php _e('Select a campaign', 'wpproads'); ?></option>
                                    <?php
									foreach( $campaigns as $campaign )
									{
										$select = $campaign_id == $campaign->ID ? 'selected' : '';
										echo '<option value="'.$campaign->ID.'" '.$select.'>'.$campaign->post_title.'</option>';
									}
									?>
                                </select>
                            </div> 
                            <span class="description select_campaign_required" style="display:none;"><?php _e('No Campaign Selected!','wpproads'); ?></span>
                        </td>
                    </tr>
		  			<tr valign="top">
                        <th scope="row">
                            <?php _e( "Link", 'wpproads' ); ?>
                            <span class="description"><?php _e('', 'wpproads'); ?></span>
                        </th>
                        <td>
                            <input type="text" name="banner_link" value="<?php echo !empty( $banner_link ) ? $banner_link : ''; ?>" placeholder="<?php _e('http://www.yourlink.com', 'wpproads'); ?>" />
                            <span class="description"></span>
                        </td>
                    </tr>
                    <tr>
                    	<th scope="row">
                            <?php _e( "Target", 'wpproads' ); ?>
                            <span class="description"><?php _e('', 'wpproads'); ?></span>
                        </th>
                        <td>
                        	<select name="banner_target">
                            	<option value="_blank" <?php echo empty($banner_target) || $banner_target == '_blank' ? 'selected' : ''; ?>>
									<?php _e('_blank, Load in a new window.', 'wpproads'); ?>
                                </option>
                                <option value="_self" <?php echo $banner_target == '_self' ? 'selected' : ''; ?>>
									<?php _e('_self, Load in the same frame as it was clicked.', 'wpproads'); ?>
                                </option>
                                <option value="_parent" <?php echo $banner_target == '_parent' ? 'selected' : ''; ?> >
									<?php _e('_parent, Load in the parent frameset.', 'wpproads'); ?>
                                </option>
                                <option value="_top" <?php echo $banner_target == '_top' ? 'selected' : ''; ?>>
									<?php _e('_top, Load in the full body of the window.', 'wpproads'); ?>
                                </option>
                          	</select>
                            <span class="description"></span>
                        </td>
                    </tr>
                    <tr>
                    	<th scope="row">
                            <?php _e( "Link Masking", 'wpproads' ); ?>
                            <span class="description"><?php _e('Turn Off link masking and link directly to the original banner url, When turned off its nut possible to save statistics for this banner.', 'wpproads'); ?></span>
                        </th>
                        <td>
                        	<select name="banner_remove_link_masking">
                            	<option value="0" <?php echo empty($remove_link_masking) ? 'selected' : ''; ?>>
									<?php _e('Use link masking (recommended)', 'wpproads'); ?>
                                </option>
                                <option value="1" <?php echo $remove_link_masking ? 'selected' : ''; ?>>
									<?php _e('Turn Off link masking', 'wpproads'); ?>
                                </option>
                          	</select>
                            <span class="description"></span>
                        </td>
                    </tr>
                    <tr>
                    	<th scope="row">
                            <?php _e( "Status", 'wpproads' ); ?>
                            <span class="description"><?php _e('', 'wpproads'); ?></span>
                        </th>
                        <td>
                        	<select name="banner_status">
                            	<option value="0" <?php echo $banner_status == 0 ? 'selected' : ''; ?>><?php _e('Draft', 'wpproads'); ?></option>
                            	<option value="1" <?php echo $banner_status == 1 ? 'selected' : ''; ?>><?php _e('Active', 'wpproads'); ?></option>
                                <option value="2" <?php echo $banner_status == 2 ? 'selected' : ''; ?>><?php _e('Inactive', 'wpproads'); ?></option>
                                <option value="3" <?php echo $banner_status == 3 ? 'selected' : ''; ?>><?php _e('Awaiting review', 'wpproads'); ?></option>
                          	</select>
                            <span class="description"></span>
                        </td>
                    </tr>
              
					
                </tbody>
            </table>
        </div>
        <?php
	}
	function wp_pro_ads_banners_meta_options_save_postdata( $post_id ) 
	{
		global $pro_ads_main, $pro_ads_responsive;
		
		// Check if our nonce is set.
		if ( ! isset( $_POST['wp_pro_ads_banners_meta_options_inner_custom_box_nonce'] ) )
		return $post_id;
		$nonce = $_POST['wp_pro_ads_banners_meta_options_inner_custom_box_nonce'];
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'wp_pro_ads_banners_meta_options_inner_custom_box' ) )
		  return $post_id;
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		  return $post_id;
		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;
		} else {
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;
		}
		/* OK, its safe for us to save the data now. */
		
		$device_sizes = $pro_ads_responsive->device_sizes();
		
		$banner_start_date = get_post_meta( $post_id, '_banner_start_date', true );
		
		// Sanitize user input.
		$advertiser_id        = sanitize_text_field( $_POST['banner_advertiser_id'] );
		$campaign_id          = sanitize_text_field( $_POST['banner_campaign_id'] );
		
		foreach( $device_sizes as $i => $device_size )
        {
			$banner_url        = sanitize_text_field( $_POST['banner_url'.$device_size['prefix']]);
			$banner_width      = !empty($banner_url) ? sanitize_text_field( $_POST['banner_width'.$device_size['prefix']]) : '';
			$banner_height     = !empty($banner_url) ? sanitize_text_field( $_POST['banner_height'.$device_size['prefix']]) : '';
			$banner_html       = $_POST['banner_html'.$device_size['prefix']];
			$banner_is_html5   = !empty($_POST['banner_is_html5'.$device_size['prefix']]) ? $_POST['banner_is_html5'.$device_size['prefix']] ? 1 : 0 : 0;
			$banner_html5_w    = $_POST['banner_html5_w'.$device_size['prefix']];
			$banner_html5_h    = $_POST['banner_html5_h'.$device_size['prefix']];
			
			update_post_meta( $post_id, '_banner_url'.$device_size['prefix'], $banner_url );
			update_post_meta( $post_id, '_banner_width'.$device_size['prefix'], $banner_width );
			update_post_meta( $post_id, '_banner_height'.$device_size['prefix'], $banner_height );
			update_post_meta( $post_id, '_banner_html'.$device_size['prefix'], $banner_html );
			update_post_meta( $post_id, '_banner_is_html5'.$device_size['prefix'], $banner_is_html5 );
			update_post_meta( $post_id, '_banner_html5_w'.$device_size['prefix'], $banner_html5_w );
			update_post_meta( $post_id, '_banner_html5_h'.$device_size['prefix'], $banner_html5_h );
	
			$path_info = !empty( $banner_url ) ? pathinfo( $banner_url ) : '';
			${'banner_type'.$device_size['prefix']} = !empty( $banner_html ) ? 'html' : '';
			${'banner_type'.$device_size['prefix']} = !empty( $path_info['extension'] ) ? $path_info['extension'] : ${'banner_type'.$device_size['prefix']};
			update_post_meta( $post_id, '_banner_type'.$device_size['prefix'], ${'banner_type'.$device_size['prefix']} );
			
			//$banner_size = '';
			// Check if allow_url_fopen is enabled.
			/*if( ini_get('allow_url_fopen') )
			{
				$banner_size = !empty( $banner_url ) ? getimagesize($banner_url) : '';
			}
			$size = !empty($banner_size) ? $banner_size[0].'x'.$banner_size[1] : '';*/
			$size = !empty($banner_width) && !empty($banner_height) ? $banner_width.'x'.$banner_height : '';
			update_post_meta( $post_id, '_banner_size'.$device_size['prefix'], $size );
			
			$hide_for_device = !empty($_POST['banner_hide_for_device'.$device_size['prefix']]) ? $_POST['banner_hide_for_device'.$device_size['prefix']] : 0 ;
			update_post_meta( $post_id, '_banner_hide_for_device'.$device_size['prefix'], $hide_for_device );
		}
		$banner_link          = sanitize_text_field( $_POST['banner_link'] );
		$banner_target        = sanitize_text_field( $_POST['banner_target'] );
		$remove_link_masking  = sanitize_text_field( $_POST['banner_remove_link_masking'] );
		$banner_no_follow     = sanitize_text_field( $_POST['banner_no_follow'] );
		$banner_contract      = sanitize_text_field( $_POST['banner_contract'] );
		$banner_duration      = sanitize_text_field( $_POST['banner_duration'] );
		$fallback_image       = sanitize_text_field( $_POST['fallback_image'] );
		$transition_duration  = sanitize_text_field( $_POST['banner_transition_duration'] );
		
		$banner_duration   = !empty($banner_contract) ? $banner_duration : '';
		$banner_status     = !empty($banner_type) ? sanitize_text_field( $_POST['banner_status'] ) : 0;
		$banner_categories = !empty($_POST['banner_categories'] ) ? $_POST['banner_categories'] : '';
		
		// Update the meta field in the database.
		update_post_meta( $post_id, '_banner_advertiser_id', $advertiser_id );
		update_post_meta( $post_id, '_banner_campaign_id', $campaign_id );
		update_post_meta( $post_id, '_banner_link', $banner_link );
		update_post_meta( $post_id, '_banner_target', $banner_target );
		update_post_meta( $post_id, '_banner_remove_link_masking', $remove_link_masking );
		update_post_meta( $post_id, '_banner_status', $banner_status );
		update_post_meta( $post_id, '_banner_no_follow', $banner_no_follow );
		update_post_meta( $post_id, '_banner_contract', $banner_contract );
		update_post_meta( $post_id, '_banner_duration', $banner_duration );
		update_post_meta( $post_id, '_banner_fallback_image', $fallback_image );
		update_post_meta( $post_id, '_banner_transition_duration', $transition_duration);
		update_post_meta( $post_id, '_banner_categories', $banner_categories );
		
		if( empty( $banner_start_date ) && $banner_status == 1)
		{
			update_post_meta( $post_id, '_banner_start_date', $pro_ads_main->time_by_timezone() );
		}
	}
	
	// BANNER - upload
	function wp_pro_ads_banners_meta_upload_custom_box($post)
	{
		global $pro_ads_banners, $bc_banner_creator, $pro_ads_responsive;
		
		wp_nonce_field( 'wp_pro_ads_banners_meta_options_inner_custom_box', 'wp_pro_ads_banners_meta_options_inner_custom_box_nonce' );
		
		$device_sizes = $pro_ads_responsive->device_sizes();
	
		foreach( $device_sizes as $i => $device_size )
        {
			${'banner_url'.$device_size['prefix']}    = get_post_meta( $post->ID, '_banner_url'.$device_size['prefix'], true );
			${'banner_width'.$device_size['prefix']}    = get_post_meta( $post->ID, '_banner_width'.$device_size['prefix'], true );
			${'banner_height'.$device_size['prefix']}    = get_post_meta( $post->ID, '_banner_height'.$device_size['prefix'], true );
			${'banner_html'.$device_size['prefix']}   = get_post_meta( $post->ID, '_banner_html'.$device_size['prefix'], true );
			${'banner_is_html5'.$device_size['prefix']}   = get_post_meta( $post->ID, '_banner_is_html5'.$device_size['prefix'], true );
			${'banner_html5_w'.$device_size['prefix']}   = get_post_meta( $post->ID, '_banner_html5_w'.$device_size['prefix'], true );
			${'banner_html5_h'.$device_size['prefix']}   = get_post_meta( $post->ID, '_banner_html5_h'.$device_size['prefix'], true );
			
			$path_info = !empty( ${'banner_url'.$device_size['prefix']} ) ? pathinfo( ${'banner_url'.$device_size['prefix']} ) : '';
			${'banner_type'.$device_size['prefix']} = !empty( ${'banner_html'.$device_size['prefix']} ) ? 'html' : '';
			${'banner_type'.$device_size['prefix']} = !empty( $path_info['extension'] ) ? $path_info['extension'] : ${'banner_type'.$device_size['prefix']};
			${'banner_hide_for_device'.$device_size['prefix']} = get_post_meta( $post->ID, '_banner_hide_for_device'.$device_size['prefix'], true );
		}
		
		if( isset($_GET['hide_vbc_example']) && !empty($_GET['hide_vbc_example']) )
		{
			update_option( 'wpproads_hide_vbc_example', 1);
		}
		?>
        <div class="tuna_meta">
			<table class="form-table">
				<tbody>
                	<tr>
                    	<td>
                            <div id="tabs-container">
                                
                                <div class="pas_size_menu_icons">
                                	<?php
                                    foreach( $device_sizes as $i => $device_size )
                                    {
                                        ?>
                                		<a class="<?php echo !$i ? 'selected': ''; ?>" data-target="box_<?php echo $device_size['type']; ?>">
                                        	<img src="<?php echo WP_ADS_URL; ?>/images/devices/<?php echo $device_size['type']; ?>.png" />
                                        </a>
                                   		<?php
									}
									?>
                                </div>
                                <div class="tab">
                                	<?php
                                    foreach( $device_sizes as $i => $device_size )
                                    {
                                        ?>
                                        <div id="tab-<?php echo $i; ?>" class="tuna_meta tab-content pas_menu_box box_<?php echo $device_size['type']; ?>" <?php echo !$i ? 'style="display:block;"' : ''; ?>>
                                        	<h2 style="margin:0; border-bottom:solid 1px #E5E5E5;">
												<?php 
												
												echo !$i ? __('Main Banner','wpproads') : sprintf(__('Optional, <small style="font-weight:normal;">This banner will be visible when it is viewed from a %s</small>','wpproads'), $device_size['name']);
												?>
                                            </h2>
                                            <table class="form-table">
                                                <tbody>
                                                	<tr>
                                                        <th scope="row">
                                                            <?php _e( "Hide banner on this device", 'wpproads' ); ?>
                                                            <span class="description"><?php _e('Do you want to hide this banner if its viewed from the selected device?', 'wpproads'); ?></span>
                                                        </th>
                                                        <td>
                                                            <select name="banner_hide_for_device<?php echo $device_size['prefix']; ?>">
                                                                <option value="0" <?php echo empty(${'banner_hide_for_device'.$device_size['prefix']}) ? 'selected' : ''; ?>><?php _e('No', 'wpproads'); ?></option>
                                                                <option value="1" <?php echo ${'banner_hide_for_device'.$device_size['prefix']} ? 'selected' : ''; ?>><?php _e('Yes', 'wpproads'); ?></option>
                                                            </select>
                                                            <span class="description"></span>
                                                        </td>
                                                    </tr>
                                                	<tr>
                                                        <td colspan="2">
                                                        	<small><?php _e('Banner Preview','wpproads'); ?></small>
                                                            <div id="banner-img-preview<?php echo $device_size['prefix']; ?>" class="img_preview">
																<?php 
                                                                echo $pro_ads_banners->get_banner_preview( $post->ID, $device_size['prefix'], 1, 'url' );
                                                                ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row">
                                                            <?php _e( "Option 1", 'wpproads' ); ?>
                                                            <span class="description"><?php _e('Upload/Select a banner.', 'wpproads'); ?></span>
                                                        </th>
                                                        <td>
                                                            <div style="float:left; width:500px;">
                                                                <input type="text" size="40" id="banner_url<?php echo $device_size['prefix']; ?>" name="banner_url<?php echo $device_size['prefix']; ?>" value="<?php echo !empty( ${'banner_url'.$device_size['prefix']} ) ? ${'banner_url'.$device_size['prefix']} : ''; ?>" placeholder="<?php _e('Banner url', 'wpproads'); ?>" />
                                                                <input class="upload_image_button<?php echo $device_size['prefix']; ?> button" type="button" value="<?php _e('Upload Banner', 'wpproads'); ?>" />
                                                                <span class="description"></span>
                                                            </div>
                                                            <div style="clear:both;"></div>
                                                        </td>
                                                    </tr>
                                                    
                                                    <tr valign="top">
                                                        <th scope="row">
                                                            <?php _e( "Width", 'wpproads' ); ?>
                                                            <span class="description"><?php _e('Image width in pixels.', 'wpproads'); ?></span>
                                                        </th>
                                                        <td>
                                                            <div style="float:left; width:500px;">
                                                               <input type="text" id="banner_width<?php echo $device_size['prefix']; ?>" name="banner_width<?php echo $device_size['prefix']; ?>" value="<?php echo !empty( ${'banner_width'.$device_size['prefix']} ) ? ${'banner_width'.$device_size['prefix']} : ''; ?>" style="width:100px;" /><?php _e('px.','wpproads'); ?>
                                                                <span class="description"></span>
                                                            </div>
                                                            <div style="clear:both;"></div>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row">
                                                            <?php _e( "Height", 'wpproads' ); ?>
                                                            <span class="description"><?php _e('Image height in pixels.', 'wpproads'); ?></span>
                                                        </th>
                                                        <td>
                                                            <div style="float:left; width:500px;">
                                                               <input type="text" id="banner_height<?php echo $device_size['prefix']; ?>" name="banner_height<?php echo $device_size['prefix']; ?>" value="<?php echo !empty( ${'banner_height'.$device_size['prefix']} ) ? ${'banner_height'.$device_size['prefix']} : ''; ?>" style="width:100px;" /><?php _e('px.','wpproads'); ?>
                                                                <span class="description"></span>
                                                            </div>
                                                            <div style="clear:both;"></div>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row">
                                                            
                                                        </th>
                                                        <td>
                                                            <div style="height:50px;">
                                                               
                                                                <span class="description"></span>
                                                            </div>
                                                            <div style="clear:both;"></div>
                                                        </td>
                                                    </tr>
                                                    <tr valign="top">
                                                        <th scope="row">
                                                            <?php _e( "Option 2", 'wpproads' ); ?>
                                                            <span class="description"><?php _e('HTML Code (adSense, iframes, text ads, HTML5 ...)', 'wpproads'); ?></span><br />
                                                            <span class="description"><?php _e('<strong>Note:</strong> if you add HTML5 code make sure to check the HTML5 checkbox underneath.', 'wpproads'); ?></span>
                                                        </th>
                                                        <td>
                                                            <textarea name="banner_html<?php echo $device_size['prefix']; ?>" style="width:100%; height:200px;"><?php echo !empty( ${'banner_html'.$device_size['prefix']} ) ? ${'banner_html'.$device_size['prefix']} : ''; ?></textarea>
                                                            <br /><br />
                                                            <div>
                                                            	<input id="banner_is_html5_btn" name="banner_is_html5<?php echo $device_size['prefix']; ?>" type="checkbox" <?php echo ${'banner_is_html5'.$device_size['prefix']} ? 'checked="checked"' : ''; ?> /> <strong><?php _e('Banner is HTML5?', 'wpproads'); ?></strong>
                                                                
                                                                <div class="html5_banner_size_cont" <?php echo !${'banner_is_html5'.$device_size['prefix']} ? 'style="display:none;"' : ''; ?>>
                                                                	<input type="text" name="banner_html5_w<?php echo $device_size['prefix']; ?>" value="<?php echo ${'banner_html5_w'.$device_size['prefix']}; ?>" placeholder="<?php _e('width','wpproads'); ?>" style="width:60px;" /><?php _e('px','wpproads'); ?>
                                                                    <input type="text" name="banner_html5_h<?php echo $device_size['prefix']; ?>" value="<?php echo ${'banner_html5_h'.$device_size['prefix']}; ?>" placeholder="<?php _e('height','wpproads'); ?>" style="width:60px;" /><?php _e('px','wpproads'); ?>
                                                                </div>
                                                            </div>
                                                            
                                                            <span class="description"></span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php
									}
									?>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        
        <div class="tuna_meta">
			<table class="form-table">
				<tbody> 
                	<?php
					if( method_exists( $bc_banner_creator, 'show_banner_creator' ) )
					{
						?>
                        <tr>
                            <td colspan="2">
                                <h3 style="margin:0 0 10px 0; padding:0 0 10px 0; border-bottom: 1px solid #EEE;"><?php _e('Visual Banner Creator:','wpproads'); ?></h3>
                                <?php echo $bc_banner_creator->show_banner_creator(); ?>
                            </td>
                        </tr>
                        <?php
					}
					else
					{
						$hide_vbc_example = get_option( 'wpproads_hide_vbc_example', 0);
						if( !$hide_vbc_example )
						{
							?>
							<tr>
								<td colspan="2" style="position:relative;">
									<h3 style="margin:0 0 10px 0; padding:0 0 10px 0; border-bottom: 1px solid #EEE;">
										<?php _e('Visual Banner Creator:','wpproads'); ?>
										<small style="color:#D3D5D8;"><em>(example)</em></small>
									</h3>
									<div style=" position:absolute; right:10px; top:15px;">
                                    	<a id="hide_vbc_example" href="<?php echo $_SERVER['REQUEST_URI']; ?>&hide_vbc_example=1" style="cursor:pointer;">x hide</a>
                                    </div>
									<a href="http://bit.ly/VisualBannerCreator" target="_blank"><img src="<?php echo WP_ADS_URL; ?>images/vbc_ex.png" /></a>
								</td>
							</tr>
                            <?php
						}
					}
					?>
                    
                    
                </tbody>
            </table>
        </div>
        <?php
	}
	
	
	// BANNER - LInk to adzones
	function wp_pro_ads_banners_meta_link_adzones_box($post)
	{
		global $pro_ads_adzones;
		
		wp_nonce_field( 'wp_pro_ads_banners_meta_options_inner_custom_box', 'wp_pro_ads_banners_meta_options_inner_custom_box_nonce' );
		
		if( $post->ID )
		{
			$banner_status = get_post_meta( $post->ID, '_banner_status', true );
			if( !empty( $banner_status ))
			{
				?>
				<div class="tuna_meta">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<?php _e( "Link to Adzone", 'wpproads' ); ?>
									<span class="description"><?php _e('Link your banner to one or more adzones.', 'wpproads'); ?></span>
								</th>
								<td>
									<?php
									$banner_size = get_post_meta( $post->ID, '_banner_size', true );
									$recommended_adzones = $pro_ads_adzones->get_adzones( 
										array( 
											'meta_query'  => array(
												'relation' => 'OR',
												array(
													'key' => '_adzone_size',
													'value' => $banner_size,
													'compare' => '='
												),
												array(
													'key' => '_adzone_size',
													'value' => '',
													'compare' => '='
												),
											)
										)
									);
									
									// Get linked adzones for this banner
									$linked_adzones = get_post_meta( $post->ID, '_linked_adzones', true );
									
									$html = '';
									$html.= '<div style="position:relative;">';
										$html.= '<div class="loading_adzone loading_adzone_'.$post->ID.'" style="position:absolute; margin:7px; z-index:1; display:none;">'.__('Loading...', 'wpproads').'</div>';
										$html.= '<div class="select-adzone-cont-'.$post->ID.'">';
											$html.= '<select data-placeholder="'.__('No adzone selected.', 'wpproads').'" style="width:100%;" class="chosen-select select-adzone select-adzone-'.$post->ID.'" multiple>';
												$html.= '<option value=""></option>';
												$html.= '<optgroup label="'.__('Recommended', 'wpproads').'">';
													foreach( $recommended_adzones as $adzone )
													{
														$disabled = !$pro_ads_adzones->check_if_adzone_is_active( $adzone->ID, 1, $post->ID ) ? 'disabled="true"' : '';
														$selected = !empty($linked_adzones) ? in_array($adzone->ID, $linked_adzones) ? 'selected' : '' : '';
														$html.= '<option '.$disabled.'  value="'.$adzone->ID.'" bid="'.$post->ID.'" '.$selected.'>'.$adzone->post_title.'</option>';
													}
												$html.= '</optgroup>';
												
												// Get all other adzones (all not recommended adzones)
												$all_adzones = $pro_ads_adzones->get_adzones(
													array( 
														'meta_query'  => array(
															'relation' => 'AND',
															array(
																'key' => '_adzone_size',
																'value' => $banner_size,
																'compare' => '!='
															),
															array(
																'key' => '_adzone_size',
																'value' => '',
																'compare' => '!='
															),
														)
													)
												);
												
												$html.= '<optgroup label="'.__('All', 'wpproads').'">';
													foreach( $all_adzones as $adzone )
													{
														$disabled = !$pro_ads_adzones->check_if_adzone_is_active( $adzone->ID, 1, $post->ID ) ? 'disabled="true"' : '';
														$selected = !empty($linked_adzones) ? in_array($adzone->ID, $linked_adzones) ? 'selected' : '' : '';
														$html.= '<option '.$disabled.' value="'.$adzone->ID.'" bid="'.$post->ID.'" '.$selected.'>'.$adzone->post_title.'</option>';
													}
												$html.= '</optgroup>';
											$html.= '</select>';
										$html.= '</div>';
									$html.= '</div>';
									echo $html;
									?>
									<div style="clear:both;"></div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php
			}
			else
			{
				_e('You need to save the banner before you can add it to an adzone.','wpproads');	
			}
		}
	}
	
	
	// BANNER - Optional Settings
	function wp_pro_ads_banners_meta_optional_settings_box($post)
	{
		wp_nonce_field( 'wp_pro_ads_banners_meta_options_inner_custom_box', 'wp_pro_ads_banners_meta_options_inner_custom_box_nonce' );
		
		$banner_no_follow       = get_post_meta( $post->ID, '_banner_no_follow', true );
		$banner_contract        = get_post_meta( $post->ID, '_banner_contract', true );
		$banner_duration        = get_post_meta( $post->ID, '_banner_duration', true );
		$fallback_image         = get_post_meta( $post->ID, '_banner_fallback_image', true );
		$transition_duration    = get_post_meta( $post->ID, '_banner_transition_duration', true );
		$banner_categories      = get_post_meta( $post->ID, '_banner_categories', true );
		?>
		<div class="tuna_meta">
			<table class="form-table">
				<tbody>
		  			<tr valign="top">
						<th scope="row">
							<?php _e( "No Follow", 'wpproads' ); ?>
							<span class="description"><?php _e('Do you want to add rel nofollow to your link?', 'wpproads'); ?></span>
						</th>
						<td>
							<select name="banner_no_follow">
                            	<option value="0" <?php echo $banner_no_follow == 0 ? 'selected' : ''; ?>></option>
                            	<option value="1" <?php echo $banner_no_follow == 1 ? 'selected' : ''; ?>><?php _e('rel="nofollow"', 'wpproads'); ?></option>
                          	</select>
							<div style="clear:both;"></div>
						</td>
					</tr>
                    <tr valign="top">
						<th scope="row">
							<?php _e( "Transition duration", 'wpproads' ); ?>
							<span class="description"><?php _e('Select the time this banner should be visible if its rotating. <strong>Note:</strong> This option will only work if the adzone has the BX Slider rotation type selected.', 'wpproads'); ?></span>
						</th>
						<td>
							<input type="number" min="1" style="width:50px;" id="transition_duration" name="banner_transition_duration" value="<?php echo !empty( $transition_duration ) ? $transition_duration : ''; ?>" placeholder="5" /> <?php _e('Sec.','wpproads'); ?>
                            <span class="description"><?php _e('Time in seconds.', 'wpproads'); ?></span>
							<div style="clear:both;"></div>
						</td>
					</tr>
                    <tr valign="top">
						<th scope="row">
							<?php _e( "Flash Fallback image", 'wpproads' ); ?>
							<span class="description"><?php _e('Upload/Select a fallback image for flash banners on devices that not support Flash.', 'wpproads'); ?></span>
						</th>
						<td>
							<div style="float:left; width:500px;">
                            	<input type="text" size="40" id="fallback_image" name="fallback_image" value="<?php echo !empty( $fallback_image ) ? $fallback_image : ''; ?>" placeholder="<?php _e('fallback image url', 'wpproads'); ?>" />
								<input class="upload_fallback_image_button button" type="button" value="<?php _e('Upload Fallback Image', 'wpproads'); ?>" />
                                
                                <div><small><?php _e('Image Preview','wpproads'); ?></small></div>
                                <div id="fallback-img-preview"><?php echo !empty( $fallback_image ) ? '<img src="'.$fallback_image.'" />' : __('No image selected','wpproads'); ?></div>
								<span class="description"></span>
							</div>
							<div style="clear:both;"></div>
						</td>
					</tr>
                    <tr valign="top">
						<th scope="row">
							<?php _e( "Contract", 'wpproads' ); ?>
							<span class="description"><?php _e('Select the contract type and duration for this banner.', 'wpproads'); ?></span>
						</th>
						<td>
                            <select id="banner_contract" name="banner_contract">
                            	<option value="0" <?php echo $banner_contract == 0 ? 'selected' : ''; ?> txt=""></option>
                            	<option value="1" <?php echo $banner_contract == 1 ? 'selected' : ''; ?> txt="<?php _e('Amount of clicks', 'wpproads'); ?>"><?php _e('Pay per click', 'wpproads'); ?></option>
                            	<option value="2" <?php echo $banner_contract == 2 ? 'selected' : ''; ?> txt="<?php _e('Amount of views', 'wpproads'); ?>"><?php _e('Pay per view', 'wpproads'); ?></option>
                                <option value="3" <?php echo $banner_contract == 3 ? 'selected' : ''; ?> txt="<?php _e('Amount of days', 'wpproads'); ?>"><?php _e('Duration', 'wpproads'); ?></option>
                          	</select>
                            
                            <span class="description"><?php _e('Leave empty to keep this banner active.', 'wpproads'); ?></span>
						</td>
					</tr>
                    <tr id="banner_duration_tr" <?php echo !empty($banner_duration) && $banner_contract ? '' : 'style="display:none;"'; ?>>
                    	<th scope="row">
                            <span class="banner_contract_duration"><?php _e('Amount of clicks', 'wpproads'); ?></span>
                            <span class="description"><?php _e('', 'wpproads'); ?></span>
                        </th>
                        <td>
                        	<input type="text" name="banner_duration" value="<?php echo !empty($banner_duration) ? $banner_duration : ''; ?>" style="width:50px;">
                            <span class="description"></span>
                        </td>
                    </tr>
                    <tr valign="top">
						<th scope="row">
							<?php _e( "Categories", 'wpprogeo' ); ?>
							<span class="description"><?php _e('Select categories if you only want to show this banner for specific categories.', 'wpproads'); ?></span>
						</th>
						<td>
							<div style="float:left; width:500px;">
								<select name="banner_categories[]" data-placeholder="<?php _e('All Categories.', 'wpprogeo'); ?>" style="width:100%;" class="chosen-select" multiple>
									<option value=""></option>
									<?php
									$taxonomies = get_taxonomies();
									$allowed_taxonomies = apply_filters( 'wp_pro_ads_banner_limit_categories', array('category'));
									
									foreach($taxonomies as $i => $taxonomy)
									{
										$terms = get_terms($taxonomy);
										foreach($terms as $cat)
										{
											if(in_array($cat->taxonomy, $allowed_taxonomies))
											{
												$selected = !empty($banner_categories) && is_array($banner_categories) ? in_array($cat->term_id, $banner_categories) ? 'selected' : '' : '';
												echo '<option value="'.$cat->term_id.'" '.$selected.'>'.$cat->name.' - (ID:'.$cat->term_id.')</option>';
											}
										}
									}
									?>
								</select>
								
								<span class="description"></span>
							</div>
							<div style="clear:both;"></div>
						</td>
					</tr>
                    
                </tbody>
            </table>
        </div>
        <?php
	}
	
	
	
	
	
	// BANNER - sidebar
	function wp_pro_ads_banners_meta_side_stats_box($post)
	{
		$banner_clicks          = get_post_meta( $post->ID, '_banner_clicks', true );
		$banner_impressions     = get_post_meta( $post->ID, '_banner_impressions', true );
		$banner_start_date      = get_post_meta( $post->ID, '_banner_start_date', true );
		
		$ctr = !empty($banner_clicks) && !empty($banner_impressions) ? $banner_clicks / $banner_impressions * 100 : 0;
		$round_ctr = round($ctr,2).'%';
		?>
        <div class="stats_header_cont">
        	<p>
            	<?php echo sprintf(__('<strong>NOTE:</strong> These stats should not be used for publication. They include every single unfiltered action. Stats for publication can be found on the %s.', 'wpproads'), '<a href="admin.php?page=wp-pro-ads-stats&group=banner&group_id='.$post->ID.'">'.__('full banner statistics page','wpproads').'</a>'); ?>
            </p>
        	
            <div class="stats_header_box" style="width:27%;">
                <div style="font-size:11px;"><?php _e('Total Clicks','wpproads'); ?></div>
                <div style="font-size:16px; font-weight:bold; margin:7px 0;"><?php echo !empty($banner_clicks) ? $banner_clicks : 0; ?></div>
           	</div>
            <div class="stats_header_box" style="width:27%;">
                <div style="font-size:11px;"><?php _e('Total Views','wpproads'); ?></div>
                <div style="font-size:16px; font-weight:bold; margin:7px 0;"><?php echo !empty($banner_impressions) ? $banner_impressions : 0; ?></div>
           	</div>
            <div class="stats_header_box" style="width:27%;">
                <div style="font-size:11px;"><?php _e('CTR','wpproads'); ?></div>
                <div style="font-size:16px; font-weight:bold; margin:7px 0;"><?php echo $round_ctr; ?></div>
           	</div>
            <div class="clearFix"></div>
			
            <div style="margin:10px 0 0;">
            	<a class="button-secondary" href="admin.php?page=wp-pro-ads-stats&group=banner&group_id=<?php echo $post->ID; ?>"><?php _e('Full banner statistics','wpproads'); ?></a>
            </div>
        </div>
        <?php
	}
	
	
	
	
	
	
	function wp_pro_ads_adzones_meta_sizes_custom_box( $post )
	{
		global $pro_ads_responsive;
		
		wp_nonce_field( 'wp_pro_ads_adzones_meta_sizes_inner_custom_box', 'wp_pro_ads_adzones_meta_sizes_inner_custom_box_nonce' );
		
		$device_sizes = $pro_ads_responsive->device_sizes();
		
		foreach( $device_sizes as $i => $device_size )
        {
			${'size'.$device_size['prefix']}                   = get_post_meta( $post->ID, '_adzone_size'.$device_size['prefix'], true );
			${'custom'.$device_size['prefix']}                 = get_post_meta( $post->ID, '_adzone_custom_size'.$device_size['prefix'], true );
			${'responsive'.$device_size['prefix']}             = get_post_meta( $post->ID, '_adzone_responsive'.$device_size['prefix'], true );
			${'fix_size'.$device_size['prefix']}               = get_post_meta( $post->ID, '_adzone_fix_size'.$device_size['prefix'], true );
			${'adzone_hide_for_device'.$device_size['prefix']} = get_post_meta( $post->ID, '_adzone_hide_for_device'.$device_size['prefix'], true );
		}
		?>
        <div class="tuna_meta">
			<table class="form-table">
				<tbody>
                	<tr>
                    	<td>
                            <div id="tabs-container">
                              
                                <div class="pas_size_menu_icons">
                                	<?php
                                    foreach( $device_sizes as $i => $device_size )
                                    {
                                        ?>
                                		<a class="<?php echo !$i ? 'selected': ''; ?>" data-target="box_<?php echo $device_size['type']; ?>">
                                        	<img src="<?php echo WP_ADS_URL; ?>/images/devices/<?php echo $device_size['type']; ?>.png" />
                                        </a>
                                   		<?php
									}
									?>
                                </div>
                                <div class="tab">
                                    <?php
                                    foreach( $device_sizes as $i => $device_size )
                                    {
                                        ?>
                                        <div id="tab-<?php echo $i; ?>" class="tuna_meta tab-content pas_menu_box box_<?php echo $device_size['type']; ?>" <?php echo !$i ? 'style="display:block;"' : ''; ?>>
                                            <table class="form-table">
                                                <tbody>
                                                    <tr>
                                                        <th scope="row">
                                                            <?php echo sprintf(__( "Size - %s", 'wpproads' ), $device_size['name']); ?>
                                                            <span class="description"><?php echo $device_size['desc']; ?></span>
                                                        </th>
                                                        <td>
                                                            <select name="adzone_size<?php echo $device_size['prefix']; ?>" id="size_list<?php echo $device_size['prefix']; ?>">
                                                                <?php
                                                                if( $device_size['type'] != 'desktop' )
                                                                {
                                                                    ?>
                                                                    <option value="" <?php echo empty( ${'size'.$device_size['prefix']} ) ? 'selected="selected"' : ''; ?>></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                                <option value="468x60" <?php echo ${'size'.$device_size['prefix']} == '468x60' ? 'selected="selected"' : ''; ?>>
                                                                    IAB <?php _e('Full Banner', 'wpproads'); ?> (468 x 60)
                                                                </option>
                                                                <option value="120x600" <?php echo ${'size'.$device_size['prefix']} == '120x600' ? 'selected="selected"' : ''; ?>>
                                                                    IAB <?php _e('Skyscraper', 'wpproads'); ?> (120 x 600)
                                                                </option>
                                                                <option value="728x90" <?php echo ${'size'.$device_size['prefix']} == '728x90' ? 'selected="selected"' : ''; ?>>
                                                                    IAB <?php _e('Leaderboard', 'wpproads'); ?> (728 x 90)
                                                                </option>
                                                                <option value="300x250" <?php echo ${'size'.$device_size['prefix']} == '300x250' ? 'selected="selected"' : ''; ?>>
                                                                    IAB <?php _e('Medium Rectangle', 'wpproads'); ?> (300 x 250)
                                                                </option>
                                                                <option value="120x90" <?php echo ${'size'.$device_size['prefix']} == '120x90' ? 'selected="selected"' : ''; ?>>
                                                                    IAB <?php _e('Button 1', 'wpproads'); ?> (120 x 90)
                                                                </option>
                                                                <option value="160x600" <?php echo ${'size'.$device_size['prefix']} == '160x600' ? 'selected="selected"' : ''; ?>>
                                                                    IAB <?php _e('Wide Skyscraper', 'wpproads'); ?> (160 x 600)
                                                                </option>
                                                                <option value="120x60" <?php echo ${'size'.$device_size['prefix']} == '120x60' ? 'selected="selected"' : ''; ?>>
                                                                    IAB <?php _e('Button 2', 'wpproads'); ?> (120 x 60)
                                                                </option>
                                                                <option value="125x125" <?php echo ${'size'.$device_size['prefix']} == '125x125' ? 'selected="selected"' : ''; ?>>
                                                                    IAB <?php _e('Square Button', 'wpproads'); ?> (125 x 125)
                                                                </option>
                                                                <option value="180x150" <?php echo ${'size'.$device_size['prefix']} == '180x150' ? 'selected="selected"' : ''; ?>>
                                                                    IAB <?php _e('Rectangle', 'wpproads'); ?> (180 x 150)
                                                                </option>
                                                                <option value="custom" <?php echo !empty(${'custom'.$device_size['prefix']}) ? 'selected="selected"' : ''; ?>>
                                                                    <?php _e('Custom', 'wpproads'); ?>
                                                                </option>
                                                                <option value="responsive" <?php echo !empty(${'responsive'.$device_size['prefix']}) ? 'selected="selected"' : ''; ?>>
                                                                    <?php _e('Full Width', 'wpproads'); ?>
                                                                </option>
                                                            </select>
                                                            <span class="description"><?php echo $device_size['type'] == 'desktop' ? __('Required - Main adzone size','wpproads') : __('Optional - Responsive adzone sizes','wpproads'); ?></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">
                                                            <?php _e( "Hide adzone on this device", 'wpproads' ); ?>
                                                            <span class="description"><?php _e('Do you want to hide this adzone if its viewed from the selected device?', 'wpproads'); ?></span>
                                                        </th>
                                                        <td>
                                                            <select name="adzone_hide_for_device<?php echo $device_size['prefix']; ?>">
                                                                <option value="0" <?php echo empty(${'adzone_hide_for_device'.$device_size['prefix']}) ? 'selected' : ''; ?>><?php _e('No', 'wpproads'); ?></option>
                                                                <option value="1" <?php echo ${'adzone_hide_for_device'.$device_size['prefix']} ? 'selected' : ''; ?>><?php _e('Yes', 'wpproads'); ?></option>
                                                            </select>
                                                            <span class="description"></span>
                                                        </td>
                                                    </tr>
                                                    <tr id="custom_size<?php echo $device_size['prefix']; ?>" <?php echo !empty(${'custom'.$device_size['prefix']}) ? '' : 'style="display:none;"'; ?>>
                                                        <th scope="row">
                                                            <?php _e('Custom size', 'wpproads'); ?>
                                                            <span class="description"><?php _e('', 'wpproads'); ?></span>
                                                        </th>
                                                        <td>
                                                            
                                                            <?php
                                                            if( !empty(${'custom'.$device_size['prefix']}) )
                                                            {
                                                                $sz = explode('x', ${'size'.$device_size['prefix']});	
                                                            }
                                                            ?>
                                                            <div style="float:left; width:100px;">
                                                                <div><small><?php _e('Width', 'wpproads'); ?>:</small></div>
                                                                <div><input type="text" name="custom_w<?php echo $device_size['prefix']; ?>" value="<?php echo !empty($sz[0]) ? $sz[0] : ''; ?>" style="width:50px;"><small>Px.</small></div>
                                                            </div>
                                                            
                                                            <div style="float:left;">
                                                                <div><small><?php _e('Height', 'wpproads'); ?>:</small></div>
                                                                <div><input type="text" name="custom_h<?php echo $device_size['prefix']; ?>" value="<?php echo !empty($sz[1]) ? $sz[1] : ''; ?>" style="width:50px;"><small>Px.</small></div>
                                                            </div>
                                                            <div class="clearFix"></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    	<th scope="row">
															<?php _e('Size Options', 'wpproads'); ?>
                                                            <span class="description"><?php _e('<strong>FIXED:</strong> Adzone will keep its size no matter if the banners are larger or smaller.<br><br> <strong>VARIABLE:</strong> Adzone adapts itself to the height of the banner but will never exceed the selected adzone height.', 'wpproads'); ?></span>
                                                        </th>
                                                    	<td><div class="switch_btn"><input type="checkbox" name="adzone_fix_size<?php echo $device_size['prefix']; ?>" value="<?php echo ${'fix_size'.$device_size['prefix']}; ?>" <?php echo ${'fix_size'.$device_size['prefix']} ? 'checked' : ''; ?>></div></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    
                                    <div class="clearFix"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
	}
	
	
	
	
	
	function wp_pro_ads_adzones_meta_options_custom_box( $post ) 
	{
		global $pro_ads_main;
		
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wp_pro_ads_adzones_meta_options_inner_custom_box', 'wp_pro_ads_adzones_meta_options_inner_custom_box_nonce' );
	
		$description             = get_post_meta( $post->ID, '_adzone_description', true );
		$adzone_rotation         = get_post_meta( $post->ID, '_adzone_rotation', true );
		$rotation_type           = get_post_meta( $post->ID, '_adzone_rotation_type', true );
		$rotation_time           = get_post_meta( $post->ID, '_adzone_rotation_time', true );
		$rotation_effect         = get_post_meta( $post->ID, '_adzone_rotation_effect', true );
		$adzone_rotation_order   = get_post_meta( $post->ID, '_adzone_rotation_order', true );
		$horizontal              = get_post_meta( $post->ID, '_adzone_grid_horizontal', true );
		$vertical                = get_post_meta( $post->ID, '_adzone_grid_vertical', true );
		$max_banners             = get_post_meta( $post->ID, '_adzone_max_banners', true );
		$adzone_center           = get_post_meta( $post->ID, '_adzone_center', true );
		$adzone_hide_empty       = get_post_meta( $post->ID, '_adzone_hide_empty', true );
		$adzone_rotation_ajax    = get_post_meta( $post->ID, '_adzone_rotation_ajax', true );
		$adzone_default_url      = get_post_meta( $post->ID, '_adzone_default_url', true );
		$adzone_default_url_link = get_post_meta( $post->ID, '_adzone_default_url_link', true );
		$adzone_no_buyandsell    = get_post_meta( $post->ID, '_adzone_no_buyandsell', true );
		?>
		<div class="tuna_meta">
			<table class="form-table">
				<tbody>
		  			<tr valign="top">
						<th scope="row">
							<?php _e( "Description", 'wpproads' ); ?>
							<span class="description"><?php _e('', 'wpproads'); ?></span>
						</th>
						<td>
                            <textarea name="adzone_description" style="width:100%; height:100px;"><?php echo !empty( $description ) ? $description : ''; ?></textarea>
                            
                            <span class="description"></span>
						</td>
					</tr>
                    
                    
                    <tr>
                    	<th scope="row">
                            <?php _e( "Max. amount of banners", 'wpproads' ); ?>
                            <span class="description"><?php _e('How many banners are allowed in this adzone?', 'wpproads'); ?></span>
                        </th>
                        <td>
                        	<input type="text" name="adzone_max_banners" value="<?php echo !empty($max_banners) ? $max_banners : ''; ?>" style="width:50px;">
                            <span class="description"><?php _e('Leave empty to allow unlimited banners.', 'wpproads'); ?></span>
                        </td>
                    </tr>
                    <tr>
                    	<th scope="row">
                            <?php _e( "Rotate Banners", 'wpproads' ); ?>
                            <span class="description"><?php _e('', 'wpproads'); ?></span>
                        </th>
                        <td>
                        	<select id="adzone_rotation_btn" name="adzone_rotation">
                            	<option value="0" <?php echo $adzone_rotation == 0 ? 'selected' : ''; ?>><?php _e('No', 'wpproads'); ?></option>
                            	<option value="1" <?php echo $adzone_rotation == 1 ? 'selected' : ''; ?>><?php _e('Yes', 'wpproads'); ?></option>
                          	</select>
                            <span class="description"></span>
                        </td>
                    </tr>
                    <tr id="adzone_rotation_options" <?php echo $adzone_rotation ? '' : 'style="display:none;"'; ?>>
                    	<td colspan="2">
                        
                        	<table class="form-table">
								<tbody>
                                    <tr>
                                        <th scope="row">
                                            <?php _e( "Rotation Type", 'wpproads' ); ?>
                                            <span class="description"><?php _e('You have 2 options to rotate banners. Select the one that fits your needs.', 'wpproads'); ?></span>
                                        </th>
                                        <td>
                                            <select name="adzone_rotation_type">
                                                <option value="bxslider" <?php echo empty($rotation_type) || $rotation_type == 'flexslider' || $rotation_type == 'bxslider' ? 'selected' : ''; ?>><?php _e('BX Slider (recommended)', 'wpproads'); ?></option>
                                                <option value="showoff" <?php echo $rotation_type == 'showoff' ? 'selected' : ''; ?>><?php _e('Showoff', 'wpproads'); ?></option>
                                                
                                            </select>
                                            <span class="description"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <?php _e( "Rotation Time", 'wpproads' ); ?>
                                            <span class="description"><?php _e('', 'wpproads'); ?></span>
                                        </th>
                                        <td>
                                            <input type="text" name="adzone_rotation_time" value="<?php echo !empty($rotation_time) ? $rotation_time : ''; ?>" style="width:50px;">
                                            <small><?php _e('Sec.', 'wpproads'); ?></small>
                                            <span class="description"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <?php _e( "Rotation Effect", 'wpproads' ); ?>
                                            <span class="description"><?php _e('', 'wpproads'); ?></span>
                                        </th>
                                        <td>
                                            <select name="adzone_rotation_effect">
                                                <option value="fade" <?php echo $rotation_effect == 'fade' ? 'selected' : ''; ?>><?php _e('Fade', 'wpproads'); ?></option>
                                                <option value="slide" <?php echo $rotation_effect == 'slide' || $rotation_effect == 'slideLeft' || $rotation_effect == 'horizontal' ? 'selected' : ''; ?>><?php _e('Slide', 'wpproads'); ?></option>
                                                <option value="vertical" <?php echo $rotation_effect == 'vertical' ? 'selected' : ''; ?>><?php _e('Vertical', 'wpproads'); ?></option>
                                            </select>
                                            <span class="description"><?php _e( "Vertical slides are only available with BX Slider.", 'wpproads' ); ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <?php _e( "Banner Loading Order", 'wpproads' ); ?>
                                            <span class="description"><?php _e('Select the order to load banners inside the adzone.', 'wpproads'); ?></span>
                                        </th>
                                        <td>
                                            <select name="adzone_rotation_order">
                                                <option value="fixed" <?php echo $adzone_rotation_order == 'fixed' ? 'selected' : ''; ?>><?php _e('Fixed Order', 'wpproads'); ?></option>
                                                <option value="equal" <?php echo $adzone_rotation_order == 'equal' ? 'selected' : ''; ?>><?php _e('Equal Views', 'wpproads'); ?></option>
                                                <option value="random" <?php echo $adzone_rotation_order == 'random' ? 'selected' : ''; ?>><?php _e('Random Order', 'wpproads'); ?></option>
                                            </select>
                                            <span class="description"><?php _e( "", 'wpproads' ); ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <?php _e( "AJAX Rotation", 'wpproads' ); ?>
                                            <span class="description"><?php _e('Do you want to load the banners in real time using ajax?', 'wpproads'); ?></span>
                                        </th>
                                        <td>
                                            <select name="adzone_rotation_ajax">
                                                <option value="0" <?php echo empty($adzone_rotation_ajax) ? 'selected' : ''; ?>><?php _e('No', 'wpproads'); ?></option>
                                                <option value="1" <?php echo $adzone_rotation_ajax ? 'selected' : ''; ?>><?php _e('Yes', 'wpproads'); ?></option>
                                            </select>
                                            <span class="description"><?php _e( "", 'wpproads' ); ?></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                    	</td>
                    </tr>
                    <tr>
                    	<th scope="row">
                            <?php _e('AD Grid', 'wpproads'); ?>
                            <span class="description"><?php _e('Show multiple ads at once. <br><strong>Note:</strong> This option has no rotation effect. Banners will load in random order on each page refresh.', 'wpproads'); ?></span>
                        </th>
                        <td>
                            <div style="float:left; width:100px;">
                                <div><small><?php _e('Horizontal', 'wpproads'); ?>:</small></div>
                                <div><input type="text" name="adzone_grid_horizontal" value="<?php echo !empty($horizontal) ? $horizontal : ''; ?>" style="width:50px;"></div>
                            </div>
                        	
                            <div style="float:left;">
                                <div><small><?php _e('Vertical', 'wpproads'); ?>:</small></div>
                                <div><input type="text" name="adzone_grid_vertical" value="<?php echo !empty($vertical) ? $vertical : ''; ?>" style="width:50px;"></div>
                            </div>
                            <div class="clearFix"></div>
                            <span class="description"><?php _e('Leave empty to show one banner at the time.', 'wpproads'); ?></span>
                        </td>
                    </tr>
                    <tr>
                    	<th scope="row">
                            <?php _e( "Center Adzone", 'wpproads' ); ?>
                            <span class="description"><?php _e('Do you want this adzone to be centered?', 'wpproads'); ?></span>
                        </th>
                        <td>
                        	<select name="adzone_center">
                            	<option value="0" <?php echo empty($adzone_center) ? 'selected' : ''; ?>><?php _e('No', 'wpproads'); ?></option>
                            	<option value="1" <?php echo $adzone_center ? 'selected' : ''; ?>><?php _e('Yes', 'wpproads'); ?></option>
                          	</select>
                            <span class="description"></span>
                        </td>
                    </tr>
                    <tr>
                    	<th scope="row">
                            <?php _e( "Hide adzone if empty", 'wpproads' ); ?>
                            <span class="description"><?php _e('Do you want to hide this adzone if its empty?', 'wpproads'); ?></span>
                        </th>
                        <td>
                        	<select name="adzone_hide_empty">
                            	<option value="0" <?php echo empty($adzone_hide_empty) ? 'selected' : ''; ?>><?php _e('No', 'wpproads'); ?></option>
                            	<option value="1" <?php echo $adzone_hide_empty ? 'selected' : ''; ?>><?php _e('Yes', 'wpproads'); ?></option>
                          	</select>
                            <span class="description"></span>
                        </td>
                    </tr>
                    <?php
					if( $pro_ads_main->buyandsell_is_active() || $pro_ads_main->buyandsell_woo_is_active() )
					{
						?>
                        <tr>
                            <th scope="row">
                                <?php _e( "Remove Buy and Sell Option", 'wpproads' ); ?>
                                <span class="description"><?php _e('Do you want to disable Buy and Sell options for this adzone?', 'wpproads'); ?></span>
                            </th>
                            <td>
                                <select name="adzone_no_buyandsell">
                                    <option value="0" <?php echo empty($adzone_no_buyandsell) ? 'selected' : ''; ?>><?php _e('No', 'wpproads'); ?></option>
                                    <option value="1" <?php echo $adzone_no_buyandsell ? 'selected' : ''; ?>><?php _e('Yes', 'wpproads'); ?></option>
                                </select>
                                <span class="description"></span>
                            </td>
                        </tr>
                        <?php
					}
					?>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e( "Default adzone image", 'wpproads' ); ?>
                            <span class="description"><?php _e('Upload/Select a default image that will show when the azone is empty.', 'wpproads'); ?></span>
                        </th>
                        <td>
                            <div style="float:left; width:500px;">
                                <input type="text" size="40" id="adzone_default_url" name="adzone_default_url" value="<?php echo !empty( $adzone_default_url ) ? $adzone_default_url : ''; ?>" placeholder="<?php _e('Default Banner url', 'wpproads'); ?>" />
                                <input class="upload_default_adzone_button button" type="button" value="<?php _e('Upload Image', 'wpproads'); ?>" />
                                <span class="description"><?php echo sprintf(__('<strong>Note:</strong> This image will replace the default "advertise here" button if you have the %s or %s add-on activated.', 'wpproads'), '<a href="http://bit.ly/buyandsellads" target="_blank">Buy and Sell</a>', '<a href="http://bit.ly/BuyAndSellAdsWoocommerce" target="_blank">Buy and Sell WooCommerce</a>  Add-On' ); ?></span>
                                <br />
                                <div id="adzone_default_url-preview"><?php echo !empty( $adzone_default_url ) ? '<img src="'.$adzone_default_url.'" />' : __('No image selected','wpproads'); ?></div>
                            </div>
                            <div style="clear:both;"></div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e( "Default adzone image link", 'wpproads' ); ?>
                            <span class="description"><?php _e('Add a link that will be added to the default showing when the azone is empty.', 'wpproads'); ?></span>
                        </th>
                        <td>
                            <input type="text" size="40" id="adzone_default_url_link" name="adzone_default_url_link" value="<?php echo !empty( $adzone_default_url_link ) ? $adzone_default_url_link : ''; ?>" placeholder="<?php _e('http://', 'wpproads'); ?>" />
                            <span class="description"><?php _e('', 'wpproads'); ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
	}
	
	function wp_pro_ads_adzones_meta_options_save_postdata( $post_id ) 
	{
		global $pro_ads_responsive;
		
		// Check if our nonce is set.
		if ( ! isset( $_POST['wp_pro_ads_adzones_meta_options_inner_custom_box_nonce'] ) )
		return $post_id;
		$nonce = $_POST['wp_pro_ads_adzones_meta_options_inner_custom_box_nonce'];
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'wp_pro_ads_adzones_meta_options_inner_custom_box' ) )
		  return $post_id;
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		  return $post_id;
		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;
		} else {
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;
		}
		/* OK, its safe for us to save the data now. */
		
		
		$device_sizes = $pro_ads_responsive->device_sizes();
		foreach( $device_sizes as $device_size )
		{
			if( $_POST['adzone_size'.$device_size['prefix']] == 'custom' )
			{
				$size = $_POST['custom_w'.$device_size['prefix']].'x'.$_POST['custom_h'.$device_size['prefix']];
			}
			elseif( $_POST['adzone_size'.$device_size['prefix']] == 'responsive' )
			{
				$size = '';
			}
			else
			{
				$size = $_POST['adzone_size'.$device_size['prefix']];
			}
			$custom = $_POST['adzone_size'.$device_size['prefix']] == 'custom' ? 1 : 0;
			$responsive = $_POST['adzone_size'.$device_size['prefix']] == 'responsive' ? 1 : 0;
			$adzone_fix_size = !empty($_POST['adzone_fix_size'.$device_size['prefix']]) ? $_POST['adzone_fix_size'.$device_size['prefix']] : 0 ;
			$hide_for_device = !empty($_POST['adzone_hide_for_device'.$device_size['prefix']]) ? $_POST['adzone_hide_for_device'.$device_size['prefix']] : 0 ;
			
			update_post_meta( $post_id, '_adzone_size'.$device_size['prefix'], $size );
			update_post_meta( $post_id, '_adzone_custom_size'.$device_size['prefix'], $custom );
			update_post_meta( $post_id, '_adzone_responsive'.$device_size['prefix'], $responsive );
			update_post_meta( $post_id, '_adzone_fix_size'.$device_size['prefix'], $adzone_fix_size );
			update_post_meta( $post_id, '_adzone_hide_for_device'.$device_size['prefix'], $hide_for_device );
		}
		
			
		// Sanitize user input.
		$description             = sanitize_text_field( $_POST['adzone_description'] );
		$rotation_type           = sanitize_text_field( $_POST['adzone_rotation_type'] );
		$rotation                = sanitize_text_field( $_POST['adzone_rotation'] );
		$rotation_time           = sanitize_text_field( $_POST['adzone_rotation_time'] );
		$rotation_effect         = sanitize_text_field( $_POST['adzone_rotation_effect'] );
		$adzone_rotation_order   = sanitize_text_field( $_POST['adzone_rotation_order'] );
		$rotation_ajax           = sanitize_text_field( $_POST['adzone_rotation_ajax'] );
		$horizontal              = sanitize_text_field( $_POST['adzone_grid_horizontal'] );
		$vertical                = sanitize_text_field( $_POST['adzone_grid_vertical'] );
		$max_banners             = sanitize_text_field( $_POST['adzone_max_banners'] );
		$adzone_center           = sanitize_text_field( $_POST['adzone_center'] );
		$adzone_hide_empty       = sanitize_text_field( $_POST['adzone_hide_empty'] );
		$adzone_default_url      = sanitize_text_field( $_POST['adzone_default_url'] );
		$adzone_default_url_link = sanitize_text_field( $_POST['adzone_default_url_link'] );
		$adzone_no_buyandsell    = isset($_POST['adzone_no_buyandsell']) ? sanitize_text_field( $_POST['adzone_no_buyandsell'] ) : 0;
		
		// Update the meta field in the database.
		update_post_meta( $post_id, '_adzone_description', $description );
		update_post_meta( $post_id, '_adzone_rotation_type', $rotation_type );
		update_post_meta( $post_id, '_adzone_rotation', $rotation );
		update_post_meta( $post_id, '_adzone_rotation_time', $rotation_time );
		update_post_meta( $post_id, '_adzone_rotation_effect', $rotation_effect );
		update_post_meta( $post_id, '_adzone_rotation_order', $adzone_rotation_order );
		update_post_meta( $post_id, '_adzone_rotation_ajax', $rotation_ajax );
		update_post_meta( $post_id, '_adzone_grid_horizontal', $horizontal );
		update_post_meta( $post_id, '_adzone_grid_vertical', $vertical );
		update_post_meta( $post_id, '_adzone_max_banners', $max_banners );
		update_post_meta( $post_id, '_adzone_center', $adzone_center );
		update_post_meta( $post_id, '_adzone_hide_empty', $adzone_hide_empty );
		update_post_meta( $post_id, '_adzone_default_url', $adzone_default_url );
		update_post_meta( $post_id, '_adzone_default_url_link', $adzone_default_url_link );
		update_post_meta( $post_id, '_adzone_no_buyandsell', $adzone_no_buyandsell );
	}
   
}
?>