<?php

/**

 * Plugin Name: ComprasEC - Andina Licores Api Dashboard

 * Plugin Uri:

 * Description: Metodos para uso del dashboard usando API de Wordpress

 * Version: 1.0.0

 * Author: John Calle

 */

$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));





/*   METODOS REGISTRADOS EN LA API*/

    

    add_action( 'rest_api_init', 

        function () 
        {
            register_rest_route(
                'custom-plugin', '/login/',
                array(
                    'methods'  => 'POST',
                    'callback' => 'login',
                )
             );
         }

    );
    
    add_action( 'rest_api_init', 

        function () 
        {
            register_rest_route(
                'custom-plugin', '/verificar/usuario/',
                array(
                    'methods'  => 'POST',
                    'callback' => 'verificarUsuario',
                )
             );
         }

    );


    add_action( 'rest_api_init', 
        function ()
        {
            register_rest_route( 
                'custom-plugin', '/registeruser/', 
                array(
                    'methods'=> 'POST',
                    'callback'=> 'reg'
                ) 
            );
        }
    );

    add_action( 'rest_api_init', 
        function ()
        {
            register_rest_route( 
                'custom-plugin', '/clientes/(?P<id>\d+)', 
                array(
                    'methods'=> 'GET',
                    'callback'=> 'obtener_clientes'
                ) 
            );
        }
    );


    add_action( 'rest_api_init',

        function ()
        {
             register_rest_route( 
                'custom-plugin', '/users/(?P<id>\d+)', 
                array(
                    'methods'=> 'GET',
                    'callback'=> 'obtener_data_usuario'
                ) 
            );
        }
    );

    
    add_action( 'rest_api_init',

        function ()
        {
            register_rest_route( 
                'custom-plugin', '/searchClient/(?P<id>\d+)', 
                array(
                    'methods' => 'GET',
                    'callback' => 'consult_client',
                ) );
        }
    );

     add_action( 'rest_api_init', 
        function ()
        {
            register_rest_route( 
                'custom-plugin', '/vendedor/(?P<id>\d+)', 
                array(
                    'methods'=> 'GET',
                    'callback'=> 'consult_vendedor'
                ) 
            );
        }
    );
    
    add_action( 'rest_api_init',

        function ()
        {
            register_rest_route( 
                'custom-plugin', '/pedido/(?P<id>\d+)', 
                array(
                    'methods' => 'GET',
                    'callback' => 'obtener_orders',
                ) );
        }
    );

    add_action( 'rest_api_init',

        function ()
        {
            register_rest_route( 
                'custom-plugin', '/pedido/cliente/(?P<id>\d+)', 
                array(
                    'methods' => 'GET',
                    'callback' => 'obtener_orders_cliente',
                ) );
        }
    );

    add_action( 'rest_api_init',

        function ()
        {
            register_rest_route( 
                'custom-plugin', '/vendedor/pedidos/(?P<id>\d+)', 
                array(
                    'methods' => 'GET',
                    'callback' => 'obtener_orders_vendedor',
                ) );
        }
    );

    add_action( 'rest_api_init',

        function ()
        {
            register_rest_route( 
                'custom-plugin', '/cuenta/(?P<id>\d+)', 
                array(
                    'methods' => 'GET',
                    'callback' => 'obtener_cuenta_user',
                ) );
        }
    );


    

    add_action( 'rest_api_init',

        function ()
        {
            register_rest_route( 
                'custom-plugin', '/solicitud/', 
                array(
                    'methods' => 'POST',
                    'callback' => 'enviarSolicitud',
                ) );
        }
    );
    
    add_action( 'rest_api_init',

        function ()
        {
            register_rest_route( 
                'custom-plugin', '/notificacion/', 
                array(
                    'methods' => 'POST',
                    'callback' => 'enviarNotificacion',
                ) );
        }
    );
    
    add_action( 'rest_api_init',

        function ()
        {
            register_rest_route( 
                'custom-plugin', '/logout/', 
                array(
                    'methods' => 'POST',
                    'callback' => 'wp_oauth_server_logout',
                ) );
        }
    );


