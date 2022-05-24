<?php
//require("phpsqlajax_dbinfo.php");

$agente=$_GET["id"];
$fecha=$_GET["fecha"];

function parseToXML($htmlStr)
{
$xmlStr=str_replace('<','&lt;',$htmlStr);
$xmlStr=str_replace('>','&gt;',$xmlStr);
$xmlStr=str_replace('"','&quot;',$xmlStr);
$xmlStr=str_replace("'",'&#39;',$xmlStr);
$xmlStr=str_replace("&",'&amp;',$xmlStr);
return $xmlStr;
}

include ("conexion.php");


// Select all the rows in the markers table
//$query = "SELECT * FROM `t_ultima_conexion` INNER JOIN `t_mov` ON `t_ultima_conexion`.`USUARIO`=`t_mov`.`movusr` AND `t_ultima_conexion`.`HORA`=`t_mov`.`movhor` AND `t_ultima_conexion`.`FECHA`=`t_mov`.`movfec` ";
$client = new SoapClient("http://179.49.47.4/WebServiceCO.asmx?WSDL");
$result = $client->json_GA_GENERICO(array('V_BANDERA'=>'34', 'V_PR1'=>$fecha, 'V_PR2'=>$agente));

$json = $result->json_GA_GENERICOResult;
$stdJson=json_decode($json);
//var_dump($query);
header("Content-type: text/xml");

// Start XML file, echo parent node
echo '<markers>';

foreach( $stdJson  as $r){
	foreach( $r  as $s){
		echo '<marker ';
		  echo 'name="' . $s->GESTION." ".$s->DOCUMENTO . '" ';
		  echo 'address="' . $s->CLI_NOMBRE . '" ';
		  echo 'lat="' . $s->LATITUD . '" ';
		  echo 'lng="' . $s->LONGITUD . '" ';
		  echo 'type="' . $s->DOCUMENTO . '" ';
		  echo '/>';
		}
	}

// End XML file
echo '</markers>';

?>
