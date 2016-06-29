<?php
class Pro_Ads_Welcome {	

	public function __construct() 
	{
		// Updates
		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_init', array( $this, 'pas_verify_plugin_version') );
		add_action( 'admin_init', array( $this, 'pas_welcome') );
	}
	
	
	
	
	
	
	
	/*
	 * Verify Plugin Version
	 *
	 * @access public
	 * @return array
	*/
	public function pas_verify_plugin_version()
	{	
		$plugin_version = PAS()->version;
			
		// check installed version
		$installed_version = get_option( 'pro_ad_system_version',0);
		
		if($installed_version != $plugin_version)
		{
			update_option( 'pro_ad_system_version', PAS()->version );
			wp_safe_redirect( admin_url( 'index.php?page=pas-about&pas-updated=true' ) );
		}
	}
	
	
	
	
	
	
	
	/*
	 * Admin Menus
	 *
	 * @access public
	 * @return array
	*/
	public function admin_menus() 
	{
		$welcome_page_title = __( 'Welcome to WP PRO Advertising System', 'wpproads' );

		// About
		$about = add_dashboard_page( $welcome_page_title, $welcome_page_title, 'manage_options', 'pas-about', array( $this, 'about_screen' ) );
		remove_submenu_page( 'index.php', 'pas-about' );
		
		add_action( 'admin_print_styles-'. $about, array( $this, 'admin_css' ) );
	}
	
	
	/*
	 * Admin CSS
	 *
	 * @access public
	 * @return array
	*/
	public function admin_css() 
	{
		wp_enqueue_style( 'pas-activation', WP_ADS_TPL_URL.'/css/pas-activation.css' );
	}
	
	
	
	/*
	 * About PFS Screen
	 *
	 * @access public
	 * @return array
	*/
	public function about_screen() 
	{
		?>
		<div class="wrap about-wrap pas-welcome-box">
			<div id="pas_welcome_header">
			
                <p class="logo"><span>WP PRO Advertising System</span></p>
                <p class="h3">
                <?php
                    if(!empty($_GET['pas-updated']))
                        $message = __( 'Thank you for updating WP PRO Advertising System to version ', 'wpproads' );
                    else
                        $message = __( 'Thank you for purchasing WP PRO Advertising System ', 'wpproads' );
                        
                    printf( __( '%s%s', 'wpproads' ), $message,	WP_ADS_VERSION );
                ?>
                </p>			
                <p class='h4'>
				<?php 
                    if(!empty($_GET['pas-updated']))
                        printf( __( 'We hope you will enjoy the new features we have added!','wpproads'));
                    else
                        printf( __( 'We hope you will enjoy the WP PRO Advertising System - mange your ads the easy way!','wpproads'));
                ?>
                </p>
            </div>
            <p class="pas-actions">
                <a href="<?php echo admin_url('admin.php?page=wp-pro-advertising'); ?>" class="pas_admin_btn btn_prime"><?php _e( 'AD Dashboard', 'wpproads' ); ?></a>
                
                <a class="pas_admin_btn " href="http://wordpress-advertising.com/faq/" target='_blank'><?php _e( 'FAQ', 'wpproads' ); ?></a>
                
                <a class="pas_admin_btn " href="http://tunasite.com/helpdesk/" target='_blank'><?php _e( 'Support', 'wpproads' ); ?></a>
    
               <!-- <a class="pas_admin_btn " href="http://www.tunasite.com/blog/" target='_blank'><?php _e( 'News', 'wpproads' ); ?></a>
                <a class="pas_admin_btn " href="http://www.tunasite.com/documentation/changelog/" target='_blank'><?php _e( 'Changelog', 'wpproads' ); ?></a>-->
                
            </p>
            <div class='pas-welcome-twitter'>
                <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://bit.ly/WPPROADSYSTEM" data-text="Most Complete Advertising System for Wordpress." data-via="wptuna" data-size="large" data-hashtags="#wpproads">Tweet</a>
    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
            </div>
            
			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wp-pro-advertising' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to the AD Dashboard', 'wpproads' ); ?></a>
			</div>
		</div>
		<?php
	}
	
	
	
	
	/*
	 * PAS Welcome
	 *
	 * @access public
	 * @return array
	*/
	public function pas_welcome() 
	{
		// Exit if no activation redirect transient is set
	    if ( !get_transient( '_pas_activation_redirect' )  )
			return;

		// Delete the redirect transient
		delete_transient( '_pas_activation_redirect' );
		
		// Exit if activating from network, or bulk, or in an iframe
		if ( is_network_admin() || !is_admin() || isset( $_GET['activate-multi'] ) || defined( 'IFRAME_REQUEST' ) )
			return;
		
		// plugin is updated
		if ( ( isset( $_GET['action'] ) && 'upgrade-plugin' == $_GET['action'] ) && ( isset( $_GET['plugin'] ) && strstr( $_GET['plugin'], 'wppas.php' ) ) ){
			wp_safe_redirect( admin_url( 'index.php?page=pas-about&pas-updated=true' ) );
		
		// first time activation
		}else{
			wp_safe_redirect( admin_url( 'index.php?page=pas-about' ) );
		}
	}
	
		
}