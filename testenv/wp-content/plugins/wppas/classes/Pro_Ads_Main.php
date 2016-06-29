<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Pro_Ads_Main' ) ) :

class Pro_Ads_Main {	


	//public $IP = '127.0.0.1';
	public $IP = false;
	
	
	
	public function __construct() 
	{
		$this->get_visitor_ip();
		
		add_action( 'wp_loaded', array( $this, 'wpproads_rss' ) );	
		add_action( 'wp_loaded', array( $this, 'wpproads_html5' ) );	
	}
	
	
	
	
	/*
	 * Returns the date by the sites selected timezone.
	 *
	 * https://codex.wordpress.org/Function_Reference/current_time
	 *
	 * @return time
	*/
	public function time_by_timezone( $type = 'timestamp' ) 
	{
		return current_time($type);
		
	}
	
	
	
	
	/*
	 * Outputs or returns the debug marker.
	 *
	 * @param bool $echo Whether or not to echo the debug marker.
	 *
	 * @return string
	*/
	public function debug_marker( $echo = true ) 
	{
		$marker = '<!-- Ads on this site are served by ' . PAS()->plugin_str . ' v' . PAS()->version . ' - wordpress-advertising.com -->';
		if ( $echo === false ) {
			return $marker;
		}
		else {
			echo "\n${marker}\n";
		}
	}
	
	
	
	
	
	/*
	 * Get Domain from url
	 *
	 * @access public
	 * @return host
	*/
	public function get_domain_url( $url )
	{
		if( !empty($url))
		{
			$parse = parse_url($url);
			return $parse['host'];
		}
	}
	
	
	
	
	/*
	 * Get Visitor IP
	 *
	 * @access public
	 * @return IP
	*/
	public function get_visitor_ip() {
	
		// Check to see if we've already retrieved the IP address and if so return the last result.
		if( $this->IP !== FALSE ) { return $this->IP; }
		// Check if cronjob is running
		$sapi_type = php_sapi_name();
		if(substr($sapi_type, 0, 3) == 'cli') { return $this->IP; }
	
		// By default we use the remote address the server has.
		$temp_ip = $_SERVER['REMOTE_ADDR'];
	
		// Check to see if any of the HTTP headers are set to identify the remote user.
		// These often give better results as they can identify the remote user even through firewalls etc, 
		// but are sometimes used in SQL injection attacks.
		if (getenv('HTTP_CLIENT_IP')) {
			$temp_ip = getenv('HTTP_CLIENT_IP');
		} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
			$temp_ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('HTTP_X_FORWARDED')) {
			$temp_ip = getenv('HTTP_X_FORWARDED');
		} elseif (getenv('HTTP_FORWARDED_FOR')) {
			$temp_ip = getenv('HTTP_FORWARDED_FOR');
		} elseif (getenv('HTTP_FORWARDED')) {
			$temp_ip = getenv('HTTP_FORWARDED');
		} 

		// Trim off any port values that exist.
		if( strstr( $temp_ip, ':' ) !== FALSE ) {
			$temp_a = explode(':', $temp_ip);
			$temp_ip = $temp_a[0];
		}
		
		// Check to make sure the http header is actually an IP address and not some kind of SQL injection attack.
		$long = ip2long($temp_ip);
	
		// ip2long returns either -1 or FALSE if it is not a valid IP address depending on the PHP version, so check for both.
		if($long == -1 || $long === FALSE) {
			// If the headers are invalid, use the server variable which should be good always.
			$temp_ip = $_SERVER['REMOTE_ADDR'];
		}

		// If the ip address is blank, use 127.0.0.1 (aka localhost).
		if( $temp_ip == '' ) { $temp_ip = '127.0.0.1'; }
		
		$this->IP = $temp_ip;
		
