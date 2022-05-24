<?php
/**
 * Woocommerce Andina Licores Webservices - Functions
 *
 * @package WebservicesAndinaLicores
 * @version 1.0.0
 * @since   1.0.0
 * @author  ComprasEC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( !function_exists( 'al_show_sale_price_at_cart' ) ) {
    /**
     * This function is used to show sale price in cart.
     * @version 1.0.0
     * @since   1.0.0
     */
    function al_show_sale_price_at_cart( $old_display, $cart_item, $cart_item_key ) {
        /** @var WC_Product $product */
        $product = $cart_item['data'];
    
        if ( $product ) {
            return $product->get_price_html();
        }
    
        return $old_display;
    
    }
    add_filter( 'woocommerce_cart_item_price', 'al_show_sale_price_at_cart', 10, 3 );
}

if ( !function_exists( 'al_show_sale_price_at_checkout' ) ) {
    /**
     * This function is used to show sale price in checkout.
     * @version 1.0.0
     * @since   1.0.0
     */
    function al_show_sale_price_at_checkout( $subtotal, $cart_item, $cart_item_key ) {
    
        $product = $cart_item['data'];
        $quantity = $cart_item['quantity'];
    
        if ( ! $product ) {
            return $subtotal;
        }
        $regular_price = $sale_price = $suffix = '';
    
        if ( $product->is_taxable() ) {
    
            if ( 'excl' === (new WC_Cart())->get_tax_price_display_mode() ) {
    
                $regular_price = wc_get_price_excluding_tax( $product, array( 'price' => $product->get_regular_price(), 'qty' => $quantity ) );
                $sale_price    = wc_get_price_excluding_tax( $product, array( 'price' => $product->get_sale_price(), 'qty' => $quantity ) );
    
                if ( WC()->cart->prices_include_tax && WC()->cart->tax_total > 0 ) {
                    $suffix .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                }
            } else {
    
                $regular_price = wc_get_price_including_tax( $product, array( 'price' => $product->get_regular_price(), 'qty' => $quantity ) );
                $sale_price = wc_get_price_including_tax( $product, array( 'price' => $product->get_sale_price(), 'qty' => $quantity ) );
    
                if ( ! WC()->cart->prices_include_tax && WC()->cart->tax_total > 0 ) {
                    $suffix .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                }
            }
        } else {
            $regular_price    = $product->get_price() * $quantity;
            $sale_price       = $product->get_sale_price() * $quantity;
        }
    
        if ( $product->is_on_sale() && ! empty( $sale_price ) ) {
            $price = wc_format_sale_price(
                         wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price(), 'qty' => $quantity ) ),
                         wc_get_price_to_display( $product, array( 'qty' => $quantity ) )
                     ) . $product->get_price_suffix();
        } else {
            $price = wc_price( $regular_price ) . $product->get_price_suffix();
        }
    
        // VAT suffix
        $price = $price . $suffix;
    
        return $price;
    
    }
    add_filter( 'woocommerce_cart_item_subtotal', 'al_show_sale_price_at_checkout', 10, 3 );
}


if ( ! function_exists( 'al_webservices_custom_override_checkout_fields' ) ) {
	/**
	 * This function is used to unset postcode fields.
	 *
	 * @param $fields array
	 * @version 1.0.0
	 * @since   1.0.0
	 */

	function al_webservices_custom_override_checkout_fields( $fields ) {
        unset($fields['billing']['billing_postcode']);
        unset($fields['shipping']['shipping_postcode']);
        unset( $fields['billing']['billing_address_2'] );
		return $fields;
	}
    add_filter( 'woocommerce_checkout_fields' , 'al_webservices_custom_override_checkout_fields' );
}


if ( !function_exists ( 'cp_handle_stock_with_andina_licores' ) ) {
    /**
     * This function is used to verify the stock with SAP before add product to cart.
     * @version 1.0.0
     * @since   1.0.0
     */
    function cp_handle_stock_with_andina_licores( $passed, $product_id, $quantity, $variation_id = 0, $variations = null ) {
        if ( is_user_logged_in() ) {
            $class = new AL_Sync_Single_Product(wp_get_current_user()->ID, wp_get_current_user()->roles);
            $validate = $class->verifyStockOfProduct( $product_id, $variation_id, $quantity );
            if (!$validate['passed']) {
                wc_add_notice($validate['message'], 'error');
                wp_redirect( get_page_by_path( 'cart' ) );
            }
            return $validate['passed'];
        }
    }
    add_filter( 'woocommerce_add_to_cart_validation', 'cp_handle_stock_with_andina_licores', 10, 5);
}


