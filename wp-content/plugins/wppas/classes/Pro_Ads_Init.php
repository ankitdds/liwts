<?php
/**
 * Init related functions and actions.
 *
 * @author 		Tunafish
 * @package 	wp_pro_ad_system/classes
 * @version     4.0.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Pro_Ads_Init' ) ) :


class Pro_Ads_Init {	
	
	
	

	public function __construct() 
	{
		global $pro_ads_main, $pro_ads_codex, $pro_ads_cpts, $pro_ads_advertisers, $pro_ads_campaigns, $pro_ads_banners, $pro_ads_adzones, $pro_ads_statistics, $pro_ads_templates, $pro_ads_shortcodes, $pro_ads_multisite, $pro_ads_responsive, $pro_ads_browser, $pro_ad_custom_widgets, $pro_ads_updates, $pro_ads_geo_targeting, $pro_ads_banner_creator;
		
		$wpproads_stats_version = get_option('wpproads_stats_version', '_new');
		
		// Run this on activation.
		register_activation_hook( WP_ADS_FILE, array( $this, 'install' ) );
		
		// Load Functions ------------------------------------------------- 
		require_once( WP_ADS_INC_DIR .'/ajax_functions.php');
		
		// Load Classes --------------------------------------------------- 
		require_once( WP_ADS_DIR.'classes/extends/Pro_Ads_CPT_Meta_Options.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_CPTs.php');	
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Main.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Codex.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Advertisers.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Banners.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Campaigns.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Adzones.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Statistics'.$wpproads_stats_version.'.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Templates.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Shortcodes.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Multisite.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Browser.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Responsive.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Tinymce.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_RestAPI.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Api.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Custom_Widgets.php');
		
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Updates.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Vsc_Class.php');
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Post_Control.php');
		//require_once( WP_ADS_DIR.'classes/Pro_Ads_Welcome.php');
		
		/* ----------------------------------------------------------------
		 * Set Classes
		 * ---------------------------------------------------------------- */
		$pro_ads_cpt_meta_options = new Pro_Ads_CPT_Meta_Options();
		$pro_ads_cpts = new Pro_Ads_CPTs();	
		$pro_ads_main = new Pro_Ads_Main();
		$pro_ads_codex = new Pro_Ads_Codex();
		$pro_ads_advertisers = new Pro_Ads_Advertisers();
		$pro_ads_banners = new Pro_Ads_Banners();
		$pro_ads_campaigns = new Pro_Ads_Campaigns();
		$pro_ads_adzones = new Pro_Ads_Adzones();
		$pro_ads_statistics = new Pro_Ads_Statistics();
		$pro_ads_templates = new Pro_Ads_Templates();
		$pro_ads_shortcodes = new Pro_Ads_Shortcodes();
		$pro_ads_multisite = new Pro_Ads_Multisite();
		$pro_ads_browser = new Pro_Ads_Browser();
		$pro_ads_responsive = new Pro_Ads_Responsive();
		$pro_ads_tinymce = new Pro_Ads_Tinymce();
		$pro_ads_rest_api = new RestAPI();
		$pro_ads_api = new Pro_Ads_Api();
		$pro_ads_updates = new Pro_Ads_Updates();
		$pro_ads_post_control = new Pro_Ads_Post_Control();
		//$pro_ads_welcome = $pro_ads_multisite->pro_ads_load_admin_data() ? new Pro_Ads_Welcome() : '';
		$pro_ads_vsc_class = new WPPROADVSC_AddonClass();
		
		
		// Actions --------------------------------------------------------
		add_action('init', array( $this, 'init_method') );
		add_action('init', array( $this, 'custom_rewrite_tags'), 10, 0);
		add_action('admin_init', array( $this, 'admin_init_method') );
		add_action('wp_enqueue_scripts', array($this, 'wppas_enqueue_scripts') );
		add_action('wp_head', array( $this, 'add_to_head'), 1 );
		add_action('wppas_head', array( $pro_ads_main, 'debug_marker' ), 2 );
		add_action('admin_menu', array( $this,'admin_actions') );
		add_action('admin_head', array( $this, 'menu_highlight' ) );
		add_action('wp_footer', array($this, 'add_to_footer') );
		add_action('widgets_init', array($this, 'pro_ad_adzone_widgets_init'), 30 );
		add_action('admin_bar_menu', array($this, 'pro_ads_admin_bar'), 100);
		add_action('admin_notices', array($this, 'pro_ads_admin_notices') );
		add_filter('admin_footer_text', array( $this, 'wpproads_admin_footer_text' ), 1 );

		
		// Custom Tables
		if(!class_exists('WP_List_Table')){
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}
		require_once( WP_ADS_INC_DIR.'/tables'.$wpproads_stats_version.'/stats.php');
		require_once( WP_ADS_INC_DIR.'/tables'.$wpproads_stats_version.'/stats_day.php');
		require_once( WP_ADS_INC_DIR.'/tables'.$wpproads_stats_version.'/stats_year.php');
		require_once( WP_ADS_INC_DIR.'/tables'.$wpproads_stats_version.'/stats_all.php');
	}
	
	
	/**
	 * Install WPPAS
	 */
	public function install() 
	{
		global $pro_ads_updates;
		
		$this->create_tables();
		$pas_version = get_option( 'pro_ad_system_version', 0 );
		
		//$current_version = get_option( 'pro_ad_system_version', null );
		set_transient( '_pas_activation_redirect', 1, 60 * 60 );
		
		// Update Settings
		$wpproads_enable_stats = get_option('wpproads_enable_stats', 0);
		if( empty($pas_version) && empty( $wpproads_enable_stats ) ) 
		{
			update_option( 'wpproads_enable_stats', 1);
			update_option( 'wpproads_enable_userdata_stats', 1);
		}
		
		// Update version
		$pro_ads_updates->pro_ads_updates();
	}
	
	
	
	
	
	/*
	 * Init actions
	 *
	 * @access public
	 * @return null
	*/
	public function init_method() 
	{	
		global $pro_ads_bs_templates, $pro_ads_main, $pro_ads_updates, $pro_ads_statistics;
		
		define( 'WP_ADS_USER_CAN', current_user_can( WP_ADS_ROLE_USER ) && $pro_ads_main->buyandsell_is_active() || current_user_can( WP_ADS_ROLE_USER ) && $pro_ads_main->buyandsell_woo_is_active()  ? WP_ADS_ROLE_USER : $this->get_admin_role() );
		
		$pro_ads_main->daily_updates();
		$pro_ads_updates->pro_ads_updates();
		
		
		/* --------------------------------------------------------------------------------------
		 * @since v4.3.4 Rotation javascript files moved to Pro_Ads_Adzones.php -> display_adzone() and are only loaded when needed.
		 * -------------------------------------------------------------------------------------- */
		//wp_enqueue_script('wppas_jshowoff', WP_ADS_TPL_URL . '/js/jquery.jshowoff.min.js', array( 'jquery' ), false, true );
		//wp_enqueue_script('wppas_flexslider', WP_ADS_TPL_URL . '/js/jquery.flexslider.min.js', array( 'jquery' ), false, true );
		//wp_enqueue_script('wppas_bxslider',  WP_ADS_TPL_URL . '/js/jquery.bxslider.min.pas.js', array( 'jquery' ), false, true );
		
		/* --------------------------------------------------------------------------------------
		 * @since v4.3.4 Stylesheet and Main javascript files are moved to Pro_Ads_Shortcodes.php and is only loaded when needed. Stylesheet also gets loaded in the admin area.
		 * -------------------------------------------------------------------------------------- */
		// wp_enqueue_style("wp_pro_ads_style", WP_ADS_TPL_URL."/css/wpproads.min.css", false, WP_ADS_VERSION, "all");
		// wp_enqueue_script('wp_pro_ads_js_functions', WP_ADS_TPL_URL.'/js/wppas.min.js');
		
		/* --------------------------------------------------------------------------------------
		 * @since v4.3.4 File moved to Pro_Ads_Adzones.php -> display_adzone_as_corner_curl() and is only loaded when needed.
		 * -------------------------------------------------------------------------------------- */
		// wp_enqueue_script('wp_pro_ads_corncurl', WP_ADS_TPL_URL.'/js/corncurl.min.js');
	}
	
	
	
	
	
	/*
	 * Admin Init actions
	 *
	 * @access public
	 * @return null
	*/
	public function admin_init_method()
	{
		/* -------------------------------------------------------------------------------------------------
		 * Code to activate the auto updater.
		 * ------------------------------------------------------------------------------------------------- */
		$wpproads_license_key = get_option('wpproads_license_key', '');
		//set_site_transient('update_plugins', null);
		require( WP_ADS_DIR.'/classes/PLU_Auto_Plugin_Updater.php');
		$api_url = 'http://tunasite.com/updates/?plu-plugin=ajax-handler';
		// current plugin version | remote url | Plugin Slug (plugin_directory/plugin_file.php) | users envato license key (default: '') | envato item ID (default: '')
		new PLU_Auto_Plugin_Updater(WP_ADS_VERSION, $api_url, WP_ADS_PLUGIN_SLUG.'/'.WP_ADS_PLUGIN_SLUG.'.php', $wpproads_license_key, WP_ADS_ENVATO_ID);
	}
	
	
	
	
	
	/*
	 * Enqueue Scripts & Styles for the Front end
	 *
	 * @access public
	 * @return null
	*/
	public function wppas_enqueue_scripts()
	{
		// Enqueue scripts
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script("jquery-effects-core");
		wp_enqueue_script("jquery-effects-shake");
		wp_enqueue_script('wppas_dummy_advertising', WP_ADS_TPL_URL . '/js/advertising.js');
		//wp_enqueue_script('wppas_jquery_cookie', WP_ADS_TPL_URL . '/js/jquery.cookie.js', array( 'jquery' ), WP_ADS_VERSION, true );
		
		// Register scripts & styles for later use
		wp_register_script('wp_pro_add_js_functions', WP_ADS_TPL_URL.'/js/wppas.min.js' );
		//wp_register_script('wp_pro_add_js_functions', WP_ADS_TPL_URL.'/js/wppas.js' );
		wp_register_script('wp_pro_add_corncurl', WP_ADS_TPL_URL.'/js/corncurl.min.js');
		wp_register_script('wp_pro_add_scrolltofixed', WP_ADS_TPL_URL.'/js/jquery-scrolltofixed-min.js');
		wp_register_script('wppas_jquery_cookie', WP_ADS_TPL_URL . '/js/jquery.cookie.js' );
		wp_register_script('wppas_jshowoff', WP_ADS_TPL_URL . '/js/jquery.jshowoff.min.js', array( 'jquery' ), WP_ADS_VERSION, true );
		wp_register_script('wppas_bxslider',  WP_ADS_TPL_URL . '/js/jquery.bxslider.min.pas.js', array( 'jquery' ), WP_ADS_VERSION, true );
		wp_register_style( 'wp_pro_add_style', WP_ADS_TPL_URL."/css/wppas.min.css", false, WP_ADS_VERSION, "all" );
		
		// Localize scripts
		wp_localize_script('wp_pro_add_js_functions', 'wppas_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}
	
	
	
	
	
	/*
	 * Init Custom rewrite tags
	 *
	 * @access public
	 * @return null
	 *
	 * Usage:
	 * global $wp_query;
	 * $wp_query->query_vars['pasID'];
	*/
	public function custom_rewrite_tags() 
	{
		add_rewrite_tag('%pasID%', '([^&]+)'); // //([0-9]+)
		add_rewrite_tag('%pasSLUG%', '([^&]+)');
		add_rewrite_tag('%pasZONE%', '([^&]+)');
		add_rewrite_tag('%pasREF%', '([^&]+)');
		
		$mod_rewrite_str   = get_option('wp_ads_mod_rewrite', 'pas');
		add_rewrite_rule('^'.$mod_rewrite_str.'/([^/]*)/([^/]*)/([^/]*)/?','index.php?pasSLUG=$matches[1]&pasZONE=$matches[2]&pasREF=$matches[3]','top');
	}
	
	
	
	

	/*
	 * Get admin role
	 *
	 * @access public
	 * @return $role
	*/
	public function get_admin_role()
	{
		$role = WP_ADS_ROLE_ADMIN;
		
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		
		if ( is_multisite() ) 
		{
			if( is_plugin_active_for_network('wppas/wppas.php') )
			{
				$role = WP_ADS_ROLE_SUPERADMIN;
			}
		}
		
		return $role;
	}
	
	
	
	
	
	/*
	 * Admin page Init actions
	 *
	 * @access public
	 * @return null
	*/
	public function admin_actions() 
	{
		global $pro_ads_main, $pro_ads_multisite;
		
		// Check if admin actions need to be loaded.
		if( $pro_ads_multisite->pro_ads_load_admin_data() )
		{
			wp_enqueue_style( 'wpproads_standard_admin', WP_ADS_TPL_URL . '/css/admin_standard.css', false, WP_ADS_VERSION, "all" );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker');
			
			if(is_admin() && isset( $_GET['page'] ) || is_admin() && isset($_GET['post_type']) || is_admin() && isset($_GET['post']) && $this->wp_pro_ad_check_cpt( get_post_type( $_GET['post'] )) )
			{
				if( 
					isset( $_GET['page'] ) && $_GET['page'] == 'wp-pro-advertising' || 
					isset( $_GET['page'] ) && $_GET['page'] == 'wp-pro-ads-stats' ||  
					isset( $_GET['page'] ) && $_GET['page'] == 'wp-pro-ads-options' || 
					isset( $_GET['page'] ) && $_GET['page'] == 'pro-ads-bs-options' || 
					isset( $_GET['post_type'] ) && $this->wp_pro_ad_check_cpt( $_GET['post_type'] ) ||
					isset( $_GET['post'] ) && $this->wp_pro_ad_check_cpt( get_post_type( $_GET['post'] ))
					
				 ){
					wp_enqueue_script('jquery');
					wp_enqueue_script('jquery-ui-core');
					wp_enqueue_script("jquery-effects-core");
					wp_enqueue_script("jquery-effects-shake");
					wp_enqueue_script('jquery-ui-sortable');
					wp_enqueue_script('jquery-ui-datepicker');
					wp_enqueue_script('pro_ad_admin_js', WP_ADS_TPL_URL . '/js/admin.js', array( 'jquery' ), WP_ADS_VERSION, true );
					wp_enqueue_script('wp_pro_ads_js_cufon', WP_ADS_TPL_URL.'/js/cufon-yui.js');
					wp_enqueue_script('wp_pro_ads_js_font', WP_ADS_TPL_URL.'/js/ITCAvantGardeStd-Bold_700.font.js');
					wp_enqueue_script('wp_pro_ads_js_switch_button', WP_ADS_TPL_URL.'/js/jquery.switchButton.js', array( 'jquery' ), WP_ADS_VERSION, true);
					wp_enqueue_script('wp_pro_ads_js_timepicker', WP_ADS_TPL_URL.'/js/timepicker/jquery.timepicker.min.js', array( 'jquery' ), WP_ADS_VERSION, true);
					wp_enqueue_script('wp_pro_ads_js_timepickerdatepair', WP_ADS_TPL_URL.'/js/timepicker/datepair.js', array( 'jquery' ), WP_ADS_VERSION, true);
					wp_enqueue_script('wp_pro_ads_js_timepickerdatepairjquery', WP_ADS_TPL_URL.'/js/timepicker/jquery.datepair.js', array( 'jquery' ), WP_ADS_VERSION, true);
					wp_register_script('wp_pro_ads_ace', WP_ADS_TPL_URL.'/js/ace/ace.js');
					
					// Localize scripts
					wp_localize_script('pro_ad_admin_js', 'wppas_local', 
						array( 
							'variable'    => __('VARIABLE', 'wpproads'), 
							'fixed'       => __('FIXED','wpproads') ,
							'date_format' => $pro_ads_main->dateformat_PHP_to_jQueryUI( get_option( 'date_format' ) )
						) 
					);
					
					// Main CSS
					wp_enqueue_style("wp_pro_ads_main_style", WP_ADS_TPL_URL."/css/wppas.min.css", false, WP_ADS_VERSION, "all");
					wp_enqueue_style("wp_pro_ads_timepicker_style", WP_ADS_TPL_URL."/css/jquery.timepicker.css", false, WP_ADS_VERSION, "all");
					
					
					// Statistics only
					if( isset( $_GET['page'] ) && $_GET['page'] == 'wp-pro-ads-stats' )
					{
						wp_enqueue_script('pro_ad_statistics_flot', WP_ADS_TPL_URL . '/js/jquery.flot.min.js');
						wp_enqueue_script('pro_ad_statistics_flot_time', WP_ADS_TPL_URL . '/js/jquery.flot.time.js');
						wp_enqueue_script('pro_ad_statistics_flot_pie', WP_ADS_TPL_URL . '/js/jquery.flot.pie.min.js');
						wp_enqueue_style( 'pro_ad_statistics_flot_style', WP_ADS_TPL_URL . '/css/graph.css', false, WP_ADS_VERSION, "all" );
					}
					
					// Chosen
					wp_enqueue_style( 'chosen_style', WP_ADS_INC_URL . '/chosen/chosen.css', false, WP_ADS_VERSION, "all" );
					wp_enqueue_script( 'chosen', WP_ADS_INC_URL . '/chosen/chosen.jquery.min.js', array( 'jquery' ), false, true );
					
					// Load media
					if( function_exists('wp_enqueue_media') )
					{
						wp_enqueue_media();
					}
					
					// Wordpress thickbox: Example link: <a href="" class="thickbox">Link</a>. (http://manchumahara.com/2010/03/22/using-wordpress-native-thickbox/) 
					wp_enqueue_script('thickbox',null,array('jquery'));
					wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
					
					
					wp_enqueue_style( 'wpproadstuna_admin_style', WP_ADS_TPL_URL . '/css/tuna-admin.css', false, WP_ADS_VERSION, "all" );
					wp_enqueue_style( 'wpproads_tuna_admin_colors', WP_ADS_TPL_URL . '/css/tuna-admin-colors.php', false, WP_ADS_VERSION, "all" );
					wp_enqueue_style( 'wp_pro_ad_admin_style', WP_ADS_TPL_URL . '/css/admin.css', false, WP_ADS_VERSION, "all" );
					wp_enqueue_style( 'wp_pro_ad_UI_style', WP_ADS_TPL_URL . '/css/jqueryUI/jquery-ui.min.css', false, WP_ADS_VERSION, "all" );
				 }
			}
			
			
			// Create menu
			//if ( is_main_site() ){ 
			/*if( function_exists('add_object_page') ) 
			{
				add_object_page(
					__('Advertising', 'wpproads'), 
					__('Advertising', 'wpproads'), 
					WP_ADS_USER_CAN, 
					"wp-pro-advertising", 
					array( $this, "wp_pro_ad_dashboard"), 
					WP_ADS_URL."images/banner_icon_20.png"
				);
			}
			else
			{*/
			add_menu_page(
				__('Advertising', 'wpproads'), 
				__('Advertising', 'wpproads'), 
				WP_ADS_USER_CAN,  
				"wp-pro-advertising", 
				array( $this, "wp_pro_ad_dashboard"),
				WP_ADS_URL."images/banner_icon_20.png" // since v4.6.19
			);
			//}
			
			add_submenu_page("wp-pro-advertising", __('AD Dashboard', 'wpproads'), __('AD Dashboard', 'wpproads'), WP_ADS_USER_CAN, "wp-pro-advertising", array( $this, "wp_pro_ad_dashboard"));
			add_submenu_page("wp-pro-advertising", __('Statistics', 'wpproads'), __('Statistics', 'wpproads'), WP_ADS_USER_CAN, "wp-pro-ads-stats", array( $this, "wp_pro_ad_stats"));
		
			if( current_user_can(WP_ADS_ROLE_ADMIN))
			{
				add_filter( 'custom_menu_order', array($this, 'submenu_order') );
			}
			//}
		
		}
	}
	
	
	function submenu_order( $menu_ord ) 
	{
		global $submenu;
			
		// Enable the next line to see all menu orders
		//echo '<pre>'.print_r($submenu['edit.php?post_type=advertising'],true).'</pre>';
		//echo '<pre>'.print_r($submenu['wp-pro-advertising'],true).'</pre>';
		if( isset($submenu['wp-pro-advertising']))
		{
			$arr = array();
			$arr[] = $submenu['wp-pro-advertising'][4];
			$arr[] = $submenu['wp-pro-advertising'][0];
			$arr[] = $submenu['wp-pro-advertising'][1];
			$arr[] = $submenu['wp-pro-advertising'][2];
			$arr[] = $submenu['wp-pro-advertising'][3];
			$arr[] = $submenu['wp-pro-advertising'][5];
			//$arr[] = $submenu['wp-pro-advertising'][6];
			$submenu['wp-pro-advertising'] = $arr;
		}
	
		return $menu_ord;
	}
	
	/*
	 * Highlights the correct top level admin menu item for post types.
	*/
	public function menu_highlight() 
	{
		global $menu, $submenu, $parent_file, $submenu_file, $self, $post_type, $taxonomy;

		if ( isset( $post_type ) ) {
			if ( in_array( $post_type, PAS()->cpts ) ) {
				$submenu_file = 'edit.php?post_type=' . esc_attr( $post_type );
				$parent_file  = 'wp-pro-advertising';
			}
		}
	}
	
	
	
	/*
	 * Admin menu functions
	 *
	 * @access public
	 * @return html
	*/
	public function wp_pro_ad_dashboard()
	{
		include( WP_ADS_TPL_DIR.'/pro_ad_dashboard.php');
	}
	public function wp_pro_ad_stats()
	{
		include( WP_ADS_TPL_DIR.'/pro_ad_stats.php');
	}
	public function wp_pro_ad_options()
	{
		include( WP_ADS_TPL_DIR.'/pro_ad_options.php');
	}
	
	
	
	
	
	
	
	/*
	 * Create the database tables the plugin needs to function.
	 *
	 * @access public
	 * @return void
	*/
	public function create_tables() 
	{
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$wpdb->hide_errors();
		
		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) 
		{
			if ( ! empty($wpdb->charset ) ) 
			{
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty($wpdb->collate ) )
			{
				$collate .= " COLLATE $wpdb->collate";
			}
		}
		
		/*$sql_wpproads_stats = "CREATE TABLE " . $wpdb->prefix . "wpproads_stats (
			id int(11) NOT NULL AUTO_INCREMENT,
			date int(11) NOT NULL,
			time int(11) NOT NULL,
			type VARCHAR( 50 ) NOT NULL,
			hits int(11) NOT NULL,
			UNIQUE KEY id (id)
		) ".$collate.";";*/
		
		$sql_stats = "CREATE TABLE " . $wpdb->prefix . "pro_ad_system_stats (
			id int(11) NOT NULL AUTO_INCREMENT,
			advertiser_id mediumint(9) NOT NULL,
			campaign_id mediumint(9) NOT NULL,
			banner_id mediumint(9) NOT NULL,
			adzone_id mediumint(9) NOT NULL,
			date int(11) NOT NULL,
			time int(11) NOT NULL,
			type VARCHAR( 50 ) NOT NULL,
			ip_address VARCHAR( 50 ) NOT NULL,
			city  VARCHAR( 50 ) NOT NULL,
			country  VARCHAR( 50 ) NOT NULL,
			country_cd  VARCHAR( 5 ) NOT NULL,
			browser  VARCHAR( 50 ) NOT NULL,
			platform  VARCHAR( 50 ) NOT NULL,
			device  VARCHAR( 20 ) NOT NULL,
			refferal_url TEXT NOT NULL,
			refferal_host VARCHAR( 200 ) NOT NULL,
			UNIQUE KEY id (id),
			KEY advertiser_id (advertiser_id),
			KEY banner_id (banner_id),
			KEY adzone_id (adzone_id),
			KEY date (date),
			KEY time (time),
			KEY banner_id_date (banner_id, date)
		) ".$collate.";";
		
		dbDelta( $sql_stats );
		
		// pro_ad_system_stats
		$sql_wpproads_user_stats = "CREATE TABLE " . $wpdb->prefix . "wpproads_user_stats (
			id int(11) NOT NULL AUTO_INCREMENT,
			advertiser_id mediumint(9) NOT NULL,
			campaign_id mediumint(9) NOT NULL,
			banner_id mediumint(9) NOT NULL,
			adzone_id mediumint(9) NOT NULL,
			date int(11) NOT NULL,
			time int(11) NOT NULL,
			hour int(11) NOT NULL,
			type VARCHAR( 50 ) NOT NULL,
			ip_address VARCHAR( 50 ) NOT NULL,
			city  VARCHAR( 50 ) NOT NULL,
			country  VARCHAR( 50 ) NOT NULL,
			country_cd  VARCHAR( 5 ) NOT NULL,
			browser  VARCHAR( 50 ) NOT NULL,
			platform  VARCHAR( 50 ) NOT NULL,
			device  VARCHAR( 20 ) NOT NULL,
			refferal_url TEXT NOT NULL,
			refferal_host VARCHAR( 200 ) NOT NULL,
			hits int(11) NOT NULL,
			UNIQUE KEY id (id),
			KEY advertiser_id (advertiser_id),
			KEY banner_id (banner_id),
			KEY adzone_id (adzone_id),
			KEY date (date),
			KEY time (time),
			KEY hour (hour),
			KEY banner_id_date (banner_id, date)
		) ".$collate.";";
		
		
		//dbDelta( $sql_wpproads_stats );
		dbDelta( $sql_wpproads_user_stats );
	}
	
	
	
	
	
	
	
	/*
	 * Create Widget
	 *
	 * @access public
	 * @return string
	*/
	public function pro_ad_adzone_widgets_init() 
	{	
		register_widget('Wppas_Custom_Widgets');
	}


	
	
	
		
	
	
	
	/*
	 * Add stuff to the website <head>
	 *
	 * @access public
	 * @return string
	*/
	public function add_to_head()
	{
		$custom_css = get_option('wpproads_custom_css', '');
		$wpproads_google_analytics_id = get_option('wpproads_google_analytics_id', '');
		
		/*
		 * Action: 'wppas_head' - Allow other plugins to output inside the WP PRO Advertising section of the head section.
		*/
		do_action( 'wppas_head' );
		
		// Load custom CSS ----------------------------------------
		if( !empty( $custom_css ))
		{
			echo '<style type="text/css" id="wp_pro_advertising_system_css">'.stripslashes($custom_css).'</style>',"\n";
		}
		
		// Google Analytics
		if( !empty($wpproads_google_analytics_id) )
		{
			echo "<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','wppas_ga');wppas_ga('create', '".$wpproads_google_analytics_id."', 'auto');</script>";
		}

		echo '<!-- / ', PAS()->plugin_str, ". -->\n\n";
	}
	
	
	
	
	
	/*
	 * Add stuff to the website footer
	 *
	 * @access public
	 * @return string
	*/
	public function add_to_footer()
	{
		global $pro_ads_templates;
		
		
		
	}
	

	
	
	
	
	/*
	 * Allowed CPTs
	 *
	 * @access public
	 * @return int
	*/
	public function wp_pro_ad_check_cpt( $cpt )
	{
		//$cpts = array('advertisers', 'campaigns', 'banners', 'adzones');
		
		return in_array($cpt, PAS()->cpts) ? 1 : 0;
	}
	
	
	
	
	
	
	
	/*
	 * Check if admin page is from the advertising plugin
	 *
	 * @access public
	 * @return int
	*/
	public function page_is_wpproads_admin()
	{
		$is_wpproads_admin = 0;
		
		if(is_admin() && isset( $_GET['page'] ) || is_admin() && isset($_GET['post_type']) || is_admin() && isset($_GET['post']) && $this->wp_pro_ad_check_cpt( get_post_type( $_GET['post'] )) )
		{
			if( 
				isset( $_GET['page'] ) && $_GET['page'] == 'wp-pro-advertising' || 
				isset( $_GET['page'] ) && $_GET['page'] == 'wp-pro-ads-stats' ||  
				isset( $_GET['page'] ) && $_GET['page'] == 'wp-pro-ads-options' || 
				isset( $_GET['page'] ) && $_GET['page'] == 'pro-ads-bs-options' || 
				isset( $_GET['post_type'] ) && $this->wp_pro_ad_check_cpt( $_GET['post_type'] ) ||
				isset( $_GET['post'] ) && $this->wp_pro_ad_check_cpt( get_post_type( $_GET['post'] ))
				
			 ){
				 $is_wpproads_admin = 1;
			 }
		}
		
		return $is_wpproads_admin;
	}
	
	
	
	
	
	
	
	/**
	 * Draw the Admin Bar
	 * @global object $wp_admin_bar
	 * @return null
	 */
	public function pro_ads_admin_bar()
	{
		global $wp_admin_bar, $pro_ads_main, $pro_ads_multisite;
		
		$wpproads_enable_adminbar = get_option('wpproads_enable_adminbar',1);
		
		if (!is_super_admin() || !is_admin_bar_showing() || !$wpproads_enable_adminbar || !$pro_ads_multisite->pro_ads_load_admin_data() )
			return;
		
		$admin_url = get_admin_url();
		
		// Root Menu
		$wp_admin_bar->add_menu(array(
			'id' => 'wpproads_adminbar',
			'title' => __('Advertising', 'wpproads'),
			'href' => $admin_url.'admin.php?page=wp-pro-advertising',
			'meta' => array('html' => '')
		));
		// New Requests Menu
		$wp_admin_bar->add_menu(array(
			'parent' => 'wpproads_adminbar',
			'id' => 'wpproads_advertisers',
			'title' => __('Advertisers', 'wpproads'),
			'href' => $admin_url.'edit.php?post_type=advertisers'
		));
		// Add Campaign Menu
		$wp_admin_bar->add_menu(array(
			'parent' => 'wpproads_adminbar',
			'id' => 'wpproads_campaigns',
			'title' => __('Campaigns', 'wpproads'),
			'href' => $admin_url.'edit.php?post_type=campaigns'
		));
		// Campaigns Menu
		$wp_admin_bar->add_menu(array(
			'parent' => 'wpproads_adminbar',
			'id' => 'wpproads_banners',
			'title' => __('Banners', 'wpproads'),
			'href' => $admin_url.'edit.php?post_type=banners'
		));
		// Settings Menu
		$wp_admin_bar->add_menu(array(
			'parent' => 'wpproads_adminbar',
			'id' => 'wpproads_adzones',
			'title' => __('Adzones', 'wpproads'),
			'href' => $admin_url.'edit.php?post_type=adzones'
		));
		// Settings Menu
		$wp_admin_bar->add_menu(array(
			'parent' => 'wpproads_adminbar',
			'id' => 'wpproads_statistics',
			'title' => __('Statistics', 'wpproads'),
			'href' => $admin_url.'admin.php?page=wp-pro-ads-stats'
		));
	}
	
	
	
	
	
	
	/**
	 * Change the admin footer text on WP PRO Advertising admin pages
	 *
	 * @since  4.2.9
	 * @param  string $footer_text
	 * @return string
	 */
	public function wpproads_admin_footer_text( $footer_text ) 
	{
		$is_wpproads_admin = $this->page_is_wpproads_admin();
		
		// Check to make sure we're on a WP PRO Advertising admin page
		if ( $is_wpproads_admin ) 
		{
			// Change the footer text
			$footer_text = sprintf( __( 'If you like <strong>WP PRO Advertising System</strong> please leave us a <a href="%1$s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating on <a href="%1$s" target="_blank">Codecanyon.net</a>. A huge thanks in advance!', 'wpproads' ), 'http://bit.ly/WPPROADSYSTEM' );
		}

		return $footer_text;
	}
	
	
	
	
	
	/**
	 * Admin Notices
	 * @return html
	 */
	public function pro_ads_admin_notices() 
	{
		global $pro_ads_main, $pro_ads_multisite;
		
		$notice = array();
		
		if( $pro_ads_multisite->pro_ads_load_admin_data() )
		{
			/*
			 * Available Notices
			*/
			//V4.5.5 statistics new version update
			$wpproads_new_stats_455 = get_option('wpproads_new_stats_455', 0);
			$notice[] = PAS()->version >= '4.5.5' && $wpproads_new_stats_455 == '' ? sprintf(__('<p><strong>%s - Statistics Update.</strong> A Major statistics update/improvement is vailable. Its recommended but your stats will start from zero. Note: stats will never be removed. You can always switch back to the old statistics version even after updating to the new one. (General Settings -> Statistics -> Statistics version)</p>','wpproads'), 'WP Pro Advertising System').'<p class="submit"><a class="button-primary" href="'.esc_url( add_query_arg( 'wpproads_new_stats_455_update', '_new' ) ).'">'.__('Use New Statistics','wpproads').'</a> <a class="button-primary" href="'.esc_url( add_query_arg( 'wpproads_new_stats_455_update', '_old' ) ).'">'.__('Keep using Old Statistics','wpproads').'</a>' : '';
			
			/*
			 * Handle Notices
			*/
			//V4.5.5  statistics new version update
			if ( !empty( $_GET['wpproads_new_stats_455_update'] ) ) 
			{
				$stats_version = $_GET['wpproads_new_stats_455_update'] == '_new' ? '_new' : '';
				$pro_ads_multisite->wpproads_update_option( 'wpproads_stats_version', $stats_version);
				update_option( 'wpproads_new_stats_455', 1);
			}
			
			
			/*
			 * Available Notices
			*/
			//V4.0.4 statistics settings update
			$wpproads_enable_stats = get_option('wpproads_enable_stats', 0);
			$notice[] = PAS()->version >= '4.0.4' && $wpproads_enable_stats == '' ? sprintf(__('<p><strong>%s - Settings update required.</strong> Please update the Statistics settings. Select your option below.</p>','wpproads'), 'WP Pro Advertising System').'<p class="submit"><a class="button-primary" href="'.esc_url( add_query_arg( 'wpproads_stats_update', 'enable' ) ).'">'.__('Enable Statistics','wpproads').'</a> <a class="button-primary" href="'.esc_url( add_query_arg( 'wpproads_stats_update', 'disable' ) ).'">'.__('Disable Statistics','wpproads').'</a>' : '';
			
			/*
			 * Handle Notices
			*/
			//V4.0.4 statistics settings update
			if ( !empty( $_GET['wpproads_stats_update'] ) ) 
			{
				$status = $_GET['wpproads_stats_update'] == 'enable' ? 1 : 0;
				update_option( 'wpproads_enable_stats', $status);
				update_option( 'wpproads_enable_userdata_stats', $status);
			}
		}
		
		
		if( !empty($notice) )
		{
			foreach($notice as $note)
			{
				echo !empty($note) ? '<div class="updated wpproads-message">'.$note.'</div>' : '';
			}
		}
		
	}

}

endif;