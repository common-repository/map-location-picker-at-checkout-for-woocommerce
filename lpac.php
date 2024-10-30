<?php

/**
 * Kikote - Location Picker at Checkout Plugin for WooCommerce.
 *
 * @link              https://uriahsvictor.com
 * @link              https://github.com/UVLabs/location-picker-at-checkout-for-woocommerce
 * @since             1.0.0
 * @package           Lpac
 *
 * @wordpress-plugin
 * Plugin Name:       Kikote - Location Picker at Checkout for WooCommerce
 * Plugin URI:        https://lpacwp.com
 * Description:       Allow customers to choose their shipping or pickup location using a map at checkout.
 * Version:           1.10.4
 * Requires at least: 5.7
 * Author:            Uriahs Victor
 * Author URI:        https://lpacwp.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       map-location-picker-at-checkout-for-woocommerce
 * Domain Path:       /languages
 * WC requires at least: 5.5
 * WC tested up to: 9.3
 * Requires Plugins: woocommerce
 * Requires PHP: 7.4
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
if ( !defined( 'LPAC_VERSION' ) ) {
    define( 'LPAC_VERSION', '1.10.4' );
}
/**
 * Check PHP version
 */
if ( function_exists( 'phpversion' ) ) {
    if ( version_compare( phpversion(), '7.4', '<' ) ) {
        add_action( 'admin_notices', function () {
            echo "<div class='notice notice-error is-dismissible'>";
            /* translators: 1: Opening <p> HTML element 2: Opening <strong> HTML element 3: Closing <strong> HTML element 4: Closing <p> HTML element  */
            echo sprintf(
                esc_html__( '%1$s%2$sKikote - Location Picker at Checkout for WooCommerce NOTICE:%3$s PHP version too low to use this plugin. Please change to at least PHP 7.4. You can contact your web host for assistance in updating your PHP version.%4$s', 'map-location-picker-at-checkout-for-woocommerce' ),
                '<p>',
                '<strong>',
                '</strong>',
                '</p>'
            );
            echo '</div>';
        } );
        return;
    }
}
/**
 * Check PHP versions
 */
if ( defined( 'PHP_VERSION' ) ) {
    if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
        add_action( 'admin_notices', function () {
            echo "<div class='notice notice-error is-dismissible'>";
            /* translators: 1: Opening <p> HTML element 2: Opening <strong> HTML element 3: Closing <strong> HTML element 4: Closing <p> HTML element  */
            echo sprintf(
                esc_html__( '%1$s%2$sKikote - Location Picker at Checkout for WooCommerce NOTICE:%3$s PHP version too low to use this plugin. Please change to at least PHP 7.4. You can contact your web host for assistance in updating your PHP version.%4$s', 'map-location-picker-at-checkout-for-woocommerce' ),
                '<p>',
                '<strong>',
                '</strong>',
                '</p>'
            );
            echo '</div>';
        } );
        return;
    }
}
/**
 * Check that WooCommerce is active.
 *
 * This needs to happen before freemius does any work.
 *
 * @since 1.0.0
 */
