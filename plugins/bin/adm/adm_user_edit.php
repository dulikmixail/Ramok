<?

#Админка: редактирование пользователя

$id = $_REQUEST['id'];
$a = $_REQUEST['a'];

$backlink = $_SESSION['backurl_useredit'];


if($a=='save'){
	$account_type = $_REQUEST['account_type'];
	$show_lk = $_REQUEST['show_lk'];
	mysql_query("UPDATE tbl_user SET account_type='" .$account_type. "', show_lk='" .$show_lk. "' WHERE id='" .$id. "'");
	}




#
#Достаем информацию о пользователе
#
$query = mysql_query("SELECT * FROM tbl_user WHERE id='" .$id. "'");
$result = mysql_fetch_array($query);
$name = $result['name'];
$nick = $result['login'];
$pass = $result['pass'];

$unn = $result['unn'];
$dogovor = $result['dogovor'];
$unn = $result['unn'];
$company = $result['company'];


$city = $result['city'];
$phone = $result['phone'];
$email = $result['email'];
$date_add = $result['date_register'];
$last_visit = $result['last_date'];
$work = $result['work'];
$special = $result['special'];
$study = $result['study'];
$account_type = $result['account_type'];
$show_lk = $result['show_lk'];

$ch_type[$account_type] = 'checked';

if($show_lk){
	$ch_show_lk = 'checked';
	}





?>