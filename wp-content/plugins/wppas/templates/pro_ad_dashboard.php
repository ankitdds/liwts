<?php
global $pro_ads_statistics, $pro_ads_main, $pro_ads_multisite, $pro_ads_bs_main, $pro_ads_bs_templates, $pro_ads_bs_woo_templates, $pro_ads_bs_woo_main, $pro_ads_adzones;

wp_enqueue_script('wp_pro_ads_ace');
$notice = array();

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if(isset($_POST['1']))
	{
		$pro_ads_multisite->wpproads_update_option( 'wpproads_custom_css', $_POST['wpproads_custom_css']);
		$pro_ads_multisite->wpproads_update_option( 'wpproads_enable_stats', $_POST['wpproads_enable_stats']);
		$pro_ads_multisite->wpproads_update_option( 'wpproads_enable_impr', $_POST['wpproads_enable_impr']);
		$pro_ads_multisite->wpproads_update_option( 'wpproads_enable_clicks', $_POST['wpproads_enable_clicks']);
		$pro_ads_multisite->wpproads_update_option( 'wpproads_stats_data', $_POST['wpproads_stats_data']);
		$pro_ads_multisite->wpproads_update_option( 'wpproads_enable_userdata_stats', $_POST['wpproads_enable_userdata_stats']);
		$pro_ads_multisite->wpproads_update_option( 'wpproads_save_impressions_type', $_POST['wpproads_save_impressions_type']);
		$pro_ads_multisite->wpproads_update_option( 'wpproads_save_clicks_type', $_POST['wpproads_save_clicks_type']);
		$pro_ads_multisite->wpproads_update_option( 'wpproads_stats_save_days', $_POST['wpproads_stats_save_days']);
		$pro_ads_multisite->wpproads_update_option( 'wpproads_stats_blocked_ips', $_POST['wpproads_stats_blocked_ips']);
		$pro_ads_multisite->wpproads_update_option( 'wpproads_google_analytics_id', $_POST['wpproads_google_analytics_id']);
		$pro_ads_multisite->wpproads_update_option( 'wpproads_stats_version', $_POST['wpproads_stats_version']);
		$pro_ads_multisite->wpproads_update_option( 'wpproads_enable_adminbar', $_POST['wpproads_enable_adminbar']);
		$pro_ads_multisite->wpproads_update_option( 'wpproads_uninstall', $_POST['wpproads_uninstall']);
		
		
		// permalinks
		$cur_str = get_option('wp_ads_mod_rewrite', 'pas');
		$update_permalinks = $cur_str != $_POST['wp_ads_mod_rewrite'] ? 1 : 0;
		update_option( 'wpproads_enable_mod_rewrite', $_POST['wpproads_enable_mod_rewrite']);
		update_option( 'wp_ads_mod_rewrite', $_POST['wp_ads_mod_rewrite']);
		
		$notice[] = !$update_permalinks ? __('General Settings are updated successfully.','wpproads') : __('Please UPDATE your <a href="'.get_admin_url().'options-permalink.php">Permalinks</a>.', 'wpproads');
	}
	elseif(isset($_POST['buyandsell_settings']))
	{
		$notice[] = $pro_ads_bs_main->wpproads_buyandsell_save_options();
	}
	elseif(isset($_POST['bs_woo_settings']))
	{
		$notice[] = $pro_ads_bs_woo_main->wpproads_buyandsell_woo_save_settings_options();
	}
	elseif(isset($_POST['bsads_woo']))
	{
		$notice[] = $pro_ads_bs_woo_main->wpproads_buyandsell_woo_save_options();
	}
	elseif(isset($_POST['wpproads_post_template']))
	{
		update_option( 'wpproads_enable_post_ads', $_POST['wpproads_enable_post_ads'] );
		update_option( 'wpproads_post_ads_top', $_POST['wpproads_post_ads_top'] );
		update_option( 'wpproads_post_ads_center', $_POST['wpproads_post_ads_center'] );
		update_option( 'wpproads_post_ads_center_para', $_POST['wpproads_post_ads_center_para'] );
		update_option( 'wpproads_post_ads_center_align', $_POST['wpproads_post_ads_center_align'] );
		update_option( 'wpproads_post_ads_bottom', $_POST['wpproads_post_ads_bottom'] );
		
		// Multisite, network activated
		if( $pro_ads_multisite->pro_ads_plugin_is_network_activated() && is_main_site() )
		{
			update_site_option( 'wpproads_enable_post_ads', $_POST['wpproads_enable_post_ads'] );
			update_site_option( 'wpproads_post_ads_top', $_POST['wpproads_post_ads_top'] );
			update_site_option( 'wpproads_post_ads_center', $_POST['wpproads_post_ads_center'] );
			update_site_option( 'wpproads_post_ads_center_para', $_POST['wpproads_post_ads_center_para'] );
			update_site_option( 'wpproads_post_ads_center_align', $_POST['wpproads_post_ads_center_align'] );
			update_site_option( 'wpproads_post_ads_bottom', $_POST['wpproads_post_ads_bottom'] );
		}
	}
	elseif( isset($_POST['wpproads_register_plugin']))
	{
		$registered = $pro_ads_main->PLU_registration( $_POST['wpproads_license_key'] );
		
		if( $registered == 1 )
		{
			update_option( 'wpproads_license_key', $_POST['wpproads_license_key'] );
			if( $pro_ads_multisite->pro_ads_plugin_is_network_activated() && is_main_site() )
			{
				update_site_option( 'wpproads_license_key', $_POST['wpproads_license_key'] );
			}
		}
		else
		{
			update_option( 'wpproads_license_key', '' );
			if( $pro_ads_multisite->pro_ads_plugin_is_network_activated() && is_main_site() )
			{
				update_site_option( 'wpproads_license_key', '' );
			}
		}
		$notice[] = $registered == 1 ? __('Plugin registered successfully.','wpproads') : __('Please add a valid/unique license key.', 'wpproads');
	}
}