if ( !function_exists( 'sl_wc_active' ) ) {
    function sl_wc_active() {
        $active_plugins = (array) apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }
        return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) || class_exists( 'WooCommerce' );
    }

}
if ( !sl_wc_active() ) {
    add_action( 'admin_notices', function () {
        echo "<div class='notice notice-error is-dismissible'>";
        /* translators: 1: Opening <p> HTML element 2: Opening <strong> HTML element 3: Closing <strong> HTML element 4: Closing <p> HTML element  */
        echo sprintf(
            esc_html__( '%1$s%2$sKikote - Location Picker at Checkout for WooCommerce NOTICE:%3$s WooCommerce is not activated, please activate it to use the plugin.%4$s', 'map-location-picker-at-checkout-for-woocommerce' ),
            '<p>',
            '<strong>',
            '</strong>',
            '</p>'
        );
        echo '</div>';
    } );
    return;
}
if ( function_exists( 'lpac_fs' ) ) {
    lpac_fs()->set_basename( false, __FILE__ );
} else {
    // Setup Freemius.
    if ( !function_exists( 'lpac_fs' ) ) {
        /**
         * Create a helper function for easy SDK access.
         *
         * @return mixed
         * @throws Freemius_Exception Freemius Exception.
         * @since 1.0.0
         */
        function lpac_fs() {
            global $lpac_fs;
            if ( !isset( $lpac_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/vendor/freemius/wordpress-sdk/start.php';
                $lpac_fs = fs_dynamic_init( array(
                    'id'              => '8507',
                    'slug'            => 'map-location-picker-at-checkout-for-woocommerce',
                    'premium_slug'    => 'map-location-picker-at-checkout-for-woocommerce-pro',
                    'type'            => 'plugin',
                    'public_key'      => 'pk_da07de47a2bdd9391af9020cc646d',
                    'is_premium'      => false,
                    'premium_suffix'  => 'PRO',
                    'has_addons'      => false,
                    'has_paid_plans'  => true,
                    'trial'           => array(
                        'days'               => 14,
                        'is_require_payment' => true,
                    ),
                    'contact'         => true,
                    'has_affiliation' => 'selected',
                    'menu'            => array(
                        'slug'   => 'lpac-menu',
                        'parent' => array(
                            'slug' => 'sl-plugins-menu',
                        ),
                    ),
                    'is_live'         => true,
                ) );
            }
            return $lpac_fs;
        }

        // Init Freemius.
        lpac_fs();
        /**
         * Signal that SDK was initiated.
         *
         * @since 1.0.1
         */
        do_action( 'lpac_fs_loaded' );
    }
    /**
     * Composer autoload. DO NOT PLACE THIS LINE BEFORE FREEMIUS SDK RUNS.
     *
     * Doing that will cause the plugin to throw an error when trying to activate PRO when the Free version is active or vice versa.
     * This is because both PRO and Free are generated from the same codebase, meaning composer autoloader file would already be
     * present and throw an error when trying to be redefined.
     */
    require_once dirname( __FILE__ ) . '/vendor/autoload.php';
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-lpac-activator.php
     */
    if ( !function_exists( 'activate_lpac' ) ) {
        /**
         * Code that runs when the plugin is activated.
         *
         * @return void
         * @since 1.0.0
         */
        function activate_lpac() {
            require_once plugin_dir_path( __FILE__ ) . 'includes/class-lpac-activator.php';
            Lpac_Activator::activate();
        }

    }
    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-lpac-deactivator.php
     */
    if ( !function_exists( 'deactivate_lpac' ) ) {
        /**
         * Code that runs when the plugin is deactivated.
         *
         * @return void
         * @since 1.0.0
         */
        function deactivate_lpac() {
            require_once plugin_dir_path( __FILE__ ) . 'includes/class-lpac-deactivator.php';
            Lpac_Deactivator::deactivate();
        }

    }
    register_activation_hook( __FILE__, 'activate_lpac' );
    register_deactivation_hook( __FILE__, 'deactivate_lpac' );
    /**
     * Map Builder
     */
    function lpac_redirect_to_map_builder() {
        ?>
			<h1><?php 
        esc_html_e( 'Map Builder', 'map-location-picker-at-checkout-for-woocommerce' );
        ?> (PRO)</h1>
			<p style='font-size: 18px'><?php 
        esc_html_e( 'Create custom maps showing your store locations and serviceable areas and add them anywhere on your website using a shortcode.', 'map-location-picker-at-checkout-for-woocommerce' );
        ?> <a href='https://lpacwp.com/docs/map-builder/' target='_blank'><?php 
        esc_html_e( 'Learn more', 'map-location-picker-at-checkout-for-woocommerce' );
        ?> >></a></p>
			<p><img src="<?php 
        echo LPAC_PLUGIN_ASSETS_PATH_URL;
        ?>img/map-builder.png" alt="kikote map builder screenshot" width='1000px'></p>
		<?php 
    }

    if ( !defined( 'WP_FS__DEFAULT_PRIORITY' ) ) {
        define( 'WP_FS__DEFAULT_PRIORITY', 10 );
    }
    lpac_fs()->add_submenu_item(
        esc_html__( 'Map Builder', 'map-location-picker-at-checkout-for-woocommerce' ),
        'lpac_redirect_to_map_builder',
        $page_title = true,
        $capability = 'manage_options',
        $menu_slug = 'map-builder',
        $before_render_function = false,
        $priority = WP_FS__DEFAULT_PRIORITY,
        $show_submenu = true,
        $class = ''
    );
    require __DIR__ . '/class-lpac-uninstall.php';
    require __DIR__ . '/admin-pointers.php';
    lpac_fs()->add_action( 'after_uninstall', array(new Lpac_Uninstall(), 'remove_plugin_settings') );
    lpac_fs()->add_filter( 'show_deactivation_subscription_cancellation', '__return_false' );
    lpac_fs()->add_filter( 'plugin_icon', function () {
        return dirname( __FILE__ ) . '/assets/img/logo.png';
    } );
    /**
     * Constants
     */
    define( 'LPAC_BASE_FILE', basename( plugin_dir_path( __FILE__ ) ) );
    define( 'LPAC_PLUGIN_NAME', 'lpac' );
    define( 'LPAC_PLUGIN_DIR', __DIR__ . '/' );
    define( 'LPAC_PLUGIN_ASSETS_DIR', __DIR__ . '/assets/' );
    define( 'LPAC_PLUGIN_ASSETS_PATH_URL', plugin_dir_url( __FILE__ ) . 'assets/' );
    define( 'LPAC_PLUGIN_PATH_URL', plugin_dir_url( __FILE__ ) );
    define( 'LPAC_INSTALLED_AT_VERSION', get_option( 'lpac_installed_at_version', constant( 'LPAC_VERSION' ) ) );
    define( 'LPAC_IS_PREMIUM_VERSION', lpac_fs()->is_premium() );
    define( 'LPAC_GOOGLE_MAPS_API_LINK', 'https://maps.googleapis.com/maps/api/js?key=' );
    define( 'LPAC_GOOGLE_MAPS_API_KEY', get_option( 'lpac_google_maps_api_key', '' ) );
    define( 'LPAC_GOOGLE_MAPS_DIRECTIONS_LINK', 'https://maps.google.com/maps?daddr=' );
    define( 'LPAC_WAZE_DIRECTIONS_LINK', 'https://waze.com/ul?ll=' );
    $debug = false;
    if ( defined( 'SL_DEV_DEBUGGING' ) ) {
        $debug = true;
    }
    define( 'LPAC_DEBUG', $debug );
    // HPOS Compatibility
    add_action( 'before_woocommerce_init', function () {
        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    } );
    // Blocks checkout incompatibility.
    add_action( 'before_woocommerce_init', function () {
        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, false );
        }
    } );
    $version = ( LPAC_DEBUG ? 'weekly' : 'quarterly' );
    $google_params = array("v={$version}");
    $libraries = array();
    $places_autocomplete = get_option( 'lpac_enable_places_autocomplete', 'no' );
    if ( $places_autocomplete !== 'no' ) {
        array_push( $libraries, 'places' );
    }
    if ( !empty( $libraries ) ) {
        $libraries = implode( ',', $libraries );
        array_push( $google_params, "libraries={$libraries}" );
    }
    // Map Region.
    $region = get_option( 'lpac_google_map_region' );
    if ( !empty( $region ) ) {
        $google_params[] = "region={$region}";
    }
    // Callback parameter is required even though we're not making use of it.
    $google_params[] = 'callback=GMapsScriptLoaded';
    // Bring our parameters together.
    $google_params = implode( '&', $google_params );
    define( 'LPAC_GOOGLE_MAPS_PARAMS', $google_params );
    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    if ( !function_exists( 'soaringleads_kikote_init' ) ) {
        function soaringleads_kikote_init() {
            do_action( 'before_kikote_init' );
            $instance = \Lpac\Bootstrap\Main::get_instance();
            $instance->run();
            do_action( 'after_kikote_init' );
        }

    }
    add_action( 'plugins_loaded', 'soaringleads_kikote_init' );
}