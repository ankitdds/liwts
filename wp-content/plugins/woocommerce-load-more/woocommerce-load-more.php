<?php
/**
 * Plugin Name: WooCommerce Load More
 * Plugin URI: http://berocket.com/product/woocommerce-load-more-products
 * Description: Infinite Scrolling, AJAX Products Loading
 * Version: 1.0.1
 * Author: BeRocket
 * Requires at least: 4.0
 * Author URI: http://berocket.com
 */
define( "BeRocket_Load_More_Products_version", '1.0.1' );
define( "BeRocket_LMP_domain", 'BRaapf' ); 

define( "LMP_TEMPLATE_PATH", plugin_dir_path( __FILE__ ) . "templates/" );
load_plugin_textdomain( BeRocket_LMP_domain, false, WP_PLUGIN_DIR . '/languages' );

require_once dirname( __FILE__ ) . '/includes/functions.php';
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

class BeRocket_LMP {

    public static $defaults = array(
        'br_lmp_general_settings'   => array(
            'type'                      => 'infinity_scroll',
            'use_mobile'                => '',
            'mobile_type'               => 'more_button',
            'mobile_width'              => '767',
            'products_per_page'         => '',
            'loading_image'             => 'fa-spinner',
            'buffer'                    => '50',
        ),
        'br_lmp_button_settings'    => array(
            'button_text'               => 'Load More',
            'custom_class'              => '',
            'background-color'          => '#aaaaff',
            'color'                     => '#333333',
            'font-size'                 => '22',
            'padding-left'              => '25',
            'padding-right'             => '25',
            'padding-top'               => '15',
            'padding-bottom'            => '15',
            'hover'                     => array(
                'background-color'          => '#9999ff',
                'color'                     => '#111111',
            ),
        ),
        'br_lmp_selectors_settings' => array(
            'use_filters_settings'      => '',
            'products'                  => 'ul.products',
            'item'                      => 'li.product',
            'pagination'                => '.woocommerce-pagination',
            'next_page'                 => '.woocommerce-pagination a.next',
        ),
        'br_lmp_javascript_settings'=> array(
            'before_update'             => '',
            'after_update'              => '',
        ),
    );

