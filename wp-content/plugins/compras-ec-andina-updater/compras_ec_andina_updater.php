<?php
/**
 * Plugin Name: ComprasEC - Andina Sync
 * Description: Sincroniza los datos de productos y transacciones realizadas en la tienda. 
 * Version: 1.0.0
 * Author: ComprasEC - John Calle
 * Contributor: Github - @mvargaslandolfi1993
 */

if ( ! defined( 'ABSPATH' ) ) { exit;}

$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));

require('includes/functions.php');

if (in_array('woocommerce/woocommerce.php', $active_plugins)) {
    
    //------ se ejecuta cuando aplasta el boton de realizacion de pedido ------//
    
    add_action( 'woocommerce_before_checkout_process', 'action_woocommerce_before_checkout_process', 10, 1 );

    //-------------------------------------------------------------------------//
    
    //------------ Se ejecuta cuando agrega producto al carrito.  -------------//

    add_filter( 'woocommerce_add_to_cart_validation', 'UpdateStockPressAddCar', 10, 3); 

    //-------------------------------------------------------------------------//

    //------------------- se ejecuta despues de procesar pedido ---------------//

    add_action( 'woocommerce_after_checkout_validation', 'action_woocommerce_after_checkout_validation', 10, 2);

    //-------------------------------------------------------------------------// 
    
    //----------  permite cambiar el tipo de input del template   -------------//

    add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');

    //-------------------------------------------------------------------------//
    
    //----------  cambia el texto que se ve en stock  ------------------------//
    
    add_filter( 'woocommerce_get_availability_text', 'set_stock_bodega', 99, 2 );

    //------------------------------------------------------------------------//
    

    //--------------- Coloca la info de credito disponible -------------------//

    add_action( 'wp_footer', 'presupuesto_action' );
    
    //------------------------------------------------------------------------//

    //--------------  Cambia campo Codigo Postal a no requerido -------------//

    add_filter( 'woocommerce_default_address_fields' , 'custom_override_default_address_fields' );

    //-----------------  Calcula total descuento    -------------------------//

    add_action( 'woocommerce_after_calculate_totals', 'woocommerce_after_calculate_totals', 30 );
    
    //-----------------------------------------------------------------------//

    //------------- se ejecuta cuando agrega producto al carrito. ----------//

    //add_filter( 'woocommerce_add_to_cart_validation', 'UpdatePresupuesto', 10, 3); 

    //----------------------------------------------------------------------//

    //-------------------   Cambia  a 4 decimales   -----------------------//

    add_filter( 'wc_get_price_decimals', 'change_prices_decimals', 20, 1 );

    //----------------------------------------------------------------------//

    //------      Registro de Scripts   --------------//
    
    wp_register_style('css_andina', plugins_url( 'assets/css/style.css', __FILE__ ));
    
    wp_enqueue_style('css_andina');


    wp_register_script('jquery', "https://code.jquery.com/jquery-migrate-3.5.1.min.js", array(), '3.5.1' );
            
    wp_enqueue_script('jquery');

    wp_register_script('loader', "https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js", array());

    wp_enqueue_script('loader');

    wp_register_script('woocommerce_scripts', plugins_url( 'assets/js/script.js', __FILE__ ), array( 'jquery') );

            
    wp_enqueue_script('woocommerce_scripts');    
        
    //------------------------------------------------//
   
    function change_prices_decimals( $decimals ){
        if( is_cart() || is_checkout() )
            $decimals = 2;
        return $decimals;
    }

    function woocommerce_after_calculate_totals( $cart ) {

        $total_with_iva=0;
        $total_no_iva=0;
        
        $id=wp_get_current_user()->ID;
        $user=get_user_meta($id);
        $user_meta=get_userdata($id);
        $user_roles=$user_meta->roles;
        $lista=explode("_",$user_roles[0]);
        global $woocommerce;
        
        $items = $woocommerce->cart->get_cart();

        foreach($items as $item => $values) {

            $material=array(            
                "p_material"    => $values['data']->get_id(),       
                "p_bodega"      =>  $user['_cli_bodega'][0],  
                "p_lisprecio"   => $lista[1],  
                "p_cliente"     => $user['_cli_codigo'][0],  
                "p_agencia"     => '',       
                "p_cant"        => $values['quantity']
            );
            $resp=send_data_SOAP("wsConsultaMateriales",$material);
            
            $prod=json_decode($resp->wsConsultaMaterialesResult);
            
            error_log('Consulta producto:'.$prod->Materiales[0]->STOCK);

            
            $discount=$prod->Materiales[0]->DESCTO;
            
            $descuento=0;
            
            if($discount !=null)
            {
                $descuento = $values['data']->get_price()*$values['quantity']*($discount/100);
                $new_price=$values['data']->get_price()*$values['quantity']-$descuento;
                error_log('Se ha modificado el precio del producto: '.$values['data']->get_id().' de: $'.$values['data']->get_price()*$values['quantity'].' a : $'.$new_price.' ya que tiene un descuento de: '.$discount.'%');
                $values['data']->set_price($new_price);

            }

            $product=get_post_meta($values['data']->get_id());
            
            if($product["_pro_impuesto"][0] == 1)
            {
                $total_with_iva+=$total + $values['quantity'] *$product["_alg_wc_price_by_user_role_regular_price_".$user_roles[0]][0];
                $total_with_iva-=$descuento;
            }
            else{
                $total_no_iva+=$total + $values['quantity'] *$product["_alg_wc_price_by_user_role_regular_price_".$user_roles[0]][0];
                $total_no_iva-=$descuento;
            }
        }

        $total_with_iva = round($total_with_iva,2);
        
        $total_with_iva = round($total_with_iva*1.12,2);
        
        $total_no_iva = round($total_no_iva,2);
        
        $cart->total = round($total_with_iva+$total_no_iva,2);

    }

    //------------------------------------------------//
    
    function custom_override_default_address_fields( $address_fields ) {
        
        $address_fields['postcode']['required'] = false;

        return $address_fields;
    }
    
    function presupuesto_action() {

        $cod=wp_get_current_user()->ID;
        $user=get_user_meta($cod);

        if (is_user_logged_in() && $user['_cli_lista'][0] == 3 || $user['_cli_lista'][0] == 116 || $user['_cli_lista'][0] == 104 ) {
            echo '<div name="presupuesto" id="presupuesto" style="
                    border-left: 2px solid black;
                    border-top: 2px solid black;
                    border-bottom: 2px solid black;
                    text-align: center;
                    position: fixed;
                    top: 40%;
                    right: 0;
                    background-color: #092143;
                    color: #092143;
                    padding: 0;
                    font-size: 15px;
                    width: 50px;
                    height:80px;
                    font-weight: bold;
                ">
                <div id="icon-arrow"><i class="fas fa-chevron-right" style="color:#fff; font-size:15pt; margin:60% auto;"></i></div>
                    <div id="credito" style="display:none;">Crédito disponible</div>
                    <div id="sdisponible" class="saldo" style="font-weight: bold; display:none;
                                font-size: 35px;
                                color: #092143;"
                    > 
                        $'.$user['_cli_saldo_disponible'][0].'
                    </div>
                </div>';
        }
        
    }
  
     //verifica el stock de la bodega asignada, si no existe pone un mensaje de postegacion de dias
     function UpdatePresupuesto( $passed, $product_id, $quantity){
        
        $bandera=true;
        $id=wp_get_current_user()->ID;
        $user=get_user_meta($id);
        $product=get_post_meta($product_id);
        $user_meta=get_userdata($id);
        $user_roles=$user_meta->roles;


        $valueA=$user['_cli_saldo_disponible'][0];

        $valueA=$valueA-($quantity*$product["_alg_wc_price_by_user_role_regular_price_".$user_roles[0]][0]);


        $updated = update_user_meta( $id, '_cli_saldo_disponible', $valueA );

        if($updated){
            ?>
                <script>
                    $("#sdisponible").load(" #sdisponible");
                </script>
            
            <?php
        } 

        return true;
    }

    function set_stock_bodega( $availability, $product ) {
        // wp_delete_post( 190004557, true);
        $cod=wp_get_current_user()->ID;
        $user=get_user_meta($cod);
        $user_meta=get_userdata($id);
        $user_roles=$user_meta->roles;

        $product_meta = get_post_meta($product->get_id());

        //$sql="SELECT * FROM al_product_stock WHERE bg_id IN (".$user['_cli_bodega'][0].") AND product_id=".$product->get_id();
        $sql="SELECT * FROM al_product_stock WHERE bg_id=".$user['_cli_bodega'][0]." and product_id=".$product->get_id();
        
        global $wpdb;
        $results = $wpdb->get_results($wpdb->prepare($sql));
        // dump_error_log($results[0]->ps_stock);
        if($wpdb->num_rows >0) {
            $availability=$results[0]->ps_stock;
        }
        if ($availability == 0 || $user_roles[0] == "vendedor_al") {
            wp_enqueue_style(
                'custom_css', 
                plugins_url( 'assets/css/custom.css', __FILE__ )
            );
        }
        return $availability." disponibles";
    }
    // Cambia el look de los inputs de facturación
    function custom_override_checkout_fields ($fields) {
        
        $id=wp_get_current_user()->ID;
        $user=get_user_meta($id);

        $data_combo = array();

        
        for($i=0;$i<count($user['_cli_direccion'])-1;$i++)
        {
            $address= explode("?", $user['_cli_direccion'][$i]);
            if ($user['_cli_almacen'][0] === $address[0]) {
                $val=$address[0]."?".$address[2]."?".$address[3]."?".$address[4];
                $data_combo["$val"]=$address[1];
            }
        }

        $data_combo = (object) $data_combo;
        unset( $fields['billing']['billing_address_2'] );
        $fields['billing']['billing_address_1']['type'] = 'select';

        $fields['billing']['billing_address_1']['class'] = array('select2-selection');
        $fields['billing']['billing_address_1']['options'] =  $data_combo;
        
        unset( $fields['billing']['billing_first_name'] );
        unset( $fields['billing']['billing_last_name'] );

        $fields['billing']['billing_first_name']  = get_user_meta( $id, 'billing_first_name');
        $fields['billing']['billing_last_name']  =  get_user_meta( $id, 'billing_last_name');;
       
        // $address_fields['address_1']['required']=true;

        return $fields;
    }

    //Revisa el valor del producto cuando se agrega al carrito y si tiene descuento ese producto se lo aplica.
    
    
    //verifica el stock de la bodega asignada, si no existe pone un mensaje de postegacion de dias---falta actualizar stock
    function action_woocommerce_before_checkout_process($array){

        $id=wp_get_current_user()->ID;
        $user=get_user_meta($id);

        $user_meta=get_userdata($id);
        $user_roles=$user_meta->roles;
        $lista=explode("_",$user_roles[0]);
        $address    = explode("?", $_POST['billing_address_1']);
        $almacen    = $address[1];
        $dirEntrega = $address[0];
        $bodega     = $address[2];
        $pventa     = $address[3];
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        
        foreach($items as $item => $values) {
                        
            $material=array(            
                "p_material"=> $values['data']->get_id(),       
                "p_bodega"=>  $bodega,  
                "p_lisprecio"=> $lista[1],  
                "p_cliente"=> $user['_cli_codigo'][0],  
                "p_agencia"=> '',       
                "p_cant"=> $values['quantity']
            );
            // dump_error_log($material);
            // wc_add_notice("Esta bien la data enviandose","error");
            
            $resp=send_data_SOAP("wsConsultaMateriales",$material);
            $prod=json_decode($resp->wsConsultaMaterialesResult);

            if($values['quantity'] > $prod->Materiales[0]->STOCK){
                if ($prod->Materiales[0]->STOCK > 0) {
                     wc_add_notice("Solo hay ".$prod->Materiales[0]->STOCK." existencias para el producto (".$prod->Materiales[0]->PRO_NOMBRE."), debes remover del carrito para continuar con el proceso de compra","error");
                }else{
                     wc_add_notice("Disculpe, no hay existencias para el producto (".$prod->Materiales[0]->PRO_NOMBRE."), debes remover del carrito para continuar con el proceso de compra","error");
                }
            }
            /*
            else
            {
                // wc_add_notice("Se agregara correctamente ".$values['quantity']." productos. (".$prod->Materiales[0]->PRO_NOMBRE.")","error");
                // actualizar_stock($values['data']->get_id(),$values['quantity'],$user['_cli_bodega'][0]);
            }
            */  
                
        }
        /*

        $initi_fact=190004558;
        //Hacer un count de facturas y validar faltante
        
        $id=wp_get_current_user()->ID;
        $user=get_user_meta($id);
        $sql="SELECT * FROM al_terms t,al_bodega b WHERE t.name=b.bg_name and b.bg_id=".$bodega;
        $data=consult($sql);
        $results=$data["results"];
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
       
   
        if( $data["nrows"] > 0){ // si llega respuesta
            $cont=0;
            foreach($items as $item => $values) { 
                $product =  get_post_meta( $values['data']->get_id());
                if($product["_stock_at_".$results[0]->term_id][0] >= $values['quantity']){
                    $bandera=true;
                }
                else{
                    if($product["_stock"][0] >= $values['quantity'])
                    {
                        // wc_clear_notices();
                        $notice="De momento existen del producto ".$cont.": ".$product["_stock_at_".$results[0]->term_id][0]." productos en bodega. Que se entregaran en el transcurso de 24 horas.";
                        wc_add_notice($notice,"error");
                        
                        $notice="El resto de productos ( ".($values['quantity']-$product["_stock_at_".$results[0]->term_id][0]).") seran entregados en 72 horas.";
                        wc_add_notice($notice,"error");
    
                        $bandera=true;
                    }
                }
                $cont++;
            } 
            
        }
        else{
            wc_add_notice("No hay respuesta del servidor BD","error");
            $bandera=false;
        }
        */

    }


    
    //una vez creado el pedido se envia los datos de factura a web service de andina
    function action_woocommerce_after_checkout_validation( $data, $errors ) { // validaciones en el check out
    
        
        if(count($errors->errors) != 0 )//validacion de campos de facturacion
            return; 
        $id         = wp_get_current_user()->ID;
        $user       = get_user_meta($id);
        $user_meta  = get_userdata($id);
        $user_roles = $user_meta->roles;
        $lista      = explode("_",$user_roles[0]);
        $result;
        $ped_value  = 0;
        $is_fact    = false;
        $address    = explode("?", $data['billing_address_1']);
        $almacen    = $address[1];
        $dirEntrega = $address[0];
        $bodega     = $address[2];
        $pventa     = $address[3];

        error_log('El usuario: '.$user['_cli_ruc'][0].' empezo un proceso de compra. --------------------------------------------------------------------------------');
        $cabecera=array(    
            "ppn_almacen"       => $almacen,
            "ppn_pventa"        => $pventa, 
            "ppn_cliente"       => $user['_cli_codigo'][0],
            "ppn_concepto"      => $data['order_comments'] ? $data['order_comments'] : "Enviado desde B2B Andina Licores",    
            "ppn_listapre"      => $user['_cli_lista'][0],
            "ppn_ruta"          => $user['_cli_ruta'][0], 
            "ppn_DirEntrega"    => $dirEntrega,
            "ppn_agente"        => '',
            "ppn_fecha"         => '',
            "ppn_impuesto"      => '',    
            "ppn_politica"      => ''
        );
        dump_error_log($cabecera);
       
        
        if($data["payment_method"] == "credit_direct_gateway" or $data["payment_method"] == "bacs")
        {
            $result=send_data_SOAP("wsInsertCabeceraPedido",$cabecera);
            $ped_value=$result->wsInsertCabeceraPedidoResult;
        }
        else{
            $result=send_data_SOAP("wsInsertCabeceraFactura",$cabecera);
            $ped_value=$result->wsInsertCabeceraFacturaResult;
            $is_fact=true;
        }
        $count_errors= array();
        $count =0;
        if($ped_value != 0)
        {
            error_log("Si esta bien los datos se ha creado la cabecera");
            
            global $woocommerce;
            $items = $woocommerce->cart->get_cart();
            $total = 0;
            foreach($items as $item => $values) {
                
                // dump_error_log($values);
                // $product=get_post_meta($values['data']->get_id(),"_alg_wc_price_by_user_role_regular_price_".$user_roles[0]);
                $product=get_post_meta($values['data']->get_id());
                $prod_price=$product["_alg_wc_price_by_user_role_regular_price_".$user_roles[0]][0];
                $total = $prod_price + $total;
                $material=array(            
                    "p_material"    => $values['data']->get_id(),       
                    "p_bodega"      => $bodega, //$user['_cli_bodega'][0],  
                    "p_lisprecio"   => $lista[1],  
                    "p_cliente"     => $user['_cli_codigo'][0],  
                    "p_agencia"     => '',       
                    "p_cant"        => $values['quantity']
                );
                $resp=send_data_SOAP("wsConsultaMateriales",$material);
                $prod=json_decode($resp->wsConsultaMaterialesResult);
    
                $discount=$prod->Materiales[0]->DESCTO;
                $descuento=0;
                if($discount !=null)
                {
                    //$discount = $prod_price-($prod_price*$discount/100);
                    $prod_price=$prod_price;
                }
                else{
                    $discount=0;
                }
                
                $detalle=array(            
                    "Ppn_factura"       => $ped_value,     
                    "Ppn_producto"      => $values['data']->get_id(), 
                    "Ppn_unidad"        => $product["_uni_medida"][0],  
                    "Ppn_cantidad"      => $values['quantity'],   
                    "Ppn_precio"        => $prod_price,     
                    //"Ppn_porc_desc"=> $product["_desc_price_".$user_roles[0]][0],     
                    "Ppn_porc_desc"     => $discount,
                    "Ppn_ice"           => '', 
                    "Ppn_irbp"          => '',    
                    "Ppn_iva"           => $product["_pro_impuesto"][0],   
                    "Ppn_bodega"        => $bodega//$user['_cli_bodega'][0]
                );
                $det=send_data_SOAP("wsInsertDetalle_Ped_Fac",$detalle);
                
                if($det->wsInsertDetalle_Ped_FacResult == 0 )
                {
                    $count_errors[$count] = array( 
                                                    'code'=> $values['data']->get_id(),
                                                    'response'=> $det->wsInsertDetalle_Ped_Fac 
                                                );
                    $count++;
                }
                dump_error_log($detalle);
                error_log($det->wsInsertDetalle_Ped_FacResult);
                    
            }

            if($is_fact){
                $recibo=array(
                    "Ppn_factura"       => $ped_value,
                    "Ppn_tipopago"      => 2, 
                    "Ppn_valor"         => 0,    
                    "Ppn_nrodoc"        => 0,   
                    "Ppn_nrocta"        => 0,   
                    "Ppn_emisor"        => 0,
            );
            //$rec=send_data_SOAP("wsInsertaRecibo",$recibo);
            }else{
                error_log("no entra al recibo por que no es una factura");
            }
            error_log("detalle de errores: ".$count_errors);
            if(empty($count_errors))
            {
                $finaliza=array(
                    "Ppn_factura"       =>$ped_value,
                    "Ppn_transporte"    =>0,    
                    "Ppn_iva_transp"    =>0,
                    "Ppn_desc2"         =>0, 
                    "Ppn_desc2_0"       =>0,
                );
                // validar si retorna correctamente
                $fin=send_data_SOAP("wsFinaliza_Ped_Fac",$finaliza);
                error_log($fin->wsFinaliza_Ped_FacResult." : Si el anterior es cero algo fallo, caso contrario esta correcto para enviar a producción");
                
                //FINALIZAR EL PEDIO O FACTURA
                
                if($fin->wsFinaliza_Ped_FacResult != 0){
                    $produccion=array(
                        "Ppn_factura"=>$ped_value
                    );
                    $pd=send_data_SOAP("wsProduccion_Ped_fac",$finaliza);
                    
                    $valueA=$user['_cli_saldo_disponible'][0];

                    $valueA = $valueA - $total;

                    $updated = update_user_meta( $id, '_cli_saldo_disponible', $valueA );
                }
            }
            else{
                send_mail_error_order($ped_value,$count_errors);
                $notice="Lo sentimos, hubo un error al momento de entregar tu pedido a bodega. Estamos revisando el problema y nos pondremos en contacto contigo. Gracias";
                wc_add_notice($notice,"error");
            }
        }
        else{
            error_log("La data esta llegando con valor cero");
            $notice="Lo sentimos, hubo un error al generar la cabecera de tu pedido.";
            wc_add_notice($notice,"error");
        }
        error_log('El usuario: '.$user['_cli_ruc'][0].' finalizo un proceso de compra. --------------------------------------------------------------------------------');
        return;
    }
    

    function send_mail_error_order($facturaNro,$item_det)
    {
        $detalle_fact="";
        foreach ($item_det as $key => $items) {
            $detalle_fact.= "<br> Ppn_producto = ". $item['code']." codigo respuesta = ".$item['response']." <br>";
        }
        $destinatario = "ptenezaca@andinalicores.com.ec"; 
        $asunto = "Error proceso pedido"; 
        $cuerpo = ' 
        <html> 
            <head> 
                <title>Error en el pedido</title> 
            </head> 
        <body> 
            <h1>Cabecera Nro: '.$facturaNro.' </h1> 
            <p> 
                <b>Saludos Cordiales Patricio</b>. <br><br><br>
                Al parecer existe un error en la factura detallada, revisar por favor. A continuacion se detalla la respuesta de cada item dentro del pedido.<br><br>
                
                Estos son los productos y su respuesta en el detalle de la ordden:<br><br>
                '.$detalle_fact.'
                
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
        $headers .= "From: Andina Licores <andina@licores.com>\r\n"; 

        mail($destinatario,$asunto,$cuerpo,$headers);
    }

    //verifica el stock de la bodega asignada, si no existe pone un mensaje de postegacion de dias
    function UpdateStockPressAddCar( $passed, $product_id, $quantity){
        //falta actualizar stock antes de mostrar
        
        $bandera=true;
        $id=wp_get_current_user()->ID;
        $user=get_user_meta($id);

        $sql="SELECT * FROM al_terms t,al_bodega b WHERE t.name=b.bg_name and b.bg_id=".$user['_cli_bodega'][0];

        $data=consult($sql);
        $results=$data["results"];
        $product=get_post_meta($product_id);
        $pro = wc_get_product( $product_id );
        $nombre = $pro->get_name();
        //echo $product["_stock_at_".$results[0]->term_id][0];
        if( $data["nrows"] > 0){ // si llega respuesta
            if($product["_stock_at_".$results[0]->term_id][0] >= $quantity){
                $bandera=true;
                
            }
            else{
                if($product["_stock"][0] >= $quantity)
                {
                    if ($prod->Materiales[0]->STOCK > 0) {
                        $notice="Solo hay ".$product["_stock_at_".$results[0]->term_id][0]." existencias para el producto ".$nombre.", debes remover del carrito  para continuar con el proceso de compra";
                        wc_add_notice($notice,"error");
                    }else{
                        $notice="No hay existencias para el producto ".$nombre.", debes remover del carrito para continuar el proceso de compra.";
                        wc_add_notice($notice,"error");
                    }
                    $bandera=true;
                }
            }
        }
        else{
            wc_add_notice("Hubo un error de conexión, intente nuevamente en unos minutos.","error");
            $bandera=false;
        }

    return $bandera;
    }


    // add_action( 'woocommerce_review_order_before_payment', 'refresh_payment_methods' );
    function refresh_payment_methods(){
        ?>
        <script type="text/javascript">
            (function($){
                $( 'form.checkout' ).on( 'change', 'input[name^="payment_method"]', function() {
                    $('body').trigger('update_checkout');
                });
            })(jQuery);
        </script>
        <?php
    }

    //consume soap services
    function send_data_SOAP($function,$array){
        //$URLServices="http://179.49.47.4/WebServiceB2B.asmx?WSDL";
        $URLServices="http://200.24.205.212/WebServiceB2B.asmx?WSDL";
        $client = new SoapClient($URLServices);
        $response = $client->__soapCall($function,array($array)); 


        return $response;

    }

    //obtiene la orden generada
    function get_last_order_id(){
        global $wpdb;
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM `al_wc_order_stats` ORDER BY 1 DESC LIMIT 1"));
        return $results["order_id"];
    }
    
    //enviar consultas sql para obtener fila necesitada
    function consult($query) {
        global $wpdb;
        $results = $wpdb->get_results($wpdb->prepare($query));

        $nrows=$wpdb->num_rows;
        
        $data=array(
            "nrows" => $nrows,
            "results" => $results,
        );
        return $data;
    }
    
    //imprime var_dump en el log del sistema
    function dump_error_log( $object=null ){
        ob_start();
        var_dump( $object );
        $contents = ob_get_contents();
        ob_end_clean();
        error_log( $contents );
    }

    //------   ACTUALIZAR EL STOCK DE LAS TABLAS -------------//

    function actualizar_stock($id,$cantidad,$bodega)
    {
        $location=get_locationId($bodega);
        $post=get_post_meta($id,'_stock_at_'.$location);
        $new=$post[0]-$cantidad;

        $new_stock_total=get_sum_stock($id);

        update_post_meta( $id, "_stock_at_".$location, $new);

        $sql="UPDATE al_product_stock SET ps_stock=".$new." WHERE product_id=".$id." AND bg_id=".$bodega;
        global $wpdb;
        if(!$wpdb->query($sql))
        {
            wc_add_notice("Ha ocurrido un problema al actualizar stock","error");
            error_log("Ha ocurrido un problema al actualizar stock de la base de datos woocommerce","error");
        }

        $new_stock_total=get_sum_stock($id);

        update_post_meta( $id, "_stock", $new_stock_total);
    }
    
    function get_locationId($bg){
        $id=0;
        
        $sql="SELECT * FROM al_terms t,al_bodega b WHERE t.name=b.bg_name and b.bg_id=".$bg;
        
        global $wpdb;
        
        $results = $wpdb->get_results($wpdb->prepare($sql));
        
        if($wpdb->num_rows >0){
            $id=$results[0]->term_id;
        }
        return $id;
    }

    function get_sum_stock($id){
        $sum=0;
        
        $sql="SELECT SUM(meta_value) suma FROM al_postmeta WHERE meta_key LIKE '%_stock_%' AND post_id =".$id;

        global $wpdb;

        $results = $wpdb->get_results($wpdb->prepare($sql));
        
        if($wpdb->num_rows >0){
            $sum=$results[0]->suma;
        }

        return $sum;
    }

    //Cron atributos 
    function cron_andina_update_atribbutes() {
        /*
        $URLServices="http://200.24.205.212/WebServiceB2B.asmx?WSDL";
        $client = new \SoapClient($URLServices);
        $result = $client->__soapCall('wsConsultaMateriales_Masivo', array() );
        $data=json_decode(json_encode($result),true);
        foreach ($data as $value)
            $data= $value;

        $response = json_decode($data,true);
        foreach($response['Materiales'] as $product){
            andina_set_atributtes( $product );
        }
        */
    }
    add_action( 'andina_update_atribbutes', 'cron_andina_update_atribbutes', 10, 0 );


    function andina_set_atributtes($producto){
        /*
        $product = new WC_Product_Variable($producto['PRO_CODIGO']);

        $attribute = new WC_Product_Attribute();//declaramos nuestro primer atributo
        //$attribute->set_id(0);//le damos una id
        $attribute->set_name('brand');// y un nombre

        $marcas = array(0 => $producto['MARCA']);
        $attribute->set_visible(true);
        $attribute->set_variation(true);
        $attribute->set_options($marcas);//le asignamos los valores al atributo
        $product->set_attributes($attribute);
        $product->save();
        */
    }
    //-----------------------------------------------------------------//
}
