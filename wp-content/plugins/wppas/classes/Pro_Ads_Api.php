<?php
class Pro_Ads_API {	

	public function __construct() 
	{
		// API adzone request ---------------------------------------------------
		add_action( 'wp_loaded', array( $this, 'wp_pro_ad_api_load_adzone' ) );	
		// REST API data request ------------------------------------------------ 
		//add_action( 'wp_loaded', array( $this, 'wp_pro_ad_rest_api_load_data' ) );	
		
		// Filters --------------------------------------------------------------
		add_filter( 'wpproads_api_display_adzone', array($this, 'wp_pro_ads_api_display_adzone'));
		add_filter( 'wpproads_api_create_adzone', array($this, 'wp_pro_ads_api_create_adzone'));
	}
	
	
	
	
	
	
	
	
	/*
	 * API Allow developers to display/create default Adzones.
	 *
	 * @access public
	 * @return string
	*/
	function wp_pro_ads_api_display_adzone( $args )
	{
		global $wpdb, $pro_ads_multisite, $pro_ads_main;
		
		$defaults = array(
			'name'             => '',
			'description'      => '',
			'size'             => '300x250',
			'center'           => 1,
			'hide_empty'       => 0,
			'rotation'         => 0,
			'rotation_type'    => 'bxslider',
			'rotation_time'    => 3,
			'rotation_effect'  => 'fade',
			'rotation_order'   => 'fixed',
			'fix_size'         => 1,
			'default_url'      => ''
		);
		$adzone = wp_parse_args( $args, $defaults );
		
		if( !empty($adzone['name']))
		{
			// Check if adzone exists.
			$query = $wpdb->get_results("SELECT ID FROM ".$pro_ads_multisite->wpproads_db_prefix()."posts WHERE post_name = '" . sanitize_title($adzone['name']) . "' LIMIT 1");
			
			if( $query ) 
			{
				$adzone_id = $query[0]->ID;
			} 
			else 
			{
				$adzone_id = $this->api_create_adzone($adzone);
			}
			
			return do_shortcode("[pro_ad_display_adzone id=".$adzone_id."]");
		}
	}
	
	
	
	
	/*
	 * API Allow developers to create Adzones.
	 *
	 * @access public
	 * @return ID
	*/
	public function wp_pro_ads_api_create_adzone( $args )
	{
		global $wpdb, $pro_ads_multisite, $pro_ads_main;
		
		$defaults = array(
			'name'             => '',
			'description'      => '',
			'size'             => '300x250',
			'center'           => 1,
			'hide_empty'       => 0,
			'rotation'         => 0,
			'rotation_type'    => 'bxslider',
			'rotation_time'    => 3,
			'rotation_effect'  => 'fade',
			'rotation_order'   => 'fixed',
			'fix_size'         => 1,
			'default_url'      => ''
		);
		$adzone = wp_parse_args( $args, $defaults );
		
		if( !empty($adzone['name']))
		{
			// Check if adzone exists.
			$query = $wpdb->get_results("SELECT ID FROM ".$pro_ads_multisite->wpproads_db_prefix()."posts WHERE post_name = '" . sanitize_title($adzone['name']) . "' LIMIT 1");
			
			if( !$query ) 
			{
				$adzone_id = $this->api_create_adzone($adzone);
			}
			
			return $adzone_id;
		}
	}
	
	
	
	
	public function api_create_adzone( $adzone )
	{
		global $current_user, $pro_ads_main;
		
		$data = array(
			'post_title'       		=> $adzone['name'],
			'post_content'     		=> '',
			'post_status'      		=> 'publish',
			'post_type'        		=> 'adzones',
			'post_date'             => date('Y-m-d H:i:s', $pro_ads_main->time_by_timezone()),
			'post_date_gmt'         => date('Y-m-d H:i:s', time()),
			'post_author'           => $current_user->ID,
			'ping_status'           => get_option('default_ping_status'), 
			'post_parent'           => 0,
			'menu_order'            => 0,
			'to_ping'               => '',
			'pinged'                => '',
			'post_password'         => '',
			'guid'                  => '',
			'post_content_filtered' => '',
			'post_excerpt'          => '',
			'import_id'             => 0,
			'tags_input'            => '',
			'filter' => true	
		);	
		$adzoneID = wp_insert_post( $data );
		
		// Check if custom size
		$sizes = $pro_ads_main->available_banner_sizes(0);
		$custom_size = in_array($adzone['size'], $sizes) ? 0 : 1;
		
		update_post_meta( $adzoneID, '_adzone_description', $adzone['description'] );
		update_post_meta( $adzoneID, '_adzone_custom_size', $custom_size );
		update_post_meta( $adzoneID, '_adzone_size', $adzone['size'] );	
		update_post_meta( $adzoneID, '_adzone_fix_size', $adzone['fix_size'] );
		update_post_meta( $adzoneID, '_adzone_rotation_type', $adzone['rotation_type'] );
		update_post_meta( $adzoneID, '_adzone_rotation', $adzone['rotation'] );
		update_post_meta( $adzoneID, '_adzone_rotation_time', $adzone['rotation_time'] );
		update_post_meta( $adzoneID, '_adzone_rotation_effect', $adzone['rotation_effect'] );
		update_post_meta( $adzoneID, '_adzone_rotation_order', $adzone['rotation_order'] );
		update_post_meta( $adzoneID, '_adzone_center', $adzone['center'] );
		update_post_meta( $adzoneID, '_adzone_hide_empty', $adzone['hide_empty'] );
		update_post_meta( $adzoneID, '_adzone_default_url', $adzone['default_url'] );
		
		return $adzoneID;
	}
	
	
	
	
	/*
	 * Api load adzone
	 *
	 * @access public
	 * @return int
	*/
	public function wp_pro_ad_api_load_adzone( $id = 0, $full_html = 1)
	{
		global $pro_ads_adzones, $pro_ads_main, $pro_ads_codex;
		
		if( isset( $_GET['wpproadszoneid'] ) && !empty( $_GET['wpproadszoneid'] ) || $id )
		{
			if( method_exists( $pro_ads_adzones, 'display_adzone' ) )
			{
				$adzone_id = $_GET['wpproadszoneid'] ? $_GET['wpproadszoneid'] : $id;
				$custom_css = get_option('wpproads_custom_css', '');
				$pro_ads_main->daily_updates();
				$ref_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
				
				if( $full_html )
				{
					?>
					<!DOCTYPE>
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                    <?php
				}
				?>
                <!-- Ads on this site are served by WP PRO Advertising System - All In One Ad Manager <?php echo WP_ADS_VERSION; ?> - wordpress-advertising.com -->
                <script type='text/javascript' src='<?php bloginfo('wpurl'); ?>/wp-admin/load-scripts.php?load=jquery-core'></script>
				<script type="text/javascript" src="<?php echo WP_ADS_TPL_URL.'/js/jquery.jshowoff.min.js'; ?>"></script>
                <script type="text/javascript" src="<?php echo WP_ADS_TPL_URL.'/js/jquery.bxslider.min.pas.js'; ?>"></script>
                <?php
				wp_enqueue_style( 'wp_pro_add_style' );
				wp_enqueue_script( 'wp_pro_add_js_functions' );
                
				// Buy and sell
				if( $pro_ads_main->buyandsell_is_active() )
				{
					?>
                    <script type="text/javascript" src="<?php echo WP_ADS_BS_TPL_URL.'/js/buyandsell.js'; ?>"></script>
                	<link rel="stylesheet" id="wp_pro_add_style-css" href="<?php echo WP_ADS_BS_TPL_URL; ?>/css/buyandsell.css" type="text/css" media="all">
                    <?php
				}
				?>
				<style type="text/css">
					body { margin:0; padding:0; }
					<?php echo $custom_css; ?>
				</style>
                <?php
				if( $full_html )
				{
					?>
                    <title>WP PRO ADVERTISING SYSTEM - All In One Ad Manager for Wordpress</title>
                    </head>
                    <body>
                    <?php
				}
                    $arr = $pro_ads_adzones->get_adzone_data($adzone_id, array('id' => $adzone_id));
                    
                    if( $pro_ads_codex->wpproads_adzone_is_grid( $adzone_id )  )
                    {
                        echo $pro_ads_adzones->display_adzone_grid( $adzone_id, $arr['atts'] );
                    }
                    else
                    {
                        $adzone = $pro_ads_adzones->display_adzone( $adzone_id, $arr['atts'], $ref_url );
						echo $adzone['html'];
                    }
				if( $full_html )
				{
                    ?>
                    </body>
                    </html>
                	<?php
				}
			}
			else
			{
				_e('WP Pro Ad System Error: function "pro_ad_display_adzone" does not exists.','wpproads');	
			}
			exit();
		}
	}
	
	
	
	
	
	
	
	/*
	 * REST API
	 *
	 * @access public
	 * @return 
	*/
	public function wp_pro_ad_rest_api_load_data()
	{
		if( isset( $_GET['wppasapi'] )) // && !empty( $_GET['wpproadsapi'] ) 
		{
			if( !empty($_SERVER['HTTP_REFERER']))
			{
				$data = RestAPI::processRequest(); 
				
				if( $data->getMethod() == 'get' || $data->getMethod() == 'post' )
				{	
					$status = 200;
					//$data_list = json_encode($data->getRequestVars());
					$data_list = $data->getRequestVars();
					$content_type = 'application/json';
					
					RestAPI::sendResponse($status, $data_list, $content_type);
				}
				else
				{
					RestAPI::sendResponse(404);
				}
			}
			else
			{
				RestAPI::sendResponse(401);
			}
		}
	}
	
	
	
	
}
?>