		return $this->IP;
	}
	/*
	public function get_visitor_ip()
	{
		// Get the visitor IP    
        if(isset($_SERVER['HTTP_X_FORWARD_FOR']))
        {
            $this->IP = $_SERVER['HTTP_X_FORWARD_FOR'];
        } 
		else 
		{
            $this->IP = $_SERVER['REMOTE_ADDR'];
		}	
	}*/
	
	
	
	
	
	
	/*
	 * Get Visitor Device
	 *
	 * @since v4.2.9
	 * @access public
	 * @return string
	*/
	public function get_visitor_device()
	{
		global $pro_ads_browser;
		
		$device = 'desktop';
		
		if( $pro_ads_browser->isMobile() )
		{
			$device = 'mobile';
		}
		elseif( $pro_ads_browser->isTablet() )
		{
			$device = 'tablet';
		}
		
		return $device;
	}
	
	
	
	
	
	
	/*
	 * Get User Data
	 *
	 * @since v4.6.9
	 * @access public
	 * @return array
	*/
	public function get_user_data( $empty = 0 )
	{
		global $pro_ads_browser;
		
		$geo = $this->get_geo_info();
		
		if( !$empty)
		{
			$userdata = array(
				'geo'            => $geo,
				'geo_city'       => $geo['city'],
				'geo_country'    => $geo['country'],
				'geo_country_cd' => $geo['country_cd'],
				'browser'        => $pro_ads_browser->getBrowser(),
				'platform'       => $pro_ads_browser->getPlatform(),
				'device'         => $this->get_visitor_device()
			);
		}
		else
		{
			$userdata = array(
				'geo'            => '',
				'geo_city'       => '',
				'geo_country'    => '',
				'geo_country_cd' => '',
				'browser'        => '',
				'platform'       => '',
				'device'         => ''
			);
		}
		
		return $userdata;
	}
	
	
	
	
	
	
	/*
	 * daily update function
	 *
	 * @access public
	 * @return array
	*/
	public function daily_updates( $force = 0) 
	{	
		global $wpdb, $pro_ads_campaigns, $pro_ads_banners;
		
		/**
		 * HOURLY UPDATES
		 */
		// Update campaigns with specific timing.
		$last_hour_update = get_option( 'wpproads_hourly_update', 0 );
		//$time = strtotime('-1 hour');
		$hour = mktime(date("H"), 0, 0);
		
		if( $last_hour_update < $hour || $force )
		{
			if(!defined('DOING_AJAX')) {
				$pro_ads_campaigns->update_campaign_status( array('meta_compare' => '!=', 'meta_key' => '_campaign_timing_start', 'meta_value' => ''));
			}
			
			update_option( 'wpproads_hourly_update', $hour );
		}
		
		/**
		 * DAILY UPDATES
		 */
		// Update daily options. This happens only once a day.
		$last_update = get_option( 'wpproads_daily_update', 0 );
		$today = date('Y').date('m').date('d');
		
		if( $last_update < $today || $force )
		{
			$pro_ads_campaigns->update_campaign_status();
			
			$banners = $pro_ads_banners->get_banners( 
				array(
					'meta_key'       => '_banner_contract',
					'meta_value'     => 3
				)
			);
			
			foreach( $banners as $banner )
			{
				$pro_ads_banners->update_banner_status( $banner->ID );
			}
				
			update_option( 'wpproads_daily_update', $today );
			
			// Remove outdated statistics
			$wpproads_stats_save_days = get_option('wpproads_stats_save_days', '');
			if( !empty($wpproads_stats_save_days) )
			{
				$outdate = strtotime(date('Y-m-d H:i:s') . ' -'.$wpproads_stats_save_days.' days');
				$wpdb->query( "DELETE FROM " . $wpdb->prefix . "pro_ad_system_stats WHERE date < ".$outdate.";" );
				$wpdb->query( "DELETE FROM " . $wpdb->prefix . "wpproads_user_stats WHERE date < ".$outdate.";" );
			}
		}
	}
	
	
	
	
	
	
	/*
	 * Check if ADD_ON Buy and Sell ads is active
	 *
	 * @access public
	 * @return array
	*/
	public function buyandsell_is_active() 
	{
		global $pro_ads_bs_templates;
		
		if( method_exists( $pro_ads_bs_templates, 'buyandsell_placeholder' ) )
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	
	
	/*
	 * Check if ADD_ON Buy and Sell Woocommerce ads is active
	 *
	 * @access public
	 * @return array
	*/
	public function buyandsell_woo_is_active() 
	{
		global $pro_ads_bs_woo_templates;
		
		if( method_exists( $pro_ads_bs_woo_templates, 'buyandsell_placeholder' ) )
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	
	
	
	/*
	 * Check if ADD_ON Geo Targetting
	 *
	 * @access public
	 * @return bool
	*/
	public function pro_geo_targeting_is_active() 
	{
		global $pro_geo_targeting;
		
		if( method_exists( $pro_geo_targeting, 'get_user_geo_data' ) )
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	
	
	
	
	
	
	
	/*
	 * Geo Info - get city and country
	 *
	 * @access public
	 * @return array
	*/
	public function get_geo_info() 
	{
		// Check if the Geo Targeting Add-On is installed.
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		if( !is_plugin_active( 'wp_pro_geo_targeting/wp_pro_geo_targeting.php' ) )
		{
			// Since V4.6.0 this option is only available with the WP PRO Geo Targeting plugin activated.
			$geo = array('city' => '', 'country' => '', 'country_cd' => '', 'continent_cd' => '');
		}
		else
		{
			global $pro_geo_targeting;
			
			$geo = $pro_geo_targeting->get_visitor_data();
		}
		
		return $geo;
	}
	
	
	
	
	
	
	/*
	 * Detect search engine bots
	 *
	 * @access public
	 * @return int $isbot
	*/
	public function detect_bots()
	{
		include(WP_ADS_INC_DIR.'/robot_list.php');
		
		$isbot = 0;
		$ua_string = '';
		
		if( array_key_exists('HTTP_USER_AGENT', $_SERVER) ) {
			$ua_string = $_SERVER['HTTP_USER_AGENT'];
		}
		
		foreach( $wppas_robot_array as $i => $bot )
		{
			if(stripos($ua_string, trim($bot)) !== FALSE) 
			{
				$isbot = 1;
				return $isbot;
				break;
			}
		}
		
		return $isbot;
	}
	
	
	
	
	
	
	
	/*
	 * Adzone RSS feed
	 *
	 * @access public
	 * @return rss
	*/
	public function wpproads_rss( $adzone_id = 0 )
	{
		global $pro_ads_main, $pro_ads_adzones, $pro_ads_shortcodes;
		
		if( !empty( $adzone_id ) || isset( $_GET['wpproads-rss'] ) && !empty( $_GET['wpproads-rss'] ) )
		{
			$html = '';
			$adzoneID = !empty( $adzone_id ) ? $adzone_id : $_GET['wpproads-rss'];
			$atts = $pro_ads_shortcodes->default_atts($adzoneID);
			
			// http://kb.mailchimp.com/merge-tags/rss-blog/rss-item-tags
			// Mailchimp RSS code
			// *|RSSITEMS:|* *|RSSITEM:CONTENT_FULL|* *|END:RSSITEMS|*
			
			header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
			
			$html.= '<?xml version="1.0" encoding="UTF-8"?>';
			$html.= '<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" version="2.0">';	
				$html.= '<channel>';
					$html.= '<title>'.get_bloginfo('name').'</title>';
					$html.= '<atom:link href="'.get_bloginfo('url').'/?wpproads-rss='.$adzoneID.'" rel="self" type="application/rss+xml" />';
					$html.= '<link>'.get_bloginfo('url').'</link>';
					$html.= '<description><![CDATA['.get_bloginfo('description').']]></description>';
					$html.= '<lastBuildDate>'.date('r', $pro_ads_main->time_by_timezone()).'</lastBuildDate>';
					$html.= '<language>'.get_bloginfo('language').'</language>';
					$html.= '<generator>http://wordpress-advertising.com/?v='.WP_ADS_VERSION.'</generator>';
					
					$adzone = $pro_ads_adzones->display_adzone( $adzoneID, $atts );
						
					$html.= '<item>';
						$html.= '<title>Adzone</title>';
						$html.= '<link>'.get_bloginfo('url').'</link>';
						$html.= '<guid isPermaLink="false">'.get_bloginfo('url').'/?wpproads-rss='.$adzoneID.'</guid>';
						$html.= '<description><![CDATA[ '.get_the_title( $adzoneID ).' ]]></description>';
						$html.= '<content:encoded><![CDATA['.$adzone['html'].']]></content:encoded>';
						$html.= '<pubDate>'.date('r', $pro_ads_main->time_by_timezone()).'</pubDate>';
					$html.= '</item>';
	
				$html.= '</channel>';
			$html.= '</rss>';
			
			echo $html;
			
			exit();
		}
	}
	
	
	/*
	 * Iframe HTML5 Banners
	 *
	 * @access public
	 * @return rss
	*/
	public function wpproads_html5()
	{
		global $pro_ads_responsive;
		
		
		if( isset( $_GET['wpproads-html5'] ) && !empty( $_GET['wpproads-html5'] ) )
		{
			$html = '';
			$device = $pro_ads_responsive->get_device_type();
			$banner_html = get_post_meta( $_GET['wpproads-html5'], '_banner_html'.$device['prefix'], true );
			$html.= do_shortcode($banner_html);
			
			echo $html;
			exit();
		}
		
		
	}
	
	
	
	
	
	
	/*
	 * Return available default banner sizes.
	 *
	 * @access public
	 * @return array
	*/
	public function available_banner_sizes( $full = 1 )
	{
		if($full )
		{
			$banner_sizes = array(
				array('size' => '468x60', 'name' => '468 x 60'),
				array('size' => '120x600', 'name' => '120 x 600'),
				array('size' => '728x90', 'name' => '728 x 90'),
				array('size' => '300x250', 'name' => '300 x 250'),
				array('size' => '120x90', 'name' => '120 x 90'),
				array('size' => '160x600', 'name' => '160 x 60'),
				array('size' => '120x60', 'name' => '120 x 60'),
				array('size' => '125x125', 'name' => '125 x 125'),
				array('size' => '180x150',  'name' => '180 x 150')
			);
		}
		else
		{
			$banner_sizes = array('468x60','120x600','728x90','300x250','120x90','160x600','120x60','125x125','180x150');
		}
		
		return $banner_sizes;
	}	
	
	
	
	
	
	
	
	/*
	 * Matches each symbol of PHP date format standard
	 * with jQuery equivalent codeword
	 * @author Tristan Jahier
	 * http://stackoverflow.com/questions/16702398/convert-a-php-date-format-to-a-jqueryui-datepicker-date-format
	*/
	public function dateformat_PHP_to_jQueryUI($php_format)
	{
		$SYMBOLS_MATCHING = array(
			// Day
			'd' => 'dd',
			'D' => 'D',
			'j' => 'd',
			'l' => 'DD',
			'N' => '',
			'S' => '',
			'w' => '',
			'z' => 'o',
			// Week
			'W' => '',
			// Month
			'F' => 'MM',
			'm' => 'mm',
			'M' => 'M',
			'n' => 'm',
			't' => '',
			// Year
			'L' => '',
			'o' => '',
			'Y' => 'yy',
			'y' => 'y',
			// Time
			'a' => '',
			'A' => '',
			'B' => '',
			'g' => '',
			'G' => '',
			'h' => '',
			'H' => '',
			'i' => '',
			's' => '',
			'u' => ''
		);
		$jqueryui_format = "";
		$escaping = false;
		for($i = 0; $i < strlen($php_format); $i++)
		{
			$char = $php_format[$i];
			if($char === '\\') // PHP date format escaping character
			{
				$i++;
				if($escaping) $jqueryui_format .= $php_format[$i];
				else $jqueryui_format .= '\'' . $php_format[$i];
				$escaping = true;
			}
			else
			{
				if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
				if(isset($SYMBOLS_MATCHING[$char]))
					$jqueryui_format .= $SYMBOLS_MATCHING[$char];
				else
					$jqueryui_format .= $char;
			}
		}
		return $jqueryui_format;
	}
	
	
	
	
	
	
	
	/*
	 * Plugin registration
	 *
	 * @access public
	 * @return array
	*/
	public function PLU_registration( $key )
	{
		global $wp_version;
		
		$wpproads_license_key = get_option('wpproads_license_key', '');
			
		$request_string = array(
			'body' => array(
				'action'      => 'register', 
				'envato_id'   => WP_ADS_ENVATO_ID,
				'item_slug'   => WP_ADS_PLUGIN_SLUG,
				'license-key' => $key,
				'api-key'     => md5(get_bloginfo('url')),
				'url'         => get_bloginfo('url'),
				'email'       => get_bloginfo('admin_email'),
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);
		
        $request = wp_remote_post('http://tunasite.com/updates/?plu-plugin=ajax-handler', $request_string);
		
		if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            return $request['body'];
        }
        return false;
	}
	
}
endif;