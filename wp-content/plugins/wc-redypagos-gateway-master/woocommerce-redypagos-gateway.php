<?php
/**
 * Plugin Name: Woocommerce RedyPagos Gateway
 * Description: Pasarela de pago la cual te permite pagar con tus tarjetas de crédito y débito preferidas.
 * Version: 1.0.0
 * Author: ComprasEC
 * Author URI: https://www.compras-ec.com/
 * Copyright: © 2021 ComprasEC
 * WC tested up to: 5.7
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP: 7.0
 * Contributor: Github - @mvargaslandolfi1993
 *
 * @package WCRedypagosGateway
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
 
// Check if WooCommerce is active.
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ), true ) && ! ( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) ) ) {
	return;
}

if ( !function_exists( 'rp_check_woocommerce_is_active' ) ) {

	/**
	 * verify that the plugin Woocommerce is active
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function rp_check_woocommerce_is_active()
	{
		if (!is_plugin_active('woocommerce/woocommerce.php')) {
			add_action('admin_notices', 'rp_dependency_plugin_woocommerce_notice');
			deactivate_plugins(plugin_basename(__FILE__));
			if (isset($_GET['activate'])) {
				unset($_GET['activate']);
			}
		}
	}

	/**
	 * notify that the woocommerce plugin is not installed
	 */
	function rp_dependency_plugin_woocommerce_notice()
	{
		?>
		<div class="error">
			<p>Lo sentimos, debe instalar el plugin <b>Woocommerce</b> antes de utilizar <b>Woocommerce Redypagos Gateway</b></p>
		</div>
		<?php
	}

    add_action('admin_init', 'rp_check_woocommerce_is_active');
}

//include functions
require_once 'includes/wc-redypagos-functions.php';

if ( !function_exists( 'redypagos_add_gateway_class' ) ) {
    /**
     * This function is used to add RedyPagos Gateway
     * @version  1.0.0
     * @since    1.0.0
     */

    function redypagos_add_gateway_class( $gateways ) {
        $gateways[] = 'WC_Redypagos_Gateway';
        return $gateways;
    }

    add_filter( 'woocommerce_payment_gateways', 'redypagos_add_gateway_class' );
}


