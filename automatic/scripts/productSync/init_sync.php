<?php 

  # no se registra el caché de WSDL
  ini_set("soap.wsdl_cache_enabled", "0");
  set_time_limit(10000);

//   require __DIR__ . '/vendor/autoload.php';

//   use Automattic\WooCommerce\Client;

  // $woocommerce = new Client(
  //     'http://andinalicores.com.ec/index.php',
  //     'ck_8c9346d422baaf5ec2c5fd48f959733e6a840fc6',
  //     'cs_86948df2fabff18172944f076e59b696f9ba78c0',
  //     [
  //         'wp_api' => true,
  //         'version' => 'wc/v3',
  //         'query_string_auth' => true // Force Basic Authentication as query string true and using under HTTPS
  //     ]
  // );



  # Incluir "logger"
  include_once "logger.php";
  include_once "connection.php";
  include_once "dataSync/categories.php";
  include_once "dataSync/products.php";
  include_once "dataSync/price.php";
  include_once "dataSync/stock.php";



  $URLServices="http://200.24.205.212/WebServiceB2B.asmx?WSDL";
  $client = new SoapClient($URLServices);

  CategoriesSync($URLServices,$client);
  ProductsSync($URLServices,$client);
  PriceSync($URLServices,$client);
  StockSync($URLServices,$client);
  ProductsSyncStockTotal($URLServices,$client);
  //read_txt_image();


  

?>

     