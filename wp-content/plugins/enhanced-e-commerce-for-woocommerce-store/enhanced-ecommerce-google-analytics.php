<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              tatvic.com
 * @since             1.0.0
 * @package           Enhanced E-commerce for Woocommerce store
 *
 * @wordpress-plugin
 * Plugin Name:       Conversios.io - Google Analytics and Google Shopping plugin for WooCommerce
 * Plugin URI:        https://www.tatvic.com/tatvic-labs/woocommerce-extension/
 * Description:       Automates eCommerce tracking in Google Analytics, dynamic remarkting in Google Ads, and provides complete Google Shopping features.
 * Version:           4.7.3
 * Author:            Tatvic
 * Author URI:        www.tatvic.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       www.tatvic.com
 * Domain Path:       /languages
 * WC requires at least: 3.5.0
 * WC tested up to: 6.5.0
 */

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
    die;
}
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_TVC_VERSION', '4.7.3' );
$fullName = plugin_basename( __FILE__ );
$dir = str_replace('/enhanced-ecommerce-google-analytics.php','',$fullName);
if ( ! defined( 'ENHANCAD_PLUGIN_NAME' ) ) {
    define( 'ENHANCAD_PLUGIN_NAME', $dir);
}
// Store the directory of the plugin
if ( ! defined( 'ENHANCAD_PLUGIN_DIR' ) ) {
    define( 'ENHANCAD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
// Store the url of the plugin
if ( ! defined( 'ENHANCAD_PLUGIN_URL' ) ) {
    define( 'ENHANCAD_PLUGIN_URL', plugins_url() . '/' . ENHANCAD_PLUGIN_NAME );
}

if ( ! defined( 'TVC_API_CALL_URL' ) ) {
   define( 'TVC_API_CALL_URL', 'https://connect.tatvic.com/laravelapi/public/api' );
}
if ( ! defined( 'TVC_AUTH_CONNECT_URL' ) ) {    
    define( 'TVC_AUTH_CONNECT_URL', 'conversios.io' );
}

if(!defined('TVC_Admin_Helper')){
    include(ENHANCAD_PLUGIN_DIR . '/admin/class-tvc-admin-helper.php');
}

add_action( 'upgrader_process_complete', 'tvc_upgrade_function',10, 2);
function tvc_upgrade_function( $upgrader_object, $options ) {
  if ($options['action'] == 'update' && $options['type'] == 'plugin' ) {
    foreach($options['plugins'] as $each_plugin) {
      if ($each_plugin==$fullName) {         
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $TVC_Admin_Helper->update_app_status();
      }
    }    
  }
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-enhanced-ecommerce-google-analytics-activator.php
 */
function activate_enhanced_ecommerce_google_analytics() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-enhanced-ecommerce-google-analytics-activator.php';
    Enhanced_Ecommerce_Google_Analytics_Activator::activate();
    set_transient( '_conversios_activation_redirect', 1, 30 );
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-enhanced-ecommerce-google-analytics-deactivator.php
 */
function deactivate_enhanced_ecommerce_google_analytics() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-enhanced-ecommerce-google-analytics-deactivator.php';
    Enhanced_Ecommerce_Google_Analytics_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_enhanced_ecommerce_google_analytics' );
register_deactivation_hook( __FILE__, 'deactivate_enhanced_ecommerce_google_analytics' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-enhanced-ecommerce-google-analytics.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function run_enhanced_ecommerce_google_analytics() {

    $plugin = new Enhanced_Ecommerce_Google_Analytics();
    $plugin->run();

}
run_enhanced_ecommerce_google_analytics();