$custom_css = get_option('wpproads_custom_css', '');
$wpproads_license_key = get_option('wpproads_license_key', '');
$wpproads_enable_stats = get_option('wpproads_enable_stats', 0);
$wpproads_enable_impr = get_option('wpproads_enable_impr', 1);
$wpproads_enable_clicks = get_option('wpproads_enable_clicks', 1);
$wpproads_stats_data = get_option('wpproads_stats_data', 'hour');
$wpproads_enable_userdata_stats = get_option('wpproads_enable_userdata_stats', 0);
$wpproads_save_clicks_type = get_option('wpproads_save_clicks_type', 'unique');
$wpproads_save_impressions_type = get_option('wpproads_save_impressions_type', 'unique');
$wpproads_stats_save_days = get_option('wpproads_stats_save_days', '');
$wpproads_stats_version = get_option('wpproads_stats_version', '_new');
$wpproads_stats_blocked_ips = get_option('wpproads_stats_blocked_ips', '');
$wpproads_enable_post_ads = get_option('wpproads_enable_post_ads',array());
$wpproads_google_analytics_id = get_option('wpproads_google_analytics_id', '');
$wpproads_post_ads_top = get_option('wpproads_post_ads_top',0);
$wpproads_post_ads_center = get_option('wpproads_post_ads_center',0);
$wpproads_post_ads_center_para = get_option('wpproads_post_ads_center_para', 2);
$wpproads_post_ads_center_align = get_option('wpproads_post_ads_center_align', '');
$wpproads_post_ads_bottom = get_option('wpproads_post_ads_bottom',0);
$wpproads_enable_adminbar = get_option('wpproads_enable_adminbar',1);
$wpproads_uninstall = get_option('wpproads_uninstall', 0);

$wpproads_enable_mod_rewrite = get_option('wpproads_enable_mod_rewrite', 0);
$wp_ads_mod_rewrite = get_option('wp_ads_mod_rewrite', 'pas');
?>

