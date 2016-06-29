<?php
class Pro_Ads_Campaigns {	

	public function __construct() 
	{	
		
	}
	
	
	/*
	 * Get all campaigns
	 *
	 * @access public
	 * @return null
	*/
	public function get_campaigns( $custom_args = array() ) 
	{	
		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'campaigns',
			'post_status'      => 'publish'
		);
		
		//return get_posts( array_merge( $args, $custom_args ) );
		$query = new WP_Query( array_merge( $args, $custom_args ) );
		
		return $query->get_posts();
	}
	
	
	
	
	
	/*
	 * Check campaign status
	 *
	 * @access public
	 * @return array
	*/
	public function get_status( $status_nr ) 
	{	
		if( $status_nr == 1 )
		{
			$status = array( 
				'name'       => __('Running','wpproads'), 
				'name_clean' => 'running', 
			);
		}
		elseif( $status_nr == 2 )
		{
			$status = array( 
				'name'       => __('Finished','wpproads'), 
				'name_clean' => 'finished', 
			);
		}
		elseif( $status_nr == 3 )
		{
			$status = array( 
				'name'       => __('On Hold','wpproads'), 
				'name_clean' => 'on-hold', 
			);
		}
		else
		{
			$status = array( 
				'name'       => __('Draft','wpproads'), 
				'name_clean' => 'draft', 
			);
		}
		
		return $status;
	}
	
	
	
	
	
	/*
	 * Check Campaign Status
	 *
	 * 0 = draft, 1 = running, 2 = finished
	 *
	 * @access public
	 * @param int $status, string $sdate, string $edate, int $campaign_id (default: 0)
	 * @return int $status
	*/
	public function check_campaign_status( $campaign_id ) // $sdate, $edate,
	{	
		global $pro_ads_banners;
		
		$status = 1;
		$now = current_time('timestamp');
		$sdate = get_post_meta( $campaign_id, '_campaign_start_date', true );
		$edate = get_post_meta( $campaign_id, '_campaign_end_date', true );
		
		if( $now < $sdate )
		{
			$status = 0;
		}
		elseif( !empty( $edate) && $now > $edate )
		{
			$status = 2;
			
			// update banner status
			if( !empty($campaign_id))
			{
				$banners = $this->get_linked_banners( $campaign_id );
				foreach( $banners as $banner )
				{
					update_post_meta( $banner->ID, '_banner_status', 2 );
					$pro_ads_banners->remove_banner_from_adzone( $banner->ID );
				}
			}
		}
		else
		{
			$timing_start = get_post_meta( $campaign_id, '_campaign_timing_start', true );
			if( !empty($timing_start) )
			{
				$timing_end = get_post_meta( $campaign_id, '_campaign_timing_end', true );
				if(str_replace(':','',date_i18n('G:i', $now)) < str_replace(':','',$timing_start) || str_replace(':','',date_i18n('G:i', $now)) > str_replace(':','',$timing_end))
				{
					$status = 3;
				}
			}
		}
		
		return $status;
	}
	
	
	
	
	
	
	
	
	
	/*
	 * UPDATE CAMPAIGNS STATUS
	 *
	 * @access public
	 * @return array
	*/
	public function update_campaign_status( $arr = array() ) 
	{	
		$campaigns = $this->get_campaigns( $arr );
		
		if( !empty($campaigns))
		{
			foreach($campaigns as $campaign )
			{
				$status = $this->check_campaign_status( $campaign->ID );
				update_post_meta( $campaign->ID, '_campaign_status', $status );
			}
		}
	}
	
	
	
	
	/*
	 * Get linked banners for a campaign
	 *
	 * @access public
	 * @param int $id (campaign id)
	 * @return array
	*/
	public function get_linked_banners( $id )
	{
		global $pro_ads_banners;
		
		$banners = $pro_ads_banners->get_banners( 
			array(
				'meta_key'  => '_banner_campaign_id',
				'meta_value' => $id
			)
		);
		
		return $banners;
	}
	
}
?>