<?php
/**
 * Woocommerce Andina Licores Webservices  - Sync single products
 *
 * @package WebservicesAndinaLicores
 * @version 1.0.0
 * @since   1.0.0
 * @author  ComprasEC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'AL_Sync_Single_Product' ) ) :

    /**
	 * AL_Sync_Single_Product Class
	 *
	 * @class   AL_Sync_Single_Product
	 * @version 1.0.0
	 * @since   1.0.0
	 */
    class AL_Sync_Single_Product extends AL_Connection_Webservice
    {
        /**
         * This function is used to verify the stock of product by ID
         * @version 1.0.0
         * @since   1.0.0
         * @return  boolean
         */
        public function verifyStockOfProduct( $productID, $variationID, $quantity, $fee = false)
		{
			try {
                $product = $variationID !== 0 ? new WC_Product_Variation( $variationID ) : wc_get_product( $productID);
                $response = $this->consultApiMaterials( $product, $quantity );
                if (!$response) {
                    return ["passed" => false, "message" => 'El producto ('.$product->get_name().') no se encuentra disponible, debes removerlo del carrito para continuar con el proceso de compra'];
                }
                $response =  json_decode( $response );
                $price = !empty($response->Materiales[0]->PVP) ? $response->Materiales[0]->PVP : "";
                $discount = !empty($response->Materiales[0]->DESCTO) ? $response->Materiales[0]->DESCTO : 0; 
                if ($fee && !empty($price)) {
                    $price = $response->Materiales[0]->V_PRECIO_R;
                }
                $this->updateProduct(
                    $product->get_id(), 
                    $price, 
                    $this->getDiscount( floatval($discount), floatval( $price ), $fee),
                    $response->Materiales[0]->STOCK ? intval($response->Materiales[0]->STOCK) : 0,
                    $response->Materiales[0]->BOD_CODIGO,
                    $discount
                );
                
                $this->updateAllStock($product->get_id());

                if (empty($response->Materiales) || empty( $response->Materiales[0]->PVP ) ) {
                    return ["passed" => false, "message" => 'El producto ('.$product->get_name().') no se encuentra disponible, debes removerlo del carrito para continuar con el proceso de compra'];
                }
                return $this->validateStockByQuantity( intval($response->Materiales[0]->STOCK), $quantity, $response->Materiales[0]->PRO_NOMBRE);
            } catch (\Exception $e) {
                return ["passed" => false, "message" => "Lo sentimos, hubo un error de conexión. En la brevedad estaremos revisando el problema y nos pondremos en contacto contigo. Gracias"];
            }
		}
         /**
          * This function is used to update price, discount and stock of product.
          * @version  1.0.0
          * @since    1.0.0
          * @param    int      ID product
          * @param    float    Product Price
          * @param    float    Discount
          * @param    integer  Product stock
          * @param    string   Stock location
          */
        private function updateProduct($productID, $pvp, $discount, $stock, $bodega, $percentage)
        {
            update_post_meta( $productID, '_alg_wc_price_by_user_role_regular_price_lista_'.$this->get_user_list(), $pvp );
			update_post_meta( $productID, '_alg_wc_price_by_user_role_sale_price_lista_'.$this->get_user_list(), $discount > 0 ? $discount : "");
			update_post_meta( $productID, '_alg_wc_discount_percentage_lista_'.$this->get_user_list(), $percentage > 0 ? $percentage : "");
			update_post_meta( $productID, '_stock_at_'.$this->get_store_cli_code($bodega), $stock);
        }

        /**
         * This function is used to update all stock of the product.
         * @version     1.0.0
         * @since       1.0.0
         * @param int   Product ID
         */
        private function updateAllStock($productID)
        {
            $stock = 0;
            $arrayStores = [49, 50, 51, 52, 53, 54, 57, 58];
            for ($i=0; $i < count($arrayStores); $i++) {
                $availability = get_post_meta($productID, '_stock_at_'.$arrayStores[$i], true);
                if(intval($availability) > 0)
                $stock += $availability;
            }
			update_post_meta( $productID, '_stock', intval($stock));
        } 

		/**
         * This function is used to verify the stock of product by SKU
         * @version 1.0.0
         * @since   1.0.0
         * @return  boolean
         */
        private function validateStockByQuantity($stock, $quantity, string $product_name )
        {
            $result = [
                "passed"    => false,
                "message"   => ""
            ];
            if($stock < $quantity){
                if($stock === 0){
                    $result['message'] = 'Se han agotado las existencias del producto ('.$product_name.'), debes removerlo del carrito para continuar con el proceso de compra';
                    return $result;
                }
                $availability = $quantity - $stock;
                $result['message'] = 'Solo quedan '.$stock.' ('.($product_name).',) disponibles, debes remover ('.$availability.') unidad(s) del carrito para continuar con el proceso de compra';
                return $result;
            }else{
                $result['passed'] = true;
                return $result;
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
         * This function is used to calculate the product's discount.
         * @version     1.0.0
         * @since       1.0.0
         * @param float Product percentage discount
         * @param int   Product quantity 
         * @param float Product price
         * @return float
         */
        private function getDiscount($discount, $price, $fee) {
            if ($discount > 0) {
                $discount = $discount > 0 ? $discount / 100 : 0;
                $sale = $price * $discount;
                $discount = $price - $sale;
                return $discount;
            }
            return 0;
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
    }
endif;