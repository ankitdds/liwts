<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPPAS_Adzone_Data' ) ) :

class WPPAS_Adzone_Data {	
	
	
	/**
	 * Show Adzone
	 *
	 * $args (array) - args: adzone_id, device_size
	 * return bool
	 */
	public static function show_adzone($args = array())
	{
		global $pro_ads_main, $pro_ads_responsive, $pro_geo_targeting_main, $pro_ads_adzones;
		
		$defaults = array(
			'adzone_id' => 0,
			'hide_if_loggedin' => 0,
			'adzone_type' => ''
		);
		$args = wp_parse_args($args, $defaults);
		
		$device = $pro_ads_responsive->get_device_type();
		
		$linked_banner_ids = get_post_meta( $args['adzone_id'], '_linked_banners', true );
		$linked_banner_ids = $pro_ads_adzones->wpproads_reset_linked_banner_ids($linked_banner_ids);
		$hide_for_device = get_post_meta( $args['adzone_id'], '_adzone_hide_for_device'.$device['prefix'], true );
		$adzone_hide_empty = self::hide_empty_adzone(array('adzone_id' => $args['adzone_id']));
		$show = 1;
		
		/**
		 * ADD-ON: Geo Targeting
		 *
		 * Check if "Geo Targeting Plugin" is installed.
		 */
		if( $pro_ads_main->pro_geo_targeting_is_active() )
		{
			$show = $pro_geo_targeting_main->show_content( $args['adzone_id'] );
		}
		
		// Hide ads for logged in users
		$show = $args['hide_if_loggedin'] && is_user_logged_in() ? 0 : $show;
		
		// Hide adzone if empty
		if( $args['adzone_type'] == 'flyin'){
			$show = empty($linked_banner_ids) ? 0 : $show;
		}else{
			$show = empty($linked_banner_ids) && $adzone_hide_empty ? 0 : $show;
		}
		
		// Hide for device
		$show = $hide_for_device ? 0 : $show;
		
		/**
		 * Filter for developers to show/hide adzones
		 */
		$show = apply_filters( 'wp_pro_ads_show_adzone', $show, $args['adzone_id']);
		
		return $show;
	}
	


	
	
	
	
	/**
	 * Show Adzone Banners
	 *
	 * $args (array) - args: adzone_id (int), banners (array), 
	 * return bool
	 */
	public static function show_adzone_banners($args = array())
	{
		global $pro_ads_main, $pro_ads_adzones, $pro_geo_targeting_main;
		
		$banners = $args['banners'];
		$adzone_hide_empty = self::hide_empty_adzone(array('adzone_id' => $args['adzone_id']));
		
		/**
		 * ADD-ON: Geo Targeting
		 *
		 * Check if "Geo Targeting Plugin" is installed.
		 */
		if( $pro_ads_main->pro_geo_targeting_is_active() )
		{
			$banners = $pro_geo_targeting_main->geo_target_before_post($banners, 1);
		}
		
		$show = empty($banners) && $adzone_hide_empty ? 0 : 1;
		
		return $show;
	}





	/**
	 * Is Adzone Responsive
	 *
	 * $args (array) - args: adzone_id, device_size
	 * return bool
	 */
	public static function is_responsive($args = array())
	{
		$adzone_size = get_post_meta( $args['adzone_id'], '_adzone_size'.$args['device_size'], true);
		
		// Check if specific device settings are defined. If not return default desktop data.
		return !empty($adzone_size) ? get_post_meta( $args['adzone_id'], '_adzone_fix_size'.$args['device_size'], true) : get_post_meta( $args['adzone_id'], '_adzone_fix_size', true);
	}
	
	
	
	
	
	
	
	
	
	/**
	 * Hide adone if empty
	 *
	 * $args (array) - args: adzone_id
	 * return bool
	 */
	public static function hide_empty_adzone($args = array())
	{
		return get_post_meta( $args['adzone_id'], '_adzone_hide_empty', true);
	}


}
endif;
?>