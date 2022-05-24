<?php

      function PriceSync($URLServices,$client)

      {

        $conn=OpenConnection();

        $result = $client->wsConsultaPrecios_Masivo();

        $data=json_decode(json_encode($result),true);

        foreach ($data as $value) {

          $data= $value;

        }

        $productData = json_decode($data,true);

        

        foreach($productData as $valuePrice){

          foreach($valuePrice as $price){ 

            activateMultiPrice($conn,$price);

            InsertOrUpdateMultiPrice($conn,$price);

            InsertOrUpdatePrice($conn,$price);

          }

        }

        CloseConnection($conn);

      }





      function InsertOrUpdatePrice($conn,$price)

      {

        $desc="";

        if($price["DESCTO"] != "")

        {

            $desc=$price["DESCTO"];

        }



        if(!searchProductPrice($conn,$price))

        {

          $sql="INSERT INTO `al_product_price` (`pp_lista`, `product_id`, `pp_price`, `pp_desc`) VALUES ";

          $sql.="(".$price["V_LISTAPRE"].",".$price["V_PRODUCTO"].",".$price["V_PRECIO"].",".$desc.")";

          if (mysqli_query($conn, $sql)) {

            error_log( "New record of price product in table al_product_price created successfully");

          } else {

            error_log( "Mensaje desde la línea: " . __LINE__ . " del archivo " . __FILE__ . "\nError: " . $sql . "<br>" . mysqli_error($conn));

          }

        }

        else

        {

          $sql="UPDATE al_product_price set pp_price=".$price["V_PRECIO"].", pp_desc=".$desc." where pp_lista=".$price["V_LISTAPRE"]." and product_id=". $price["V_PRODUCTO"] ;

          if (mysqli_query($conn, $sql)) {

            error_log( "Updated record of price of product in table al_product_price successfully");

          } else {

            error_log( "Mensaje desde la línea: " . __LINE__ . " del archivo " . __FILE__ . "\nError: " . $sql . "<br>" . mysqli_error($conn));

          }

        }

      }



      function searchProductPrice($conn,$price)

      {

        $bandera=true;

        $sql="SELECT * from al_product_price where pp_lista=".$price["V_LISTAPRE"]." and product_id=".$price["V_PRODUCTO"] ;

        $resultado=mysqli_query($conn, $sql) or die (mysql_error());

        if (mysqli_num_rows($resultado)==0){

          $bandera= false;

        }

        return $bandera;

      }



      function activateMultiPrice($conn, $price)

      {

            $sql="";

            $option="";

            if(!isExistMultiPrice($conn,$price)){

              $sql="INSERT INTO al_postmeta (`post_id`, `meta_key`, `meta_value`) VALUES (".$price["V_PRODUCTO"].", '_alg_wc_price_by_user_role_per_product_settings_enabled', 'yes')";

              $option="New record created in al_postmeta ";

            }

            else{

              if(!isActiveMultiPrice($conn,$price)){

                  $sql="UPDATE  al_postmeta set meta_value='yes' where post_id=".$price["V_PRODUCTO"]." and meta_key= '_alg_wc_price_by_user_role_per_product_settings_enabled'";

                  $option="Update al_postmeta, price by user role activated";

              }

              else{

                  $sql="select * from al_postmeta where meta_id=1";

              }

            }

            

            if (mysqli_query($conn, $sql)) {

                error_log( $option);

            } else {

                error_log( "Mensaje desde la línea: " . __LINE__ . " del archivo " . __FILE__ . "\nError: " . $sql . "<br>" . mysqli_error($conn));

            }

      }

      

  function InsertOrUpdateMultiPrice($conn, $price){
    $sql="";
    $option="";

    $desc="";
    if($price["DESCTO"] != null)
      $desc=$price["DESCTO"];

    if(!searchProductPriceRegularPostmeta($conn,$price)){
      $sql="INSERT INTO al_postmeta (`post_id`, `meta_key`, `meta_value`) VALUES (".$price["V_PRODUCTO"].",'_alg_wc_price_by_user_role_regular_price_lista_".$price["V_LISTAPRE"]."','".$price["V_PRECIO"]."')";
      $option="New record created in al_postmeta, price for rol lista_".$price["V_LISTAPRE"]."added";
    }
    else{
      $sql="UPDATE  al_postmeta set meta_value='".$price["V_PRECIO"]."' where post_id=".$price["V_PRODUCTO"]." and meta_key= '_alg_wc_price_by_user_role_regular_price_lista_".$price["V_LISTAPRE"]."'";
      $option="Updated al_postmeta, price by user role ";
    }

    insertDB($conn,$sql, $option);


    if(!searchProductDescPostMeta($conn,$price)){
      $sql="INSERT INTO al_postmeta (`post_id`, `meta_key`, `meta_value`) VALUES (".$price["V_PRODUCTO"].",'_desc_price_lista_".$price["V_LISTAPRE"]."','".$desc."')";
      $option="New record created in al_postmeta, price for rol lista_".$price["V_LISTAPRE"]."added";
      insertDB($conn,$sql, $option);
      
        $sql="INSERT INTO al_postmeta (`post_id`, `meta_key`, `meta_value`) VALUES (".$price["V_PRODUCTO"].",'_alg_wc_price_by_user_role_sale_price_lista_".$price["V_LISTAPRE"]."','".$desc."')";
        $option="New record created in al_postmeta, price for rol lista_".$price["V_LISTAPRE"]."added";
    
        insertDB($conn,$sql, $option);    
    }
    else{
      $sql="UPDATE  al_postmeta set meta_value='".$desc."' where post_id=".$price["V_PRODUCTO"]." and meta_key= '_desc_price_lista_".$price["V_LISTAPRE"]."'";
      $option="Updated al_postmeta, desc by user role ";
     insertDB($conn,$sql, $option);
    
      $sql="UPDATE  al_postmeta set meta_value='".$desc."' where post_id=".$price["V_PRODUCTO"]." and meta_key= '_alg_wc_price_by_user_role_sale_price_lista_".$price["V_LISTAPRE"]."'";
      $option="Updated al_postmeta, desc by user role ";
    insertDB($conn,$sql, $option);
    }
    // echo $sql;

    if(!searchProductPriceEmptyPostmeta($conn,$price)){
      $sql="INSERT INTO al_postmeta (`post_id`, `meta_key`, `meta_value`) VALUES (".$price["V_PRODUCTO"].",'_alg_wc_price_by_user_role_empty_price_lista_".$price["V_LISTAPRE"]."','no')";
      $option="New record created in al_postmeta, price for rol empty price lista_".$price["V_LISTAPRE"]."added";
    }
    else{
      $sql="select * from al_postmeta where post_id=1";
    }

    insertDB($conn,$sql, $option);
  }

      

  function searchProductPriceRegularPostmeta($conn,$price){
    $bandera=true;
    $sql="SELECT * from al_postmeta where post_id=".$price["V_PRODUCTO"]." and meta_key='_alg_wc_price_by_user_role_regular_price_lista_".$price["V_LISTAPRE"]."'" ;
    $resultado=mysqli_query($conn, $sql) or die (mysql_error());

    if (mysqli_num_rows($resultado)==0){
      $bandera= false;
    }
    return $bandera;
  }

  function searchProductDescPostMeta($conn,$price){
    $bandera=true;
    $sql="SELECT * from al_postmeta where post_id=".$price["V_PRODUCTO"]." and meta_key='_desc_price_lista_".$price["V_LISTAPRE"]."'" ;
    $resultado=mysqli_query($conn, $sql) or die (mysql_error());
    
    if (mysqli_num_rows($resultado)==0){
      $bandera= false;
    }
    return $bandera;
  }

      

      function searchProductPriceEmptyPostmeta($conn,$price)

      {

        $bandera=true;

        $sql="SELECT * from al_postmeta where post_id=".$price["V_PRODUCTO"]." and meta_key='_alg_wc_price_by_user_role_empty_price_lista_".$price["V_LISTAPRE"]."'" ;

        $resultado=mysqli_query($conn, $sql) or die (mysql_error());

        if (mysqli_num_rows($resultado)==0){

          $bandera= false;

        }

        return $bandera;

      }

      

      function isActiveMultiPrice($conn,$price)

      {

        $bandera=true;

        $sql="SELECT * from al_postmeta where post_id=".$price["V_PRODUCTO"]." and meta_key='_alg_wc_price_by_user_role_per_product_settings_enabled' and meta_value='yes'";

        $resultado=mysqli_query($conn, $sql) or die (mysql_error());

        if (mysqli_num_rows($resultado)==0){

          $bandera= false;

        }

        return $bandera;

      }

      

      function isExistMultiPrice($conn,$price)

      {

        $bandera=true;

        $sql="SELECT * from al_postmeta where post_id=".$price["V_PRODUCTO"]." and meta_key='_alg_wc_price_by_user_role_per_product_settings_enabled'";

        $resultado=mysqli_query($conn, $sql) or die (mysql_error());

        if (mysqli_num_rows($resultado)==0){

          $bandera= false;

        }

        return $bandera;

      }





      function read_txt_image()

      {

        $conn=OpenConnection();

        $arrayIds=[];

        $file = fopen("lista.txt", "r") or exit("Error abriendo fichero!");

        while($linea = fgets($file)) {

            if (feof($file)) break;



            $cadena=explode(".",$linea);

            array_push( $arrayIds,$cadena[0]);

        }

        fclose($file);





        foreach($arrayIds as $codigo){

          AddImageProduct($conn,$codigo);

        }

        CloseConnection($conn);

      }



      function AddImageProduct($conn,$codigo)

      {

        $sql="";

        $option="";



        $id=getImagePostId($conn,$codigo);

        if($id != 0)

        {

          if(!searchProductImagePostmeta($conn,$codigo)){

            $sql="INSERT INTO al_postmeta (`post_id`, `meta_key`, `meta_value`) VALUES (".$codigo.",'_thumbnail_id',".$id.")";

            $option="New record created in al_postmeta, Image Asigned to product by Id=".$codigo;

          }

          else{

            $sql="UPDATE al_postmeta SET meta_value=".$id." where post_id=".$codigo." and meta_key='_thumbnail_id'";

            $option="Updated Image code to product by Id=".$codigo;

          }

  

          if (mysqli_query($conn, $sql)) {

              error_log( $option);

          } else {

              error_log( "Mensaje desde la línea: " . __LINE__ . " del archivo " . __FILE__ . "\nError: " . $sql . "<br>" . mysqli_error($conn));

          }

        }

        

      }



      function searchProductImagePostmeta($conn,$codigo)

      {

        $bandera=true;

        $sql="SELECT * from al_postmeta where post_id=".$codigo." and meta_key='_thumbnail_id'" ;

        $resultado=mysqli_query($conn, $sql) or die (mysql_error());

        if (mysqli_num_rows($resultado)==0){

          $bandera= false;

        }

        return $bandera;

      }





      function getImagePostId($conn,$codigo)

      {

        $id=0;

        $sql="SELECT * from al_posts where post_title=".$codigo." and post_type='attachment'" ;

        $resultado=mysqli_query($conn, $sql) or die (mysql_error());



        if ($resultado->num_rows > 0) {

          // output data of each row

          while($row = $resultado->fetch_assoc()) {

            $id=$row["ID"];

          }

        } 

        return $id;

      }

?>