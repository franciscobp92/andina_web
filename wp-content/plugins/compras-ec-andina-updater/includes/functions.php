<?php

    //------------------------------------------------//
    
 	add_action('wp_ajax_updated_checkout','updated_checkout');

    add_action('wp_ajax_nopriv_updated_checkout', 'updated_checkout');

    function updated_checkout() {
         
		$total_with_iva=0;
        $total_no_iva=0;
        
        $id=wp_get_current_user()->ID;
        $user=get_user_meta($id);
        $user_meta=get_userdata($id);
        $user_roles=$user_meta->roles;
        $lista=explode("_",$user_roles[0]);

        global $woocommerce;
        
        $items = $woocommerce->cart->get_cart();
        $array = [];
        foreach($items as $item => $values) {

            $material=array(            
                "p_material"=> $values['data']->get_id(),		
                "p_bodega"=>  $user['_cli_bodega'][0],	
                "p_lisprecio"=> $lista[1],	
                "p_cliente"=> $user['_cli_codigo'][0],	
                "p_agencia"=> '',		
                "p_cant"=> $values['quantity']
            );
            //DESCUENTO 

            $resp=send_data_SOAP("wsConsultaMateriales",$material);
            
            $prod=json_decode($resp->wsConsultaMaterialesResult);

            $discount=$prod->Materiales[0]->DESCTO;
            
            $descuento=0;
            
            if($discount !=null)
            {
                $descuento      =  $values['data']->get_price()*($discount/100);
                $descuentoTotal = $values['data']->get_price()*$values['quantity']*($discount/100);
                $new_price      = $values['data']->get_price()-$descuento;
                $price          = $new_price*$values['quantity'];
                $values['data']->set_price($new_price);
                
            }else{
                $new_price      = $values['data']->get_price();
                $descuento      = 0;
                $price          = $new_price*$values['quantity'];
            }
            
            $productos = array(
                    'product_id'        => $values['product_id'],
                    'descuento'         => $discount ? number_format($discount,2) : 0,
                    'totalDescuento'    => number_format($descuentoTotal,2),
                    'precioNuevo'       => number_format($new_price,2),
                    'subtotal'          => number_format($price,2)
            );

            array_push($array, $productos);
            
        }
         wp_send_json($array);  
    }


    function script_hooks()
    {
        $id=wp_get_current_user()->ID;
        $user=get_user_meta($id);
        $array = [];

        for($i=0;$i<count($user['_cli_direccion'])-1;$i++)
        {
            $address= explode("?", $user['_cli_direccion'][$i]);
            $values = [
                "bodega" => $address[0]."?".$address[3]."?".$address[5]."?".$address[6],
                "direccion" => $address[1],
                "dir"       => $address[0]
            ];
            array_push($array, $values);
        }

        wp_enqueue_style(
                'font_awesome',
                'https://pro.fontawesome.com/releases/v5.10.0/css/all.css',
                array(),
                '4.0.0'
            );
        
        wp_register_script('woocommerce_address', plugins_url( '../assets/js/address.js', __FILE__ ), array( 'jquery') );

        wp_enqueue_script('woocommerce_address');    
        
        wp_localize_script( 'woocommerce_address', 'params', array(
            'array'         => (object) $array,
            'actual'        => $user['_cli_almacen'],
            "isLoggedIn"    => is_user_logged_in(),
            'url'           => admin_url( 'admin-ajax.php' ),
            'nonce'         => wp_create_nonce( 'my-ajax-nonce' ))
        );

        wp_enqueue_script('woocommerce_address');
    }

    add_action('wp_enqueue_scripts', 'script_hooks');


    add_action('wp_ajax_change_user_address','change_user_address');

    add_action('wp_ajax_nopriv_change_user_address', 'change_user_address');

    function change_user_address()
    {
        $id=wp_get_current_user()->ID;
        $user=get_user_meta($id);
        $bodega= explode("?", $_POST['data']);
        update_user_meta( $id, '_cli_almacen', $bodega[0] );
        update_user_meta( $id, '_cli_bodega', $bodega[1] );
        update_user_meta( $id, 'billing_city', $bodega[2]);
        update_user_meta( $id, 'billing_state', $bodega[3]);
        wp_send_json(true);
    }
    //-------------------------------------------------//    

    /* ELIMINACION DE CAMPO ZIP CODE */
    add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields_post_code' );
    function custom_override_checkout_fields_post_code( $fields ) {
        unset($fields['billing']['billing_postcode']);
        return $fields;
    }

    /* OCULTAR PRODUCTOS SEGUN STOCK EN SU BODEGA */
    add_action('woocommerce_product_query', 'show_only_instock_products');
    function show_only_instock_products($query) {
        $id=wp_get_current_user()->ID;
        $user=get_user_meta($id);
        $user_meta=get_userdata($id);
        $user_roles=$user_meta->roles;
        $lista=explode("_",$user_roles[0]);
        if (!is_user_logged_in()) {
            $meta_query = $query->get( 'meta_query' );
            $meta_query[] = array(
                    'key'       => '_can_visible_by_guest',
                    'compare'   => 'LIKE',
                    'value'     => 'yes'
            );
            $query->set( 'meta_query', $meta_query );
        }else{
            if ($user_roles[0] == "vendedor_al") {
                $meta_query = $query->get( 'meta_query' );
                $meta_query[] = array(
                        'key'       => '_can_visible_by_guest',
                        'compare'   => 'LIKE',
                        'value'     => 'yes'
                );
                $query->set( 'meta_query', $meta_query );
            }else if ($user['_cli_bodega'][0] === "300") {
                $meta_query = $query->get( 'meta_query' );
                $meta_query[] = array(
                        'key'       => '_stock_at_49',
                        'compare'   => '>',
                        'value'     => 0
                );
                $query->set( 'meta_query', $meta_query );
            }else if ($user['_cli_bodega'][0] == "301") {
                $meta_query = $query->get( 'meta_query' );
                $meta_query[] = array(
                        'key'       => '_stock_at_50',
                        'compare'   => '>',
                        'value'     => 0
                );
                $query->set( 'meta_query', $meta_query );
            }else if ($user['_cli_bodega'][0] == "302") {
                $meta_query = $query->get( 'meta_query' );
                $meta_query[] = array(
                        'key'       => '_stock_at_51',
                        'compare'   => '>',
                        'value'     => 0
                );
                $query->set( 'meta_query', $meta_query );
            }else if ($user['_cli_bodega'][0] == "306") {
                $meta_query = $query->get( 'meta_query' );
                $meta_query[] = array(
                        'key'       => '_stock_at_52',
                        'compare'   => '>',
                        'value'     => 0
                );
                $query->set( 'meta_query', $meta_query );
            }else if ($user['_cli_bodega'][0] == "307") {
                $meta_query = $query->get( 'meta_query' );
                $meta_query[] = array(
                        'key'       => '_stock_at_53',
                        'compare'   => '>',
                        'value'     => 0
                );
                $query->set( 'meta_query', $meta_query );
            }else if ($user['_cli_bodega'][0] == "308") {
                $meta_query = $query->get( 'meta_query' );
                $meta_query[] = array(
                        'key'       => '_stock_at_54',
                        'compare'   => '>',
                        'value'     => 0
                );
                $query->set( 'meta_query', $meta_query );
            }else if ($user['_cli_bodega'][0] == "311") {
                $meta_query = $query->get( 'meta_query' );
                $meta_query[] = array(
                        'key'       => '_stock_at_57',
                        'compare'   => '>',
                        'value'     => 0
                );
                $query->set( 'meta_query', $meta_query );
            }else if ($user['_cli_bodega'][0] == "312") {
                $meta_query = $query->get( 'meta_query' );
                $meta_query[] = array(
                        'key'       => '_stock_at_58',
                        'compare'   => '>',
                        'value'     => 0
                );
                $query->set( 'meta_query', $meta_query );
            }    
        }
    }

    add_filter( 'woocommerce_get_price_html', 'ocultar_precios' );
    function ocultar_precios( $price ) {
        $id=wp_get_current_user()->ID;
        $user=get_user_meta($id);
        $user_meta=get_userdata($id);
        $user_roles=$user_meta->roles;
        $lista=explode("_",$user_roles[0]);
        if ($user_roles[0] === "vendedor_al") {
            add_filter( 'woocommerce_loop_add_to_cart_link', '__return_false' );
            remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
            return '';
        }else{
            return $price;
        }
    }