<?php

    function CategoriesSync($URLServices,$client)
    {
    
        $result = $client->wsConsultaMateriales_Masivo();
        $data=json_decode(json_encode($result),true);
        foreach ($data as $value) {
            $data= $value;
        }
        $productData = json_decode($data,true);
        
        $ArrayCategory=[];
        $ArraySubcategory=[];
        
        foreach($productData as $valueProduct){
            foreach($valueProduct as $product){ 
            array_push ( $ArrayCategory , $product["SECCION"] );
            array_push ( $ArraySubcategory ,$product["SECCION"] .",". $product["SUBSECION"] );
            }
        }

        #parameter true, significa si es Seccion y false si es subseccion
        insertCategory(array_unique($ArrayCategory),true);
        insertCategoryTaxonomy(array_unique($ArrayCategory),true);
        insertCategory(array_unique($ArraySubcategory),false);
        insertCategoryTaxonomy(array_unique($ArraySubcategory),false);
    }


    function insertCategory($CategoryList,$isCategory)
    {
        $conn=OpenConnection();
        $dato="";
        foreach($CategoryList as $category)
        {
            if($isCategory){
            $dato=$category;
            }
            else{
            $data=explode(",", $category);
            $dato=$data[1];
            }

            if(!searchCategory($conn,$dato))
            {
            if($isCategory)
            {
                $sql ="INSERT INTO al_terms (name,slug,term_group) VALUES ('" . $category . "','" . str_replace(" ","-",strtolower(trim($category))) . "','0')";
            }
            else{
                $sbData=explode(",", $category);
                $sql ="INSERT INTO al_terms (name,slug,term_group) VALUES ('" . $sbData[1] . "','" . str_replace(" ","-",strtolower(trim($sbData[1]))) . "','0')";
                // error_log( $sql);
            }
            
            if (mysqli_query($conn, $sql)) {
                error_log( "New record of category created successfully");
            } else {
                error_log( "Mensaje desde la línea: " . __LINE__ . " del archivo " . __FILE__ . "\nError: " . $sql . "<br>" . mysqli_error($conn));
            }
            
            }
            else
            {
            error_log("Mensaje desde la línea: " . __LINE__ . " del archivo " . __FILE__ . "\nCategory $category already exists");
            }
            
        }
        CloseConnection($conn);
    
    }

    function insertCategoryTaxonomy($CategoryList,$isParent)
    {
        $conn=OpenConnection();
        $dato=0;
        $idCategory=0;
        foreach($CategoryList as $category)
        {
            if($isParent){
            $dato=idCategory($conn,$category);
            }
            else{
            $data=explode(",", $category);
            $dato=idCategory($conn,$data[1]);
            }
            
            if(!searchCategoryTaxonomy($conn,$dato))
            {
            if($isParent)
                $sql ="INSERT INTO al_term_taxonomy (term_id, taxonomy, description, parent, count) VALUES ('".$dato."', 'product_cat', '', '0', '0')";
            else
            {
                $sbData=explode(",", $category);
                $parentId=idCategory($conn,$sbData[0]);
                $sql ="INSERT INTO al_term_taxonomy (term_id, taxonomy, description, parent, count) VALUES ('".$dato."', 'product_cat', '', '".$parentId."', '0')";
            }
                

            if (mysqli_query($conn, $sql)) {
                error_log( "New record of category created successfully on al_term_taxonomy");
            } else {
                error_log( "Mensaje desde la línea: " . __LINE__ . " del archivo " . __FILE__ . "\nError on insert al_term_taxonomy row: " . $sql . "<br>" . mysqli_error($conn));
            }
            
            }
            else
            {
            error_log("Mensaje desde la línea: " . __LINE__ . " del archivo " . __FILE__ . "\nCategory taxonomy $category already exists");
            }
            
        }
        CloseConnection($conn);
    
    }


    function searchCategory($conn,$category)
    {
        $bandera=true;
        $sql="SELECT term_id from al_terms where name='".$category."'";
        $resultado=mysqli_query($conn, $sql) or die (mysql_error());
        if (mysqli_num_rows($resultado)==0)
        {
            $bandera= false;
        }

        return $bandera;

    }

    function searchCategoryTaxonomy($conn,$idCategory)
    {
        $bandera=true;
        $sql="SELECT term_taxonomy_id from al_term_taxonomy where term_id='".$idCategory."'";
        $resultado=mysqli_query($conn, $sql) or die (mysql_error());
        if (mysqli_num_rows($resultado)==0)
        {
            $bandera= false;
        }

        return $bandera;

    }

    function idCategory($conn,$category)
    {
        $data=0;
        $sql="SELECT term_id from al_terms where name='".$category."'";
        $resultado=mysqli_query($conn, $sql) or die (mysql_error());
        while($row = mysqli_fetch_assoc($resultado)) {
            $data=$row["term_id"];
        }
        return $data;
    }
?>