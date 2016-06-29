<?php
/**
 * Codex related functions and actions.
 *
 * @author 		Tunafish
 * @package 	wp_pro_ad_system/classes
 * @version     4.4.6
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Pro_Ads_Codex' ) ) :


class Pro_Ads_Codex {	
	
	

	public function __construct() 
	{
		
	}
	
	
	
	
	
	/*
	 * Load adzone data array
	 *
	 * @access public
	 * @return array
	*/
	public function wpproads_load_adzone_data( $adzone_id, $args = array() )
	{
		global $pro_ads_adzones;
		
		return $pro_ads_adzones->get_adzone_data( $adzone_id, $args = array() );
	}
	
	
	
	
	
	
	
	/*
	 * Load banners linked to an adzone
	 *
	 * @access public
	 * @return array
	*/
	public function wpproads_load_adzone_banners( $arr )
	{
		global $pro_ads_banners, $pro_ads_multisite;
		
		$banners = '';
		$orderby = $arr['orderby'];
		
		if( !empty($arr['linked_banner_ids']) )
		{	
			// Check if adzone is grid
			if( $this->wpproads_adzone_is_grid( $arr['adzone_id'] ) )
			{
				$orderby = $arr['adzone_rotation'] ? 'rand' : 'post__in';
			}
			
			$defaults = array(
				'posts_per_page' => $arr['limit'],
				'post__in'       => $arr['linked_banner_ids'], 
				//'include'       => $arr['linked_banner_ids'], // used for get_posts
				'orderby'        => $orderby, 
				'meta_key'       => '_banner_status',
				'meta_value'     => 1
				/*'meta_query'     => array(
					array(
						'key'     => '_banner_status',
						'value'   => 1,
						'compare' => '='
					)
				) */
			);
			$args = apply_filters( 'wp_pro_ads_load_adzone_banners', $defaults );
			$data = wp_parse_args( $args, $defaults );
			
			$banners = $pro_ads_banners->get_banners( $data );
		}
		
		
		
		return $banners;	
	}
	
	

	
	
	
	/*
	 * Check if campaign is active
	 *
	 * @access public
	 * @return bool
	*/
	public function wpproads_campaign_is_active( $banner_id )
	{
		global $pro_ads_multisite;
									
		$campaign_id = get_post_meta( $banner_id, '_banner_campaign_id', true );
		$campaign_status = get_post_meta( $campaign_id, '_campaign_status', true );
		
		return $campaign_status;
	}
	
	
	
	
	
	
	/*
	 * Check if adzone is grid
	 *
	 * @access public
	 * @return bool
	*/
	public function wpproads_adzone_is_grid( $adzone_id )
	{	
		$adzone_is_grid = 0;
		
		$grid_horizontal   = get_post_meta( esc_attr($adzone_id), '_adzone_grid_horizontal', true );
		$grid_vertical     = get_post_meta( esc_attr($adzone_id), '_adzone_grid_vertical', true );
		
		if( !empty($grid_horizontal) && !empty($grid_vertical) )
		{
			$adzone_is_grid = 1;
		}
		
		return $adzone_is_grid;
	}
	
	
	
	
	
	
	/*
	 * Join banner and adzone html
	 *
	 * @access public
	 * @return bool
	*/
	public function wpproads_join_adzone_html( $adzone_str, $banner_str )
	{
		return str_replace('%s%', $banner_str, $adzone_str);
	}
	
	
	
	
	/*
	 * Load Statistics
	 * @since v4.4.9
	 *
	 * Query options:
	 *
	 * advertiser_id, campaign_id, banner_id, adzone_id
	 * date (timestamp), time (timestamp)
	 * type (impression|click)
	 * ip_address
	 *
	 * @access public
	 * @return array
	*/
	public function wpproads_load_statistics( $args = array() )
	{
		global $pro_ads_statistics;
		
		$defaults = array(
			'query' => ''
		);
		$data = wp_parse_args( $args, $defaults );
		
		return $pro_ads_statistics->load_statistics( $data['query'] );
	}
	
}
endif;
?>