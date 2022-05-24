<?php
    function ProductsSync($URLServices,$client)
    {
        $conn=OpenConnection();
        inactive_all_products_before_insert($conn);
        
        $result = $client->wsConsultaMateriales_Masivo();
        $data=json_decode(json_encode($result),true);
        foreach ($data as $value) {
            $data= $value;
        }
        $productData = json_decode($data,true);
        $cont=1;
        foreach($productData as $valueProduct){
            foreach($valueProduct as $product){ 
                insertProduct($product,$conn);
                insertProductMeta($conn,$product);
                insertProductRelationships($conn,$product);
                insertPostmeta($conn, $product);
                InsertProductManage($conn,$product);
            }
        }
        
        CloseConnection($conn);
    }



    function ProductsSyncStockTotal($URLServices,$client)
    {
        $conn=OpenConnection();
        $result = $client->wsConsultaMateriales_Masivo();
        $data=json_decode(json_encode($result),true);
        foreach ($data as $value) {
            $data= $value;
        }
        $productData = json_decode($data,true);
        foreach($productData as $valueProduct){
            foreach($valueProduct as $product){ 
                add_meta_stock($conn,$product);
            }
        }
        CloseConnection($conn);
    }

    
    function clean($n_string) {
       //$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
       
       $n_string = str_replace(' ', '', $n_string);
       $n_string = str_replace(' ', '', $n_string);
       $n_string = preg_replace('/\s+/', '', $n_string);
       $n_string = preg_replace('/\s+/', '', $n_string);
    
       $n_string =preg_replace('/[^A-Za-z0-9\-]/', '', $n_string); // Removes special chars.
       
       
       return $n_string;
    }
    
    function insertProduct($product,$conn)
    {
        $desc="";
        $descp="";
        $sql="";
        $option="";
        

        if($product['DEX_DESCRIP_PROD']!= null)
            $desc=str_replace(";",",",$product['DEX_DESCRIP_PROD']);

        if($product['DEX_DESCRIP_CORTA_PROD']!= null)
            $descp=str_replace(";",",",$product['DEX_DESCRIP_CORTA_PROD']);
        
        if(!searchProduct($conn,$product["PRO_CODIGO"]))
        {
            $sql ="INSERT INTO `al_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES ";
            //$sql.="(".$product["PRO_CODIGO"].", 1,'".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', '".$desc.".', '".$product["PRO_NOMBRE"]."', '".$descp."', 'publish', 'open', 'closed', '', '".str_replace(" ","-",strtolower(trim($product["PRO_NOMBRE"]))) ."', '', '', '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', '', 0, 'http://andinalicores.com.ec/?post_type=product&#038;p=" . $product["PRO_CODIGO"]. "', 0, 'product', '', 0)";
            $PRO_NOMBRE=clean($product["PRO_NOMBRE"]);
            $sql.="(".$product["PRO_CODIGO"].", 1,'".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', '".$desc.".', '".$product["PRO_NOMBRE"]."', '".$descp."', 'publish', 'open', 'closed', '', '".str_replace(" ","-",strtolower(trim($PRO_NOMBRE))) ."', '', '', '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', '', 0, 'http://andinalicores.com.ec/?post_type=product&#038;p=" . $product["PRO_CODIGO"]. "', 0, 'product', '', 0)";

          

        
        }
        else
        {
            //$sql ="UPDATE al_posts SET post_status='publish' WHERE ID=".$product["PRO_CODIGO"]."";

            $sql ="UPDATE al_posts SET post_title='".$product["PRO_NOMBRE"]."', post_content='".$desc."', post_title='".$product["PRO_NOMBRE"]."', post_excerpt='".$descp."', post_status='publish', post_type='product' WHERE ID=".$product["PRO_CODIGO"]."";
            // $sql ="UPDATE al_posts SET post_content='".$desc."', post_excerpt='".$descp."' WHERE ID=".$product["PRO_CODIGO"].";";
            //$sql="select * from al_posts where ID=1";
            // echo $sql;
            $option="actualizado producto".$product["PRO_CODIGO"];
        }

        insertDB($conn,$sql, $option);
    }

    function insertProductMeta($conn,$product)
    {
        if(!searchProductMeta($conn,$product["PRO_CODIGO"]))
        {
            $sql="INSERT INTO `al_wc_product_meta_lookup` (`product_id`, `sku`, `virtual`, `downloadable`, `min_price`, `max_price`, `onsale`, `stock_quantity`, `stock_status`, `rating_count`, `average_rating`, `total_sales`, `tax_status`, `tax_class`) VALUES";
            $sql.="('".$product["PRO_CODIGO"]."', '".$product["PRO_ID"]."', '0', '0', 0, 0, '0', NULL, 'instock', '0', '0.00', '0', 'taxable', '')";
            if (mysqli_query($conn, $sql)) {
            error_log( "New record of productMeta in table al_wc_product_meta_lookup created successfully");
            } else {
            error_log( "Mensaje desde la línea: " . __LINE__ . " del archivo " . __FILE__ . "\nError: " . $sql . " : \n" . mysqli_error($conn));
            }
        }
        else{
            error_log("Mensaje desde la línea: " . __LINE__ . " del archivo " . __FILE__ . "\ProductMeta ".$product["PRO_CODIGO"]." already exists");
        }
    
    }

    function insertProductRelationships($conn,$product)
    {
        $type=2; # Simple Product id
        $idCat=$dato=idCategory($conn,$product["SECCION"]);
        $idSubCat=idCategory($conn,$product["SUBSECION"]);
        $data=array(2,$idCat,$idSubCat);
        foreach($data as $id)
        {
            if(!searchProductRelationship($conn,$product["PRO_CODIGO"],$id))
            {
                $sql="INSERT INTO `al_term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`) VALUES ('".$product["PRO_CODIGO"]."', '".$id."', '0')"; 
                
                insertDB($conn,$sql, "New record of productRelationship");
               
            }
            else
            {
             error_log("Mensaje desde la línea: " . __LINE__ . " del archivo " . __FILE__ . "\Product Relationship ".$product["PRO_CODIGO"]." con el tipo ".$id." already exists");
            }
        }
    }

    function searchProductRelationship($conn,$idProduct,$idCategory)
    {
        $bandera=true;
        $sql="SELECT * from al_term_relationships where `object_id`=". $idProduct . " and term_taxonomy_id=". $idCategory;
        $resultado=mysqli_query($conn, $sql) or die (mysql_error());
        if (mysqli_num_rows($resultado)==0)
        {
            $bandera= false;
        }

        return $bandera;

    }

    function searchProduct($conn,$idProduct)
    {
        $bandera=true;
        $sql="SELECT ID from al_posts where ID='".$idProduct."'";
        $resultado=mysqli_query($conn, $sql) or die (mysql_error());
        if (mysqli_num_rows($resultado)==0)
        {
            $bandera= false;
        }

        return $bandera;

    }

    function searchProductMeta($conn,$idProduct)
    {
        $bandera=true;
        $sql="SELECT product_id from al_wc_product_meta_lookup where product_id=".$idProduct;
        $resultado=mysqli_query($conn, $sql) or die (mysql_error());
        if (mysqli_num_rows($resultado)==0)
        {
            $bandera= false;
        }

        return $bandera;

    }
    
    function insertPostmeta($conn, $product)
    {
        $option="";
        $sql="";
        if(!searchPostMeta($conn,$product["PRO_CODIGO"],"_sku")){
            $sql="INSERT INTO `al_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES (".$product["PRO_CODIGO"].",'_sku','". $product["PRO_ID"]."');";
            $option="New record _sku, ";
        }
        else{
            $sql = "UPDATE `al_postmeta` SET `meta_value`=".$product["PRO_ID"] . " where post_id=".$product["PRO_CODIGO"]." and meta_key='_sku';";
            $option="Updated row _sku, ";
        }

        insertDB($conn,$sql, $option);

        if(!searchPostMeta($conn,$product["PRO_CODIGO"],"_uni_medida")){
            $sql="INSERT INTO `al_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES (".$product["PRO_CODIGO"].",'_uni_medida','". $product["CODIGO_UNIMED"]."');";
            $option="New record _uni_medida, ";
        }
        else{
            $sql= "UPDATE `al_postmeta` SET `meta_value`=".$product["CODIGO_UNIMED"] . " where post_id=".$product["PRO_CODIGO"]." and meta_key='_uni_medida';";
            $option="Updated row _uni_medida, ";
        }
        insertDB($conn,$sql, $option);
        if(!searchPostMeta($conn,$product["PRO_CODIGO"],"_pro_impuesto")){
            $sql="INSERT INTO `al_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES (".$product["PRO_CODIGO"].",'_pro_impuesto','". $product["PRO_IMPUESTO"]."');";
            $option="New record _uni_medida, ";
        }
        else{
            $sql = "UPDATE `al_postmeta` SET `meta_value`=".$product["PRO_IMPUESTO"] . " where post_id=".$product["PRO_CODIGO"]." and meta_key='_pro_impuesto';";
            $option="Updated row _uni_medida, ";
        }
        insertDB($conn,$sql, $option);
        
        if (strstr((string)$product["PRO_ID"], (string)500) || strstr((string)$product["PRO_ID"], (string)600) ) {
			$sql="INSERT INTO `al_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES (".$product["PRO_CODIGO"].",'_can_visible_by_guest', 'yes');";
            $option="Insertar visibilidad de producto invitado, ";
            insertDB($conn,$sql, $option);
			//$o = "Visiblidad guest no ya que es un articulo promocion";
		}else{
		    if(!searchPostMeta($conn,$product["PRO_CODIGO"],"_can_visible_by_guest")){
                $sql="INSERT INTO `al_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES (".$product["PRO_CODIGO"].",'_can_visible_by_guest', 'yes');";
                $option="Insertar visibilidad de producto invitado, ";
                insertDB($conn,$sql, $option);
			}
		}
		
		if(!searchPostMeta($conn,$product["PRO_CODIGO"],"_alg_wc_price_by_user_role_regular_price_guest")){
            $sql="INSERT INTO `al_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES (".$product["PRO_CODIGO"].",'_alg_wc_price_by_user_role_regular_price_guest', 0);";
            $option="Insertar visibilidad de producto invitado, ";
                    
        }else{
            $sql = "UPDATE `al_postmeta` SET `meta_value`='0' where post_id=".$product["PRO_CODIGO"]." and meta_key='_alg_wc_price_by_user_role_regular_price_guest';";
            $option="Visibilidad tienda actualizada, ";
        }
        insertDB($conn,$sql, $option);
        
        if(!searchPostMeta($conn,$product["PRO_CODIGO"],"_alg_wc_price_by_user_role_regular_price_vendedor_al")){
            $sql="INSERT INTO `al_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES (".$product["PRO_CODIGO"].",'_alg_wc_price_by_user_role_regular_price_vendedor_al', 0);";
            $option="Insertar visibilidad de producto vendedor, ";
                    
        }else{
            $sql = "UPDATE `al_postmeta` SET `meta_value`='0' where post_id=".$product["PRO_CODIGO"]." and meta_key='_alg_wc_price_by_user_role_regular_price_vendedor_al';";
            $option="Visibilidad tienda actualizada, ";
        }
        insertDB($conn,$sql, $option);
        
    }


    function insertDB($conn,$sql, $option)
    {
        if (mysqli_query($conn, $sql)) {
            error_log("".$option." of insertPostmeta in table al_postmeta created successfully");
        } else {
            error_log( "Mensaje desde la línea: " . __LINE__ . " del archivo " . __FILE__ . "\nError: " . $sql . "<br>" . mysqli_error($conn));
        }
        
    }
    
    function searchPostMeta($conn,$idProduct,$meta)
    {
        $bandera=true;
        $sql="SELECT * from al_postmeta where post_id=".$idProduct." and meta_key='".$meta."'";
        $resultado=mysqli_query($conn, $sql) or die (mysql_error());
        if (mysqli_num_rows($resultado)==0)
        {
            $bandera= false;
        }

        return $bandera;

    }
    
 
    function InsertProductManage($conn,$product)
    {
        $sqlManage="";
        $option="";
        if(!searchManageProduct($conn,$product))
        {
           $sqlManage="INSERT INTO al_postmeta (post_id, meta_key, meta_value) VALUES ('".$product["PRO_CODIGO"]."', '_manage_stock', 'yes')";
           $option="New record";
           
        }
        else
        {
          $sqlManage="UPDATE al_postmeta SET meta_value='yes' WHERE post_id='".$product["PRO_CODIGO"]."' and meta_key='_manage_stock'";
          $option="Updated row";
          
        }
        
        insertDB($conn,$sqlManage, $option);
    }
    
    
    function searchManageProduct($conn,$product)
    {
        $bandera=true;
        $sql="SELECT * FROM al_postmeta WHERE meta_key LIKE '_manage_stock' and post_id=".$product["PRO_CODIGO"] ;
        $resultado=mysqli_query($conn, $sql) or die (mysql_error());
        if (mysqli_num_rows($resultado)==0){
          $bandera= false;
        }
        return $bandera;
    }

    function add_meta_stock($conn,$product)
    {
        $sqlManage="";
        $option="";

        $cant=get_sum_stock($conn,$product);
        // echo "codigo:   ".$product["PRO_CODIGO"]. "<br>cantidad: ".$cant;
        if(!search_meta_stock($conn,$product))
        {
           $sqlManage="INSERT INTO al_postmeta (post_id, meta_key, meta_value) VALUES ('".$product["PRO_CODIGO"]."', '_stock', '".$cant."')";
           $option="New record";
           
        }
        else
        {
          $sqlManage="UPDATE al_postmeta SET meta_value='".$cant."' WHERE post_id='".$product["PRO_CODIGO"]."' and meta_key='_stock'";
          $option="Updated row";
          
        }
        
        insertDB($conn,$sqlManage, $option);
    }
    
    function get_sum_stock($conn,$product)
    {
      $sum=0;
      $sql="SELECT SUM(meta_value) suma FROM al_postmeta WHERE meta_key LIKE '%_stock_%' AND post_id =".$product["PRO_CODIGO"];
      $result = $conn->query($sql);
      if ($result->num_rows > 0) 
      {
        while($row = $result->fetch_assoc()) {
          $sum=$row["suma"];
        }
      }

    //   echo $sql;
      return $sum;
    }

    function inactive_all_products_before_insert($conn)
    {
        // $status = "inherit";
        $status = "private";
        $postType = "product";
        $sql = "UPDATE al_posts SET post_status='".$status."' WHERE `post_type` LIKE 'product'";   
        $option="Inactivar productos";
        insertDB($conn,$sql, $option);
    }

    function search_meta_stock($conn,$product)
    {
      $bandera=true;
      $sql="SELECT * FROM al_postmeta WHERE meta_key ='_stock' AND  post_id=".$product['PRO_CODIGO'];
      $resultado=mysqli_query($conn, $sql) or die (mysql_error());
      if (mysqli_num_rows($resultado)==0){
        $bandera= false;
      }
      
      return $bandera;
    }
?>