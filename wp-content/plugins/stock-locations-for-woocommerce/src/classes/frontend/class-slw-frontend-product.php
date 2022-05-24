<?php
/**
 * SLW Frontend Product Class
 *
 * @since 1.3.0
 */

namespace SLW\SRC\Classes\Frontend;

use SLW\SRC\Helpers\SlwFrontendHelper;
use SLW\SRC\Helpers\SlwWpmlHelper;

if ( !defined( 'WPINC' ) ) {
	die;
}

if( !class_exists('SlwFrontendProduct') ) {

	class SlwFrontendProduct
	{
		/**
		 * Construct.
		 *
		 * @since 1.3.0
		 */
		public function __construct()
		{
			// get settings
			$this->plugin_settings = get_option( 'slw_settings' );

			// check if show in cart is enabled
			if( isset( $this->plugin_settings['show_in_product_page']) && $this->plugin_settings['show_in_product_page'] != 'no' ) {
				add_action( 'woocommerce_before_add_to_cart_button', array($this, 'simple_location_select') );
				add_action( 'woocommerce_single_variation', array($this, 'variable_location_select') );
				add_filter( 'woocommerce_add_cart_item_data', array($this, 'add_to_cart_location_validation'), 10, 3 );
			}

			add_action( 'wp_ajax_get_variation_locations', array($this, 'get_variation_locations') );
			add_action( 'wp_ajax_nopriv_get_variation_locations', array($this, 'get_variation_locations') );
			
		}

		/**
		 * Add stock locations selection to simple product page.
		 *
		 * @since 1.3.0
		 */
		public function simple_location_select()
		{
			global $product;
			
			if( empty( $product ) || $product->get_type() != 'simple' ) return;
			
			global $slw_plugin_settings;
			$everything_stock_status_to_instock = array_key_exists('everything_stock_status_to_instock', $slw_plugin_settings);			
			
			
			$product_id            = SlwWpmlHelper::object_id( $product->get_id() );
			$stock_locations       = SlwFrontendHelper::get_all_product_stock_locations_for_selection( $product_id, $everything_stock_status_to_instock );
			$default_location      = isset( $this->plugin_settings['default_location_in_frontend_selection'] ) ? get_post_meta( $product_id, '_slw_default_location', true ) : 0;
			$lock_default_location = isset( $this->plugin_settings['lock_default_location_in_frontend'] ) && $this->plugin_settings['lock_default_location_in_frontend'] == 'on' ? true : false;
			$product_stock_price_status = isset( $this->plugin_settings['product_stock_price_status'] ) && $this->plugin_settings['product_stock_price_status'] == 'on' ? true : false;

			if( ! empty( $stock_locations ) ) {
				// lock to default location if enabled			

				
				if( $lock_default_location && $default_location != 0 ) {
					
					$stock_price = $stock_locations[$default_location]['price'];
					$stock_location_name = $stock_locations[$default_location]['name'];
					
					if($product_stock_price_status){
						$stock_location_name .= ' '.wc_price($stock_price);
					}
					
					$selected = ($stock_locations[$default_location]['quantity']>0?'selected="selected"':'');
					
					
					echo '<div style="display:block; width:100%;"><select id="slw_item_stock_location_simple_product" class="slw_item_stock_location display_'.$this->plugin_settings['show_in_product_page'].' default" name="slw_add_to_cart_item_stock_location" style="display:block;" required disabled>';
					echo '<option data-price="'.$stock_price.'" data-quantity="'.$stock_locations[$default_location]['quantity'].'" value="'.$default_location.'" '.$selected.'>'.$stock_location_name.'</option>';
					echo '</select></div>';
					return;
				}

				// default behaviour
				echo '<div style="display:block; width:100%;"><select id="slw_item_stock_location_simple_product" class="slw_item_stock_location display_'.$this->plugin_settings['show_in_product_page'].' remaining" name="slw_add_to_cart_item_stock_location" style="display:block;" required>';
				if( $default_location != 0 ) {
					echo '<option data-price="" data-quantity="" value="0">'.__('Select location...', 'stock-locations-for-woocommerce').'</option>';
				} else {
					echo '<option data-price="" data-quantity="" value="0" selected>'.__('Select location...', 'stock-locations-for-woocommerce').'</option>';
				}
				
				
				$priority_used = 0;
				foreach( $stock_locations as $id => $location ) {
					
					$selected = '';
					
					$slw_location_priority = get_term_meta($id, 'slw_location_priority', true);
					
					if($location['quantity']>0){
						if( $default_location != 0 && $location['term_id'] == $default_location){
							$selected = 'selected="selected"';
						}else{
							if($slw_location_priority>$priority_used){						
								$priority_used = $slw_location_priority;
								$selected = 'selected="selected"';
							}						
						}
					}
					
					
					
					$stock_price = $location['price'];
					
					$stock_location_name = $location['name'];
					
					if($product_stock_price_status){
						$stock_location_name .= ' '.wc_price($stock_price);
					}


					$disabled = '';
					if( $location['quantity'] < 1 && $location['allow_backorder'] != 1 && !$everything_stock_status_to_instock) {
						$disabled = 'disabled="disabled"';
					}
					//if( $default_location != 0 && $location['term_id'] == $default_location ) {
					//	echo '<option data-price="'.$stock_price.'" data-quantity="'.$location['quantity'].'" value="'.$location['term_id'].'" '.$disabled.' '.$selected.'>'.$stock_location_name.'</option>';
					//} else {
						echo '<option data-priority="'.$slw_location_priority.'" data-price="'.$stock_price.'" data-quantity="'.$location['quantity'].'" value="'.$location['term_id'].'" '.$disabled.' '.$selected.'>'.$stock_location_name.'</option>';
					//}
				}
				echo '</select></div>';
			}
		}

		/**
		 * Add stock locations selection to variable product page.
		 *
		 * @since 1.3.0
		 */
		public function variable_location_select()
		{
			global $product;
			if( empty($product) ) return;
			$product_id            = SlwWpmlHelper::object_id( $product->get_id() );

			$term_id = (is_product()?false:get_queried_object_id());

			$product               = wc_get_product( $product_id );
			if( empty($product) || $product->get_type() != 'variable' ) return;

			$default_location      = isset( $this->plugin_settings['default_location_in_frontend_selection'] ) ? get_post_meta( $product->get_id(), '_slw_default_location', true ) : 0;
			$lock_default_location = isset( $this->plugin_settings['lock_default_location_in_frontend'] ) && $this->plugin_settings['lock_default_location_in_frontend'] == 'on' ? true : false;
			
			echo '<div style="display:'.($term_id?'none !important':'block').'; width:100%;">';
			if( $lock_default_location && $default_location != 0 ) {
				echo '<select id="slw_item_stock_location_variable_product" class="slw_item_stock_location display_'.$this->plugin_settings['show_in_product_page'].'" name="slw_add_to_cart_item_stock_location" required disabled>';
			} else {
				echo '<select id="slw_item_stock_location_variable_product" class="slw_item_stock_location display_'.$this->plugin_settings['show_in_product_page'].'" name="slw_add_to_cart_item_stock_location" required>';
			}
			if($term_id){
				echo '<option data-price="" data-quantity="" value="'.$term_id.'" selected></option>';
			}else{
				if( $default_location != 0 ) {
					echo '<option data-price="" data-quantity="" value="0" disabled>'.__('Select location...', 'stock-locations-for-woocommerce').'</option>';
				} else {
					echo '<option data-price="" data-quantity="" value="0" disabled selected>'.__('Select location...', 'stock-locations-for-woocommerce').'</option>';
				}
			}
			echo '</select></div>';
		}

		/**
		 * Get variation locations.
		 *
		 * @since 1.3.0
		 */
		public function get_variation_locations()
		{
			
			if( isset( $_POST['variation_id'] ) && isset( $_POST['product_id'] ) && $_POST['action'] == 'get_variation_locations' ) {
				
				
				$variation_id          = sanitize_text_field( $_POST['variation_id'] );
				$variation_id          = SlwWpmlHelper::object_id( $variation_id );
				$product_id            = sanitize_text_field( $_POST['product_id'] );
				$product_id            = SlwWpmlHelper::object_id( $product_id );
				$product_variation_id  = ($variation_id?$variation_id:$product_id);

				$stock_locations       = SlwFrontendHelper::get_all_product_stock_locations_for_selection( $variation_id );
				$default_location      = isset( $this->plugin_settings['default_location_in_frontend_selection'] ) ? get_post_meta( $product_id, '_slw_default_location', true ) : 0;
				
				
				
				
				if( !empty($stock_locations) ) {
					wp_send_json_success( compact( 'stock_locations', 'default_location' ) );
				} else {
					wp_send_json_error( array(
						'error' => __('No locations found for this product/variant!', 'stock-locations-for-woocommerce')
					) );
				}
			}
			die();
		}

		/**
		 * Validate cart item selected location.
		 *
		 * @since 1.3.0
		 */
		function add_to_cart_location_validation( $cart_item_data, $product_id, $variation_id ) {
			if( isset( $_POST['slw_add_to_cart_item_stock_location'] ) ) {
				$cart_item_data['stock_location'] = sanitize_text_field( $_POST['slw_add_to_cart_item_stock_location'] );
			}
			return $cart_item_data;
		}

	}

}