    function __construct() {
        if ( ! @ is_network_admin() ) {

            if ( ( is_plugin_active( 'woocommerce/woocommerce.php' ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) && br_get_woocommerce_version() >= 2.1 ) {
                add_action ( 'wp_head', array( __CLASS__, 'wp_header' ) );
                add_action ( 'init', array( __CLASS__, 'include_front' ) );
                add_action ( 'admin_init', array( __CLASS__, 'include_admin' ) );
                add_action ( 'admin_menu', array( __CLASS__, 'lmp_options' ) );
                add_filter ( 'berocket_aapf_user_func', array( __CLASS__, 'filters_compatibility' ) );
                add_filter ( 'berocket_lgv_user_func', array( __CLASS__, 'list_grid_compatibility' ) );
            }
        }
    }

    public static function wp_header() {
        $options = BeRocket_LMP::get_lmp_option ( 'br_lmp_button_settings' );
        echo '<style>
            .lmp_load_more_button .lmp_button:hover {
                background-color: '.$options['hover']['background-color'].'!important;
                color: '.$options['hover']['color'].'!important;
            }
            .lazy{opacity:0;}
        </style>';
    }

    public static function list_grid_compatibility( $user_func ) {
        $after_set = 'jQuery(window).scrollTop(jQuery(window).scrollTop() + 1).scrollTop(jQuery(window).scrollTop() - 1);';
        $user_func['after_style_set'] = $after_set . $user_func['after_style_set'];
        return $user_func;
    }

    public static function filters_compatibility( $user_func ) {
        $after_update = "lmp_update_state();";
        $user_func['after_update'] = $after_update . $user_func['after_update'];
        return $user_func;
    }

    public static function get_load_more_button() {
        $options = BeRocket_LMP::get_lmp_option ( 'br_lmp_button_settings' );
        $button = '<div class="lmp_load_more_button">';
        $button .= '<a class="lmp_button '.$options['custom_class'].'" style="';
        $button .= 'background-color: '.$options['background-color'].'; color: '.$options['color'].'; font-size: '.$options['font-size'].'px;';
        $button .= 'padding: '.$options['padding-top'].'px '.$options['padding-right'].'px '.$options['padding-bottom'].'px '.$options['padding-left'].'px;';
        $button .= '" href="#load_next_page">'.$options['button_text'].'</a>';
        $button .= '</div>';
        return $button;
    }

    public static function add_javascript_data() {
        $general_options = BeRocket_LMP::get_lmp_option ( 'br_lmp_general_settings' );
        $button_options = BeRocket_LMP::get_lmp_option ( 'br_lmp_button_settings' );
        $selectors_options = BeRocket_LMP::get_lmp_option ( 'br_lmp_selectors_settings' );
        $javascript_options = BeRocket_LMP::get_lmp_option ( 'br_lmp_javascript_settings' );
        if ( $selectors_options['use_filters_settings'] && br_is_plugin_active( 'filters', '2.0.5' ) ) {
            $filters_settings = apply_filters( 'berocket_aapf_listener_br_options', get_option('br_filters_options') );
            $products_selector = @ $filters_settings['products_holder_id'];
            $item_selector = @ $filters_settings['products_holder_id'].' .product';
            $pagination_selector = @ $filters_settings['woocommerce_pagination_class'];
            $next_page_selector = @ $filters_settings['woocommerce_pagination_class'].' .next';
        } else {
            $products_selector = $selectors_options['products'];
            $item_selector = $selectors_options['item'];
            $pagination_selector = $selectors_options['pagination'];
            $next_page_selector = $selectors_options['next_page'];
        }
        $image_class = 'lmp_rotate';
        $image = '<div class="lmp_products_loading">';
        if ( $general_options['loading_image'] ) {
            $image .= '<i class="fa '.$general_options['loading_image'].' '.$image_class.'"></i>';
        } else {
            $image .= '<i class="fa fa-spinner '.$image_class.'"></i>';
        }
        $image .= '</div>';
        $load_more_button = self::get_load_more_button();
        wp_localize_script(
            'berocket_lmp_js',
            'the_lmp_js_data',
            array(
                'type'          => $general_options['type'],
                'use_mobile'    => $general_options['use_mobile'],
                'mobile_type'   => $general_options['mobile_type'],
                'mobile_width'  => $general_options['mobile_width'],
                'is_AAPF'       => br_is_plugin_active( 'filters', '2.0.5' ),
                'buffer'        => $general_options['buffer'],

                'load_image'    => $image,
                'load_img_class'=> '.lmp_products_loading',

                'load_more'     => $load_more_button,

                'lazy_load'     => false,

                'end_text'      => '',

                'javascript'    => $javascript_options,

                'products'      => $products_selector,
                'item'          => $item_selector,
                'pagination'    => $pagination_selector,
                'next_page'     => $next_page_selector,
            )
        );
    }

    public static function include_front() {
        wp_enqueue_script( 'berocket_lmp_js', plugins_url( 'js/load_products.js', __FILE__ ), array( 'jquery' ), BeRocket_Load_More_Products_version );
        wp_enqueue_script( 'berocket_lmp_lazyloadXT', plugins_url( 'js/jquery.lazyloadxt.min.js', __FILE__ ), array( 'jquery' ), BeRocket_Load_More_Products_version );
        wp_register_style( 'berocket_lmp_style', plugins_url( 'css/load_products.css', __FILE__ ), "", BeRocket_Load_More_Products_version );
        wp_enqueue_style( 'berocket_lmp_style' );
        wp_register_style( 'berocket_lmp_animate', plugins_url( 'css/animate.min.css', __FILE__ ), "", BeRocket_Load_More_Products_version );
        wp_enqueue_style( 'berocket_lmp_animate' );
        wp_register_style( 'font-awesome', plugins_url( 'css/font-awesome.min.css', __FILE__ ) );
        wp_enqueue_style( 'font-awesome' );
        self::add_javascript_data();
    }

    public static function include_admin() {
        if( @ $_GET['page'] == 'br-load-more-products' ) {
            if ( function_exists( 'wp_enqueue_media' ) ) {
                wp_enqueue_media();
            } else {
                wp_enqueue_style( 'thickbox' );
                wp_enqueue_script( 'media-upload' );
                wp_enqueue_script( 'thickbox' );
            }
            wp_register_style( 'berocket_lmp_admin_style', plugins_url( 'css/admin.css', __FILE__ ), "", BeRocket_Load_More_Products_version );
            wp_enqueue_style( 'berocket_lmp_admin_style' );
            wp_register_style( 'berocket_lmp_fa_select_style', plugins_url( 'css/select_fa.css', __FILE__ ), "", BeRocket_Load_More_Products_version );
            wp_enqueue_style( 'berocket_lmp_fa_select_style' );
            wp_enqueue_script( 'berocket_lmp_admin', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), BeRocket_Load_More_Products_version );
            wp_enqueue_script( 'berocket_lmp_admin_fa', plugins_url( 'js/admin_select_fa.js', __FILE__ ), array( 'jquery' ), BeRocket_Load_More_Products_version );
            wp_enqueue_script( 'berocket_aapf_widget-colorpicker', plugins_url( 'js/colpick.js', __FILE__ ), array( 'jquery' ) );
            wp_register_style( 'berocket_aapf_widget-colorpicker-style', plugins_url( 'css/colpick.css', __FILE__ ) );
            wp_enqueue_style( 'berocket_aapf_widget-colorpicker-style' );
        }
        
        register_setting('br_lmp_general_settings', 'br_lmp_general_settings', array( __CLASS__, 'sanitize_lmp_option' ));
        register_setting('br_lmp_button_settings', 'br_lmp_button_settings', array( __CLASS__, 'sanitize_lmp_option' ));
        register_setting('br_lmp_selectors_settings', 'br_lmp_selectors_settings', array( __CLASS__, 'sanitize_lmp_option' ));
        register_setting('br_lmp_lazy_load_settings', 'br_lmp_lazy_load_settings', array( __CLASS__, 'sanitize_lmp_option' ));
        register_setting('br_lmp_messages_settings', 'br_lmp_messages_settings', array( __CLASS__, 'sanitize_lmp_option' ));
        register_setting('br_lmp_javascript_settings', 'br_lmp_javascript_settings', array( __CLASS__, 'sanitize_lmp_option' ));
        register_setting('br_lmp_license_settings', 'br_lmp_license_settings', array( __CLASS__, 'sanitize_lmp_option' ));
        add_settings_section( 
            'br_lgv_general_page',
            'General Settings',
            'br_lmp_general_callback',
            'br_lmp_general_settings'
        );

        add_settings_section( 
            'br_lgv_button_page',
            'Button Settings',
            'br_lmp_button_callback',
            'br_lmp_button_settings'
        );

        add_settings_section( 
            'br_lgv_selectors_page',
            'Selectors Settings',
            'br_lmp_selectors_callback',
            'br_lmp_selectors_settings'
        );

        add_settings_section( 
            'br_lgv_lazy_load_page',
            'Lazy Load Settings',
            'br_lmp_lazy_load_callback',
            'br_lmp_lazy_load_settings'
        );

        add_settings_section( 
            'br_lgv_messages_page',
            'Messages Settings',
            'br_lmp_messages_callback',
            'br_lmp_messages_settings'
        );

        add_settings_section( 
            'br_lgv_javascript_page',
            'JavaScript Settings',
            'br_lmp_javascript_callback',
            'br_lmp_javascript_settings'
        );

        add_settings_section( 
            'br_lgv_license_page',
            'License Settings',
            'br_lmp_license_callback',
            'br_lmp_license_settings'
        );
    }
    /**
     * Function add options button to admin panel
     *
     * @access public
     *
     * @return void
     */
    public static function lmp_options() {
        add_submenu_page( 'woocommerce', __('Load More Products settings', BeRocket_LMP_domain), __('Load More Products', BeRocket_LMP_domain), 'manage_options', 'br-load-more-products', array(
            __CLASS__,
            'lmp_option_form'
        ) );
    }
    /**
     * Function add options form to settings page
     *
     * @access public
     *
     * @return void
     */
    public static function lmp_option_form() {
        include LMP_TEMPLATE_PATH . "settings.php";
    }

