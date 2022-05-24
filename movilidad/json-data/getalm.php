<?php
  include("conexion.php");
  //echo date("H:i", strtotime("04:25 AM"));
  if (isset($_POST["latitud"]) && isset($_POST["longitud"]) && isset($_POST["fecha"]) && isset($_POST["hora"]) && isset($_POST["filtro"])){
    $fecha=$_POST["fecha"];
    $hora=date("H:i", strtotime($_POST["hora"]));
    $filtro=$_POST["filtro"];
    $latitud=$_POST["latitud"];
    $longitud=$_POST["longitud"];
    if ($fecha=='' || $hora=='' || $filtro==''){
      $sql_json="SELECT *,0 as distancia
      FROM `talma`";
    }else{
      $sql_json="SELECT *,(acos(sin(radians($latitud)) * sin(radians(almlat)) +
      cos(radians($latitud)) * cos(radians(almlat)) *
      cos(radians($longitud) - radians(almlon))) * 6378) as
      distancia
      FROM `talma`
      where almcve in(select almcve from tarti
						where artcod not in(select artcod from tdoch
          								where docfec='".$fecha."'
          								and docupr='".$hora."'))";
    }
    $json_data=json_encode(getArraySQL($sql_json),JSON_HEX_QUOT);
    echo $json_data;
  }
  /*$encriptada=crypt("contraseña","S@MP3R");

  if($encriptada==crypt("contraseña","SSSS@MP3R")){
    echo $encriptada;
  }
  else{
    echo "bad ".$encriptada." ".crypt("contraseña","SSSS@MP3R") ;
  }*/
?>
