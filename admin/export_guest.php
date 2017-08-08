<?
#Скрипт экспорта гостей
require "global.php";

$period = $_GET['period'];

if(!empty($period)){
	$period = split(' - ', $period);
	$date1 = date("Y-m-d", strtotime($period[0]));
	$date2 = date("Y-m-d", strtotime($period[1]));

	$query = mysql_query("SELECT * FROM tbl_guest WHERE date>='" .$date1. "' AND date<='" .$date2. "' ORDER BY date DESC");
	}
else{
	$query = mysql_query("SELECT * FROM tbl_guest ORDER BY date DESC");
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

xlsWriteLabel(0,0,'Комания');
xlsWriteLabel(0,1,'Имя');
xlsWriteLabel(0,2,'Город');
xlsWriteLabel(0,3,'Телефон');
xlsWriteLabel(0,4,'E-mail');
xlsWriteLabel(0,5,'Что хотел');
xlsWriteLabel(0,6,'Дата');


$i=1;
while($result=mysql_fetch_array($query)){
	
	xlsWriteLabel($i,0,$result['name']);		
	xlsWriteLabel($i,1,$result['contact']);
	xlsWriteLabel($i,2,$result['city']);	
	xlsWriteLabel($i,3,$result['phone']);	
	xlsWriteLabel($i,4,$result['mail']);
	xlsWriteLabel($i,5,$result['interes']);	
	xlsWriteLabel($i,6,$result['date']);	
	$i++;
	}


xlsEOF(); //заканчиваем собирать
		






?>
