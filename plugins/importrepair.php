<?

include("../phpc/config.php");

$db_connection = mysql_connect(DatabaseHost, DatabaseUser, DatabasePass);
mysql_select_db(DatabaseName, $db_connection);
mysql_query("SET NAMES cp1251");

$handle = fopen('http://mail.ramok.by:1992/kasbi/xml/new_repair.csv', "rb");
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

#$query = mysql_query("TRUNCATE TABLE tbl_dolg");

while (list($key, $val) = each($arr_customers)){
	$arr_vals = split(';', $val);
	$unn = $arr_vals[0];
	$cashregister = $arr_vals[1];
	$date = date("Y-m-d H:i:s", strtotime($arr_vals[2]));
	$status = $arr_vals[3];
	$price = $arr_vals[4];
	
	if($status=='ready'){
		$status = 1;

		#закачиваеи акт
		$docname = $unn.'+'.$cashregister.'.doc';
		$folder = "../files/repair";
		if(!file_exists("$folder/".$docname)){
			$file = file_get_contents('http://mail.ramok.by:1234/kasbi/xml/repair_docs/'.$docname);
			  $fp = fopen("$folder/".$docname, "w+");
				 if (fwrite($fp, $file)) { 
					  echo "файл сохранен!<br>";
				 } else {
				echo "Произошла ошибка!";
			   }
			fclose($fp);
			}
		}
	else{
		$status = 0;
		}
	
	if($unn && $cashregister){
		$query = mysql_query("SELECT * FROM tbl_repair WHERE unn='" .$unn. "' AND cashregister='" .$cashregister. "'");
		if(mysql_num_rows($query)){
			$query = mysql_query("UPDATE tbl_repair SET status='" .$status. "', date='" .$date. "', price='" .$price. "' WHERE unn='" .$unn. "' AND cashregister='" .$cashregister. "'");
			}
		else{
			$query = mysql_query("INSERT INTO tbl_repair SET unn='" .$unn. "' , cashregister='" .$cashregister. "', status='" .$status. "', date='" .$date. "', price='" .$price. "'");
			}
		}
	
	print $val.'<br>';
	}

#Теперь удаляем старое
$query = mysql_query("SELECT * FROM tbl_repair LIMIT 100,150");
while($result = mysql_fetch_array($query)){
	if($result['status']==1){
		@unlink('../files/repair/'.$result['unn'].'+'.$result['cashregister'].'.doc');
		mysql_query("DELETE FROM tbl_repair WHERE unn='" .$result['unn']. "' AND cashregister='" .$result['cashregister']. "'");
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