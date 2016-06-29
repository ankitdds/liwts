<?php
class Pro_Ads_Multisite {	

	public function __construct() 
	{
		
	}

	
	
	/*
	 * Check if the plugin is network activated
	 *
	 * @access public
	 * @return bool
	*/
	public function pro_ads_plugin_is_network_activated()
	{
		$active = 0;
		
		if( is_multisite() )
		{
			if( !function_exists( 'is_plugin_active_for_network' ) )
			{
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
				// Makes sure the plugin is defined before trying to use it
			}
			 
			if( is_plugin_active_for_network( 'wppas/wppas.php' ) ) 
			{
				$active = 1;
			}
		}
		
		return $active;
	}
	
	
	
	
	
	
	
	/*
	 * Check if specific admin data has to loaded.
	 *
	 * @access public
	 * @return bool
	*/
	public function pro_ads_load_admin_data()
	{
		$visible = 0;
		
		if( is_multisite() && $this->pro_ads_plugin_is_network_activated() && is_main_site() || is_multisite() && !$this->pro_ads_plugin_is_network_activated() || !is_multisite() )
		{
			$visible = 1;
		}
		
		return $visible;
	}
	
	
	
	
	
	/*
	 * MULTISITE get data from main site using set_blog_id() or switch_to_blog()
	 *
	 * @access public
	 * @return null
	*/
	public function wpproads_wpmu_load_from_main_start()
	{	
		if( $this->pro_ads_plugin_is_network_activated() && !is_main_site() )
		{
			//global $wpdb;
			
			switch_to_blog( BLOG_ID_CURRENT_SITE );
			// $wpdb->set_blog_id( BLOG_ID_CURRENT_SITE );
		}
	}
	
	
	/*
	 * MULTISITE get data from main site using set_blog_id() or switch_to_blog()
	 *
	 * @access public
	 * @return null
	*/
	public function wpproads_wpmu_load_from_main_stop()
	{	
		if( $this->pro_ads_plugin_is_network_activated() && is_main_site() )
		{
			//global $wpdb;
			restore_current_blog();	
		}
	}
	
	
	
	
	
	
	
	/*
	 * Load site option - get_option() - for multisite installations.
	 *
	 * @access public
	 * @return array/string
	*/
	public function wpproads_get_option( $name, $value = '' )
	{
		global $wpdb;
		
		if( $this->pro_ads_plugin_is_network_activated() )
		{
			$option = get_site_option($name, $value);
		}
		else
		{
			$option = get_option($name, $value);
		}
		
		return $option;
	}
	
	
	
	
	
	
	
	/*
	 * Load site option - get_option() - for multisite installations.
	 *
	 * @access public
	 * @return null
	*/
	public function wpproads_update_option( $name, $value = '' )
	{
		global $wpdb;
		
		update_option($name, $value);
		
		if( $this->pro_ads_plugin_is_network_activated() && is_main_site() )
		{
			update_site_option($name, $value);
		}
	}
	
	
	
	
	
	
	/*
	 * Load site option - get_option() - for multisite installations.
	 *
	 * @access public
	 * @return string
	*/
	public function wpproads_do_shortcode( $shortcode )
	{
		global $wpdb;
		
		//$this->pro_ads_plugin_is_network_activated() ? $wpdb->set_blog_id( BLOG_ID_CURRENT_SITE ) : '';
		$this->wpproads_wpmu_load_from_main_start();
		$value = do_shortcode($shortcode);
		$this->wpproads_wpmu_load_from_main_stop();
		//$this->pro_ads_plugin_is_network_activated() ? $wpdb->set_blog_id( get_current_blog_id() ) : '';
		
		return $value;
	}
	
	
	
	
	
	
	/*
	 * Load site url.
	 *
	 * @access public
	 * @return string
	*/
	public function wpproads_get_site_url()
	{	
		$url = is_multisite() && $this->pro_ads_plugin_is_network_activated() ? get_site_url( BLOG_ID_CURRENT_SITE ) : get_site_url( get_current_blog_id() );
		
		return $url;
	}
	
	
	
	
	
	/*
	 * Database Prefix
	 *
	 * @access public
	 * @return string
	*/
	public function wpproads_db_prefix()
	{
		global $wpdb;
		
		if ( $this->pro_ads_plugin_is_network_activated() ) 
		{ 
			$db_prefix = $wpdb->get_blog_prefix( BLOG_ID_CURRENT_SITE ); 
		}
		else
		{
			$db_prefix = $wpdb->prefix;
		}
		
		return $db_prefix;
	}
	
}
?>