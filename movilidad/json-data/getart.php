<?php
  include("conexion.php");
  $almcve="[]";
  if (isset($_GET["almcve"]) && isset($_GET["fecha"])){
    $almcve=$_GET["almcve"];
    $fecha=$_GET["fecha"];
    $sql="select * from talma A
inner join tarti B
on A.almcve=B.almcve
inner join tindi C
on B.artcod=C.indcod
where A.almcve='".$almcve."'";
    $json_data=getArraySQL($sql);

    $sql_horas="select artcod,docfec,docupr from tdoch
where docfec='".$fecha."'
and artcod in(
	select artcod from tarti
	where almcve='".$almcve."');";
    $json_data_horas=getArraySQL($sql_horas);

    $sql_listado="select * from thoras";
    $json_data_listado=getArraySQL($sql_listado);
    $data = array(
    'canchas' => $json_data,
    'horas' => $json_data_horas,
    'listado' => $json_data_listado
    );
    echo json_encode($data);
  }else{
    echo $almcve;
  }

?>
