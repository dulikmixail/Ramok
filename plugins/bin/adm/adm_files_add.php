<?

#Файлы

#Админка - добавление статьи в медраздел

include("../plugins/img_resize.php");

$update = $_REQUEST['update'];
$id = $_REQUEST['id'];
$id_category = $_REQUEST['id_category'];
$id_thematic = $_REQUEST['id_thematic'];
$delete = $_REQUEST['delete'];
$backurl = $_SESSION['backurl_filesadd'];

if($delete){
    $filePath = "../images/files/" .$delete;
    if(file_exists($filePath)) {
        if(unlink($filePath)){
            mysql_query("update tbl_files set img='' where id='" .$id. "'");
            $ErrMsg = '<div class="err">Файл был удален</div>';
        }
        else{
            $ErrMsg = '<div class="err">Невозможно удалить файл. Возможно его не существует</div>';        
        }
    }
    else{
        $ErrMsg = '<div class="err">Невозможно найти файл.</div>';    
    }   
}

if($update){
    $head_text = 'Редактирование файла';
	$name = $_REQUEST['name'];
	$filename = $_REQUEST['filename'];
	$text = $_REQUEST['text'];	
	$idtovar = $_REQUEST['idtovar'];	
	
	$idtovar = '['.str_replace(',','],[',$idtovar).']';
	
	$img = $_FILES['img']['tmp_name'];
	$img_name = $_FILES['img']['name'];
		
	if($img_name){
		$umd="".md5(uniqid(rand())).".jpg";
		$img_name = $umd;
		$filePath = "../images/files/" .$img_name;
		move_uploaded_file($img, $filePath);
		resizeImage($filePath, $filePath, 100, 100, 1, 0, 0);
		
		if(strlen($img_old)>0){
			$filePath = "../images/files/" .$img_old;
			if(file_exists($filePath))unlink($filePath); 
			}
		$img_edit = ", img='" .$img_name. "'";
		}
	
	if($id){
		$query = mysql_query("UPDATE tbl_files SET rubr='" .$id_category. "', idtovar='" .$idtovar. "', caption='" .$name. "', name='" .$filename. "', opis='" .$text. "' " .$img_edit. " WHERE id='" .$id. "'");
		header("Location: ".$backurl);
		exit();
		}
	else{
		$query = mysql_query("INSERT INTO tbl_files SET rubr='" .$id_category. "', idtovar='" .$idtovar. "', caption='" .$name. "', name='" .$filename. "', opis='" .$text. "', img='" .$img_name. "'");
		header("Location: ".$backurl);
		exit();		
		}
	}
else{
	if($id){
        $head_text = 'Редактирование файла';
		#Достаем информацию о товаре
		$query = mysql_query("SELECT * FROM tbl_files WHERE id='" .$id. "'");
		$result = mysql_fetch_array($query);
		$name = $result['caption'];
		$filename = $result['name'];		
		$id_category = $result['rubr'];
		$text = $result['opis'];		
		$img = $result['img'];
		$idtovar = $result['idtovar'];
			
		$idtovar = str_replace('[','',$idtovar);
		$idtovar = str_replace(']','',$idtovar);
			
			
		if($img){
			$show_img = '
			<tr>
				<td nowrap class=TableRow1 >Загруженное изображение: </td>
				<td class=TableRow1 align=left>
					' .$img. '<br />
					<img src="/images/files/' .$img. '"><br />
					<a href="/adm/files/add?id=' .$id. '&delete=' .$img. '" style="float:right;">Удалить</a>
				</td>
			</tr>';
			}
		
		}
	else{
        $head_text = 'Добавление файла';		
		}
	}

#Достаем тематики
function tree($ids,$level,$CatID,$table){ 
	$level++;
	$query = "SELECT * FROM " .$table. " WHERE id_parent=" . $ids . " ORDER BY position";
	$result = mysql_query($query);
	
	if($level==1){
		while($print = mysql_fetch_array($result)){ 
			$ids = $print['id'];
			if($CatID==$ids){
				$left_menu .= '<option selected value="' .$print['id']. '">' .$print['name']. '</option>';
				}
			else{
				$left_menu .= '<option value="' .$print['id']. '">' .$print['name']. '</option>';
				}
			$left_menu .= tree($ids,$level,$CatID,$table);
			}
		}
	if($level==2){
		while($print = mysql_fetch_array($result)){ 
			$ids = $print['id'];
			if($CatID==$ids){
				$left_menu .= '<option selected value="' .$print['id']. '">&nbsp;&nbsp;&nbsp;&nbsp;' .$print['name']. '</option>';
				}
			else{
				$left_menu .= '<option value="' .$print['id']. '">&nbsp;&nbsp;&nbsp;&nbsp;' .$print['name']. '</option>';
				}
			$left_menu .= tree($ids,$level,$CatID,$table);
			}
		}
	if($level==3){
		while($print = mysql_fetch_array($result)){ 
			$ids = $print['id'];

			if($CatID==$ids){
				$left_menu .= '<option selected value="' .$print['id']. '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .$print['name']. '</option>';
				}
			else{
				$left_menu .= '<option value="' .$print['id']. '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .$print['name']. '</option>';
				}			
			$left_menu .= tree($ids,$level,$CatID,$table);
			}
		}
	if($level==4){
		while($print = mysql_fetch_array($result)){ 
			$ids = $print['id'];
			if($CatID==$ids){
				$left_menu .= '<option selected value="' .$print['id']. '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .$print['name']. '</option>';
				}
			else{
				$left_menu .= '<option value="' .$print['id']. '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .$print['name']. '</option>';
				}			
			}
		}
			
	return $left_menu;
	}
$list_category = tree(0,0,$id_category,'tbl_catalog');





?>