/*   FUNCIONES PARA LOS METODOS*/   





    function obtener_data_usuario($data) {
        $userdata =get_user_meta( $data['id']);
        if ( is_wp_error($userdata) ){   
            echo $user_id->get_error_message();
        }
        return $userdata;
    }


    function verificarUsuario(WP_REST_Request $request) {
        global $wpdb;
        $user = get_user_by('email', $request['email']);
        if($user) {
            return true;
        }else{
            $sql="SELECT * FROM al_usermeta WHERE meta_key='_cli_ruc' and meta_value=" . $request["ruc"];
            $results = $wpdb->get_results($wpdb->prepare($sql));
    
            $response=array();
            
            $nrow= $wpdb->num_rows;
            if($nrow > 0){
                return true;
            }else{
                return false;
            }
        }
    }


    function login(WP_REST_Request $request ){
        
        $data=array();
        $data['user_login'] = $request['username'];
        $data['user_password'] = $request["password"];

        $response;
        $user = wp_signon( $data, false );
        if ( is_wp_error($user) )
        {
            // echo $user->get_error_message();
            return $user;
        }
        else{
            $userTk=getTokenUser($request['username'],$request["password"]);
            $response=json_decode($userTk, true);
            $response["ID"]=$user->ID;
            $response["roles"]=$user->roles[0];
            $d=wp_get_session_token();
            $user->token=wp_get_session_token();

    
            $userdata =get_user_meta($user->ID);
            if($user->roles[0] == "vendedor_al"){
                
                $response["code"] =$userdata['_cli_codigo_vend'][0];
            }
            else{
                $response["code"] =$userdata['_cli_codigo'][0];
                $response["mail_emp"] =$userdata['_cli_mail_emp'][0];
            }

            return $response;
        }
    }





    function reg(WP_REST_Request $request){
        global $wpdb;
        $sql="SELECT * FROM al_usermeta WHERE meta_key='_cli_codigo' and meta_value=" . $request["codigo"];
        $results = $wpdb->get_results($wpdb->prepare($sql));

        $response=array();

        $nrow=$wpdb->num_rows;
        if($nrow == 0){
            $creds = array();
            $creds['user_login'] = $request["user"] . rand(100, 1000);
            $creds['user_password'] = $request["password"];
            $creds['user_email'] = $request['email'];
            $creds['first_name'] = $request['first_name'];
            $creds['last_name'] = $request['last_name'];
            $creds['role'] = $request['roles'];
            $user_id = wp_insert_user( $creds );
            if ( is_wp_error($user_id) )
            {                
                echo $nrow;
                echo $user_id->get_error_message();
                $response['code']="incorrect";
            }
            else{
                if($request["type"]=="cliente"){
                    add_user_meta( $user_id, '_cli_codigo', $request["codigo"]);
                    add_user_meta( $user_id, '_cli_bodega', $request["bodega"]);
                    add_user_meta( $user_id, '_cli_cupo', $request["cupo"]);
                    add_user_meta( $user_id, '_cli_saldo_disponible', $request["saldo_disponible"]);
                    add_user_meta( $user_id, '_cli_saldo_consumido', $request["saldo_consumido"]);
                    add_user_meta( $user_id, '_cli_pago', $request["pago"]);
                    add_user_meta( $user_id, '_cli_lista', $request["lista"]);
                    add_user_meta( $user_id, '_cli_almacen', $request["almacen"]);
                    add_user_meta( $user_id, '_cli_ruta', $request["ruta"]);
                    add_user_meta( $user_id, '_cli_mail_emp', $request["mail_emp"]);
                    add_user_meta( $user_id, '_cli_telefono1', $request["telefono1"]);
                    add_user_meta( $user_id, '_cli_telefono2', $request["telefono2"]);
                    add_user_meta( $user_id, '_cli_pventa', $request["pventa"]);
                    add_user_meta( $user_id, '_cli_nombre_cliente', $request["nombre_cliente"]);
                    add_user_meta( $user_id, '_cli_ruc', $request["ruc"]);
                    add_user_meta( $user_id, '_cli_nombre_comercial', $request["nombre_comercial"]);
                    add_user_meta( $user_id, '_cli_mail_inicio', $request['email']);
                    add_user_meta( $user_id, '_cli_cupo_gastado', '0');
                    add_user_meta( $user_id, '_cli_bodega_sesion','0');

                    $data = explode(";", $request["direccion"]);
                    for($i=0;$i<count($data);$i++){
                        add_user_meta( $user_id, '_cli_direccion',$data[$i] );
                    }
                    
                    //SET DIRECTION
                    $address = explode("?", $data[0]);
                    update_user_meta( $user_id, '_cli_dir', $address[0] );
                    update_user_meta( $user_id, 'billing_address_1', $address[1] );
                    update_user_meta( $user_id, '_cli_almacen', $address[2] );
                    update_user_meta( $user_id, '_cli_bodega',  $address[3] );
                    update_user_meta( $user_id, '_cli_pventa',  $address[4] );
                    update_user_meta( $user_id, 'billing_city', $address[5] );
                    update_user_meta( $user_id, 'billing_state', str_replace(";", "", $address[6]) );
                
                
                    //datos de facturacion
                    add_user_meta( $user_id, 'billing_email', $request["mail_emp"]);
                    add_user_meta( $user_id, 'billing_company', $request["nombre_comercial"]);
                    add_user_meta( $user_id, 'billing_phone', $request["telefono1"]);
					add_user_meta( $user_id, 'billing_first_name', $request["nombre_cliente"]);
                    add_user_meta( $user_id, 'billing_last_name', $request["nombre_cliente"]);
                }
                else{
                    add_user_meta( $user_id, '_cli_mail_inicio', $request['email']);
                    add_user_meta( $user_id, '_cli_id',$request["codigo"]);
                    add_user_meta( $user_id, '_cli_codigo_vend',$request["id"]);
                    add_user_meta( $user_id, '_cli_nombre',$request["nombre"]);
                }
                
                
                $sql="SELECT * FROM al_users ORDER BY id DESC LIMIT 1";
                $results = $wpdb->get_results($wpdb->prepare($sql));

                wp_set_password( $request["password"],$results[0]->ID);
                
                $wpdb->query('COMMIT');
                
                $response['code']="correct";


            }
        }
        else{
            $response['code']="already_exist";
        }

        return $response;
    }


    
    function consult_client(WP_REST_Request $request)
    {
        //$url='http://179.49.47.4/WebServiceB2B.asmx?wsdl';
        $url='http://200.24.205.212/WebServiceB2B.asmx?wsdl';
        $client = new SoapClient($url);
        $codigo=$request["id"];
        $result = $client->wsConsultaCliente(["pReferencia" => $codigo]);

        $data=json_decode(json_encode($result),true);
        foreach ($data as $value)
            $data= $value;

        $clientData = json_decode($data,true);

        $response = json_encode($clientData,true);
        return $response;
    }
    
    function consult_vendedor(WP_REST_Request $request)
    {
        //$url='http://179.49.47.4/WebServiceB2B.asmx?wsdl';
        $url='http://200.24.205.212/WebServiceB2B.asmx?wsdl';
        $client = new SoapClient($url);
        $codigo=$request["id"];
        $result = $client->wsConsultaAgente(["p_agente" => $codigo]);
        $data=json_decode(json_encode($result),true);
        foreach ($data as $value)
            $data= $value;

        $clientData = json_decode($data,true);

        $response = json_encode($clientData,true);
        return $response;
    }


    function enviarSolicitud(WP_REST_Request $request){
        $destinatario = "comercial@andinalicores.com.ec"; 
        $asunto = "Solicitud de nuevo cliente"; 
        $cuerpo = ' 
        <html> 
            <head> 
                <title>Formulario con información</title> 
            </head> 
        <body> 
            <h1>Solicitud </h1> 
            <p> 
                <b>Saludos Cordiales</b>. <br><br><br>
                Al parecer hay alguien interesado(a) en ser cliente de Andina Licores,<br>
                Por favor ponerse en contacto con la persona.<br><br>
                
                A continuación la información del prospecto:<br><br>
                
                    CÉDULA/RUC:'.$request["ruc"].' <br> <br>
                    NOMBRES:'.strtoupper($request["nombre"]).' <br> <br>
                    DIRECCION:'.strtoupper($request["direccion"]).' <br> <br>
                    EMAIL:'.$request["email"].' <br> <br>
                    TELEFONO:'.$request["telefono"].' <br><br> <br>

                Saludos.
            </p> 
        </body> 
        </html> 
        '; 

        //para el envío en formato HTML 
        $headers = "MIME-Version: 1.0\r\n"; 
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

        //dirección del remitente 
        $headers .= "From: Contacto <comercial@andinalicores.com.ec>\r\n"; 

        $data=array();

        if(mail($destinatario,$asunto,$cuerpo,$headers))
        {
            $data['code']="correct";
        }else{
            $data['code']="error";
        }

    
        return $data;
        
    }

    function enviarNotificacion(WP_REST_Request $request){
        
        $destinatario = $request['destinatario']; 
        $asunto = $request['asunto'];
        $cuerpo = ' 
        <html> 
            <head> 
                <title>"'.$request['title'].'"</title> 
            </head> 
        <body> 
            <div style="width: 300px; margin: auto;">
                <img src="https://andinalicores.com.ec/b2b/assets/logoas.png" width= "128" height="129" style="display: block; margin: auto;">
                <h1 style="text-align:center;">¡Hola '.$request['name'].'!</h1>
                '.$request['body'].' 
            </div>
            <br>
        </body> 
        </html> 
        '; 

        //para el envío en formato HTML 
        $headers = "MIME-Version: 1.0\r\n"; 
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

        //dirección del remitente 
        $headers .= "From: Contacto <comercial@andinalicores.com.ec>\r\n"; 

        $data=array();
        $send = mail($destinatario,$asunto,$cuerpo,$headers);
        if($send)
        {
            $data['code']="Correo electronico enviado con exito";
            $data['error'] = false;
        }else{
            $data['code']="Hubo un error enviando el correo electronico";
            $data['error'] = true;
        }

        return $data;
        
    }

    function wp_oauth_server_logout(WP_REST_Request $request) {
        wp_logout();
        $data=is_user_logged_in();
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
            }
        }
        return $data;
    }


    function obtener_orders(WP_REST_Request $request) {
        global $wpdb;
        

        $url='http://200.24.205.212/WebServiceB2B.asmx?wsdl';
        $client = new SoapClient($url);
        $codigo=$request["id"];
        $user=$request['ID'];
        $result = $client->wsConsulta_PedFac(["p_Referencia" => $codigo]);
        $data=json_decode(json_encode($result),true);
        //encode devuelve en string
        //decode en json
        foreach ($data as $value)
            $data= $value;
    
        $clientData = json_decode($data,true);

        $array=[];
        foreach($clientData["Detalle_PedFac"] as $orders)
        {
            

            $id=wc_get_product_id_by_sku($orders["SKU_PEDIDO"]);
            $data=get_user_meta($user,'_cli_lista');
            $price=get_post_meta($id,'_alg_wc_price_by_user_role_regular_price_lista_'.$data[0]);
            $sql="SELECT * FROM al_posts WHERE post_type='attachment' and post_title=" . $id;
            $query = $wpdb->get_results($wpdb->prepare($sql));
            if(count($query) > 0)
                $orders["CODE"]=$query[0]->guid;
            else    
                $orders["CODE"]="";
                
            $orders["PRICE"]=$price[0];
            array_push($array, $orders);
        }
             

        return $array;
    }

    function obtener_orders_cliente(WP_REST_Request $request) {
        global $wpdb;
        

        $url='http://200.24.205.212/WebServiceB2B.asmx?wsdl';
        $client = new SoapClient($url);
        $codigo=$request["id"];
        //$user=$request['ID'];
        $result = $client->wsConsulta_PedFac_Clientes(["p_cliente" => $codigo]);
        $data=json_decode(json_encode($result),true);
        //encode devuelve en string
        //decode en json
        foreach ($data as $value)
            $data= $value;
    
        $clientData = json_decode($data,true);

        $array=[];
        foreach($clientData["PedFacClientes"] as $orders)
        {
            

            $id=wc_get_product_id_by_sku($orders["SKU_PEDIDO"]);
            $data=get_user_meta($user,'_cli_lista');
            $price=get_post_meta($id,'_alg_wc_price_by_user_role_regular_price_lista_'.$data[0]);
            $sql="SELECT * FROM al_posts WHERE post_type='attachment' and post_title=" . $id;
            $query = $wpdb->get_results($wpdb->prepare($sql));
            if(count($query) > 0)
                $orders["CODE"]=$query[0]->guid;
            else    
                $orders["CODE"]="";
                
            $orders["PRICE"]=$price[0];
            array_push($array, $orders);
        }
             
        $resp["PedFacClientes"]=$array;
        return $resp;
    }

    function getTokenUser($user,$pass)
    {
        //datos a enviar
        $data = array("username" => $user,"password" =>$pass);
        //url contra la que atacamos
        $ch = curl_init("https://andinalicores.com.ec/wp-json/jwt-auth/v1/token");
        //a true, obtendremos una respuesta de la url, en otro caso, 
        //true si es correcto, false si no lo es
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //establecemos el verbo http que queremos utilizar para la petición
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        //enviamos el array data
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        //obtenemos la respuesta
        $response = curl_exec($ch);
        // Se cierra el recurso CURL y se liberan los recursos del sistema
        curl_close($ch);
        if(!$response) {
            return false;
        }else{
            return $response;
        }
    }

    function obtener_clientes (WP_REST_Request $request) {
        $url='http://200.24.205.212/WebServiceB2B.asmx?wsdl';
        $client = new SoapClient($url);
        $codigo=$request["id"];
        $result = $client->wsConsulta_Agente_Cliente(["p_Agente" => $codigo]);
        $data=json_decode(json_encode($result),true);
        foreach ($data as $value)
            $data= $value;

        $clientData = json_decode($data,true);
        return $clientData;
    }
    
    function obtener_orders_vendedor(WP_REST_Request $request) {
        global $wpdb;
        

        $url='http://200.24.205.212/WebServiceB2B.asmx?wsdl';
        $clientSOAP = new SoapClient($url);
        $codigo=$request["id"];
        $result = $clientSOAP->wsConsulta_Agente_Cliente(["p_Agente" => $codigo]);
        //consulta los clientes del vendedor
        $data=json_decode(json_encode($result),true);
        foreach ($data as $value)
            $data= $value;

        $clientList = json_decode($data,true);

        //recorremos los clientes
        $array=[];
        foreach($clientList["AgenteCliente"] as $client) {
            $respuesta=$clientSOAP->wsConsulta_PedFac_Clientes(["p_cliente" => $client['CLD_CLIENTE']]);
            $clientData=json_decode(json_encode($respuesta),true);
            
           
            foreach ($clientData as $value) 
                $clientData=$value;

            $clientDataFinal=json_decode($clientData,true);

            foreach($clientDataFinal["PedFacClientes"] as $orders) {

                $orders['CODIGO_CLIENTE']=$client["CLD_CLIENTE"];
                $orders['CLIENTE_MAIL']=$client["CLI_MAIL"];
                $orders['CLIENTE_TELEFONO']=$client["CLI_TELEFONO1"];
                array_push($array, $orders);
            }
        }

        $resp["PedFacClientes"]=$array;


        return $resp;
    }


    function obtener_cuenta_user(WP_REST_Request $request) {

        global $wpdb;
        $url='http://200.24.205.212/WebServiceB2B.asmx?wsdl';

        $clientSOAP = new SoapClient($url);
        $codigo=$request["id"];
        $id=$request["ID"];
        
        $userdata =get_user_meta( $id);

        $result = $clientSOAP->wsConsulta_PedFac_Clientes(["p_cliente" => $codigo]);
        $data=json_decode(json_encode($result),true);
        foreach ($data as $value)
            $data= $value;
    
        $clientData = json_decode($data,true);

        

       
        $sql="SELECT * FROM al_wc_order_stats WHERE order_id IN ( select post_id from al_postmeta where meta_key='_customer_user' and meta_value=". $id . ")";
        $query = $wpdb->get_results($wpdb->prepare($sql));

        $orders=[];

        foreach($query as $order) {
            if($order->status == "wc-on-hold" ) {
                $o=(array) $order;
                $o["cliente"]=$userdata["_cli_nombre_cliente"][0];
                array_push($orders, $o);
            }
        }

        $array["cupo"]=$userdata["_cli_cupo"][0];
        $array["disponible"]=$userdata["_cli_saldo_disponible"][0];
        $array["usado"]=$userdata["_cli_saldo_consumido"][0];
        $array["nro_pedidos"]=count($clientData["PedFacClientes"]) + count($query);
        $array["orders"]=$orders;
        
        return  $array;
    }


