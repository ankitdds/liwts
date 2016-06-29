<?php
/*
 * AJAX REQUEST FUNCTIONS
 *
 * http://codex.wordpress.org/AJAX_in_Plugins
 * For not logged-in users use: add_action('wp_ajax_nopriv_my_action', 'my_action_callback');
*/



/* -------------------------------------------------------------
 * Load campaigns from a specific advertiser - when creating a banner
 * ------------------------------------------------------------- */
add_action('wp_ajax_load_advertiser_campaigns', "load_advertiser_campaigns_callback");
function load_advertiser_campaigns_callback() 
{
	global $pro_ads_campaigns;
	
	$campaigns = $pro_ads_campaigns->get_campaigns( array('meta_key' => '_campaign_advertiser_id', 'meta_value' => $_POST['uid']) );
	
	$html = '';
	$campaign_id = '';
	$html.= '<select name="banner_campaign_id" class="chosen-select select_banner_campaign" required="required">';
    	$html.= '<option value="">'.__('Select a campaign', 'wpproads').'</option>';
		foreach( $campaigns as $campaign )
		{
			$select = $campaign_id == $campaign->ID ? 'selected' : '';
			$html.= '<option value="'.$campaign->ID.'" '.$select.'>'.$campaign->post_title.'</option>';
		}
	$html.= '</select>';
	
	echo $html;
	
	exit;
}
/* -------------------------------------------------------------
 * Load campaigns from a specific advertiser - for filtering
 * ------------------------------------------------------------- */
add_action('wp_ajax_filter_advertiser_campaigns', "filter_advertiser_campaigns_callback");
function filter_advertiser_campaigns_callback() 
{
	global $pro_ads_campaigns;
	
	$campaigns = $pro_ads_campaigns->get_campaigns( array('meta_key' => '_campaign_advertiser_id', 'meta_value' => $_POST['uid']) );
	
	$html = '';
	$html.= '<select name="banner_campaign_id" class="chosen-select filter_banner_campaign">';
    	$html.= '<option value="">'.__('Select a Campaign', 'wpproads').'</option>';
		if( $campaigns )
		{
			foreach( $campaigns as $campaign )
			{
				$select = '';
				$html.= '<option value="'.$campaign->ID.'" '.$select.'>'.$campaign->post_title.'</option>';
			}
		}
	$html.= '</select>';
	
	echo $html;
	
	exit;
}





/* -------------------------------------------------------------
 * Link banner to adzone
 * ------------------------------------------------------------- */
add_action('wp_ajax_link_to_adzone', "link_to_adzone_callback");
function link_to_adzone_callback() 
{
	global $pro_ads_adzones;
	
	// link banner to adzone
	//update_post_meta( $_POST['aid'], 'linked_banners', ''  );
	$linked_banners = get_post_meta( $_POST['aid'], '_linked_banners', true );
	$max_banners    = get_post_meta( $_POST['aid'], '_adzone_max_banners', true );
	$banner_status  = get_post_meta( $_POST['bid'], '_banner_status', true );
	
	if( empty( $linked_banners ))
	{
		if( $pro_ads_adzones->check_if_adzone_is_active( $_POST['aid'] ) && $banner_status == 1)
		{
			$linked_banners = array( $_POST['bid'] );
			update_post_meta( $_POST['aid'], '_linked_banners', array_values(array_filter($linked_banners))  );
			
			// link adzone to banner
			$adzone_ids = explode(',', $_POST['result']);
			update_post_meta( $_POST['bid'], '_linked_adzones', $adzone_ids  );
		}
	}
	else
	{
		if( $_POST['action_type'] == 'remove' )
		{
			if (($key = array_search($_POST['bid'], $linked_banners)) !== false) unset($linked_banners[$key]);
			// link adzone to banner
			$adzone_ids = explode(',', $_POST['result']);
			update_post_meta( $_POST['bid'], '_linked_adzones', $adzone_ids  );
		}
		else
		{
			if( $pro_ads_adzones->check_if_adzone_is_active( $_POST['aid'] ) && $banner_status == 1)
			{
				array_push($linked_banners, $_POST['bid']);
				// link adzone to banner
				$adzone_ids = explode(',', $_POST['result']);
				update_post_meta( $_POST['bid'], '_linked_adzones', $adzone_ids  );
			}
		}
		update_post_meta( $_POST['aid'], '_linked_banners', array_values(array_filter($linked_banners)) );
	}
	
	exit;
}






