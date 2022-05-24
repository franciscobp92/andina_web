<?php

  function StockSync($URLServices,$client){
    $conn=OpenConnection();
    $result = $client->wsConsultaStock_Masivo();
    $data=json_decode(json_encode($result),true);
    foreach ($data as $value) {
      $data= $value;
    }
    $productData = json_decode($data,true);
    foreach($productData as $valueStock){
      foreach($valueStock as $stock){ 
        InsertOrUpdateStock($conn,$stock);
        InsertOrUpdateLocations($conn,$stock);
      }
    }
    CloseConnection($conn);
  }



  function InsertOrUpdateStock($conn,$stock){
    if(!searchProductStock($conn,$stock)){
      $sql="INSERT INTO al_product_stock (`bg_id`, `product_id`, `ps_stock`, `ps_unidad`) VALUES ";
      $sql.="(".$stock["DMO_BODEGA"].",".$stock["DMO_PRODUCTO"].",".$stock["STOCK"].",".$stock["PRO_UNIDAD"].")";
      if (mysqli_query($conn, $sql)) {
        error_log( "New record of stock product in table al_product_stock created successfully");
      } 
      else {
        error_log( "Mensaje desde la línea: " . __LINE__ . " del archivo " . __FILE__ . "\nError: " . $sql . "<br>" . mysqli_error($conn));
      }
    } 
    else{
      $sql="UPDATE al_product_stock set ps_stock=".$stock["STOCK"].", ps_unidad=".$stock["PRO_UNIDAD"]." where bg_id=".$stock["DMO_BODEGA"]." and product_id=". $stock["DMO_PRODUCTO"] ;
      if (mysqli_query($conn, $sql)) {
        error_log( "Updated record of stock of product in table al_product_stock successfully");
      } 
      else {
        error_log( "Mensaje desde la línea: " . __LINE__ . " del archivo " . __FILE__ . "\nError: " . $sql . "<br>" . mysqli_error($conn));
      }
    }
  }



function searchProductStock($conn,$stock){
  $bandera=true;
  $sql="SELECT * FROM al_product_stock where bg_id=".$stock["DMO_BODEGA"]." and product_id=".$stock["DMO_PRODUCTO"] ;
  $resultado=mysqli_query($conn, $sql) or die (mysql_error());
  if (mysqli_num_rows($resultado)==0){
    $bandera= false;
  }
  return $bandera;
}

      

        

  function InsertOrUpdateLocations($conn,$stock){
    $id=get_locationId($conn,$stock);
    $sql="";
    $option="";
    if($id != 0){
      if(!search_asigned_location($conn,$stock,$id)){
        $sql="INSERT INTO al_term_relationships (object_id, term_taxonomy_id, term_order) VALUES ('".$stock["DMO_PRODUCTO"]."', '".$id."', '0')";
        $option="row Created";
        insertDB($conn,$sql, $option);
      }

      if(!search_stock_product_location($conn,$stock,$id)){
        $sql="INSERT INTO al_postmeta (post_id, meta_key, meta_value) VALUES ('".$stock["DMO_PRODUCTO"]."', '_stock_at_".$id."','".$stock["STOCK"]."')";
        $option="row Created";
      }
      else{
        $sql="UPDATE al_postmeta SET meta_value='".$stock["STOCK"]."' WHERE meta_key='_stock_at_".$id."' AND post_id='".$stock["DMO_PRODUCTO"]."'";
            var_dump($sql);

        $option="row Updated";
      }
      insertDB($conn,$sql, $option);
    }
    else{
      error_log( "location unasigned");
    }
  }

        

  function get_locationId($conn,$stock){
    $id=0;
    $sql="SELECT * FROM al_terms t,al_bodega b WHERE t.name=b.bg_name and b.bg_id=".$stock["DMO_BODEGA"];
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $id=$row["term_id"];
      } 
    }
    return $id;
  }


  function search_stock_product_location($conn,$stock,$id){
    $bandera=true;
    $sql="SELECT * FROM al_postmeta where meta_key='_stock_at_".$id."' and post_id=".$stock["DMO_PRODUCTO"] ;
    $resultado=mysqli_query($conn, $sql) or die (mysql_error());
    if (mysqli_num_rows($resultado)==0){
      $bandera= false;
    }
    return $bandera;
  }



  function search_asigned_location($conn,$stock,$id){
    $bandera=true;
    $sql="SELECT * FROM al_term_relationships WHERE object_id='".$stock["DMO_PRODUCTO"]."' AND term_taxonomy_id='".$id."'";
    $resultado=mysqli_query($conn, $sql) or die (mysql_error());
    if (mysqli_num_rows($resultado)==0){
      $bandera= false;
    }
    return $bandera;
  }



?>