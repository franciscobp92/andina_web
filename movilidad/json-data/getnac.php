<?php
$url = 'https://elcanaldelfutbol.com/load/scores?type=1&group=0&_=1462104000';
$obj = file_get_contents($url);
$obj=str_replace("var response =","",$obj);

$obj=str_replace('time','"time"',$obj);
$obj=str_replace('scores','"scores"',$obj);
$obj=str_replace('team','"team"',$obj);
$obj=str_replace('type','"type"',$obj);
$obj=str_replace('groupid','"groupid"',$obj);
$obj=str_replace('event','"event"',$obj);
$obj=str_replace('position','"position"',$obj);
$obj=str_replace('dif','"dif"',$obj);
$obj=str_replace('points','"points"',$obj);
$obj=str_replace('pj','"pj"',$obj);
$obj=str_replace('pg','"pg"',$obj);
$obj=str_replace('pe:','"pe":',$obj);
$obj=str_replace('pp','"pp"',$obj);
$obj=str_replace('gf','"gf"',$obj);
$obj=str_replace('gc','"gc"',$obj);
$obj=str_replace('id:','"id":',$obj);
$obj=str_replace('name','"name"',$obj);
echo $obj;
?>
