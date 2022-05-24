<?php
/**
 * Woocommerce RedyPagos Gateway - Functions
 *
 * @package WCRedypagosGateway
 * @version 1.0.0
 * @since   1.0.0
 * @author  ComprasEC
 */

 if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( !function_exists( 'redypagos_add_custom_fee' ) ) {
	/**
	 * This function is used to add a fee for user's that not given in list 005
	 */
	function redypagos_add_custom_fee ( $cart ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
		
		$payment_method = WC()->session->get('chosen_payment_method');
		$user = get_user_meta(wp_get_current_user()->ID);
		if ($payment_method === "wc_redypagos_gateway" && $user['_cli_lista'][0] !== "5") {
			$fee = floatval($cart->get_displayed_subtotal()) * 0.03;
			$cart->add_fee( __( 'Recargo', 'woocommerce' ) , $fee, true );
		}
	}
// 	add_action( 'woocommerce_cart_calculate_fees', 'redypagos_add_custom_fee', 10, 1 );
}

if (!function_exists('redypagos_load_js')) {
	/**
	 * This function is used to enqueue custom JS.
	 * PENDING POR APPROVAL
	 */
	function redypagos_load_js()
	{
		?>
			<script>
				(function ($, window, document) {
					'use strict';
					// execute when the DOM is ready
					jQuery(document.body).on('change', 'input[name="payment_method"]', function () {
						jQuery('body').trigger('update_checkout');
					})
				}(jQuery, window, document));
			</script>
      	<?php
	}
	add_action('wp_footer', 'redypagos_load_js');
}

if ( !function_exists( 'redypagos_custom_thankyou_message' ) ) {
	/**
	 * This function is used to customizer thankyou page with link of Redypagos
	 * @version 1.0.0
	 * @since  	1.0.0
	 */
	function redypagos_custom_thankyou_message( $order_id ) {
		echo '
			<div class="col-12 text-center">
				<h3>Ingresa al siguiente link para realizar tu pago</h3>
				<a class="btn btn-primary" style="background: #15bd0a; color:#fff;" href="'.get_post_meta($order_id, 'redypagos_link', true).'" target="_blank">Link de pago</a>
			</div>
			<div class="col-12">
				<br>
				<p>Una vez realizado tu pago, debes ingresar al siguiente <a href="'. get_site_url() .'/redypagos-comprobante/?order_id='.$order_id.'&action=uplf" target="_blank">Link</a> para registrar tu pago. El proceso de verificación de pago puede demorar entre 24 y 48hr.</p>
				<br>
			</div>
		';
	}
	add_action('woocommerce_thankyou_wc_redypagos_gateway', 'redypagos_custom_thankyou_message', 5);
}

if ( !function_exists( 'redypagos_add_content_email' ) ) {
	
	/**
	 * This function is used to add content to email processing order
	 * @version 1.0.0
	 * @since 	1.0.0
	 */
	function redypagos_add_content_email( $order, $sent_to_admin, $plain_text, $email ) {
	   if ( $email->id == 'customer_on_hold_order' && $order->get_payment_method() === "wc_redypagos_gateway" ) {
	       $link = get_post_meta($order->get_id(), 'redypagos_link', true);
			echo '
				<p class="email-upsell-p">Una vez realizado tu pago, debes ingresar al siguiente <a href="'. get_site_url() .'/redypagos-comprobante/?order_id='.$order->get_id().'&action=uplf" target="_blank">Link</a> para registrar tu pago. El proceso de verificación de pago puede demorar entre 24 y 48hr.</p>
			';
	   }
	}
	add_action( 'woocommerce_email_before_order_table', 'redypagos_add_content_email', 20, 4 );
}

if ( ! function_exists( 'redypagos_add_meta_boxes' ) ) {
	/**
	 * This function is used for adding meta container admin shop_order pages for Redypagos
	 * @version 1.0.0
	 * @since 	1.0.0
	 */
    function redypagos_add_meta_boxes()
    {
    	global $post;
    	$post_type = get_post_type( $post->ID );
    	if( $post_type == 'shop_order' ) {
	    	$order = wc_get_order($post->ID);
    		if( $order->get_payment_method() == 'wc_redypagos_gateway' ){
	        	add_meta_box( 'redypagos_fields', __('Datos de Redypagos','woocommerce'), 'redypagos_add_fields', 'shop_order', 'side', 'core' );
        	}
        }
    }
	add_action( 'add_meta_boxes', 'redypagos_add_meta_boxes' );
}

