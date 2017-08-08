<?

include("../phpc/config.php");

$db_connection = mysql_connect(DatabaseHost, DatabaseUser, DatabasePass);
mysql_select_db(DatabaseName, $db_connection);
mysql_query("SET NAMES cp1251");


$handle = fopen('http://mail.ramok.by:1992/Kasbi/xml/import/price.xml', "rb");
#$handle = fopen('http://ramok.by/plugins/price.xml', "rb");
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
	$tname=$arr_val[3];
	$articul=$arr_val[7];
	$price=$arr_val[15];//9
	$count=$arr_val[17];
	$usd=$arr_val[19];


	//$articul = @iconv("UTF-8", "windows-1251", $articul);	
	//$tname = @iconv("UTF-8", "windows-1251", $tname);	

	//if($articul&&$price&&$count){
	if($articul&&$price&&$count){
		$i++;
		
		$query = mysql_query("SELECT id, price, name FROM tbl_products WHERE artikul='" .$articul. "'");
	  if(mysql_num_rows($query)){
			$result = mysql_fetch_array($query);
			$n++;
			$found .= '<strong>' .$articul.'</strong> ' .$result['name']. ' - ' .$price. ' - ' .$usd. '$<br>';
			if($result['price']>$price){
				$price_old = $result['price'];
				mysql_query("UPDATE tbl_products SET price='" .$price. "', price_old='" .$price_old. "', price_old_date='" .$date_add. "', count='" .$count. "', price_usd='" .$usd. "' WHERE id='" .$result['id']. "'");
				}
			else{
				mysql_query("UPDATE tbl_products SET price='" .$price. "', count='" .$count. "', price_usd='" .$usd. "' WHERE id='" .$result['id']. "'");
				}
			$ids1 .= $result['id'].',';
			}
		else{
			
			$query = mysql_query("SELECT id, price, name FROM tbl_modification WHERE artikul='" .$articul. "'");
			if(mysql_num_rows($query)){
				$result = mysql_fetch_array($query);
				$n++;
				$found .= '<strong>' .$articul.'</strong> ' .$result['name']. ' - ' .$price. ' - ' .$usd. '$<br>';
				if($result['price']>$price){
					$price_old = $result['price'];
					mysql_query("UPDATE tbl_modification SET price='" .$price. "', price_old='" .$price_old. "', price_old_date='" .$date_add. "', count='" .$count. "', price_usd='" .$usd. "' WHERE id='" .$result['id']. "'");
					}
				else{
					mysql_query("UPDATE tbl_modification SET price='" .$price. "', count='" .$count. "', price_usd='" .$usd. "' WHERE id='" .$result['id']. "'");
					}
				$ids2 .= $result['id'].',';
				}
			else{
				$notfound .= '<strong>' .$articul.'</strong> ' .$tname.'<br>';
				}			
			
			}		

		
		}
	else{
		if($usd){
			mysql_query("UPDATE tbl_products SET price_usd='" .$usd. "' WHERE artikul='" .$articul. "'");
			mysql_query("UPDATE tbl_modification SET price_usd='" .$usd. "' WHERE artikul='" .$articul. "'");
			}
		}
	
	$name = str_replace('&quot;', '"', $name);
	//if($unn>100){
		//$query = mysql_query("INSERT INTO tbl_dolg SET unn='" .$unn. "' , name='" .$name. "' , amount='" .$amount. "'");	
		//$i++;
		//}
	}


$ids1 = trim($ids1,',');
$ids2 = trim($ids2,',');

mysql_query("UPDATE tbl_products SET price='0' WHERE id NOT IN (" .$ids1. ")");
mysql_query("UPDATE tbl_modification SET price='0' WHERE id NOT IN (" .$ids2. ")");



?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name='yandex-verification' content='5abaf2682bba4994' />
<title>Компания Рамок</title>
<meta http-equiv="content-type" content="text/html; charset=windows-1251" />
</head>
<body>

Обработано записей: <strong><?=$i;?></strong><br>
Обновлено цен: <strong><?=$n;?></strong><br>
<br><br>

<strong>Обработанные записи:</strong><br>
<br>
<?=$found;?>

<br><br>
<strong>Не артикулов на сайте:</strong><br>
<br>
<?=$notfound;?>

</body>
</html>
