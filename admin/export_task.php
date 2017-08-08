<?
#Скрипт экспорта гостей
require "global.php";

$period = $_GET['period'];

if(!empty($period)){
	$period = split(' - ', $period);
	$date1 = date("Y-m-d 00:00:01", strtotime($period[0]));
	$date2 = date("Y-m-d 23:59:59", strtotime($period[1]));

	$query = mysql_query("SELECT * FROM tbl_user WHERE date_register>='" .$date1. "' AND date_register<='" .$date2. "' ORDER BY date_register DESC");
	}
else{
	$query = mysql_query("SELECT * FROM tbl_user ORDER BY date_register DESC");
	}
	

$i=1;
while($result=mysql_fetch_array($query)){
	print $result['name'].'%%%';
	print $result['company'].'%%%';
	print $result['email'].'%%%';
	print $result['phone'].'%%%';
	print $result['city'].'%%%';
	print $result['interes'].'%%%';
	print $result['date_register'].'%%%';
	print '!!!';	
	$i++;
	}


?>