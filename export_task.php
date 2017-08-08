<?
#Скрипт экспорта гостей
include("phpc/config.php");

$db_connection = mysql_connect(DatabaseHost, DatabaseUser, DatabasePass);
mysql_select_db(DatabaseName, $db_connection);
mysql_query("SET NAMES cp1251");

$period = $_REQUEST['period'];
$period = @base64_decode($period);

if(!empty($period)){
	$period = split(' - ', $period);
	$date1 = date("Y-m-d 00:00:01", strtotime($period[0]));
	$date2 = date("Y-m-d 23:59:59", strtotime($period[1]));

	$query = mysql_query("SELECT * FROM tbl_user WHERE date_register>='" .$date1. "' AND date_register<='" .$date2. "' AND is_exported='0' ORDER BY date_register DESC");
	//print "SELECT * FROM tbl_user WHERE date_register>='" .$date1. "' AND date_register<='" .$date2. "' AND is_exported='0' ORDER BY date_register DESC";
	}
else{
	$query = mysql_query("SELECT * FROM tbl_user WHERE is_exported='0' ORDER BY date_register DESC");
	}



$i=1;
while($result=mysql_fetch_array($query)){
	print $result['name'].'%%%';
	print $result['unn'].'%%%';
	print $result['company'].'%%%';
	print $result['email'].'%%%';
	print $result['phone'].'%%%';
	print $result['city'].'%%%';
	print $result['interes'].'%%%';
	print $result['date_register'].'%%%';
	print '!!!';	
	$i++;
	$query2 = mysql_query("UPDATE tbl_user SET is_exported='1' WHERE id='" .$result['id']. "'");
	}


?>