<?

include("../phpc/config.php");

$db_connection = mysql_connect(DatabaseHost, DatabaseUser, DatabasePass);
mysql_select_db(DatabaseName, $db_connection);
mysql_query("SET NAMES cp1251");


$handle = fopen('http://mail.ramok.by:1992/kasbi/xml/Import.xml', "rb");
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



$arr_customers = explode('
', $page);

$query = mysql_query("TRUNCATE TABLE tbl_dolg");


while (list($key, $val) = each($arr_customers)){
	$arr_val = explode('"', $val);
	$unn=$arr_val[1];
	$name=$arr_val[3];
	$amount=$arr_val[5];
	#$name = @iconv("UTF-8", "windows-1251", $name);	
	$name = str_replace('&quot;', '"', $name);
	if($unn>100){
		$query = mysql_query("INSERT INTO tbl_dolg SET unn='" .$unn. "' , name='" .$name. "' , amount='" .$amount. "'");	
		$i++;
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

Экспортировано записей о долгах: <strong><?=$i;?></strong>

</body>
</html>
