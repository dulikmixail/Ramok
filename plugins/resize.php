<?

error_reporting( E_ALL );

set_time_limit(300);

include("../phpc/config.php");

include "img_resize.php";

$db_connection = mysql_connect(DatabaseHost, DatabaseUser, DatabasePass);
mysql_select_db(DatabaseName, $db_connection);
mysql_query("SET NAMES cp1251");

print '1';

$query = mysql_query("SELECT * FROM tbl_productimg ORDER BY ProductImgID LIMIT 2000,300");
while($result = mysql_fetch_array($query)){
	$filePath = "../images/product/".$result['ProductImg'];
	if(file_exists($filePath)){
		print resizeImage($filePath, $filePath, 800, 800, 1, 0, 0);
		print  "http://ramok.by/images/product/".$result['ProductImg'].'<br>';
		}
	}


?>