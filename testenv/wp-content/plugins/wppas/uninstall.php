<?php
/**
 * WP PRO Advertising System Uninstall
 *
 * Uninstalling WP PRO Advertising deletes advertisers, campaigns, banners, adzones, statistics and all advertising settings.
 *
 * @author 		Tunafish
 * @category 	Core
 * @package 	Wp_Pro_Ad_System/Uninstaller
 * @version     4.2.4
 */
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

global $wpdb, $wp_roles, $wp_version;

$wpproads_uninstall = get_option('wpproads_uninstall', 0);

if( $wpproads_uninstall )
{
	/* ------------------------------------------------------------------
	 * Unregister Plugin
	 * Since v4.6.7
	 * ------------------------------------------------------------------ */
	$wpproads_license_key = get_option('wpproads_license_key', '');
	
	if( !empty($wpproads_license_key) )
	{	
		$request_string = array(
			'body' => array(
				'action'      => 'unregister', 
				'envato_id'   => WP_ADS_ENVATO_ID,
				'item_slug'   => WP_ADS_PLUGIN_SLUG,
				'license-key' => $wpproads_license_key,
				'api-key'     => md5(get_bloginfo('url')),
				'url'         => get_bloginfo('url'),
				'email'       => get_bloginfo('admin_email'),
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);
		
		$request = wp_remote_post('http://tunasite.com/updates/?plu-plugin=ajax-handler', $request_string);
		
		if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
			//return $request['body'];
		}
		//return false;
	}
	
	
	
	
		
	// Tables
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "pro_ad_system_stats" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "wpproads_user_stats" );
	
	// Delete options
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'wpproads_%';");
	
	// Delete posts + data
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'advertisers', 'campaigns', 'banners', 'adzones' );" );
	$wpdb->query( "DELETE FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE wp.ID IS NULL;" );
}