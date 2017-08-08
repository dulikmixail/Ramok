<?

set_time_limit(480);

include("../phpc/config.php");

$db_connection = mysql_connect(DatabaseHost, DatabaseUser, DatabasePass);
mysql_select_db(DatabaseName, $db_connection);
mysql_query("SET NAMES cp1251");


$handle = fopen('bank.xml', "rb");
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

$arr_customers = explode('/>', $page);

$i=0;
while (list($key, $val) = each($arr_customers)){
	
	$arr_val = explode('"', $val);
	$bank_code=trim($arr_val[5]);
	$bank_code = trim($bank_code,'0');
	$bank_code = trim($bank_code,'0');
	$bank_code = trim($bank_code,'0');

	$bank_code=trim($arr_val[5]);
	$bank_name=trim($arr_val[7]);
	$bank_adress=trim($arr_val[9]);
	
	$arr_bank[$bank_code]['name'] = $bank_name;
	$arr_bank[$bank_code]['adress'] = $bank_adress;

	}


print $arr_bank['302']['name'].'<br>';


#$handle = fopen('http://mail.ramok.by:1992/Kasbi/xml/allcustomers.xml', "rb");
$handle = fopen('allcustomers.xml', "rb");
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
	$company_name=trim($arr_val[3]).' '.trim($arr_val[5]);
	$customer_name=trim($arr_val[7].' '.$arr_val[9].' '.$arr_val[11]);
	
	$unn=trim($arr_val[15]);
	$city=trim($arr_val[25]);
	$adress=trim($arr_val[27]).' '.trim($arr_val[29]);	
	$phone=trim($arr_val[35]);
	$dogovor=trim($arr_val[55]);
	$bank_code=trim($arr_val[45]);
	$bank_account=trim($arr_val[47]);
	
	if($unn&&$dogovor&&is_numeric($unn)&&is_numeric($dogovor)){
		
		print $customer_name. '-' .$company_name. '-' .$unn. '-' .$city. '-' .$adress. '-' .$dogovor. '-' .$bank_code. '-' .$bank_account. '-' .$arr_bank[$bank_code]['name']. '-' .$arr_bank[$bank_code]['adress']. '<br>';
		
		//exit();
		
		$query = mysql_query("SELECT id FROM tbl_user WHERE unn='" .$unn. "'");		
		if(!mysql_num_rows($query)){
			
			$query = mysql_query("INSERT INTO tbl_user SET 
				name='" .$customer_name. "',
				company='" .$company_name. "',
				unn='" .$unn. "',
				dogovor='" .$dogovor. "',
				city='" .$city. "',
				adress='" .$adress. "',
				phone='" .$phone. "',
				bank_code='" .$bank_code. "',
				bank_schet='" .$bank_account. "',
				bank_name='" .$arr_bank[$bank_code]['name']. "',
				bank_adress='" .$arr_bank[$bank_code]['adress']. "'
			");
			
			
			}
		else{
			$query = mysql_query("UPDATE tbl_user SET 
				name='" .$customer_name. "',
				company='" .$company_name. "',
				city='" .$city. "',
				adress='" .$adress. "',
				phone='" .$phone. "',
				bank_code='" .$bank_code. "',
				bank_schet='" .$bank_account. "',
				bank_name='" .$arr_bank[$bank_code]['name']. "',
				bank_adress='" .$arr_bank[$bank_code]['adress']. "'
				WHERE unn='" .$unn. "'
			");
			
			}
		
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
