<?php
/*
Plugin Name: WooCommerce Menu Extension
Plugin URI: http://www.augustinfotech.com
Description: You can now add woocommerce links in your WP menus.
Version: 1.3
Text Domain: woocommerce-menu-extension
Author: August Infotech
Author URI: http://www.augustinfotech.com
*/

define( 'AIWOO_VERSION', '1.3' );

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

define('AI_WOO_PATH', plugin_dir_path( __FILE__ ));

if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { 

	add_action( 'plugins_loaded', create_function( '', '
			$filename  = "include/";
			$filename .= is_admin() ? "backend.inc.php" : "frontend.inc.php";
			if( file_exists( plugin_dir_path( __FILE__ ) . $filename ) )
				include( plugin_dir_path( __FILE__ ) . $filename );
	' 	)
	);
	
} else {
	add_action('admin_notices', 'aiwoo_plugin_admin_notices');
}

function aiwoo_plugin_admin_notices() {

	   $msg = sprintf( __( 'Please install or activate : %s.', $_SERVER['SERVER_NAME'] ), '<a href=https://wordpress.org/plugins/woocommerce style="color: #ffffff;text-decoration:none;font-style: italic;" target="_blank"/><strong>WooCommerce - excelling eCommerce</strong></a>' );
	   
	   echo '<div id="message" class="error" style="background-color: #DD3D36;"><p style="font-size: 16px;color: #ffffff">' . $msg . '</p></div>';   
	   
	   deactivate_plugins('woocommerce-menu-extension/woocommerce-menu-extension.php');
}