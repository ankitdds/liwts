<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Pro_Ads_Updates' ) ) :


class Pro_Ads_Updates {	
	
	
	

	public function __construct() 
	{
		
	}
	
	
	
	
	
	
	public function pro_ads_updates()
	{
		global $pro_ads_init;
		
		$pas_version = get_option( 'pro_ad_system_version', 0 );
		
		if( $pas_version != PAS()->version )
		{
			//$pro_ads_init->create_tables();
			update_option( 'pro_ad_system_version', PAS()->version );
			
			// V4.6.15 statistics database upgrade.
			if( $pas_version < '4.6.15' )
			{
				$pro_ads_init->create_tables();
			}
			
			//V4.0.6 post meta update
			if( $pas_version < '4.0.6' && PAS()->version >= '4.0.6')
			{
				global $pro_ads_advertisers, $pro_ads_campaigns, $pro_ads_banners, $pro_ads_adzones;
				
				$wpproads_new_postmeta = get_option('_wpproads_new_postmeta', 0);
				
				if( !$wpproads_new_postmeta ){	
				
					// Advertisers post meta
					$apmarr = array(
						'proad_advertiser_email',
						'proad_advertiser_wpuser'
					);
					
					$advertisers = $pro_ads_advertisers->get_advertisers();
					if( !empty($advertisers))
					{
						foreach( $advertisers as $advertiser )
						{
							update_post_meta( $advertiser->ID, '_proad_advertiser_email', '');
							update_post_meta( $advertiser->ID, '_proad_advertiser_wpuser', '');
							$meta_values = get_post_meta( $advertiser->ID );
							if( !empty($meta_values))
							{
								foreach( $meta_values as $key => $meta_value )
								{
									if(in_array($key, $apmarr)) 
									{
										update_post_meta( $advertiser->ID, '_'.$key, $meta_value[0]);
										delete_post_meta( $advertiser->ID, $key);
									}
								}
							}
						}
					}
					
					// Campaigns post meta
					$cpmarr = array(
						'campaign_advertiser_id',
						'campaign_start_date',
						'campaign_end_date',
						'campaign_status'
					);
					
					$campaigns = $pro_ads_campaigns->get_campaigns();
					if( !empty($campaigns))
					{
						foreach( $campaigns as $campaign )
						{
							update_post_meta( $campaign->ID, '_campaign_advertiser_id', '');
							update_post_meta( $campaign->ID, '_campaign_start_date', '');
							update_post_meta( $campaign->ID, '_campaign_end_date', '');
							update_post_meta( $campaign->ID, '_campaign_status', '');
							$meta_values = get_post_meta( $campaign->ID );
							if( !empty($meta_values))
							{
								foreach( $meta_values as $key => $meta_value )
								{
									if(in_array($key, $cpmarr)) 
									{
										update_post_meta( $campaign->ID, '_'.$key, $meta_value[0]);
										delete_post_meta( $campaign->ID, $key);
									}
								}
							}
						}
					}
					
					// Banners post meta
					$bpmarr = array(
						'banner_advertiser_id',
						'banner_campaign_id',
						'banner_url',
						'banner_html',
						'banner_link',
						'banner_target',
						'banner_status',
						'banner_type',
						'banner_size',
						'banner_no_follow',
						'banner_contract',
						'banner_duration',
						'banner_start_date',
						'linked_adzones',
						'banner_impressions',
						'banner_clicks'
					);
					
					$banners = $pro_ads_banners->get_banners();
					if( !empty($banners))
					{
						foreach( $banners as $banner )
						{
							update_post_meta( $banner->ID, '_banner_advertiser_id', '');
							update_post_meta( $banner->ID, '_banner_campaign_id', '');
							update_post_meta( $banner->ID, '_banner_url', '');
							update_post_meta( $banner->ID, '_banner_html', '');
							update_post_meta( $banner->ID, '_banner_link', '');
							update_post_meta( $banner->ID, '_banner_target', '');
							update_post_meta( $banner->ID, '_banner_status', '');
							update_post_meta( $banner->ID, '_banner_no_follow', '');
							update_post_meta( $banner->ID, '_banner_contract', '');
							update_post_meta( $banner->ID, '_banner_duration', '');
							update_post_meta( $banner->ID, '_banner_start_date', '');
							update_post_meta( $banner->ID, '_linked_adzones', '');
							update_post_meta( $banner->ID, '_banner_impressions', '');
							update_post_meta( $banner->ID, '_banner_clicks', '');
							$meta_values = get_post_meta( $banner->ID );
							if( !empty($meta_values ))
							{
								foreach( $meta_values as $key => $meta_value )
								{
									if(in_array($key, $bpmarr)) 
									{
										if( $key == 'linked_adzones' )
										{
											$mv =  unserialize($meta_value[0]);
											foreach( $mv as $meta_val )
											{
												$pro_ads_adzones->pro_ad_link_banner_to_adzone( $meta_val, $banner->ID );
											}
										}
										else
										{
											update_post_meta( $banner->ID, '_'.$key, $meta_value[0]);
										}
										
										delete_post_meta( $banner->ID, $key);
									}
								}
							}
						}
					}
					
					// Adzones post meta
					$azpmarr = array(
						'adzone_description',
						'adzone_size',
						'adzone_custom_size',
						'adzone_responsive',
						'adzone_rotation',
						'adzone_rotation_time',
						'adzone_rotation_effect',
						'adzone_grid_horizontal',
						'adzone_grid_vertical',
						'adzone_max_banners',
						'adzone_center',
						'adzone_hide_empty',
						'linked_banners'
					);
					
					$adzones = $pro_ads_adzones->get_adzones();
					if( !empty($adzones))
					{
						foreach( $adzones as $adzone )
						{
							update_post_meta( $adzone->ID, '_adzone_description', '');
							update_post_meta( $adzone->ID, '_adzone_size', '');
							update_post_meta( $adzone->ID, '_adzone_responsive', '');
							update_post_meta( $adzone->ID, '_adzone_rotation', '');
							update_post_meta( $adzone->ID, '_adzone_rotation_time', '');
							update_post_meta( $adzone->ID, '_adzone_rotation_effect', '');
							update_post_meta( $adzone->ID, '_adzone_grid_horizontal', '');
							update_post_meta( $adzone->ID, '_adzone_grid_vertical', '');
							update_post_meta( $adzone->ID, '_adzone_max_banners', '');
							update_post_meta( $adzone->ID, '_adzone_center', '');
							update_post_meta( $adzone->ID, '_adzone_hide_empty', '');
							update_post_meta( $adzone->ID, '_linked_banners', '');
							$meta_values = get_post_meta( $adzone->ID );
							if( !empty($meta_values))
							{
								foreach( $meta_values as $key => $meta_value )
								{
									if(in_array($key, $azpmarr)) 
									{
										if( $key == 'linked_banners' )
										{
											$mv =  unserialize($meta_value[0]);
											foreach( $mv as $meta_val )
											{
												$pro_ads_adzones->pro_ad_link_banner_to_adzone( $adzone->ID, $meta_val );
											}
										}
										else
										{
											update_post_meta( $adzone->ID, '_'.$key, $meta_value[0]);
										}
										delete_post_meta( $adzone->ID, $key);
									}
								}
							}
						}
					}
					
					update_option('_wpproads_new_postmeta', 1);
				}
			}
		}
	}
	
	
	
	
}

endif;
?>