    public static function get_lmp_default( $option_name ) {
        $options = BeRocket_LMP::$defaults[$option_name];
        return $options;
    }
    public static function sanitize_lmp_option( $input ) {
        $default = BeRocket_LMP::$defaults[$input['settings_name']];
        $result = self::recursive_array_set( $default, $input );
        return $result;
    }
    public static function recursive_array_set( $default, $options ) {
        foreach( $default as $key => $value ) {
            if( array_key_exists( $key, $options ) ) {
                if( is_array( $value ) ) {
                    if( is_array( $options[$key] ) ) {
                        $result[$key] = self::recursive_array_set( $value, $options[$key] );
                    } else {
                        $result[$key] = self::recursive_array_set( $value, array() );
                    }
                } else {
                    $result[$key] = $options[$key];
                }
            } else {
                if( is_array( $value ) ) {
                    $result[$key] = self::recursive_array_set( $value, array() );
                } else {
                    $result[$key] = '';
                }
            }
        }
        return $result;
    }
    public static function get_lmp_option( $option_name ) {
        $options = get_option( $option_name );
        if ( @ $options && is_array ( $options ) ) {
            $options = array_merge( BeRocket_LMP::$defaults[$option_name], $options );
        } else {
            $options = BeRocket_LMP::$defaults[$option_name];
        }
        return $options;
    }
}
new BeRocket_LMP;
?>