if ( !function_exists( 'al_ecommerce_create_order' ) ) {
    
    /**
     * This function is used to generate order in checkout process
     * @version 1.0.0
     * @since   1.0.0
     */
    function al_ecommerce_create_order( $order ) {
        $order_id   = $order->save();
        $order      = wc_get_order( $order_id );
        if ($order->get_payment_method() !== "wc_redypagos_gateway") {
            $response   = (new AL_Generate_Order( wp_get_current_user()->ID, wp_get_current_user()->roles ))->generate_new_order_or_invoice( $order_id, false );
            if ( !$response['error'] ) {
                update_post_meta( $order_id, '_andina_licores_invoice_number', $response['message'] );
            }else{
                throw new Exception($response['message'], 500);
            }
        }
    }
    add_action('woocommerce_checkout_create_order', 'al_ecommerce_create_order', 12, 1);
}

if ( !function_exists( 'al_webservices_change_order_edit_status' ) ) {
    /**
     * This function is used create the invoice when the order status change to completed.
     * @version 1.0.0
     * @since   1.0.0
     * @param   int $order_id
     * @param   string $status
     */
    function al_webservices_change_order_edit_status( $order_id, $status )
    {
        $order = wc_get_order( $order_id );
        $meta = get_post_custom( $order->get_id() );
        
        $user = get_user_by("ID", get_post_meta($order->get_id(), '_customer_user', true) );

        if ( $status === 'invoiced' && "yes" === get_post_meta($order->get_id(), '_redypagos_approved', true ) ) {
            $response = (new AL_Generate_Order( $user->ID, $user->roles ))->generate_new_order_or_invoice( $order_id, true );
            if ( !$response['error'] ) {
                update_post_meta( $order_id, '_andina_licores_invoice_number', $response['message'] );
            }else{
				$_SESSION['andina_licores_error'] = sprintf(__('Error generando factura', 'woocommerce') );
            }
        }else{
            $_SESSION['andina_licores_error'] = sprintf(__('Debe aprobar el pago primero antes de generar la factura.', 'woocommerce') );
        }
    }
    add_action( 'woocommerce_order_edit_status', 'al_webservices_change_order_edit_status', 11, 2 );
}

if ( !function_exists( 'al_webservices_order_status_changed' ) ) {
    
    /**
     * This function is used in case of error do not allow the order status change 
     * @version 1.0.0
     * @since   1.0.0
     */
    function al_webservices_order_status_changed( $order_id, $status_from, $status_to ) {
        $order = wc_get_order( $order_id );
        if( isset( $_SESSION['andina_licores_error'] ) && $status_to == 'invoiced') {
            $order->update_status( $status_from, $_SESSION['andina_licores_error'] );
            return;
        }
		$order->update_status( $status_to );
    }    
    add_action( 'woocommerce_order_status_changed', 'al_webservices_order_status_changed', 90, 3);
}
    
if ( !function_exists( 'al_webservices_remove_email_on_error' )) {
    /**
     * This function is used in case of error do not send email completed notification. 
     * @version 1.0.0
     * @since   1.0.0
     */
    function al_webservices_remove_email_on_error( $email_class ){
        if( isset( $_SESSION['andina_licores_error'] ) ){
            remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
        }        
    }
    add_action( 'woocommerce_email', 'al_webservices_remove_email_on_error' );
}

if ( !function_exists( 'al_webservices_change_prices_decimals' ) ) {
    /**
     * This function is used to change decimals.
     * @version 1.0.0
     * @since   1.0.0
     */
    function al_webservices_change_prices_decimals( $decimals ){
        $decimals = 2;
        return $decimals;
    }
    add_filter( 'wc_get_price_decimals', 'al_webservices_change_prices_decimals', 20, 1 );
}