/* -------------------------------------------------------------
 * Order banners in Adzone
 * ------------------------------------------------------------- */
add_action('wp_ajax_order_banners_in_adzone', "order_banners_in_adzone_callback");
function order_banners_in_adzone_callback() 
{
	//foreach ($_POST['order-item'] as $i => $value) {}
	
	$id_order = explode(',', $_POST['id_order']);
	$linked_banners_order = array();
	
	foreach($id_order as $i => $order) 
	{
		$linked_banners_order[] = $order;
	}
	
	update_post_meta( $_POST['aid'], '_linked_banners', array_values(array_filter($linked_banners_order)) );
	//print_r($linked_banners_order);
	exit;
}







/* -------------------------------------------------------------
 * Load Stats
 * ------------------------------------------------------------- */
add_action('wp_ajax_load_stats', "load_stats_callback");
function load_stats_callback() 
{
	global $pro_ads_statistics;
	
	echo $pro_ads_statistics->pro_ad_show_statistics(
			array_filter(array(
				'type'     => array('slug' => $_POST['type'], 'name' => $pro_ads_statistics->stat_types($_POST['type'])), 
				'color'    => $pro_ads_statistics->stat_type_color( $_POST['type'] ), 
				'rid'      => $_POST['rid'],
				'year'     => $_POST['year'], 
				'month'    => !empty($_POST['month']) ? $_POST['month'] : '', 
				'day'      => !empty($_POST['day']) ? $_POST['day'] : '',
				'group'    => !empty($_POST['group']) ? $_POST['group'] : '',
				'group_id' => !empty($_POST['group_id']) ? $_POST['group_id'] : ''
			))
		);
	
	// stats table
	/*$pro_ads_statistics->get_stats_table( 
		array_filter(array(
			'type'     => array('slug' => $_POST['type'], 'name' => $pro_ads_statistics->stat_types($_POST['type'])), 
			'rid'      => $_POST['rid'],
			'year'     => $_POST['year'], 
			'month'    => !empty($_POST['month']) ? $_POST['month'] : '', 
			'day'      => !empty($_POST['day']) ? $_POST['day'] : '',
			'group'    => !empty($_POST['group']) ? $_POST['group'] : '',
			'group_id' => !empty($_POST['group_id']) ? $_POST['group_id'] : ''
		))
	);*/
	
	exit;
}

add_action('wp_ajax_load_stats_from_day', "load_stats_from_day_callback");
function load_stats_from_day_callback() 
{
	global $pro_ads_statistics;
	
	echo $pro_ads_statistics->pro_ad_show_statistics(
			array(
				'type'     => array('slug' => $_POST['type'], 'name' => $pro_ads_statistics->stat_types($_POST['type'])), 
				'range'    => 'day', 
				'rid'      => 4,
				'color'    => $_POST['color'],
				'year'     => $_POST['year'], 
				'month'    => $_POST['month'], 
				'day'      => $_POST['day'],
				'group'    => !empty($_POST['group']) ? $_POST['group'] : '',
				'group_id' => !empty($_POST['group_id']) ? $_POST['group_id'] : ''
			)
		);
	
	// stats table
	/*$pro_ads_statistics->get_stats_table( 
		array(
			'type'     => array('slug' => $_POST['type'], 'name' => $pro_ads_statistics->stat_types($_POST['type'])), 
			'range'    => 'day', 
			'rid'      => 4,
			'color'    => $_POST['color'],
			'year'     => $_POST['year'], 
			'month'    => $_POST['month'], 
			'day'      => $_POST['day'],
			'group'    => !empty($_POST['group']) ? $_POST['group'] : '',
			'group_id' => !empty($_POST['group_id']) ? $_POST['group_id'] : ''
		) 
	);*/
	
	exit;
}