if ( ! function_exists( 'redypagos_add_fields' ) ) {
	/**
	 * 
	 */
    function redypagos_add_fields() {
        global $post;
		
        $code_meta = get_post_meta( $post->ID, '_redypagos_reference', true ) ? get_post_meta( $post->ID, '_redypagos_reference', true ) : '';
        $date_meta = get_post_meta( $post->ID, '_redypagos_trans_date', true ) ? get_post_meta( $post->ID, '_redypagos_trans_date', true ) : '';
        $bank_meta = get_post_meta( $post->ID, '_redypagos_bank', true ) ? get_post_meta( $post->ID, '_redypagos_bank', true ) : '';

        $comprobante = get_post_meta( $post->ID, '_redypagos_img_payment', true );

        if( $comprobante != '' ){
	        $content  = '<input type="hidden" name="rp_bacs_meta_field_nonce" value="' . wp_create_nonce() . '">';
	    	$content .= '
	    		<div style="border-bottom:solid 1px #eee;padding-bottom:13px;">
					
					<label for="_redypagos_reference">Número de transferencia</label>
	    			<input type="text" readonly style="width:250px;";" name="_redypagos_reference" placeholder="' . $code_meta . '" value="' . $code_meta . '"/>
					
					<label for="_redypagos_bank">Banco Emisor</label>
	    			<input type="text" readonly style="width:250px;";" name="_redypagos_bank" placeholder="' . $bank_meta . '" value="' . $bank_meta . '"/>
	    			
					<label for="_redypagos_trans_date">Fecha de la transferencia</label>
	    			<input type="text" readonly style="width:250px;";" name="_redypagos_trans_date" placeholder="' . $date_meta . '" value="' . $date_meta . '"/>
					
					<p style="text-align: center; margin:0;"><a href="'.$comprobante.'" target="_blank">>> VER COMPROBANTE <<</a></p>
	    		</div>
			';

			if ( empty( get_post_meta( $post->ID, '_redypagos_approved', true ) ) ) {
				$content .= '
					<p style="text-align:right;">
						<button type="submit" class="button button-primary" name="redypagos_refuse" value="redypagos_refuse">Rechazar pago</button>	
						<button type="submit" class="button" name="redypagos_approved" value="redypagos_approved">Aprobar pago</button>
					</p>
				';
			}
			
	    }else if ($comprobante == '' || "no" === get_post_meta( $post->ID, '_redypagos_approved', true ) ) {
	    	$content  = '
	    	<ul class="order_notes">
				<li class="note system-note">
					<div class="note_content">
						<p>
							<b>En espera de que el usuario envíe el comprobante de la transacción.</b>
						</p>
					</div>
				</li>
			</ul>';
    	}
    	echo $content;
    }
}

if ( ! function_exists( 'redypagos_save_wc_order_fields' ) ) {
	/**
	 * This function is used to update the Redypagos payment status
	 * @version 1.0.0
	 * @since	1.0.0
	 */
    function redypagos_save_wc_order_fields( $post_id ) {

        $post_type = get_post_type( $post_id );
        $order = wc_get_order( $post_id );

        if($post_type == 'shop_order' && $order->get_payment_method() == 'wc_redypagos_gateway'){
	        // We need to verify this with the proper authorization (security stuff).

	        // Check if our nonce is set.
	        if ( ! isset( $_POST[ 'rp_bacs_meta_field_nonce' ] ) ) {
	            return $post_id;
	        }
	        $nonce = $_REQUEST[ 'rp_bacs_meta_field_nonce' ];

	        //Verify that the nonce is valid.
	        if ( ! wp_verify_nonce( $nonce ) ) {
	            return $post_id;
	        }

	        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
	        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	            return $post_id;
	        }

	        // Check the user's permissions.
	        if ( 'page' == $_POST[ 'post_type' ] ) {

	            if ( ! current_user_can( 'edit_page', $post_id ) ) {
	                return $post_id;
	            }
	        } else {

	            if ( ! current_user_can( 'edit_post', $post_id ) ) {
	                return $post_id;
	            }
	        }
	        // --- Its safe for us to save the data ! --- //

	        // Sanitize user input  and update the meta field in the database.
        	$comprobante = get_post_meta( $post_id, '_redypagos_img_payment', true );

	    	if( isset( $_POST['redypagos_approved'] ) && !empty( $comprobante ) ) {
			    update_post_meta( $post_id, '_redypagos_approved', 'yes' );
				$order->add_order_note( "¡Pago aprobado con exito!", true );
			}else if (  isset( $_POST['redypagos_refuse'] )  ) {
				update_post_meta( $post_id, '_redypagos_approved', 'no' );
				$order->add_order_note( "¡Pago rechazado!", true );
			}
        }
	}
	add_action( 'save_post', 'redypagos_save_wc_order_fields', 10, 1 );
}

add_filter( 'generate_rewrite_rules', function ( $wp_rewrite ){
    $wp_rewrite->rules = array_merge(
        [
        	'redypagos-comprobante/(\d+)/?$' => 'index.php?order_id=$matches[1]',
		],
        $wp_rewrite->rules
    );
} );

