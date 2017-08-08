<?

include("../phpc/config.php");

$db_connection = mysql_connect(DatabaseHost, DatabaseUser, DatabasePass);
mysql_select_db(DatabaseName, $db_connection);
mysql_query("SET NAMES cp1251");


$handle = fopen('import.csv', "rb");
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


while (list($key, $val) = each($arr_customers)){
	$arr_val = explode(';', $val);
	$company=$arr_val[3].' '.$arr_val[0];
	$unn=$arr_val[1];
	$dogovor=$arr_val[2];
	$name=$arr_val[4].' '.$arr_val[5].' '.$arr_val[6];
	if($unn && $dogovor){
		$query = mysql_query("SELECT id FROM tbl_user WHERE unn='" .$unn. "' AND dogovor='" .$dogovor. "'");
		if(!mysql_num_rows($query)){
			mysql_query("INSERT INTO tbl_user SET unn='" .$unn. "', dogovor='" .$dogovor. "', company='" .$company. "', name='" .$name. "'");
			print $company.$unn.$dogovor.$name.'<br>';
			}
		}
	}


?>
