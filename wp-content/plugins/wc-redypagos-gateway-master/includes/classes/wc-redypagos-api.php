<?php
/**
 * Woocommerce Redypagos Gateway - API
 *
 * @package WCRedypagosGateway
 * @version 1.0.0
 * @since   1.0.0
 * @author  ComprasEC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Redypagos_Api' ) ) :

	/**
	 * WC_Redypagos_Api Class
	 * @class   WC_Redypagos_Api
	 * @version 1.0.0
	 * @since   1.0.0
	 */
    class WC_Redypagos_Api
    {
        /**
         * This function is used to generate pay link with Redypagos
         * @version 1.0.0
         * @since   1.0.0
         * @param   object $order instance from Woocommerce
         * @param   string $id_comercio ID comercio assigned by Redypagos
         */
        public function generateLink( object $order, string $id_comercio, array $user )
        {
            $data = [
                "error"     => false,
                "msg"       => "",
                "rp_msg"    => ""
            ];

            $response = $this->connect(
                get_option( 'woocommerce_wc_redypagos_gateway_settings' ),
                $this->getParams( $order, $id_comercio, $this->getTotals( $order ), $user )
            );
            $this->custom_logs($response);
            if ($response) {
                $response = json_decode($response);
                if ($response->estado === 0 && isset( $response->contenido ) ) {
                    $data['msg'] = $response->contenido;
                }else{
                    $data['error'] = true;
                    $data['rp_msg'] = $response->mens;
                    $data['msg']   = "Disculpe las molestias, hubo un error generando el link de pago, por favor intente nuevamente en unos minutos o comuniquese con Soporte de Andina Licores.";
                }
            }else{
                $data['error'] = true;
                $data['msg']   = "Disculpe las molestias, hubo un error generando el link de pago, por favor intente nuevamente en unos minutos o comuniquese con Soporte de Andina Licores.";
            }

            return $data;
        }

        /**
         * This function is used to connect with Redypagos API
         * @version 1.0.0
         * @since   1.0.0
         * @param   object $order instance from Woocommerce
         * @param   array  $options Options from Redypagos Gateway
         * @param   object $params Method GetParams
         */
        private function connect( array $options, object $params )
        {
            try {

                if ( 'SANDBOX' === $options['environment'] && !empty( $options['user_sandbox'] ) && empty( $options['password_sandbox'] ) ) {
                    $credentials = $options['user_sandbox']. ":" .$options['password_sandbox'];
                    $url = 'https://apipagos.redypago.com/crear_solicitud_pago';
                } else if ('PRODUCTION' === $options['environment'] && !empty( $options['user_production'] ) && empty( $options['password_sandbox'] ) ) {
                    $credentials = $options['user_production']. ":" .$options['password_production'];
                    $url = 'https://apipagos.redypago.com/crear_solicitud_pago';
                }
                
                $args = array(
                    'headers' => array(
                        'Authorization' => 'Basic ' . base64_encode( $credentials ),
                        'Content-Type' => 'application/json'
                    ),
                    'method' => 'POST',
                    'body' => json_encode($params)
                );
                $this->custom_logs($args);
                return wp_remote_retrieve_body( wp_remote_request($url, $args) );
            } catch (Exception $e) {
                $this->custom_logs($e->getMessage());
                return $e->getMessage();
            }
        }

        /**
         * This function is used to get all parmas for used Redypagos API
         * @version 1.0.0
         * @since   1.0.0
         * @param   object $order instance from Woocommerce
         * @param   string $id_comercio ID comercio assigned by Redypagos
         * @param   object $totals Method getTotals()
         */
        private function getParams( object $order, $id_comercio, $totals, $user ) 
        {
            return (object) [
                "idCommerce"        => $id_comercio,
                "nombres"           => $user['first_name'][0],
                "apellidos"         => $user['last_name'][0],
                "identificacion"    => $user['_cli_ruc'][0],
                "email"             => sanitize_email( $order->get_billing_email() ),
                "telefono"          => $order->get_billing_phone(),
                "direccion"         => $order->get_billing_address_1(),
                "descripcion"       => "Solicitud de pago pendiente generado desde plataforma B2B",
                "montoBase12"       => $totals->totalTarifa12,
                "montoBase0"        => $totals->totalTarifa0,
                "totalIva"          => number_format( round( $order->get_total_tax(), 2 ), 2, ".", ","),
                "valorPago"         => number_format( round( $order->get_total(), 2 ), 2, ".", ",")
            ];
        }   

        /**
         * This function is used to calculate rate total 12 and 0
         * @version 1.0.0
         * @since   1.0.0
         * @param   object $order instance from Woocommerce
         * @return  object 
         */
        private function getTotals( $order ) {
            $totalTarifa12  = 0;
            $totalTarifa0   = 0;
            $items          = $order->get_items();
    
            foreach ($items as $item) {
                if ($order->get_line_tax($item) > 0) {
                    $totalTarifa12  += $order->get_line_total($item, false, true);
                } else {
                    $totalTarifa0   += $order->get_line_total($item, false, true);
                }
            }
            
            return (object) [
                "totalTarifa12"  => number_format( round( $totalTarifa12, 2 ), 2, ".", ","),
                "totalTarifa0"   => number_format( round( $totalTarifa0, 2 ), 2, ".", ","),
            ];
        }

        /**
		 * Custom logs.
		 * @version  	1.0.0
		 * @since   	1.0.0
		 * @param		string | Message	
		 */
		protected function custom_logs($message) 
		{ 
			if( is_array( $message ) ) { 
				$message = json_encode($message); 
			} 
			$file = fopen( "./redypagos.log","a" ); 
			fwrite( $file, "\n" . date('Y-m-d h:i:s') . " :: " . $message ); 
			fclose($file); 
		}
    }
endif;    