if ( !function_exists( 'al_webservices_register_order_status_invoice' ) ) {
    
    /**
     * This function is used to create custom order status invoiced
     * @version  1.0.0
     * @since    1.0.0
     */
    function al_webservices_register_order_status_invoice() {
        register_post_status( 'wc-invoiced', array(
            'label'                     => _x( 'Facturada', 'Order status', 'woocommerce' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Facturada <span class="count">(%s)</span>', 'Facturada <span class="count">(%s)</span>', 'woocommerce' )
        ) );
    }
    add_action( 'init', 'al_webservices_register_order_status_invoice' );
}

if ( !function_exists( 'al_webservices_new_wc_order_statuses' ) ) {
    
    /**
     * This function is used to register the new order status.
     * @version  1.0.0
     * @since    1.0.0
     */
    function al_webservices_new_wc_order_statuses( $order_statuses ) {
        $order_statuses['wc-invoiced'] = _x( 'Facturada', 'Order status', 'woocommerce' );
        return $order_statuses;
    }
    add_filter( 'wc_order_statuses', 'al_webservices_new_wc_order_statuses' );
}

if ( !function_exists( 'al_webservices_hide_prices' ) ) {
    /**
     * This function is used to hide price for sellers
     * @version     1.0.0
     * @since       1.0.0
     * @param       float $price
     */
    function al_webservices_hide_prices( $price ) {
        if (is_user_logged_in()) {
            $user = new Andina_licores_User(wp_get_current_user()->ID, wp_get_current_user()->roles);
            if ("vendedor_al" === $user->get_role() ) {
                add_filter( 'woocommerce_loop_add_to_cart_link', '__return_false' );
                remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
                remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
                return '';
            }
            return $price;
        }
    }

    add_filter( 'woocommerce_get_price_html', 'al_webservices_hide_prices' );
}

if ( !function_exists( 'al_webservices_show_only_instock_products' ) ) {
    /**
     * This function is used to hide product according the store stock. 
     * @version  1.0.0
     * @since    1.0.0
     */
    function al_webservices_show_only_instock_products($query) {
        if ( !is_user_logged_in() || in_array( (wp_get_current_user()->roles)[0], ['vendedor_al', 'admin', 'subscriber'] ) ) {
            $meta_query = $query->get( 'meta_query' );
            $meta_query[] = array(
                'key'       => '_can_visible_by_guest',
                'compare'   => 'LIKE',
                'value'     => 'yes'
            );
            $query->set( 'meta_query', $meta_query );
        }else{
            $user = new Andina_licores_User(wp_get_current_user()->ID, wp_get_current_user()->roles);
            $meta_query = $query->get( 'meta_query' );
            $meta_query[] = array(
                    'key'       => '_stock_at_'.$user->get_store_cli_code($user->get_cli_bodega()),
                    'compare'   => '>',
                    'value'     => 0
            );
            $query->set( 'meta_query', $meta_query );
        }
    }

    add_action('woocommerce_product_query', 'al_webservices_show_only_instock_products');
}

if ( !function_exists( 'al_web_services_script_hooks' ) ) {
    /**
     * This function is used to show a list with addresses in select form through JS.
     * @version 1.0.0
     * @since   1.0.0
     */
    function al_web_services_script_hooks() {
        if ( is_user_logged_in() ) {
            $user = new Andina_licores_User(wp_get_current_user()->ID, wp_get_current_user()->roles);
            if ("vendedor_al" !== $user->get_role() ) {
                $addresses = $user->get_addresses();
                if ( !empty($addresses) ) {
                    wp_enqueue_style(
                        'font_awesome',
                        'https://pro.fontawesome.com/releases/v5.10.0/css/all.css',
                        array(),
                        '4.0.0'
                    );
                    wp_enqueue_script(
                        'loading_overlay', 
                        'https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js'
                    );
                    wp_register_script('woocommerce_address', plugins_url( '../assets/js/address.js', __FILE__ ), array( 'jquery', 'loading_overlay') );
                    wp_enqueue_script('woocommerce_address');
                    
                    wp_localize_script( 'woocommerce_address', 'params', array(
                        'array'                 => $addresses,
                        'code_dir_actual'       => $user->get_code_dir(),
                        "isLoggedIn"            => is_user_logged_in(),
                        'url'                   => admin_url( 'admin-ajax.php' ),
                        'nonce'                 => wp_create_nonce( 'my-ajax-nonce' ))
                    );
                
                    wp_enqueue_script('woocommerce_address');
                    
                    wp_register_style('css_andina', plugins_url( '../assets/css/style.css', __FILE__ ));
                    wp_enqueue_style('css_andina');
                    
                    wp_register_script('woocommerce_scripts', plugins_url( '../assets/js/script.js', __FILE__ ), array( 'jquery') );
                    wp_enqueue_script('woocommerce_scripts');    
                }
            }
        }
    }
    add_action('wp_enqueue_scripts', 'al_web_services_script_hooks');
}

if ( !function_exists( 'al_webservice_change_user_address' ) ) {
    /**
     * This function is used to update the user's address.
     * @version  1.0.0
     * @since    1.0.0
     * @return boolean
     */
    function al_webservice_change_user_address() {
        if ( is_user_logged_in()) {
            $user = new Andina_licores_User(wp_get_current_user()->ID, wp_get_current_user()->roles);
            $addresses = $user->get_addresses();
            if (!empty($addresses)) {
                $key = array_search($_POST['data'], array_column($addresses, 'cld_direccion'));
                update_user_meta( $user->get_user_id(), '_cli_almacen', $addresses[$key]->cli_almacen );
                update_user_meta( $user->get_user_id(), '_cli_bodega', $addresses[$key]->cli_bodega );
                update_user_meta( $user->get_user_id(), '_cli_pventa', $addresses[$key]->cli_pventa);
                update_user_meta( $user->get_user_id(), '_cli_dir', $addresses[$key]->codigo_dir);
                update_user_meta( $user->get_user_id(), 'billing_city', $addresses[$key]->cli_ciudad);
                update_user_meta( $user->get_user_id(), 'billing_state', $addresses[$key]->cli_provincia);
                update_user_meta( $user->get_user_id(), 'billing_address_1', $addresses[$key]->cld_direccion);
                wp_send_json(["error" => false]);
            }
        }
    }
    
    add_action('wp_ajax_al_webservice_change_user_address','al_webservice_change_user_address');
    add_action('wp_ajax_nopriv_al_webservice_change_user_address', 'al_webservice_change_user_address');
}

if ( !function_exists( 'al_webservices_budget_action' ) ) {
    /**
     * This function is used to display available budget of user's
     * @version  1.0.0
     * @since    1.0.0
     */
    function al_webservices_budget_action() {
        if ( is_user_logged_in() ) {
            $user = new Andina_licores_User(wp_get_current_user()->ID, wp_get_current_user()->roles);
            $arrBudget = ['116', '3', '104'];
            if ( in_array( $user->get_user_list(), $arrBudget ) ) {
                echo '
                    <div name="presupuesto" id="presupuesto" style="border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align: center; position: fixed; top: 61%; right: 0; background-color: #092143; color: #092143; padding: 0; font-size: 12px; width: 38px; height:66px; font-weight: bold;">
                        <div id="icon-arrow">
                            <i class="fas fa-chevron-right" style="color:#fff; font-size:15pt; margin:60% auto;"></i>
                        </div>
                        <div id="credito" style="display:none;">Crédito disponible</div>
                        <div id="sdisponible" class="saldo" style="font-weight: bold; display:none; font-size: 35px; color: #092143;"> 
                            $'.$user->get_cli_saldo_disponible().'
                        </div>
                    </div>';
            }
        }
    
    }
    add_action( 'wp_footer', 'al_webservices_budget_action' );
}

if ( !function_exists( 'al_webservices_change_availability_text' ) ) {
    /**
     * This function is used to change the availability text from product.
     * @version  1.0.0
     * @since    1.0.0
     */
    function al_webservices_change_availability_text( $availability, $product ) {
        if ( is_user_logged_in() ) {
            $user = new Andina_licores_User(wp_get_current_user()->ID, wp_get_current_user()->roles);
            $availability = get_post_meta( $product->get_id(), '_stock_at_'.$user->get_store_cli_code($user->get_cli_bodega()), true);
            if (intval($availability) === 0 || $user->get_role() === "vendedor_al") {
                wp_enqueue_style(
                    'custom_css', 
                    plugins_url( '../assets/css/custom.css', __FILE__ )
                );
                return "Agotado";
            }
            
            return $availability." disponibles";
        }
    }
    add_filter( 'woocommerce_get_availability_text', 'al_webservices_change_availability_text', 99, 2 );
}

if ( !function_exists( 'al_webservices_check_cart_items' ) ) {
    /**
     * This function is used to validate the stock item's of the cart.
     * @param  1.0.0
     * @since  1.0.0
     */
    function al_webservices_check_cart_items($array){
        try {
            if( (is_cart() || is_checkout()) && is_user_logged_in() ) {
                global $woocommerce;
                $items = $woocommerce->cart->get_cart();
                $user  = new Andina_licores_User(wp_get_current_user()->ID, wp_get_current_user()->roles);
                $class = new AL_Sync_Single_Product(wp_get_current_user()->ID, wp_get_current_user()->roles);
                $payment_method = WC()->session->get('chosen_payment_method');
                $fee = $payment_method === "wc_redypagos_gateway" && $user->get_user_list() !== "5" ? true : false;
                foreach($items as $values) {
                    $validate = $class->verifyStockOfProduct( $values['data']->get_id(), 0, $values['quantity'], $fee );
                    if (!$validate['passed']){
                        wc_add_notice($validate['message'], 'error');
                        break;
                    }
                }
                al_minimum_order_amount();
            }else if( !is_user_logged_in() ){
                return false;
            }
        } catch (\Exception $e) {
            throw new Exception("Error de conexión, por favor intente nuevamente en unos minutos", 500);
        }
    }
    add_action( 'woocommerce_check_cart_items', 'al_webservices_check_cart_items' );
    add_action( 'woocommerce_after_calculate_totals', 'al_webservices_check_cart_items');

}

if ( !function_exists( 'cron_al_webservices_synchronization_users' ) ) {
    /**
     * This function is used by Cron Manager to fix meta user's.
     * @version 1.0.0
     * @since   1.0.0
     */

    function cron_al_webservices_synchronization_users() {
        $users = get_users( array( 'role__in' => array( 'lista_3', 'lista_105', 'lista_6', 'lista_114', 'lista_119', 'lista_116', 'lista_5', "lista_104" ) ) );
        foreach ( $users as $user ){
            $userAndina = new Andina_licores_User($user->ID, $user->roles);
            $addresses = $userAndina->get_addresses();
            if (!empty($addresses)) {
                update_user_meta( $user->ID, '_cli_almacen', $addresses[0]->cli_almacen );
                update_user_meta( $user->ID, '_cli_bodega', $addresses[0]->cli_bodega );
                update_user_meta( $user->ID, '_cli_pventa', $addresses[0]->cli_pventa);
                update_user_meta( $user->ID, '_cli_dir', $addresses[0]->codigo_dir);
                update_user_meta( $user->ID, 'billing_city', $addresses[0]->cli_ciudad);
                update_user_meta( $user->ID, 'billing_state', $addresses[0]->cli_provincia);
                update_user_meta( $user->ID, 'billing_address_1', $addresses[0]->cld_direccion);
            }
        }
    }
    add_action("al_webservices_synchronization_users", "cron_al_webservices_synchronization_users", 10, 0);
}

if ( !function_exists( 'al_minimum_order_amount' ) ) {
    /**
     * This function is used to set minimum order amount for user's list 105
     */
    function al_minimum_order_amount() {
        $user = new Andina_licores_User(wp_get_current_user()->ID, wp_get_current_user()->roles);
        $minimum = 100;  // Precio minimo de la compra.
        if ( WC()->cart->total < $minimum ) {
            if ($user->get_user_list() === "5") {
                if( is_cart() ) {
                    wc_print_notice(
                        sprintf( 'Debes realizar un pedido mínimo de %s para finalizar tu compra.' , // Personalizar texto
                            wc_price( $minimum ),
                            wc_price( WC()->cart->total )
                        ), 'error'
                    );
                } else {
                    wc_add_notice(
                        sprintf( 'Debes realizar un pedido mínimo de %s para finalizar tu compra.' , // Personalizar texto
                            wc_price( $minimum ), 
                            wc_price( WC()->cart->total )
                        ), 'error'
                    );
                }
            }
        }
    }
    add_action( 'woocommerce_checkout_process', 'al_minimum_order_amount' );
    add_action( 'woocommerce_before_cart' , 'al_minimum_order_amount' );
}

function woocommerce_after_calculate_totals( $cart ) {

    global $woocommerce;
    $user       = new Andina_licores_User(wp_get_current_user()->ID, wp_get_current_user()->roles);
    $items      = $woocommerce->cart->get_cart();
    $sub_total  = 0;

    foreach($items as $values) {
        $product = get_post_meta($values['data']->get_id());
        if ( empty( $product[ "_alg_wc_price_by_user_role_sale_price_lista_". $user->get_user_list() ][0] ) ) {
            $price= $values['quantity'] * $product[ "_alg_wc_price_by_user_role_regular_price_lista_". $user->get_user_list()][0];
            $sub_total += round($price, 2);
        }else{
            $price = $values['quantity'] * $product[ "_alg_wc_price_by_user_role_sale_price_lista_". $user->get_user_list()][0];
            $sub_total += round($price, 2);
        }
    }
    $sub_total = round( $sub_total, 2 );
    $cart->subtotal = $sub_total;
    $cart->total    = round( $sub_total * 1.12, 2 );
}
add_action( 'woocommerce_after_calculate_totals', 'woocommerce_after_calculate_totals', 30 );

if ( !function_exists( 'al_sync_save_discount' ) ) {
    /**
     * This function is used to save "tiempo Envio" after create order
     * @version 1.0.0
     * @since   1.0.0
     * @param   $order
     */
    function al_sync_save_discount( $order ) {
        $arr = [];
        $user       = new Andina_licores_User(wp_get_current_user()->ID, wp_get_current_user()->roles);
        foreach ($order->get_items() as $key => $item) {
            $item->set_subtotal(round($item->get_total(), 2));
            $product        = $item['variation_id'] == 0 ? $item->get_product() : wc_get_product( $item['variation_id'] );
            $product_meta   = get_post_meta($product->get_id());
            $discount       = !empty( $product_meta[ "_alg_wc_discount_percentage_lista_". $user->get_user_list() ][0] ) ? $product_meta[ "_alg_wc_discount_percentage_lista_". $user->get_user_list() ][0] : 0;
            $values = (object)[
                "product_id" => $product->get_id(),
                "discount"   => $discount
            ];
            array_push($arr, $values);
        }
        
        $subtotal = $order->get_subtotal();
        foreach ( $order->get_items('tax') as $tax_item ) {
            $tax_item->set_tax_total(round($subtotal * 0.12, 2));
        }
        $order->set_total( round($subtotal * 1.12, 2) );
        
        $order->update_meta_data( 'al_sync_discount', json_encode($arr));
        $order->update_meta_data( '_total_order', round($subtotal * 1.12, 2));
    }
    add_action('woocommerce_checkout_create_order', 'al_sync_save_discount', 12, 1);
}


if ( ! function_exists( 'al_sync_add_meta_boxes' ) ) {
	/**
	 * This function is used for adding meta container admin shop_order pages for Redypagos
	 * @version 1.0.0
	 * @since 	1.0.0
	 */
    function al_sync_add_meta_boxes()
    {
    	global $post;
    	$post_type = get_post_type( $post->ID );
    	if( $post_type == 'shop_order' ) {
	    	$order = wc_get_order($post->ID);
    		if( $order->get_payment_method() == 'wc_redypagos_gateway' ){
	        	add_meta_box( 'total_real_order_', __('Total pagado','woocommerce'), 'al_sync_show_total_order', 'shop_order', 'side', 'core' );
        	}
        }
    }
	add_action( 'add_meta_boxes', 'al_sync_add_meta_boxes' );
}

if ( ! function_exists( 'al_sync_show_total_order' ) ) {
	/**
	 * 
	 */
    function al_sync_show_total_order() {
        global $post;
		
        $code_meta = get_post_meta( $post->ID, '_total_order', true ) ? get_post_meta( $post->ID, '_total_order', true ) : '';
        
        $content = "";

        $content .= '
            <div style="border-bottom:solid 1px #eee;padding-bottom:13px;">
                <h3>$'.$code_meta.'</h3>
            </div>
        ';
    	
        echo $content;
    }
}

function custom_order_before_calculate_totals($and_taxes = true, $order) {
    $total = get_post_meta($order->get_id(), '_total_order', true);
    $subtotal = $order->get_subtotal();
    foreach ( $order->get_items('tax') as $tax_item ) {
        $tax_item->set_tax_total(round($subtotal * 0.12, 2));
    }
    $order->set_total( $total );
    $order->save();
}
add_action('woocommerce_order_before_calculate_totals', "custom_order_before_calculate_totals", 10, 2);
add_action('woocommerce_order_after_calculate_totals', "custom_order_before_calculate_totals", 10, 2);