<div id="wpproads_dashboard" class="wrap ad_dashboard wpproads">
	
    <div class="ad_dasboard_boxes adheader">
    	<h3>WP PRO ADVERTISING SYSTEM</h3>
        <div class="version"><em><?php _e('Version','wpproads'); ?> <?php echo PAS()->version; ?></em></div>
    </div>
    <?php
	if( current_user_can(WP_ADS_ROLE_ADMIN))
	{
		?>
        <div class="ad_dasboard_boxes menu">
            <a href="post-new.php?post_type=advertisers" class="plus main_button" title="<?php _e('Add New','wpproads'); ?>">+</a><a href="edit.php?post_type=advertisers" class="main_button action_button"><?php _e('Advertisers','wpproads'); ?></a>
            <a href="post-new.php?post_type=campaigns" class="plus main_button" title="<?php _e('Add New','wpproads'); ?>">+</a><a href="edit.php?post_type=campaigns" class="main_button action_button"><?php _e('Campaigns','wpproads'); ?></a>
            <a href="post-new.php?post_type=banners" class="plus main_button" title="<?php _e('Add New','wpproads'); ?>">+</a><a href="edit.php?post_type=banners" class="main_button action_button"><?php _e('Banners','wpproads'); ?></a>
            <a href="post-new.php?post_type=adzones" class="plus main_button" title="<?php _e('Add New','wpproads'); ?>">+</a><a href="edit.php?post_type=adzones" class="main_button action_button"><?php _e('Adzones','wpproads'); ?></a>
        </div>
        <?php
	}
	?>
	
    <h2 class="messages-position"></h2>
    <?php
	// admin_notice Messages
    if( !empty($notice) )
	{
		foreach($notice as $note)
		{
			echo !empty($note) ? '<div class="updated wpproads-message"><p>'.$note.'</p></div>' : '';
		}
	}
	
	if( empty($wpproads_license_key))
	{
		?>
        <div class="updated wpproads-message">
        	<p><?php _e('Please Register your copy of the WP PRO Advertising System to receive automatic plugin updates.','wpproads'); ?></p>
            <form action="" method="post" enctype="multipart/form-data">
            	<p>
                	<input type="text" name="wpproads_license_key" style="width:300px;" placeholder="<?php _e('License Key','wpproads'); ?>"> 
                	<input type="submit" value="<?php _e('Register Plugin', 'wpproads'); ?>" class="main_button" name="wpproads_register_plugin" />
                </p>
           </form>
        </div>
        <?php
	}
	?>
    
    
    <div class="container">
    	<div class="left_content ad_dasboard_boxes">
         
            
            <div class="tuna_tab_container">
             
                <table id="tuna_tab_customization">
                    <tr>
                        <td valign="top">
                            <div id="tuna_tab_left">
                                <ul>
                                    
                                    <?php
                                    if($wpproads_enable_stats)
									{
										?>
                                        <li><a class="focused" data-target="statistics" title=""><?php _e("Today's statistics", 'wpproads'); ?></a></li>
										<?php
									}
									
									if( current_user_can(WP_ADS_ROLE_ADMIN))
									{
										?>
                                        <li><a <?php echo $wpproads_enable_stats ? '' : 'class="focused"'; ?> data-target="buyandsell-addon" title=""><?php _e('Buy and Sell Ads', 'wpproads'); ?></a></li>
                                        <li><a class="" data-target="post-template" title=""><?php _e('Manage Post Ads', 'wpproads'); ?></a></li>
                                        <li><a class="" data-target="manual-updates" title=""><?php _e('Manual Updates', 'wpproads'); ?></a></li>
                                        <li><a class="" data-target="general-settings" title=""><?php _e('General Settings', 'wpproads'); ?></a></li>
                                        <li><a class="" data-target="plugin-registration" title=""><?php _e('Plugin Registration', 'wpproads'); ?></a></li>
                                        <?php
									}
									?>
                                </ul>					
                            </div>
                        </td>
                        <td width="100%"  valign="top">
                            <div id="tuna_tab_right">
                                <p id="tuna_tab_arrow" style="top:4px"></p>
                                <div class="customization_right_in">
                                    
                                    <?php
									if($wpproads_enable_stats)
									{
										?>
                                        <div id="statistics" style="" class="nfer">
                                            <h2><?php _e("Today's Statistics", 'wpproads'); ?> <small style="font-size:12px; color:#999;"><em><?php echo date_i18n('l, F d', $pro_ads_main->time_by_timezone()); ?></em></small></h2>
                                            <em class="hr_line"></em>
                                            <form action="" method="post" enctype="multipart/form-data">
                                                <div class="tuna_meta metabox-holder">
                                    
                                                    <div class="postbox nobg">
                                                        <div class="inside">
                                                            
                                                            <!-- Toadays statistics -->
                                                            <div class="dashboard_stats ad_dasboard_boxes">
                                                                
                                                                <?php 
                                                                    echo $pro_ads_statistics->pro_ad_show_statistics_header( 
                                                                            array( 
                                                                                'text'  => array(
                                                                                    'click' => __('Clicks','wpproads'),
                                                                                    'impr'  => __('Impressions','wpproads'),
                                                                                    'ctr'   => __('CTR','wpproads'),
                                                                                ), 
                                                                                'rid'   => 4, 
                                                                                'day'   => $pro_ads_main->time_by_timezone('d'), 
                                                                                'month' => $pro_ads_main->time_by_timezone('m'), 
                                                                                'year'  => $pro_ads_main->time_by_timezone('Y')
                                                                            ) 
                                                                        ); 
                                                                ?>
                                                                <a href="?page=wp-pro-ads-stats" class="main_button"><?php _e('Check all statistics','wpproads'); ?></a>
                                                            </div>
                                                            <!-- end Toadays statistics -->
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <?php
									}
                                   
									
									if( current_user_can(WP_ADS_ROLE_ADMIN))
									{
										?>
										<div id="buyandsell-addon" <?php echo $wpproads_enable_stats ? 'style="display:none;"' : ''; ?> class="nfer">
                                            <h2>
												<?php 
												if( $pro_ads_main->buyandsell_is_active() )
												{
													_e('Buy and Sell Ads, Settings', 'wpproads');
												}
												elseif( $pro_ads_main->buyandsell_woo_is_active() )
												{
													_e('Buy and Sell Ads, Woocommerce Settings', 'wpproads');
												}
												else
												{
													_e('Buy and Sell Ads', 'wpproads');
												}
												?>
                                            </h2>
                                            <em class="hr_line"></em>
                                                <div class="tuna_meta metabox-holder">
                                    
                                                    <div class="postbox nobg">
                                                        <div class="inside">
                                                            <table class="form-table">
                                                                <tbody>
                                                                    <tr>
                                                                        <td colspan="2">
                                                                            
                                                                            <?php
                                                                            if( $pro_ads_main->buyandsell_is_active() )
                                                                            {
                                                                                $pro_ads_bs_templates->pro_ad_buy_and_sell_settings();
                                                                            }
                                                                            elseif( $pro_ads_main->buyandsell_woo_is_active() )
                                                                            {
                                                                                $pro_ads_bs_woo_templates->pro_ad_buy_and_sell_woo_settings();
                                                                            }
																			else
																			{
																				?>
                                                                                <div style="float:left; width:150px; height:100px; background:#F3F3F3; border:solid 1px #EDEDED; margin:0 10px 0;">
                                                                                    <div style="text-align:center; padding-top:40px;">
                                                                                        <a href="http://bit.ly/buyandsellads" style="text-decoration:none; color:#999;" target="_blank"><?php _e('Advertise Here','wpproads'); ?></a>
                                                                                    </div>
                                                                                </div> 
                                                                                <?php _e('Make sure your adzones do not remain empty! Let users buy advertisments directly on your website! The Buy and Sell Add-on turns your empty adzones into links where users can instantly upload their banner, pay and activate their advertisement on your website.','wpproads'); ?>
                                                                                
                                                                                <div class="clearFix"></div>
                                                                                <br /><br />
                                                                                <a href="http://bit.ly/buyandsellads" class="main_button" target="_blank"><?php _e('Download Buy and Sell Ads - ADD-ON','wpproads'); ?></a>
                                                                                <?php	
																			}
                                                                            ?>
                                                                            <span class="description manual_update_info"><?php _e('','wpproads'); ?></span>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <!-- end .postbox -->
                                                </div>
                                                <!-- end .tuna_meta -->
                                                
                                        </div>
                                        <!-- end #buyandsell-addon -->
										
                                        
                                        
                                        
                                        
                                        
                                        
                                        <div id="post-template" style="display:none;" class="nfer">
                                            <h2><?php _e('Manage Post Ads', 'wpproads'); ?></h2>
                                            <em class="hr_line"></em>
                                            <form action="" method="post" enctype="multipart/form-data">
                                                <div class="tuna_meta metabox-holder">
                                    
                                                    <div class="postbox nobg">
                                                        <div class="inside">
                                                            <table class="form-table">
                                                                <tbody>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Enable Post Ads", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('This allows you to add banners to all posts automatically. Select the post types where you want to use this function.', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_enable_post_ads" name="wpproads_enable_post_ads[]" data-placeholder="<?php _e('No post types selected.', 'wpproads'); ?>" class="chosen-select-ad-dashboard" multiple>
                                                                            	<option value="0"><?php _e('','wpproads'); ?></option>
                                                                                <?php
																				$post_types = get_post_types();
																				if( !empty($post_types ))
																				{
																					foreach( $post_types as $post_type )
																					{
																						$exclude = array('attachment', 'revision', 'nav_menu_item', 'vbc_banners');
																						if( !in_array( $post_type, PAS()->cpts ) && !in_array( $post_type, $exclude))
																						{
																							$selected = in_array($post_type, $wpproads_enable_post_ads) ? 'selected' : '';
																							echo '<option value="'.$post_type.'" '.$selected.'>'.$post_type.'</option>';
																						}
																					}
																				}
																				?>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Top AD", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('This will show an adzone above the post.', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_post_ads_top" name="wpproads_post_ads_top" data-placeholder="<?php _e('No adzone selected.', 'wpproads'); ?>" class="chosen-select-ad-dashboard">
                                                                            	<option value="0"><?php _e('Leave Empty','wpproads'); ?></option>
                                                                                <?php
																				$all_adzones = $pro_ads_adzones->get_adzones();
																				foreach( $all_adzones as $adzone )
																				{
																					$disabled = !$pro_ads_adzones->check_if_adzone_is_active( $adzone->ID ) ? 'disabled="true"' : '';
																					$selected = $adzone->ID == $wpproads_post_ads_top ? 'selected' : '';
																					echo '<option '.$disabled.' value="'.$adzone->ID.'" '.$selected.'>'.$adzone->post_title.'</option>';
																				}
																				?>
                                                                            </select>
                                                                            <span class="description"></span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Center AD", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('This will show an adzone after the 2nd paragraph of the post.', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_post_ads_center" name="wpproads_post_ads_center" data-placeholder="<?php _e('No adzone selected.', 'wpproads'); ?>" class="chosen-select-ad-dashboard">
                                                                            	<option value="0"><?php _e('Leave Empty','wpproads'); ?></option>
                                                                                <?php
																				$all_adzones = $pro_ads_adzones->get_adzones();
																				foreach( $all_adzones as $adzone )
																				{
																					$disabled = !$pro_ads_adzones->check_if_adzone_is_active( $adzone->ID ) ? 'disabled="true"' : '';
																					$selected = $adzone->ID == $wpproads_post_ads_center ? 'selected' : '';
																					echo '<option '.$disabled.' value="'.$adzone->ID.'" '.$selected.'>'.$adzone->post_title.'</option>';
																				}
																				?>
                                                                            </select>
                                                                            <span class="description"></span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Center AD Paragraph", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('Select after how many paragraphs the center ad should show.', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_post_ads_center_para" name="wpproads_post_ads_center_para">
                                                                            	<option value="1" <?php echo $wpproads_post_ads_center_para == 1 ? 'selected="selected"' : ''; ?>>1</option>
                                                                               	<option value="2" <?php echo empty($wpproads_post_ads_center_para) || $wpproads_post_ads_center_para == 2 ? 'selected="selected"' : ''; ?>>2</option>
                                                                                <option value="3" <?php echo $wpproads_post_ads_center_para == 3 ? 'selected="selected"' : ''; ?>>3</option>
                                                                                <option value="4" <?php echo $wpproads_post_ads_center_para == 4 ? 'selected="selected"' : ''; ?>>4</option>
                                                                                <option value="5" <?php echo $wpproads_post_ads_center_para == 5 ? 'selected="selected"' : ''; ?>>5</option>
                                                                                <option value="6" <?php echo $wpproads_post_ads_center_para == 6 ? 'selected="selected"' : ''; ?>>6</option>
                                                                            </select>
                                                                            <span class="description"><?php _e( "", 'wpproads' ); ?></span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Center AD Align", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('Select how you want to align the ad in your post.', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_post_ads_center_align" name="wpproads_post_ads_center_align">
                                                                            	<option value="" <?php echo empty($wpproads_post_ads_center_align) ? 'selected="selected"' : ''; ?>><?php _e('Default','wpproads'); ?></option>
                                                                               	<option value="left" <?php echo $wpproads_post_ads_center_align == 'left' ? 'selected="selected"' : ''; ?>><?php _e('Left','wpproads'); ?></option>
                                                                                <option value="right" <?php echo $wpproads_post_ads_center_align == 'right' ? 'selected="selected"' : ''; ?>><?php _e('Right','wpproads'); ?></option>
                                                                            </select>
                                                                            <span class="description"><?php _e( "Selecting Left or Right will wrap the post text around your advertisement.", 'wpproads' ); ?></span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Bottom AD", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('This will show an adzone under the post.', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_post_ads_bottom" name="wpproads_post_ads_bottom" data-placeholder="<?php _e('No adzone selected.', 'wpproads'); ?>" class="chosen-select-ad-dashboard">
                                                                            	<option value="0"><?php _e('Leave Empty','wpproads'); ?></option>
                                                                                <?php
																				$all_adzones = $pro_ads_adzones->get_adzones();
																				foreach( $all_adzones as $adzone )
																				{
																					$disabled = !$pro_ads_adzones->check_if_adzone_is_active( $adzone->ID ) ? 'disabled="true"' : '';
																					$selected = $adzone->ID == $wpproads_post_ads_bottom ? 'selected' : '';
																					echo '<option '.$disabled.' value="'.$adzone->ID.'" '.$selected.'>'.$adzone->post_title.'</option>';
																				}
																				?>
                                                                            </select>
                                                                            <span class="description"></span>
                                                                        </td>
                                                                    </tr>
                                                                    
                                                                    
                                                                    
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <!-- end .postbox -->
                                                </div>
                                                <!-- end .tuna_meta -->
                                                        
                                                
                                                <div class="btn_container_with_menu" style="margin-top:40px;">
                                                    <input type="submit" value="<?php _e('Save Post Ad Settings', 'wpproads'); ?>" class="main_button" name="wpproads_post_template" />
                                                </div>
                                            </form>
                                        </div>
                                        <!-- end #post-template -->
                                        
                                        
                                        
									
                                    
                                    
									
                                        <div id="manual-updates" style="display:none;" class="nfer">
                                            <h2><?php _e('Manual Updates', 'wpproads'); ?></h2>
                                            <em class="hr_line"></em>
                                                <div class="tuna_meta metabox-holder">
                                    
                                                    <div class="postbox nobg">
                                                        <div class="inside">
                                                            <table class="form-table">
                                                                <tbody>
                                                                    <tr>
                                                                        <td colspan="2">
                                                                            
                                                                            <?php _e('Campaign, Banner and Statistic statuses get updated once a day automatically. If you want to, you can manually Finish or Start campaigns, remove outdated statistics by clicking the update button.','wpproads'); ?>
                                                                            <br /><br />
                                                                            <a href="javascript:void(0)" id="manual_update_campaings_banners" class="main_button"><?php _e('Update Campaigns/Banners/Statistics','wpproads'); ?></a>
                                                                            <span class="description manual_update_info"><?php _e('','wpproads'); ?></span>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <!-- end .postbox -->
                                                </div>
                                                <!-- end .tuna_meta --> 
                                        </div>
                                        <!-- end #manual-updates -->
                                        
                                        
                                        
                                        
                                        
                                        <div id="general-settings" style="display:none;" class="nfer">
                                            <h2><?php _e('General Settings', 'wpproads'); ?></h2>
                                            <em class="hr_line"></em>
                                            <form action="" method="post" enctype="multipart/form-data">
                                                <div class="tuna_meta metabox-holder">
                                                
                                                	<div class="postbox nobg">
                                                        <div class="inside">
                                                        	<h3><?php _e('Settings','wpproads'); ?></h3>
                                                            <table class="form-table">
                                                                <tbody>
                                                					<tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Enable Admin Bar", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('Do you want to add the custom admin bar?', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_enable_adminbar" name="wpproads_enable_adminbar">
                                                                                <option value="0" <?php echo empty($wpproads_enable_adminbar) ? 'selected' : ''; ?>>
                                                                                    <?php _e('No', 'wpproads'); ?>
                                                                                </option>
                                                                                <option value="1" <?php echo $wpproads_enable_adminbar ? 'selected' : ''; ?>>
                                                                                    <?php _e('Yes', 'wpproads'); ?>
                                                                                </option>
                                                                            </select>
                                                                            <span class="description"></span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Uninstall Option", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('Remove all data while uninstalling the plugin?', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_uninstall" name="wpproads_uninstall">
                                                                                <option value="0" <?php echo empty($wpproads_uninstall) ? 'selected' : ''; ?>>
                                                                                    <?php _e('No', 'wpproads'); ?>
                                                                                </option>
                                                                                <option value="1" <?php echo $wpproads_uninstall ? 'selected' : ''; ?>>
                                                                                    <?php _e('Yes', 'wpproads'); ?>
                                                                                </option>
                                                                            </select>
                                                                            <span class="description"></span>
                                                                        </td>
                                                                    </tr>
                                                            	</tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <!-- end .postbox -->
                                    				
                                                    <div class="postbox nobg">
                                                        <div class="inside">
                                                        	<h3><?php _e('Permalinks','wpproads'); ?></h3>
                                                            <table class="form-table">
                                                                <tbody>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Enable Clean permalinks", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('Do you want to enable clean permalinks for your ads?', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_enable_mod_rewrite" name="wpproads_enable_mod_rewrite">
                                                                                <option value="0" <?php echo empty($wpproads_enable_mod_rewrite) ? 'selected' : ''; ?>>
                                                                                    <?php _e('No', 'wpproads'); ?>
                                                                                </option>
                                                                                <option value="1" <?php echo $wpproads_enable_mod_rewrite ? 'selected' : ''; ?>>
                                                                                    <?php _e('Yes', 'wpproads'); ?>
                                                                                </option>
                                                                            </select>
                                                                            <span class="description"></span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr id="wpproads_enable_mod_rewrite_box" <?php echo !empty($wpproads_enable_mod_rewrite) ? '' : 'style="display:none;"'; ?>>
                                                                        <th scope="row">
                                                                            <?php _e( "Link prefix", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('Add your permalink prefix. <strong>NOTE:</strong> You will need to update your Permalinks after updating.', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                        	<input type="text" name="wp_ads_mod_rewrite" value="<?php echo $wp_ads_mod_rewrite; ?>" />
                                                                            <span class="description">
																				<?php _e('ex.:','wpproads'); ?> <?php bloginfo('url'); ?>/<strong><?php echo $wp_ads_mod_rewrite; ?></strong>/banner-name/adzone-name
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                    
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <!-- end .postbox -->
                                                    
                                                    
                                                    <div class="postbox nobg">
                                                        <div class="inside">
                                                        	<h3><?php _e('Statistics','wpproads'); ?></h3>
                                                            <table class="form-table">
                                                                <tbody>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Enable Satistics", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('Do you want statistics to be saved?', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_enable_stats" name="wpproads_enable_stats">
                                                                                <option value="0" <?php echo empty($wpproads_enable_stats) ? 'selected' : ''; ?>>
                                                                                    <?php _e('No', 'wpproads'); ?>
                                                                                </option>
                                                                                <option value="1" <?php echo $wpproads_enable_stats ? 'selected' : ''; ?>>
                                                                                    <?php _e('Yes', 'wpproads'); ?>
                                                                                </option>
                                                                            </select>
                                                                            <span class="description"></span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Disable Impressions", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('Do you want to disable impressions from being saved?', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_enable_impr" name="wpproads_enable_impr">
                                                                                <option value="1" <?php echo $wpproads_enable_impr ? 'selected' : ''; ?>>
                                                                                    <?php _e('No', 'wpproads'); ?>
                                                                                </option>
                                                                                <option value="0" <?php echo empty($wpproads_enable_impr) ? 'selected' : ''; ?>>
                                                                                    <?php _e('Yes', 'wpproads'); ?>
                                                                                </option>
                                                                            </select>
                                                                            <span class="description"></span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Disable Clicks", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('Do you want to disable clicks from being saved?', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_enable_clicks" name="wpproads_enable_clicks">
                                                                                <option value="1" <?php echo $wpproads_enable_clicks ? 'selected' : ''; ?>>
                                                                                    <?php _e('No', 'wpproads'); ?>
                                                                                </option>
                                                                                <option value="0" <?php echo empty($wpproads_enable_clicks) ? 'selected' : ''; ?>>
                                                                                    <?php _e('Yes', 'wpproads'); ?>
                                                                                </option>
                                                                            </select>
                                                                            <span class="description"></span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Statistics version", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('What version do you want to use?', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_stats_version" name="wpproads_stats_version">
                                                                                <option value="" <?php echo empty($wpproads_stats_version) ? 'selected' : ''; ?>>
                                                                                    <?php _e('Deafault Statistics', 'wpproads'); ?>
                                                                                </option>
                                                                                <option value="_new" <?php echo $wpproads_stats_version == '_new' ? 'selected' : ''; ?>>
                                                                                    <?php _e('New Statistics (recommended)', 'wpproads'); ?>
                                                                                </option>
                                                                            </select>
                                                                            <span class="description"></span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Statistics data", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('How detailed do you want to track statistics? NOTE: Hourly statistics will make your database grow fast', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_stats_data" name="wpproads_stats_data">
                                                                                <option value="hour" <?php echo empty($wpproads_stats_data) || $wpproads_stats_data == 'hour' ? 'selected' : ''; ?>>
                                                                                    <?php _e('Hourly Statistics', 'wpproads'); ?>
                                                                                </option>
                                                                                <option value="day" <?php echo $wpproads_stats_data == 'day' ? 'selected' : ''; ?>>
                                                                                    <?php _e('Daily Statistics (recommended)', 'wpproads'); ?>
                                                                                </option>
                                                                            </select>
                                                                            <span class="description"><?php _e( "Only available for the NEW statistics version.", 'wpproads' ); ?></span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr id="enable_userdata_stats" <?php echo !empty($wpproads_enable_stats) ? '' : 'style="display:none;"'; ?>>
                                                                        <th scope="row">
                                                                            <?php _e( "Enable User data stats", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('Do you want specific user data for statistics to be saved?', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_enable_userdata_stats" name="wpproads_enable_userdata_stats">
                                                                                <option value="0" <?php echo empty($wpproads_enable_userdata_stats) ? 'selected' : ''; ?>>
                                                                                    <?php _e('No', 'wpproads'); ?>
                                                                                </option>
                                                                                <option value="1" <?php echo $wpproads_enable_userdata_stats ? 'selected' : ''; ?>>
                                                                                    <?php _e('Yes', 'wpproads'); ?>
                                                                                </option>
                                                                            </select>
                                                                            <span class="description"><?php echo sprintf(__('<strong>Note:</strong> User geo data will only be available with the %s plugin activated.', 'wpproads'), '<a href="http://bit.ly/wpgeotargeting" target="_blank">WP PRO GEO TARGETING</a>'); ?></span>
                                                                        </td>
                                                                    </tr>
                                                                   	<tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Save Impressions", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('Do you want unique or all impressions to be saved for statistics?', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_save_impressions_type" name="wpproads_save_impressions_type">
                                                                                <option value="unique" <?php echo empty($wpproads_save_impressions_type) || $wpproads_save_impressions_type == 'unique' ? 'selected' : ''; ?>>
                                                                                    <?php _e('Unique Impressions', 'wpproads'); ?>
                                                                                </option>
                                                                                <option value="all" <?php echo $wpproads_save_impressions_type == 'all' ? 'selected' : ''; ?>>
                                                                                    <?php _e('All Impressions', 'wpproads'); ?>
                                                                                </option>
                                                                            </select>
                                                                            <span class="description"></span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Save Clicks", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('Do you want unique or all clicks to be saved for statistics?', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <select id="wpproads_save_clicks_type" name="wpproads_save_clicks_type">
                                                                                <option value="unique" <?php echo empty($wpproads_save_clicks_type) || $wpproads_save_clicks_type == 'unique' ? 'selected' : ''; ?>>
                                                                                    <?php _e('Unique Clicks', 'wpproads'); ?>
                                                                                </option>
                                                                                <option value="all" <?php echo $wpproads_save_clicks_type == 'all' ? 'selected' : ''; ?>>
                                                                                    <?php _e('All Clicks', 'wpproads'); ?>
                                                                                </option>
                                                                            </select>
                                                                            <span class="description"></span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e( "Days to save stats", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('Choose the number of days you want to save statistics to the database. Keep this at 30 days or less to keep the database small.', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                        	<input name="wpproads_stats_save_days" type="number" value="<?php echo $wpproads_stats_save_days; ?>" />
                                                                            <span class="description"><?php _e( "Leave empty to save all stats (not recommended)", 'wpproads' ); ?></span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e('Block IP Addresses', 'wpproads'); ?>
                                                                            <span class="description"><?php _e('Add IP addresses where you don\'t want to save statistics for.','wpproads'); ?><br /><br /><?php echo sprintf(__('Your current IP: %s ','wpproads'), '<strong>'.$pro_ads_main->IP.'</strong>'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <textarea id="wpproads_stats_blocked_ips" class="ivc_input" style="height:100px; margin:10px 0 0 0;" name="wpproads_stats_blocked_ips"><?php echo $wpproads_stats_blocked_ips; ?></textarea>
                                                                            <span class="description"><?php _e('Comma separated','wpproads'); ?></span>
                                                                        </td>
                                                                    </tr>
                                                                    
                                                                    
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <!-- end .postbox -->
                                                    
                                                    
                                                    <div class="postbox nobg">
                                                        <div class="inside">
                                                        	<h3><?php _e('Google Analytics','wpproads'); ?></h3>
                                                            <p><?php _e('When you are running Google Analytics for your website and you add your tracking / web property ID (UA-XXXX-Y), all statistics will be redirected to your Google Analytics page.','wpproads'); ?></p>
                                                            <table class="form-table">
                                                                <tbody>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e( "The tracking / web property ID. The format is UA-XXXX-Y", 'wpproads' ); ?>
                                                                            <span class="description"><?php _e('You will find this ID on your Google Analytics page.', 'wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <input type="text" name="wpproads_google_analytics_id" value="<?php echo $wpproads_google_analytics_id; ?>" />
                                                                            <span class="description"></span>
                                                                        </td>
                                                                    </tr>
                                                                    
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <!-- end .postbox -->
                                                    
                                                    
                                                    <div class="postbox nobg">
                                                        <div class="inside">
                                                        	<h3><?php _e('Style','wpproads'); ?></h3>
                                                            <table class="form-table">
                                                                <tbody>
                                                                    <tr>
                                                                        <th scope="row" colspan="2">
                                                                            <?php _e('Custom CSS', 'wpproads'); ?>
                                                                            <span class="description"><?php _e('If you need to customize some style for the Ads plugin you can add the custom CSS here.','wpproads'); ?></span>
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                                    	<td colspan="2">
                                                                        	<pre id="wppas_code_editor"><?php echo stripslashes($custom_css); ?></pre>
                                                                            <textarea id="wpproads_custom_css" class="ivc_input" style="height:100px; margin:10px 0 0 0;" name="wpproads_custom_css"><?php echo stripslashes($custom_css); ?></textarea>
                                                                            <span class="description"><?php _e('','wpproads'); ?></span>
                                                                        </td>
                                                                    </tr>
                                                                    
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <!-- end .postbox -->
                                                    
                                                </div>
                                                <!-- end .tuna_meta -->
                                                        
                                                
                                                <div class="btn_container_with_menu" style="margin-top:40px;">
                                                    <input type="submit" value="<?php _e('Save General Settings', 'wpproads'); ?>" class="main_button" name="1" />
                                                </div>
                                            </form>
                                        </div>
                                        <!-- end #general-settings -->
                                        
                                        
                                        
                                        <div id="plugin-registration" style="display:none;" class="nfer">
                                            <h2><?php _e('Plugin Registration', 'wpproads'); ?></h2>
                                            <em class="hr_line"></em>
                                            <form action="" method="post" enctype="multipart/form-data">
                                                <div class="tuna_meta metabox-holder">
                                        
                                        			<div class="postbox nobg">
                                                        <div class="inside">
                                                        	<h3><?php _e('License Key','wpproads'); ?></h3>
                                                            <table class="form-table">
                                                                <tbody>
                                                                    <tr>
                                                                        <th scope="row">
                                                                            <?php _e('Register Plugin', 'wpproads'); ?>
                                                                            <span class="description"><?php _e('Register your copy of the WP PRO Advertising System to receive automatic plugin updates.','wpproads'); ?></span>
                                                                        </th>
                                                                        <td>
                                                                            <input type="text" name="wpproads_license_key" style="width:300px;" placeholder="<?php _e('License Key','wpproads'); ?>" value="<?php echo $wpproads_license_key; ?>"> 
                                                                            <span class="description"><a href="http://wordpress-advertising.com/faqs/get-license-key/" target="_blank"><?php _e('Where do I get the registration code?','wpproads'); ?></a></span>
                                                                        </td>
                                                                    </tr>
                                                                    
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <!-- end .postbox -->
                                                
                                                </div>
                                                <!-- end .tuna_meta -->
                                                        
                                                
                                                <div class="btn_container_with_menu" style="margin-top:40px;">
                                                    <input type="submit" value="<?php _e('Register Plugin', 'wpproads'); ?>" class="main_button" name="wpproads_register_plugin" />
                                                </div>
                                            </form>
                                        </div>
                                        <!-- end #general-settings -->
                                        
                                        <?php
									}
									?>
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                </div>
                                <!-- end .customization_right_in -->
                            </div>
                            <!-- end #tuna_tab_right -->
                        </td>
                    </tr>
                </table>
                
                
                
            </div> <!-- end tablist -->
            
            
            
            
            
            
            
            
        	
        </div>
        <div class="sidebar">
        	
            <div class="content">
            	<h2>#wpproads <small style="font-size:14px; color:#999;"><em><?php echo PAS()->version; ?></em></small></h2>
                <p>
                	<?php echo sprintf(__('Thanks for using the %s Wordpress plugin. We hope you like it.','wpproads'), current_user_can(WP_ADS_ROLE_ADMIN) ? '"WP PRO ADVERTISING"' : '"WP PRO ADVERTISING SYSTEM"'); ?>
                </p>
                <hr />
                <p>
                	<a href="http://wordpress-advertising.com/faq/" class="wpproads_info_btn" target="_blank">
                        <img src="<?php echo WP_ADS_URL; ?>/images/ico/question.png" /> 
                        <?php _e('FAQ','wpproads'); ?>
                    </a>
                </p>
                <p>
                	<a href="http://tunasite.com/helpdesk/" class="wpproads_info_btn" target="_blank">
                        <img src="<?php echo WP_ADS_URL; ?>/images/ico/helpdesk.png" /> 
                        <?php _e('Helpdesk','wpproads'); ?>
                    </a>
                </p>
                <p>
                	<a href="http://wordpress-advertising.com/developer-api/" class="wpproads_info_btn" target="_blank">
                        <img src="<?php echo WP_ADS_URL; ?>/images/ico/api.png" /> 
                        <?php _e('API','wpproads'); ?>
                    </a>
                </p>
                
            </div>
            
        </div>
    </div>
    
</div>
<!-- end wrap -->


<script type='text/javascript'>
jQuery(document).ready(function($) {
    
    // switching between tabs
	$('#tuna_tab_left').find('a').click(function(){
		
		var nfer_id = $(this).data('target');
		
		$('.nfer').hide();
		$('#'+nfer_id).show();
		
		change_tab_position($(this));
		
		//window.location.hash = nfer_id;
		return false;
		
	});
	// position of the arrow
	function change_tab_position(obj){

		// class switch
		$('#tuna_tab_left').find('a').removeClass('focused');
		obj.addClass('focused');

		var menu_position = obj.position();
		$('#tuna_tab_arrow').css({'top':(menu_position.top+3)+'px'}).show();
	}
	
	
	// Code Editor
	var editor = ace.edit("wppas_code_editor");
	var textarea = $("textarea[name=wpproads_custom_css]").hide();
	
	editor.setTheme("ace/theme/github");
	editor.session.setMode("ace/mode/css");
	editor.setOptions({
		maxLines: 20,
		minLines: 5,
		showPrintMargin: false
	});
	
	editor.getSession().on("change", function(){
		textarea.val(editor.getSession().getValue());
	});
    
});
</script>