<?php
  include("conexion.php");
  echo json_encode(getArraySQL("SELECT * FROM `tindi`"));
?>
