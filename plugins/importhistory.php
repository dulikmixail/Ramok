<?

include("../phpc/config.php");

$db_connection = mysql_connect(DatabaseHost, DatabaseUser, DatabasePass);
mysql_select_db(DatabaseName, $db_connection);
mysql_query("SET NAMES cp1251");


$handle = fopen('http://mail.ramok.by:1992/Kasbi/xml/new_history.xml', "rb");
if($handle){
	$contents = '';
	while (!feof($handle)) {
	  $page .= fread($handle, 8192);
	}
}
else{
	print 'Ошибочка';
	exit();
	}
fclose($handle);

$date_add = date("Y-m-d");

$arr_customers = explode('/>', $page);

#$query = mysql_query("TRUNCATE TABLE tbl_dolg");


$i=0;
while (list($key, $val) = each($arr_customers)){
	$arr_val = explode('"', $val);
	$sys_id=trim($arr_val[1]);
	$state=trim($arr_val[27]);
	$date_to=$arr_val[33];
	$date_to = str_replace('T00:00:00','',$date_to);
	$num_cushregister=trim($arr_val[41]);
	$unn=trim($arr_val[43]);
	$dogovor=trim($arr_val[45]);
	$akt=trim($arr_val[51]);
	$price=trim($arr_val[53]);
	
	if($state==1){
		$query = mysql_query("INSERT INTO tbl_cash_history SET 
			sys_id='" .$sys_id. "',
			state='" .$state. "',
			date_to='" .$date_to. "',
			num_cushregister='" .$num_cushregister. "',
			unn='" .$unn. "',
			dogovor='" .$dogovor. "',
			akt='" .$akt. "',
			price='" .$price. "'			
		");
		print $sys_id.'-' .$state. '-' .$date_to. '-' .$num_cushregister. '-' .$unn. '-' .$dogovor. '-' .$akt. '-' .$price. '<br>';
		}
	}





?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name='yandex-verification' content='5abaf2682bba4994' />
<title>Компания Рамок</title>
<meta http-equiv="content-type" content="text/html; charset=windows-1251" />
</head>
<body>



</body>
</html>
