<?


#Банер

$a = $_REQUEST['a'];

if($a=='del'){
	@unlink('../images/baner.png');
	$query = mysql_query("UPDATE tbl_baner SET img=''");
	$notify = 'Изображение удалено!<br><br>';
	}
	
		
if($a=='save'){
	
	$text = $_REQUEST['text'];
	$text = mysql_real_escape_string($text);
	$link = $_REQUEST['link'];
	$link = mysql_real_escape_string($link);

	$img_name =  $_FILES['img']['name'];
	$img = $_FILES['img']['tmp_name'];
	
	if($img_name){
		$img_name = "baner.png";
		$filePathSmall = "../images/" .$img_name;
		move_uploaded_file($img, $filePathSmall);
		$query = mysql_query("UPDATE tbl_baner SET img='" .$img_name. "'");
		}
	
	$query = mysql_query("UPDATE tbl_baner SET text='" .$text. "', link='" .$link. "'");
	
	$notify = 'Обновлено!<br><br>';
	}

$query = mysql_query("SELECT * FROM tbl_baner");
$result = mysql_fetch_array($query);
$text = $result['text'];
$text = stripslashes($text);
$img = $result['img'];
$img = stripslashes($img);
$link = $result['link'];
$link = stripslashes($link);

if($img){
	$show_img = '
	<b>Загруженное изображение:</b><br>
	<img width="250" src="/images/' .$img. '"> <a style="color:red" href="?a=del">Удалить</a>
	<br><br>';
	}

?>
