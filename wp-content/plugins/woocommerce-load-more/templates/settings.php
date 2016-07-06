<div class="wrap">  
    <div id="icon-themes" class="icon32"></div>  
    <h2>Load More Products Settings</h2>  
    <?php settings_errors(); ?>  

    <?php $active_tab = isset( $_GET[ 'tab' ] ) ? @ $_GET[ 'tab' ] : 'general'; ?>  

    <h2 class="nav-tab-wrapper">  
        <a href="?page=br-load-more-products&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e('General', BeRocket_LMP_domain) ?></a> 
        <a href="?page=br-load-more-products&tab=button" class="nav-tab <?php echo $active_tab == 'button' ? 'nav-tab-active' : ''; ?>"><?php _e('Button', BeRocket_LMP_domain) ?></a> 
        <a href="?page=br-load-more-products&tab=selectors" class="nav-tab <?php echo $active_tab == 'selectors' ? 'nav-tab-active' : ''; ?>"><?php _e('Selectors', BeRocket_LMP_domain) ?></a> 
        <a href="?page=br-load-more-products&tab=lazy_load" class="nav-tab <?php echo $active_tab == 'lazy_load' ? 'nav-tab-active' : ''; ?>"><?php _e('Lazy Load', BeRocket_LMP_domain) ?></a> 
        <a href="?page=br-load-more-products&tab=messages" class="nav-tab <?php echo $active_tab == 'messages' ? 'nav-tab-active' : ''; ?>"><?php _e('Messages', BeRocket_LMP_domain) ?></a> 
        <a href="?page=br-load-more-products&tab=javascript" class="nav-tab <?php echo $active_tab == 'javascript' ? 'nav-tab-active' : ''; ?>"><?php _e('JavaScript', BeRocket_LMP_domain) ?></a> 
    </h2>  

    <form class="lmp_submit_form" method="post" action="options.php">  
        <?php 
        if( $active_tab == 'general' ) { 
            settings_fields( 'br_lmp_general_settings' );
            do_settings_sections( 'br_lmp_general_settings' );
            echo '<input type="submit" class="button-primary" value="'.__('Save Changes', BeRocket_LMP_domain).'" />';
        } else if( $active_tab == 'button' ) {
            settings_fields( 'br_lmp_button_settings' );
            do_settings_sections( 'br_lmp_button_settings' ); 
            echo '<input type="submit" class="button-primary" value="'.__('Save Changes', BeRocket_LMP_domain).'" />';
        } else if( $active_tab == 'selectors' ) {
            settings_fields( 'br_lmp_selectors_settings' );
            do_settings_sections( 'br_lmp_selectors_settings' ); 
            echo '<input type="submit" class="button-primary" value="'.__('Save Changes', BeRocket_LMP_domain).'" />';
        } else if( $active_tab == 'lazy_load' ) {
            settings_fields( 'br_lmp_lazy_load_settings' );
            do_settings_sections( 'br_lmp_lazy_load_settings' ); 
        } else if( $active_tab == 'messages' ) {
            settings_fields( 'br_lmp_messages_settings' );
            do_settings_sections( 'br_lmp_messages_settings' ); 
        } else if( $active_tab == 'javascript' ) {
            settings_fields( 'br_lmp_javascript_settings' );
            do_settings_sections( 'br_lmp_javascript_settings' ); 
            echo '<input type="submit" class="button-primary" value="'.__('Save Changes', BeRocket_LMP_domain).'" />';
        }
        ?>
    </form> 

    <h4><?php _e('WooCommerce AJAX Product Filters developed by', BeRocket_LMP_domain) ?> <a href="http://berocket.com" target="_blank">BeRocket</a></h4>
</div>