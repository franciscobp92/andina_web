<?php
/**
 * Plugin Name: Woocommerce Andina Webservices
 * Description: Genera las validaciones de stock, generación de pedidos y facturas con Andina Licores.
 * Version: 1.0.0
 * Author: ComprasEC
 * Author URI: https://www.compras-ec.com/
 * Copyright: © 2021 ComprasEC
 * WC tested up to: 5.7
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Contributor: Github - @mvargaslandolfi1993
 *
 * @package WebservicesAndinaLicores
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
 
// Check if WooCommerce is active.
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ), true ) && ! ( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) ) ) {
	return;
}

if (!function_exists('al_webservices_check_woocommerce_is_active')) {

	/**
	 * verify that the plugin Woocommerce is active
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function al_webservices_check_woocommerce_is_active()
	{
		if (!is_plugin_active('woocommerce/woocommerce.php')) {
			add_action('admin_notices', 'al_webservices_dependency_plugin_woocommerce_notice');
			deactivate_plugins(plugin_basename(__FILE__));
			if (isset($_GET['activate'])) {
				unset($_GET['activate']);
			}
		}
	}

	/**
	 * notify that the woocommerce plugin is not installed
	 */
	function al_webservices_dependency_plugin_woocommerce_notice()
	{
		?>
		<div class="error">
			<p>Lo sentimos, debe instalar el plugin <b>Woocommerce</b> antes de utilizar <b>Woocommerce Andina Webservices</b></p>
		</div>
		<?php
	}
}

add_action('admin_init', 'al_webservices_check_woocommerce_is_active');

if ( ! class_exists( 'AL_Andina_Licores_Webservices' ) ) :

	/**
	 * Main AL_Andina_Licores_Webservices Class
	 *
	 * @class   AL_Andina_Licores_Webservices
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	final class AL_Andina_Licores_Webservices {
		/**
		 * Plugin version.
		 *
		 * @var   string
		 * @since 1.0.0
		 */
		public $version = '1.0.0';

		/**
		 * Instance variable
		 *
		 * @var   AL_Andina_Licores_Webservices The single instance of the class
		 * @since 1.0.0
		 */
		protected static $inst = null;

		/**
		 * Main AL_Andina_Licores_Webservices Instance
		 *
		 * Ensures only one instance of AL_Andina_Licores_Webservices is loaded or can be loaded.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @static
		 * @return  AL_Andina_Licores_Webservices - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$inst ) ) {
				self::$inst = new self();
			}
			return self::$inst;
		}
		
		/**
		 * AL_Andina_Licores_Webservices Constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @access  public
		 */
		public function __construct() {

			// Include required files.
			$this->includes();

			// Admin.
			if ( is_admin() ) {
				add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
				// Settings.
				require_once 'includes/settings/class-al-webservices-settings-section.php';
				$this->settings                = array();
				$this->settings['general']     = require_once 'includes/settings/class-al-webservices-settings-general.php';
			}
		}

		/**
		 * Show action links on the plugin screen.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @param   mixed $links Action link.
		 * @return  array
		 */
		public function action_links( $links ) {
			$custom_links   = array();
			$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=al_andina_licores_webservices' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
			return array_merge( $custom_links, $links );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function includes() {
			
			// Functions.
			require_once 'includes/al-webservices-functions.php';
			//class
			require_once 'includes/connection/class-andina-licores-user.php';
			require_once 'includes/connection/class-al-connection-webservice.php';
			require_once 'includes/connection/class-al-connection-generate-order.php';
			require_once 'includes/connection/class-al-sync-single-products.php';
			
		}

		/**
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @param   mixed $settings Setting link.
		 */
		public function add_woocommerce_settings_tab( $settings ) {
			$settings[] = require_once 'includes/settings/class-al-settings-webservices.php';
			return $settings;
		}

		/**
		 * Get the plugin url.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @return  string
		 */
		public function plugin_url() {
			return untrailingslashit( plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @return  string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

	}

endif;

if ( ! function_exists( 'al_andina_licores_webservices' ) ) {
	/**
	 * Returns the main instance of AL_Andina_Licores_Webservices to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  AL_Andina_Licores_Webservices
	 */
	function al_andina_licores_webservices() {
		return AL_Andina_Licores_Webservices::instance();
	}
}

al_andina_licores_webservices();