add_filter( 'query_vars', function( $query_vars ){
    $query_vars[] = 'order_id';
    $query_vars[] = 'action';
    return $query_vars;
} );

add_action( 'template_redirect', function(){
    $order_id = intval( get_query_var( 'order_id' ) );
    $action = get_query_var( 'action' );
    if ( isset( $order_id ) && isset( $action ) && $action == 'uplf' ) {
	    $order = new WC_Order( $order_id );
    	if( $order && $order->get_payment_method() == 'wc_redypagos_gateway' && empty( get_post_meta( $order_id, '_redypagos_reference', true ) ) ){
	        include plugin_dir_path( __FILE__ ) . 'templates/wc-redypagos-comprobante.php';
	        die;
	    }
	}
} );

/*-------------------------------------------------------------------------------------/*
* CODIGO COMENTADO DEBIDO A QUE FALTA APROBACION POR PARTE DE REDYPAGOS PARA USAR ESTOS ENDPOINTS
/*-------------------------------------------------------------------------------------/*

// if ( !function_exists( 'redypagos_payment_methods_link' ) ) {

	/**
	 * Add menu items payments on account page
	 * PENDING POR APPROVAL
	 */
	// function redypagos_payment_methods_link( $menu_links ){
		
	// 	$menu_links = array_slice( $menu_links, 0, 5, true ) 
	// 	+ array( 'payment-methods' => 'Metodos de pago' )
	// 	+ array_slice( $menu_links, 5, NULL, true );
		
	// 	return $menu_links;
	// }
	// add_filter ( 'woocommerce_account_menu_items', 'redypagos_payment_methods_link', 40 );
// }

// if ( !function_exists( 'redypagos_add_endpoint' ) ) {
	
	/**
	 * Register Permalink Endpoint
	 * PENDING POR APPROVAL
	 */	
	// function redypagos_add_endpoint() {
	// 	add_rewrite_endpoint( 'payment-methods', EP_PAGES );
	// }
	// add_action( 'init', 'redypagos_add_endpoint' );
// }

// if ( !function_exists( 'redypagos_my_account_endpoint_content' ) ) {
	/**
	 * Content for the new page in My Account, woocommerce_account_{ENDPOINT NAME}_endpoint
	 * PENDING POR APPROVAL
	 */
	// function redypagos_my_account_endpoint_content() {
	// 	require_once 'templates/payment-methods.php';
	// }
	// add_action( 'woocommerce_account_payment-methods_endpoint', 'redypagos_my_account_endpoint_content' );
// }

// if ( !function_exists( 'redypagos_update_card_info' ) ) {
	
	/**
	 * This function is used to update cart info
	 * PENDING POR APPROVAL
	*/
	// function redypagos_update_card_info() {
	// 	$data = json_decode(base64_decode($_POST['data']), true);
	// 	if ( substr($data['ccnum'], 0, 1) === "4" ) {
	// 		$brand = 'VISA';
	// 	} else{
	// 		$brand = 'MASTER';
	// 	}
	// 	$info = (object)[
	// 		"last" 	=> substr($data['ccnum'], -4),
	// 		"exp"	=> $data['exp'],
	// 		"brand"	=> $brand,
	// 		"token"	=> "LLKÑLKXZCZXQWE123MN123S" 
  	// 	];
	// 	update_user_meta( wp_get_current_user()->ID, 'rc_card_details', base64_encode( json_encode($info) ) );
	// 	wp_send_json( "Datos actualizados con exito!", 201 );
	// }
	// add_action('wp_ajax_redypagos_update_card_info','redypagos_update_card_info');
// }

// if ( !function_exists( 'redypagos_delete_card_info' ) ) {
	/**
	 * This function is used to update cart info
	 * PENDING POR APPROVAL
	*/
	// function redypagos_delete_card_info() {
	// 	$isDelete = delete_user_meta( wp_get_current_user()->ID, 'rc_card_details' );
	// 	if ($isDelete) {
	// 		wp_send_json( "Tarjeta eliminada con exito", 201 );
	// 	}else{
	// 		wp_send_json( "No se pudo eliminar tarjeta", 500 );
	// 	}
	// }
	// add_action('wp_ajax_redypagos_delete_card_info','redypagos_delete_card_info');
// }

// if (!function_exists('redypagos_load_js')) {

	/**
	 * This function is used to enqueue custom JS.
	 * PENDING POR APPROVAL
	 */
	// function redypagos_load_js()
	// {
	// 	wp_enqueue_script(
	// 		'all_js',
	// 		'https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js',
	// 		array('jquery'),
	// 		true
	// 	);

	// }
	// add_action('wp_footer', 'redypagos_load_js');
// }

/*-------------------------------------------------------------------------------------
* FINALIZA CODIGO COMENTADO
-------------------------------------------------------------------------------------*/


