<?

include("../phpc/config.php");

$db_connection = mysql_connect(DatabaseHost, DatabaseUser, DatabasePass);
mysql_select_db(DatabaseName, $db_connection);
mysql_query("SET NAMES cp1251");



$del = $_REQUEST['del'];


$query = mysql_query("UPDATE tbl_user set not_send='1' WHERE email='" .$del. "'");



?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name='yandex-verification' content='5abaf2682bba4994' />
<title>Компания Рамок</title>
<meta http-equiv="content-type" content="text/html; charset=windows-1251" />
</head>
<body>

Вы отписаны от рассылки!

</body>
</html>