if( !function_exists( 'redypagos_init_gateway_class' ) ) :
    
    function redypagos_init_gateway_class() {
        /**
         * Main AL_Andina_Licores_Webservices Class
         *
         * @class   AL_Andina_Licores_Webservices
         * @version 1.0.0
         * @since   1.0.0
         */

        class WC_Redypagos_Gateway extends WC_Payment_Gateway
        {
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
             * @var   WC_Redypagos_Gateway The single instance of the class
             * @since 1.0.0
             */
            protected static $inst = null;

            /**
             * Main WC_Redypagos_Gateway Instance
             *
             * Ensures only one instance of WC_Redypagos_Gateway is loaded or can be loaded.
             *
             * @version 1.0.0
             * @since   1.0.0
             * @static
             * @return  WC_Redypagos_Gateway - Main instance
             */
            public static function instance() {
                if ( is_null( self::$inst ) ) {
                    self::$inst = new self();
                }
                return self::$inst;
            }
            
            /**
             * WC_Redypagos_Gateway Constructor.
             *
             * @version 1.0.0
             * @since   1.0.0
             * @access  public
             */
            public function __construct() {
                
                $this->includes();

                $this->id = 'wc_redypagos_gateway';
                $this->icon = plugins_url( 'assets/img/redypagos-logo.jpg', __FILE__ );
                $this->has_fields = true; 
                $this->method_title = 'Redypagos';
                $this->method_description = 'Redypagos permite a los clientes pagar directa y fácilmente con cualquier tarjeta de crédito o débito del Ecuador.'; 
                $this->supports = array(
                    'products'
                );
                $this->init_form_fields();
                $this->init_settings();
                $this->title                = $this->get_option( 'title' );
                $this->description          = $this->get_option( 'description' );
                $this->enabled              = $this->get_option( 'enabled' );
                $this->environment          = $this->get_option('environment');
                $this->user                 = $this->environment === "PRUEBA" ? $this->get_option('user_sandbox') : $this->get_option( 'user_production' );
                $this->password             = $this->environment === "SANDBOX" ? $this->get_option('password_sandbox') : $this->get_option( 'password_production' );
                $this->id_comercio          = $this->get_option( 'id_comercio' );
                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            }

            /**
             * 
             */
            public function init_form_fields(){
    
                $this->form_fields = array(
                    array(
                        'title' => __( 'Configuración general', 'woocommerce-redypagos-gateway' ),
                        'type'  => 'title',
                    ),
                    'enabled' => array(
                        'title'    => __( 'Activado/Desactivado', 'woocommerce-redypagos-gateway' ),
                        'label'       => 'Redypagos',
                        'type'        => 'checkbox',
                        'description' => '',
                        'default'     => 'no'
                    ),
                    'title' => array(
                        'title'    => __( 'Titulo', 'woocommerce-redypagos-gateway' ),
                        'type'        => 'text',
                        'description' => 'Esto controla el título que ve el usuario durante el pago.',
                        'default'     => 'Pago con tarjeta',
                        'desc_tip'    => true,
                    ),
                    'description' => array(
                        'title'    => __( 'Descripción', 'woocommerce-redypagos-gateway' ),
                        'type'        => 'textarea',
                        'description' => 'Esto controla el título que ve el usuario durante el pago.',
                        'default'     => 'Pague con su tarjeta de crédito o débito preferida a través de nuestra pasarela de pago.',
                    ),
                    'environment' => array(
                        'title'    => __( 'Tipo de Ambiente', 'woocommerce-redypagos-gateway' ),
                        'description' => 'Modo de prueba o producción',
                        'type' => 'select',
                        'default' => 'Static',
                        'options' => array(
                            'SANDBOX'        => 'Prueba',
                            'PRODUCTION'     => 'Producción',
                        )
                    ),
                    array(
                        'title' => __( 'Credenciales de prueba', 'woocommerce-redypagos-gateway' ),
                        'type'  => 'title',
                    ),
                    'user_sandbox' => array(
                        'title'    => __( 'Usuario', 'woocommerce-redypagos-gateway' ),
                        'type'        => 'text',
                        'description' => 'Usuario para uso de webservices proporcionado por Redypagos',
                    ),
                    'password_sandbox' => array(
                        'title'    => __( 'Contraseña', 'woocommerce-redypagos-gateway' ),
                        'type'        => 'password',
                        'description' => 'Contraseña para uso de webservices proporcionado por Redypagos',
                    ),
                    array(
                        'title' => __( 'Credenciales de producción', 'woocommerce-redypagos-gateway' ),
                        'type'  => 'title',
                    ),
                    'user_production' => array(
                        'title'    => __( 'Usuario', 'woocommerce-redypagos-gateway' ),
                        'type'        => 'text',
                        'description' => 'Usuario para uso de webservices proporcionado por Redypagos',
                    ),
                    'password_production' => array(
                        'title'    => __( 'Contraseña', 'woocommerce-redypagos-gateway' ),
                        'type'        => 'password',
                        'description' => 'Contraseña para uso de webservices proporcionado por Redypagos',
                    ),
                    'id_comercio' => array(
                        'title'    => __( 'ID de comercio', 'woocommerce-redypagos-gateway' ),
                        'type'        => 'text',
                        'description' => 'ID de comercio suministrado por Redypagos.',
                    ),  
                );
            }       

            /**
             * 
             */
            public function payment_fields() {

                $gateways = WC()->payment_gateways->get_available_payment_gateways();
                $user = get_user_meta(wp_get_current_user()->ID);
            
                if ( ! isset( $gateways['wc_redypagos_gateway'] )) return;

                if ( $this->description ) {

                    $urlLogo = plugins_url( 'assets/img/redypagos-logo.jpg', __FILE__ );
                    $logo = "<img src='".$urlLogo."' style='float:none; margin:20px auto;'>";

                    echo wpautop( wp_kses_post( "Genera tu link de pago y realiza el pago con tu tarjeta Visa o Mastercard de preferencia<br>" ) );
                    if ($user['_cli_lista'][0] !== "5") {
                        echo wpautop( wp_kses_post( "<small><strong>Los pagos con tarjeta tienen un recargo del %3</strong></small>" ) );
                    }
                }    
            }

            /**
             * This function is used to process payment and generate link with Redypagos
             */
            public function process_payment( $order_id ) {
                global $woocommerce;
                $order = wc_get_order( $order_id );

                $res = (new WC_Redypagos_Api())->generateLink($order, $this->id_comercio, get_user_meta(wp_get_current_user()->ID));

                if ( !$res['error'] ) {
                    
                    $order->update_status('on-hold', __( 'Esperando confirmacion de pago', 'woocommerce' ));
                    $woocommerce->cart->empty_cart();
                    
                    $order->add_order_note( 'Link para comprobante de pago:  https://andinalicores.com.ec/redypagos-comprobante/?order_id='. $order_id .'&action=uplf', false );
                    
                    $order->add_order_note( 'Link de pago generado '. $res['msg'], true );
                    
                    
                    add_post_meta( $order_id, 'redypagos_link', $res['msg']);

                    return array(
                        'result' => 'success',
                        'redirect' => $this->get_return_url( $order )
                    );
                }else{
                    $order->add_order_note( "Error Descripción :" . $res['rp_msg'], false );
                    
                    update_post_meta( $order_id, "error_redypagos", $res['rp_msg'] );
                    
                    wc_add_notice( $res['msg'], 'error' );
                    
                    return;
                }
            }

            /**
            * Include required core files used in admin and on the frontend.
            * @version 1.0.0
            * @since   1.0.0
            */
            public function includes() {
                require_once 'includes/wc-redypagos-functions.php';
                //Classes
                require_once 'includes/classes/wc-redypagos-api.php';
            }
        }
    }

    add_action( 'plugins_loaded', 'redypagos_init_gateway_class' );

endif;
 
if ( function_exists( 'rp_add_action_plugin' ) ) {
    /**
     * Show action links on the plugin screen.
     * @version 1.0.0
     * @since   1.0.0
     */
    function rp_add_action_plugin( $actions, $plugin_file )  {
    
        static $plugin;
        if (!isset($plugin))
            $plugin = plugin_basename(__FILE__);
        if ($plugin == $plugin_file) {
            $settings = array('Ajustes' => '<a href="admin.php?page=wc-settings&tab=checkout&section=wc_redypagos_gateway">' . __('Ajustes', 'General') . '</a>');
            $actions = array_merge($settings, $actions);
        }
        return $actions;
    }
    add_filter( 'plugin_action_links', 'rp_add_action_plugin', 10, 5 );
}