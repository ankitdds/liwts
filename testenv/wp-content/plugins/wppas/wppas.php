<?php
/**
 * Plugin Name: WP Pro Ad System
 * Plugin URI: http://wordpress-advertising.com
 * Description: Plugin to manage advertisements on your website. Beautifully, Easily and Professional.
 * Version: 4.6.19
 * Author: Tunafish
 * Author URI: http://www.tunasite.com
 * Requires at least: 3.8
 * Tested up to: 4.2
 *
 * Text Domain: wpproads
 * Domain Path: /localization/
 *
 * @package Wp_Pro_Ad_System 
 * @category Core
 * @author Tunafish
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Wp_Pro_Ad_System' ) ) :

final class Wp_Pro_Ad_System 
{
	/**
	 * @var string
	 */
	public $version = '4.6.19';
		
	/**
	 * @var string
	 */
	public $plugin_str = 'WP PRO Advertising System - All In One Ad Manager';
	

	/**
	 * @var int
	 */
	public $envato_id = 269693;
	
	
	/**
	 * @var string
	 */
	public $lang_str = 'wpproads';
	
	/**
	 * @var array
	 */
	public $cpts = array('advertisers', 'campaigns', 'banners', 'adzones');
	
	/**
	 * @var The single instance of the class
	 */
	protected static $_instance = null;
	
	
	
	
	/**
	 * Main Wp_Pro_Ad_System Instance
	 *
	 * Ensures only one instance of Wp_Pro_Ad_System is loaded or can be loaded.
	 *
	 * @since 4.0.0
	 * @static
	 * @see PAS()
	 * @return Wp_Pro_Ad_System - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	
	


	public function __construct() 
	{	
		global $pro_ads_init;
		
		// Define constants
		$this->define_constants();
		
		// Classes ------------------------------------------------------------
		require_once( WP_ADS_DIR .'classes/Pro_Ads_Init.php');
		
		
		/* ----------------------------------------------------------------
		 * Set Classes
		 * ---------------------------------------------------------------- */
		$pro_ads_init = new Pro_Ads_Init();
	}
	
	
	private function define_constants() 
	{
		define( 'WP_ADS_VERSION', $this->version );
		define( 'WP_ADS_ENVATO_ID', $this->envato_id );
		
		define( 'WP_ADS_FILE', __FILE__ );
		define( 'WP_ADS_FOLDER', str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));
		
		//define( 'WP_ADS_URL', plugins_url().'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) );
		//define( 'WP_ADS_URL', plugins_url(str_replace(basename( __FILE__),"",plugin_basename(__FILE__))) );
		define( 'WP_ADS_URL', plugins_url( WP_ADS_FOLDER, dirname(__FILE__) ) );
		
		//define( 'WP_ADS_DIR', WP_PLUGIN_DIR. '/' .str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) );
		define( 'WP_ADS_DIR', plugin_dir_path( __FILE__ ) );
		
		define( 'WP_ADS_INC_URL', WP_ADS_URL. 'includes' );
		define( 'WP_ADS_INC_DIR', WP_ADS_DIR. 'includes' );
		define( 'WP_ADS_TPL_URL', WP_ADS_URL. 'templates' );
		define( 'WP_ADS_TPL_DIR', WP_ADS_DIR. 'templates' );
		define( 'WP_ADS_PLUGIN_SLUG', basename(dirname(__FILE__)) );
		
		define( 'WP_ADS_ROLE_SUPERADMIN', 'manage_network_users' );
		define( 'WP_ADS_ROLE_ADMIN', 'remove_users' );
		define( 'WP_ADS_ROLE_USER', 'read' );
		
		// Made this load faster then init to translate custom post types @since v4.3.2
		load_plugin_textdomain( 'wpproads', false, plugin_basename( dirname( __FILE__ ) ) . '/localization' );
	}
}

endif;


/**
 * Returns the main instance of PAS to prevent the need to use globals.
 *
 * @since  4.0.0
 * @return Wp_pro_ad_system
 */
function PAS() {
	return Wp_Pro_Ad_System::instance();
}

PAS();
?>