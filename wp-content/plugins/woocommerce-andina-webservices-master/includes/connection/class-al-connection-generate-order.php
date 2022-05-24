<?php
/**
 * Woocommerce Andina Licores Webservices  - Generate Order
 *
 * @package WebservicesAndinaLicores
 * @version 1.0.0
 * @since   1.0.0
 * @author  ComprasEC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'AL_Generate_Order' ) ) :
    
    /**
	 * AL_Generate_Order Class
	 *
	 * @class   AL_Generate_Order
	 * @version 1.0.0
	 * @since   1.0.0
	 */
    class AL_Generate_Order extends AL_Connection_Webservice {
        
        /**
         * This function is used to generate a new order or invoice
         * @param   1.0.0
         * @since   1.0.0
         * @param   integer $order_id
         * @param   boolean This variable is used to determinate if is a order or invoice.
         */
        public function generate_new_order_or_invoice( int $order_id, $isInvoice = false )
        {
            return $this->create_new_order_or_invoice( wc_get_order( $order_id ), get_post_custom( $order_id ), $isInvoice );
        }

        /**
         * This function is used to create a new order
         * @version  1.0.0
         * @since    1.0.0
         * @param    object $order Instance wc_get_order Woocommerce
         * @param    array  $meta  Custom post order meta
         * @param    boolean $isInvoice Determine if is invoice or order
         */
        private function create_new_order_or_invoice( $order, $meta, $isInvoice )
        {
            $response = [
                "error" => false,
                "message" => []
            ];
            $this->custom_logs("Empiza Orden N°". $order->get_id());
            if( $order->get_total() > 0) {
                $invoice  = $isInvoice 
                            ? $this->insert_invoice_header( $this->get_headers( $order->get_customer_note() ) ) 
                            : $this->insert_order_header($this->get_headers( $order->get_customer_note() ) );
    
                if ($invoice !== 0 && !empty($invoice) ) {
                    foreach($order->get_items() as $item) {
                        $product   = $item['variation_id'] == 0 ? $item->get_product() : wc_get_product( $item['variation_id'] );
                        
                        $res = $this->consultApiMaterials( $product, $item->get_quantity() );
                        
                        $res =  json_decode( $res );
    
                        if ($this->get_user_list() !== "5" && $order->get_payment_method() === "wc_redypagos_gateway") {
                            $price = $res->Materiales[0]->V_PRECIO_R;
                        }else {
                            $price = $res->Materiales[0]->PVP;
                        }
    
                        $details = $this->set_details_ped_fac( 
                            $this->get_params_details_ped_fac( 
                                $invoice, 
                                $product->get_id(), 
                                $item->get_quantity(), 
                                $order->get_payment_method(), 
                                $order->get_id(), 
                                $item, 
                                $price 
                            ) 
                        );
    
                        if ($details  === 0) {
                            $response['error'] = true;
                            $response['message'][] = [
                                "product" => $product->get_id(), 
                                "response" => $details->wsInsertDetalle_Ped_Fac
                            ];
                            break;
                        }
                    }
                    
                    $total = get_post_meta($order->get_id(), '_total_order', true);
    
                    if ($response['error'] === false) {
                        
                        $finalize = $this->insert_finalize_ped_fac( $this->get_params_finalize_ped_fac( $invoice ) );
                        
                        if ( $isInvoice ) {
                            $this->insert_invoice( 
                                $this->get_params_for_insert_invoice( 
                                    $invoice, 
                                    round($total, 2), 
                                    $meta, 
                                    $order 
                                ) 
                            );
                        }
    
                        if ($finalize !== 0) {
                            $isInvoice ? $order->add_order_note( "¡# Factura generada ".$invoice, false ) : $order->add_order_note( "¡# Orden generada ".$invoice, false );
                            $recibo = $this->finalize_ped_fact_result( $invoice );
                            $this->custom_logs("RECIBO");
                            $this->custom_logs($recibo);
                            if ($recibo !== 0) {
                                $this->update_available_balance( $total );
                            }else{
                                $order->add_order_note( "¡No se genero el recibo!", false );
                            }
                            $response['error']      = false;
                            $response['message']    = $invoice;
                            return $response;
                        }
                    }else{
                        $this->send_mail_error_order( $invoice , $response['message'] );
                        $order->add_order_note( "¡Hubo un problema generando la orden", false );
                        $response['error']   = true;
                        $response['message'] = "Lo sentimos, hubo un error al momento generar el pedido. En la brevedad estaremos revisando el problema y nos pondremos en contacto contigo. Gracias";
                    }
                }else{
                    $order->add_order_note( "¡Hubo un problema generando la orden", false );
                    $response['error']   = true;
                    $response['message'] = "Lo sentimos, hubo un error al momento generar el pedido. En la brevedad estaremos revisando el problema y nos pondremos en contacto contigo. Gracias";
                }
            }else{
                $response['error']   = true;
                $response['message'] = "Lo sentimos, hubo un error al momento generar el pedido. En la brevedad estaremos revisando el problema y nos pondremos en contacto contigo. Gracias";
            }    
        }
        
        /**
         * This function is used to create the params for insert order header.
         * @version 1.0.0
         * @since   1.0.0
         * @param   string Customers Note  
         * @return  array
         */
        private function get_headers( string $comments )
        {
            return array(    
                "ppn_almacen"       => $this->get_cli_almacen(),
                "ppn_pventa"        => $this->get_cli_pventa(), 
                "ppn_cliente"       => $this->get_cli_codigo(),
                "ppn_agente"        => null,
                "ppn_fecha"         => null,
                "ppn_concepto"      => $comments ? $comments : "Enviado desde B2B Andina Licores",    
                "ppn_listapre"      => $this->get_user_list(),
                "ppn_impuesto"      => null,    
                "ppn_politica"      => null,
                "ppn_ruta"          => $this->get_cli_ruta(), 
                "ppn_DirEntrega"    => $this->get_code_dir(),
            );
        }

        /**
         * This function is used to insert order header with Andina Licores ERP.
         * @version 1.0.0
         * @since   1.0.0
         * @param   array function getHeaders
         * @return  string 
         */
        private function insert_order_header( $headers ) {
            $result = $this->newSoapCallClient( $headers, "wsInsertCabeceraPedido" );
            return $result ? $result->wsInsertCabeceraPedidoResult : false;
        }

        /**
         * This function is used to insert invoice header with Andina Licores ERP.
         * @version 1.0.0
         * @since   1.0.0
         * @param   array function getHeaders
         * @return  string
         */
        private function insert_invoice_header( $headers ) {
            $result = $this->newSoapCallClient( $headers, "wsInsertCabeceraFactura" );
            return $result ? $result->wsInsertCabeceraFacturaResult : false;
        }

        /**
         * This function is used to obtain the params for create order or invoice details.
         * @version 1.0.0
         * @since   1.0.0
         * @param   int Function insert_order_header in case of order, and in case of invoice function insert_invoice_header
         * @param   int Product ID
         * @param   int Quantity
         * @return  array
         */
        private function get_params_details_ped_fac( string $invoice, int $product_id, int $qty, $paymentType, $order_id, $item, $total_price )
        {
            return array(            
                "Ppn_factura"       => $invoice,     
                "Ppn_producto"      => $product_id, 
                "Ppn_unidad"        => $this->get_uni_medida( $product_id ),  
                "Ppn_cantidad"      => $qty,   
                "Ppn_precio"        => $total_price,
                "Ppn_porc_desc"     => $this->get_product_percentage_sale( $product_id, $order_id ),
                "Ppn_ice"           => '', 
                "Ppn_irbp"          => '',    
                "Ppn_iva"           => $this->get_product_tax( $product_id ),   
                "Ppn_bodega"        => $this->get_cli_bodega()
            );
        }
        
        /**
         * This function is used to insert details about order or invoice.
         * @version 1.0.0
         * @since   1.0.0
         * @param   array Function get_params_details_ped_fac
         * @return  object
         */
        private function set_details_ped_fac( $params )
        {
            $result = $this->newSoapCallClient($params, 'wsInsertDetalle_Ped_Fac');
            return $result;
        }

        /**
         * This function is used to get params for invoice
         * @version 1.0.0
         * @since   1.0.0
         * @param   string  Function insert_invoice_header
         * @param   float   $total
         * @param   array   $meta Custom post order meta
         * @param   object  $order Instance wc_get_order Woocommerce
         * @return  array
         */
        private function get_params_for_insert_invoice( string $invoice, float $total, $meta,  $order )
        {
            return array(
                "Ppn_factura"       => $invoice,
                "Ppn_tipopago"      => $this->getPaymentType( $order->get_payment_method() ), 
                "Ppn_valor"         => number_format($total, 2, '.', ''),   
                "Ppn_nrodoc"        => $order->get_id(),   
                "Ppn_nrocta"        => $meta['_redypagos_reference'][0],  
                "Ppn_emisor"        => $meta['_redypagos_bank'][0],
            );
        }

        /**
         * This function is used to insert invoice
         * @version 1.0.0
         * @since   1.0.0
         * @param   array Function get_params_for_insert_invoice
         * @return  void
         */
        private function insert_invoice( $params )
        {
            $result = $this->newSoapCallClient( $params, "wsInsertaRecibo" );
            return $result;
        }

        /**
         * This function is used to get params for finalize order or invoice
         * @version 1.0.0
         * @since   1.0.0
         * @param   string Function insert_invoice_header
         * @return  array 
         */
        private function get_params_finalize_ped_fac( string $invoice )
        {
            return array(
                "Ppn_factura"       => $invoice,
                "Ppn_transporte"    => 0,    
                "Ppn_iva_transp"    => 0,
                "Ppn_desc2"         => 0, 
                "Ppn_desc2_0"       => 0,
            );
        }

        /**
         * This function is used to insert params for finalize order or invoice
         * @version 1.0.0
         * @since   1.0.0
         * @param   array Function get_params_finalize_ped_fac
         * @return  array
         */
        private function insert_finalize_ped_fac( $params )
        {
            $result = $this->newSoapCallClient( $params, 'wsFinaliza_Ped_Fac');
            return $result;
        }

        /**
         * This function is used to finalize order or invoice
         * @version 1.0.0
         * @since   1.0.0
         * @param   array Function insert_finalize_ped_fac
         * @return  array
         */
        private function finalize_ped_fact_result( $params )
        {
            $produccion = array( "Ppn_factura" => $params);
            $result = $this->newSoapCallClient( $produccion, "wsProduccion_Ped_fac" );
            return $result->finalize_ped_fact_result;
        }

        /**
         * This function is used to update balance of user's
         * @version 1.0.0
         * @since   1.0.0
         * @param   float Order total's
         */
        private function update_available_balance( $total )
        {
            if(floatval($total) > 0) {
                $balance = $this->get_cli_saldo_disponible() - $total;
                update_user_meta( $this->get_user_id(), '_cli_saldo_disponible', $balance );
            }
        }

        /**
         * This function is used to calculate discount percentage 
         * @version 1.0.0
         * @since   1.0.0
         * @param  int Product ID
         * @param  float Product Price
         * @return float
         */
        private function get_product_percentage_sale( $product_id, int $order_id)
        {
            $discount = json_decode(get_post_meta( $order_id, 'al_sync_discount', true));
            $percentage_discount = 0;
            foreach ($discount as $key => $value) {
                if ($value->product_id === $product_id) {
                    $percentage_discount = $value->discount;
                    break;
                }
            }
            return $percentage_discount;
        }

        /**
         * This function is used to get product tax.
         * @version 1.0.0
         * @since   1.0.0
         * @param int Product ID
         * @return float
         */
        private function get_product_tax( int $product_id )
        {
            return get_post_meta( $product_id, '_pro_impuesto', true);
        }

        /**
         * This function is used to get unit of measurement.
         * @version 1.0.0
         * @since   1.0.0
         * @param   int Product ID
         * @return  float
         */
        private function get_uni_medida( int $product_id )
        {
            return get_post_meta( $product_id, '_uni_medida', true);
        }

        /**
         * This function is used to determinate the code payment type
         * @param   string $paymentType
         * @version 1.0.0
         * @since   1.0.0
         */
        private function getPaymentType( string $paymentType )
        {
            switch ($paymentType) {
                case 'wc_redypagos_gateway':
                    return "2";
                case 'Austro':
                    return "3";
                case 'Pichincha':
                    return "4";
            }
        }

        /**
         * This function is used to connect with Andina Webservice for consulting products.
         * @since   1.0.0
         * @version 1.0.0
         * @param   object Product info
         * @param   int    Product quantity 
         */
        public function consultApiMaterials($product, $quantity)
		{
			$response = $this->newSoapCallClient( 
				$this->getMaterialsParam( $product->get_id(), $quantity ),
				"wsConsultaMateriales"
			);
			return !empty($response) ? $response->wsConsultaMaterialesResult : false;			
		}

        /**
		 * This function is used to get params for consulting materials.
		 * @version   1.0.0
         * @since     1.0.0
         * @param 	  int Product ID
		 * @param 	  int Cantidad
		 * @return 	  array 
		 */
		private function getMaterialsParam(int $id, int $qty)
		{
			return array(            
                "p_material"    => (string) $id,       
                "p_bodega"      => $this->get_cli_bodega(),  
                "p_lisprecio"   => $this->get_user_list(),  
                "p_cliente"     => $this->get_cli_codigo(),  
                "p_agencia"     => '',       
                "p_cant"        => (string) $qty
            );
		}
        /**
         * This function is used to notificate order's error
         * @param   string $invoiceNumber
         * @param   array $errors
         */
        private function send_mail_error_order( string $invoiceNumber, array $errors )
        {
            if ( !empty (get_option("al_andina_licores_notifications_emails") ) ) {
                $invoice_details = "";
                foreach ($errors as $error) {
                    $invoice_details.= "<br> Ppn_producto = ". $error['product']." codigo respuesta = ".$error['response']." <br>";
                }
                $destinatario = get_option("al_andina_licores_notifications_emails");
                $asunto = "Error proceso pedido"; 
                $cuerpo = ' 
                    <html> 
                        <head> 
                            <title>Error en el pedido</title> 
                        </head> 
                    <body> 
                        <h1>Cabecera Nro: '.$invoiceNumber.' </h1> 
                        <p> 
                            <b>Saludos Cordiales Patricio</b>. <br><br><br>
                            Al parecer existe un error en la factura detallada, revisar por favor. A continuacion se detalla la respuesta de cada item dentro del pedido.<br><br>
                            
                            Estos son los productos y su respuesta en el detalle de la orden:<br><br>
                            '.$invoice_details.'
                            
                            Una vez resuelto el incoveniente comunicarse con el cliente para notificar lo sucedido. Gracias
                            Saludos.
                        </p> 
                    </body> 
                    </html> 
                '; 
                //para el envío en formato HTML 
                $headers = "MIME-Version: 1.0\r\n"; 
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
                //dirección del remitente 
                $headers .= "From: Andina Licores <andina@andinalicores.com.ec>\r\n"; 
                mail( $destinatario, $asunto, $cuerpo, $headers );
            }
        }
    }
endif;