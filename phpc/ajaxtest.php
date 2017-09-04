<?php


require "config.php";
require "function.php";
require "database.php";


$database=new Database;

//$_GET['date_value']='01.09.2017';

$date = DateTime::createFromFormat('d.m.Y', $_GET['date_value']);
$query = mysql_query("SELECT time FROM tbl_req_SKNO_time WHERE time NOT IN
                                         (SELECT s.time FROM tbl_req_SKNO s WHERE s.date='".$date->format('Y-m-d')."')");

$array = array();
$row = substr(mysql_fetch_array($query)[0],0,5);

if($row!=false){
    $array[1]=$row;
}

while ($row = mysql_fetch_array($query)) {
    $array[] = substr($row["time"],0,5);
}

echo json_encode($array);




//if($_GET['date_value']=='30.08.2017'){
//    $array =  array(1=>'12:00');
//    echo json_encode($array);
//} else {
//
//    $array =  array(1=>'09:00', 2=>'10:00');
//    foreach ($array as $key => $value){
//        echo "{$key} => {$value} ";
//        print_r($arr);
//    }
//    echo json_encode($array);
//}
