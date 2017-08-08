<?

#Настройки

$a = $_REQUEST['a'];

if($a=='save'){
	
	$kurs = $_REQUEST['kurs'];
	$kurs = mysql_real_escape_string($kurs);
	$copyright = $_REQUEST['copyright'];
	$copyright = mysql_real_escape_string($copyright);
	$show_price = $_REQUEST['show_price'];
	$show_price = mysql_real_escape_string($show_price);
	
	$query = mysql_query("UPDATE tbl_settings SET kurs='" .$kurs. "', copyright='" .$copyright. "', show_price='" .$show_price. "'");
	
	$notify = 'Обновлено!<br><br>';
	}

$query = mysql_query("SELECT * FROM tbl_settings");
$result = mysql_fetch_array($query);
$copyright = $result['copyright'];
$copyright = stripslashes($copyright);
$kurs = $result['kurs'];
$kurs = stripslashes($kurs);
$show_price = $result['show_price'];
$show_price = stripslashes($show_price);

if($show_price==1){
	$ch_show_price = 'checked';
	}


?>