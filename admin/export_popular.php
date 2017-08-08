<?
#Скрипт экспорта гостей
require "global.php";

$type = $_REQUEST['type'];

if($type=='product'){
	$query = mysql_query("SELECT name2 AS name_item, count_open FROM tbl_products ORDER BY count_open DESC, name2 ASC");	
	}
elseif($type=='catalog'){
	$query = mysql_query("SELECT name AS name_item, count_open FROM tbl_catalog ORDER BY count_open DESC, name ASC");	
	}

header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename=guest.xls"); 
header("Content-Transfer-Encoding: binary ");

function xlsBOF() {
echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0); 
return;
}

function xlsEOF() {
echo pack("ss", 0x0A, 0x00);
return;
}

function xlsWriteNumber($Row, $Col, $Value) {
echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
echo pack("d", $Value);
return;
}

function xlsWriteLabel($Row, $Col, $Value ) {
$L = strlen($Value);
echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
echo $Value;
return;
}

xlsBOF(); //начинаем собирать файл

xlsWriteLabel(0,0,'Название');
xlsWriteLabel(0,1,'Количество');

$i=1;
while($result=mysql_fetch_array($query)){

	xlsWriteLabel($i,0,$result['name_item']);	
	xlsWriteLabel($i,1,$result['count_open']);
	$i++;
	}


xlsEOF(); //заканчиваем собирать
		

?>