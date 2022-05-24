<?php
  function connectDB(){
     $conexion = mysqli_connect("mundoweb.com.ec", "mundoweb", "webmundo", "mundoweb_tucanchinta");
      if($conexion){
          //echo 'La conexión de la base de datos se ha hecho satisfactoriamente';
      }else{
          //echo 'Ha sucedido un error inesperado en la conexión de la base de datos';
      }
      return $conexion;
  }

  function disconnectDB($conexion){
      $close = mysqli_close($conexion);
      if($close){
          //echo 'La desconexión de la base de datos se ha hecho satisfactoriamente';
      }else{
          //echo 'Ha sucedido un error inesperado en la desconexión de la base de datos';
      }
      return $close;
  }

  function getArraySQL($sql){
      //Creamos la conexión con la función anterior
      $conexion = connectDB();
      //generamos la consulta
          //mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

      if(!$result = mysqli_query($conexion, $sql)) die(); //si la conexión cancelar programa
      $rawdata = array(); //creamos un array
      //guardamos en un array multidimensional todos los datos de la consulta
      $i=0;
      while($row = mysqli_fetch_assoc($result))
      {
          $rawdata[$i] = $row;
          $i++;
      }

      disconnectDB($conexion); //desconectamos la base de datos
      return $rawdata; //devolvemos el array
  }
?>