/* -------------------------------------------------------------
 * Shotcode Editor
 * ------------------------------------------------------------- */
add_action('wp_ajax_load_wpproads_shortcodes', 'load_wpproads_shortcodes_callback');
function load_wpproads_shortcodes_callback() 
{
	global $pro_ads_templates;
	
	$pro_ads_templates->get_shortcode_editor_form();
	
	exit();
}






/* -------------------------------------------------------------
 * Manual update Campaigns/Banners
 * ------------------------------------------------------------- */
add_action('wp_ajax_manual_update_campaigns_banners', 'manual_update_campaigns_banners_callback');
function manual_update_campaigns_banners_callback() 
{
	global $pro_ads_main;
	
	$pro_ads_main->daily_updates(1);
	
	echo __('Campaign and Banner statuses are updated.','wpproads');
	
	exit();
}







/* -------------------------------------------------------------
 * LOAD RESPONSIVE ADZONES
 * ------------------------------------------------------------- */
add_action('wp_ajax_pas_responsive', 'pas_responsive_callback');
add_action('wp_ajax_nopriv_pas_responsive', 'pas_responsive_callback');
function pas_responsive_callback() 
{	
	$screen = empty($_POST['screen']) || $_POST['screen'] == 'desktop' ? '' : '_'.$_POST['screen'];
	echo do_shortcode('[pro_ad_display_adzone id="'.$_POST['adzone_id'].'" screen="'.$screen.'"]');
	
	exit();
}





/* -------------------------------------------------------------
 * AD BLOCKER DETECTION
 * ------------------------------------------------------------- */
add_action('wp_ajax_adblocker_detected', 'adblocker_detected_callback');
add_action('wp_ajax_nopriv_adblocker_detected', 'adblocker_detected_callback');
function adblocker_detected_callback()
{
	$adblock_action = array( 
		'alert' => ''
	);
	$adblock_action = apply_filters( 'wp_pro_ads_adblock_detected', $adblock_action );
	echo json_encode( $adblock_action );
	
	exit();
}





/* -------------------------------------------------------------
 * SHORTCODE EDITOR TEMPLATE
 * ------------------------------------------------------------- */
add_action('wp_ajax_load_shortcode_editor', 'load_shortcode_editor_callback');
function load_shortcode_editor_callback()
{
	global $pro_ads_templates;
	
	echo $pro_ads_templates->pro_ad_adzone_popup_screen( $_GET['adzone_id'] );
	
	exit();
}



/* -------------------------------------------------------------
 * AJAX ROTATION LOAD BANNER
 * ------------------------------------------------------------- */
add_action('wp_ajax_rotation_load_banner', 'rotation_load_banner_callback');
add_action('wp_ajax_nopriv_rotation_load_banner', 'rotation_load_banner_callback');
function rotation_load_banner_callback()
{
	global $pro_ads_statistics;
	
	$pro_ads_statistics->save_impression( $_POST['id'], $_POST['aid'] );
	echo do_shortcode('[pro_ad_display_banner id="'.$_POST['id'].'" aid="'.$_POST['aid'].'" container=0]');
	
	exit();
}


/* -------------------------------------------------------------
 * SET SCREEN SIZE COOKIE
 * ------------------------------------------------------------- */
/*
add_action('wp_ajax_pas_set_screensize', 'pas_set_screensize_callback');
add_action('wp_ajax_nopriv_pas_set_screensize', 'pas_set_screensize_callback');
function pas_set_screensize_callback() 
{
	global $pro_ads_responsive;
	
	if(isset($_POST['width']))
	{
		$type = $pro_ads_responsive->get_screen_type($_POST['width']);
		$cookie = !empty($type['prefix']) ? $type['prefix'] : 0;
		setcookie("wpproads_screentype", $cookie, 0, COOKIEPATH, COOKIE_DOMAIN);
	}
	
	exit();
}
*/
?>