<?php
class Pro_Ads_Post_Control {	

	public function __construct() 
	{
		add_filter('the_content', array($this, 'update_content'));
		add_action( 'woocommerce_before_single_product', array($this, 'woo_before_product_banners'), 10, 2);
		add_action( 'woocommerce_after_single_product', array($this, 'woo_after_product_banners'), 10, 2);
	}
	
	
	
	
	
	public function wpproads_load_post_ads()
	{
		global $wpdb, $pro_ads_main, $pro_ads_multisite;	
		
		$wpproads_post_ads_top = $pro_ads_multisite->wpproads_get_option('wpproads_post_ads_top', '');
		$wpproads_post_ads_center = $pro_ads_multisite->wpproads_get_option('wpproads_post_ads_center', '');
		$wpproads_post_ads_center_align = $pro_ads_multisite->wpproads_get_option('wpproads_post_ads_center_align', '');
		$wpproads_post_ads_bottom = $pro_ads_multisite->wpproads_get_option('wpproads_post_ads_bottom', '');
		
		$top_ad = !empty($wpproads_post_ads_top) ? $pro_ads_multisite->wpproads_do_shortcode("[pro_ad_display_adzone id=".$wpproads_post_ads_top." class='top_pas']") : '';
		$center_ad = !empty($wpproads_post_ads_center) ? $pro_ads_multisite->wpproads_do_shortcode("[pro_ad_display_adzone id=".$wpproads_post_ads_center." class='center_pas' align='".$wpproads_post_ads_center_align."']") : '';
		$bottom_ad = !empty($wpproads_post_ads_bottom) ? $pro_ads_multisite->wpproads_do_shortcode("[pro_ad_display_adzone id=".$wpproads_post_ads_bottom." class='bottom_pas']") : '';
		
		return array('top_ad' => $top_ad, 'center_ad' => $center_ad, 'bottom_ad' => $bottom_ad);	
	}
	
	
	
	

	
	/*
	 * Add ads to posts if necessary
	 *
	 * @access public
	 * @return array
	*/
	public function update_content($content) 
	{
		global $wpdb, $pro_ads_main, $pro_ads_multisite;	
		
		$wpproads_enable_post_ads = $pro_ads_multisite->wpproads_get_option( 'wpproads_enable_post_ads', 0 );
		$hide_ads_for_this_post = stripos($content, '[hide_post_ads') ? 1 : 0;
		
		if( $wpproads_enable_post_ads && !$hide_ads_for_this_post )
		{
			if(!is_single() && !is_page())
			{
				return $content;
			}
			
			global $post;
			
			$show = apply_filters( 'wpproads_show_adzone_in_post', 1, $post->ID );
			
			if( $show )
			{
				if( in_array(get_post_type( $post->ID ), $wpproads_enable_post_ads))
				{	
					$post_ads = $this->wpproads_load_post_ads();
					
					$paragraph_block = $pro_ads_multisite->wpproads_get_option( 'wpproads_post_ads_center_para', 2 );
					$content = explode ( "</p>", $content );
					$new_content = '';
					foreach( $content as $i => $cont )
					{
						if ( $i == $paragraph_block ) 
						{
							$new_content .= $post_ads['center_ad'];
						}
						$new_content .= $cont . "</p>";
					}
			
					$new_content = $post_ads['top_ad'] . $new_content . $post_ads['bottom_ad'];
					
					return $new_content;
				}
				else
				{
					return $content;
				}
			}
			else
			{
				return $content;
			}
		}
		else
		{
			return $content;
		}
	}
	
	
	
	
	
	/*
	 * Add ads to WooCommerce Products if necessary
	 *
	 * @access public
	 * @return array
	*/
	// TOP BANNER
	function woo_before_product_banners()
	{
		global $pro_ads_main, $pro_ads_multisite, $post;
		
		$wpproads_enable_post_ads = $pro_ads_multisite->wpproads_get_option( 'wpproads_enable_post_ads', 0 );
		if( $wpproads_enable_post_ads )
		{
			if( in_array(get_post_type( $post->ID ), $wpproads_enable_post_ads))
			{
				$post_ads = $this->wpproads_load_post_ads();
				echo $post_ads['top_ad'];
				echo '<div style="clear:both;"></div>';	
			}
		}
	}
	// BOTTOM BANNER
	function woo_after_product_banners()
	{
		global $pro_ads_main, $pro_ads_multisite, $post;
		
		$wpproads_enable_post_ads = $pro_ads_multisite->wpproads_get_option( 'wpproads_enable_post_ads', 0 );
		if( $wpproads_enable_post_ads )
		{
			if( in_array(get_post_type( $post->ID ), $wpproads_enable_post_ads))
			{
				echo '<div style="clear:both;"></div>';	
				$post_ads = $this->wpproads_load_post_ads();
				echo $post_ads['bottom_ad'];
			}
		}
	}
}
?>