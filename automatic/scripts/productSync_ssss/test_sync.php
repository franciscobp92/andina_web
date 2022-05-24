<?php 

    ini_set("soap.wsdl_cache_enabled", "0");
    set_time_limit(10000);
    
    //includes
    include_once "connection.php";
    include_once "dataSync/categories.php";
    include_once "dataSync/products.php";
    include_once "dataSync/price.php";
    include_once "dataSync/stock.php";
    
    $URLServices="http://179.49.47.4/WebServiceB2B.asmx?WSDL";
    $client = new SoapClient($URLServices);
    
    echo "<br><br>Categorias Sincronizando<br>";
    CategoriesSync($URLServices,$client);
    
    echo "<br><br>Productos Sincronizando<br>";
    ProductsSync($URLServices,$client);
    
    echo "<br><br>Precios Sincronizando<br>";
    PriceSync($URLServices,$client);
    
    echo "<br><br>Stock Sincronizando<br>";
    StockSync($URLServices,$client);
?>