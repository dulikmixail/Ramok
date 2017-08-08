<?

#Пакет для редактирования отзывов в админке

$a = $_REQUEST['a'];
$id = $_REQUEST['id'];

if($a=='save'){
	$name = $_REQUEST['name'];
	$date_add = $_REQUEST['date_add'];
	$text = $_REQUEST['text'];
	$phone = $_REQUEST['phone'];
	$email = $_REQUEST['email'];
	$is_show = $_REQUEST['is_show'];
	
	$date_add = date("Y-m-d", strtotime($date_add));
	
	if($id){
		#Редактирование
			
		$query = mysql_query("UPDATE tbl_voites SET name='" .$name. "', text='" .$text. "', date='" .$date_add. "', phone='" .$phone. "', email='" .$email. "', is_show='" .$is_show. "' WHERE id='" .$id. "'");		
		
		header('Location: /adm/voites');
		exit();		
		}
	else{
		#Добавление
		
		$query = mysql_query("INSERT INTO tbl_voites SET name='" .$name. "', text='" .$text. "', date='" .$date_add. "', phone='" .$phone. "', email='" .$email. "', is_show='" .$is_show. "'");
		header('Location: /adm/voites');
		exit();
		}	
	}

#Показываем данные
if($id){
	$query = mysql_query("SELECT * FROM tbl_voites WHERE id='" .$id. "'");
	$result = mysql_fetch_array($query);
	$name = $result['name'];
	$text = $result['text'];	
	$phone = $result['phone'];
	$email = $result['email'];
	
	$date_add = date("d.m.Y", strtotime($result['date']));
	
	if($result['is_show']){
		$chk_is_show = 'checked';
		}
	
	$head_text = 'Редактирование отзыва';
	}
else{
	$head_text = 'Добавление отзыва';
	}

?>