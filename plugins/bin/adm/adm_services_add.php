<?

#Услуги

$id = $_REQUEST['id'];


include('../plugins/_translit.php');

$update = $_REQUEST['update'];
$id = $_REQUEST['id'];
$delete = $_REQUEST['delete'];
$a = $_REQUEST['a'];


#Удаление параметра формы
if($a=='del_param'){
	$id_param = $_REQUEST['id_param'];
	$query = mysql_query("DELETE FROM tbl_form_param WHERE id='" .$id_param. "'");
	header("Location: /adm/services/add?id=" .$id);
	exit();
	}

#Добавление параметра формы
if($a=='add_param'){
	$param_name = $_POST['param_name'];
	$param_type = $_POST['param_type'];
	$param_value = $_POST['param_value'];	
	
	$query = mysql_query("INSERT INTO tbl_form_param VALUES ('', '" .$id. "', '" .$param_name. "', '" .$param_type. "', '" .$param_value. "')");
	header("Location: /adm/services/add?id=" .$id);
	exit();
	}

#Редактирование параметров формы
if($a=='edit_param'){
	$query = mysql_query("SELECT * FROM tbl_form_param WHERE id_category='" .$id. "'");
	while($result=mysql_fetch_array($query)){
		$param_name = $_REQUEST['param_name'.$result['id']];
		$param_type = $_REQUEST['param_type'.$result['id']];
		$param_value = $_REQUEST['param_value'.$result['id']];
		$query2 = mysql_query("UPDATE tbl_form_param SET name='" .$param_name. "', type='" .$param_type. "', value='" .$param_value. "' WHERE id='" .$result['id']. "'");
		}
	header("Location: /adm/services/add?id=" .$id);
	exit();		
	}


if($update){
    $head_text = 'Редактирование услуги';
	$name = $_REQUEST['name'];
	$teaser = $_REQUEST['teaser'];
	$caption = encodestring($name);
	$id_category = $_REQUEST['id_category'];
	$text = $_REQUEST['text'];

	$is_main = $_REQUEST['is_main'];
	$main_teaser = $_REQUEST['main_teaser'];

	$PageDescription = $_REQUEST['PageDescription'];
	$PageTitleSEO = $_REQUEST['PageTitleSEO'];
	$PageKeywords = $_REQUEST['PageKeywords'];
	$PageAuthor = $_REQUEST['PageAuthor'];
	$PageRobots = $_REQUEST['PageRobots'];		
	$img_old = $_REQUEST['img_old'];
		
	if($id){
		$query = mysql_query("UPDATE tbl_services SET id_parent='" .$id_category. "', name='" .$name. "', teaser='" .$teaser. "', caption='" .$caption. "', text='" .$text. "', PageDescription='" .$PageDescription. "', PageTitleSEO='" .$PageTitleSEO. "', PageKeywords='" .$PageKeywords. "', PageAuthor='" .$PageAuthor. "', PageRobots='" .$PageRobots. "', is_main='" .$is_main. "', main_teaser='" .$main_teaser. "'  WHERE id='" .$id. "'");
		header("Location: /adm/services");
		exit();
		}
	else{
		$query = mysql_query("INSERT INTO tbl_services SET id_parent='" .$id_category. "', name='" .$name. "', teaser='" .$teaser. "', caption='" .$caption. "', text='" .$text. "', PageDescription='" .$PageDescription. "', PageTitleSEO='" .$PageTitleSEO. "', PageKeywords='" .$PageKeywords. "', PageAuthor='" .$PageAuthor. "', PageRobots='" .$PageRobots. "', is_main='" .$is_main. "', main_teaser='" .$main_teaser. "'");
		header("Location: /adm/services");
		exit();		
		}
	}
else{
	if($id){
        $head_text = 'Редактирование услуги';
		#Достаем информацию о товаре
		$query = mysql_query("SELECT * FROM tbl_services WHERE id='" .$id. "'");
		$result = mysql_fetch_array($query);
		$name = $result['name'];
		$teaser = $result['teaser'];
		$text = $result['text'];
		$PageDescription = $result['PageDescription'];
		$PageTitleSEO = $result['PageTitleSEO'];
		$PageKeywords = $result['PageKeywords'];
		$PageAuthor = $result['PageAuthor'];
		$PageRobots = $result['PageRobots'];		
		
		$id_parent = $result['id_parent'];

		$is_main = $result['is_main'];
		$main_teaser = $result['main_teaser'];
		
		if($is_main){
			$chk_is_main = 'checked';
			}


		}
	else{
        $head_text = 'Добавление услуги';		
		}
	
	if($id_parent||!$id){
		#Достаем категории
		$query = mysql_query("SELECT * FROM tbl_services WHERE id_parent='0' ORDER BY name");
		while($result = mysql_fetch_array($query)){
			if($result['id']==$id_parent){
				$list_mdn_category .= '<option selected value="' .$result['id']. '">' .$result['name']. '</option>';
				}
			else{
				$list_mdn_category .= '<option value="' .$result['id']. '">' .$result['name']. '</option>';
				}
			}
		}
	else{
		$list_mdn_category .= '<option value="0">Нет</option>';
		}
	}



#Достаем параметры формы
$query = mysql_query("SELECT * FROM tbl_form_param WHERE id_category='" .$id. "'");
while($result = mysql_fetch_array($query)){
	$selected[$result['type']] = 'selected';
	$type = '
	<select name="param_type' .$result['id']. '" class="txt" class="txt" style="width:200px">
		<option ' .$selected[1]. ' value="1">Текст</option>
		<option ' .$selected[2]. ' value="2" style="color:green">Чекбокс</option>
		<option ' .$selected[3]. ' value="3" style="color:blue">Список</option>			
	</select>';
	$selected[$result['type']] = '';
	
	$list_params .= '
	<tr>
		<td><input name="param_name' .$result['id']. '" type="text" value="' .$result['name']. '" class="txt" class="txt" style="width:200px"></td>
		<td>' .$type. '</td>
		<td><input size="50" name="param_value' .$result['id']. '" type="text" value="' .$result['value']. '" class="txt" style="width:200px"></td>
		<td><a class=Table href="/adm/services/add?a=del_param&id_param=' .$result['id']. '&id=' .$id. '" onClick="return confirm(\'Вы уверены, что хотите удалить параметр?\')"><img border="0" title="Удалить" src="/cfg/adm/img/del.png" width=11 height=10></a></td>
	</tr>';
	}

if($list_params){
	$list_params .= '
	<tr><td colspan="4" align=right><hr size=1><input class=but type=submit value="Сохранить"></td></tr>';
	}
else{
	$list_params .= '<tr><td colspan="4" align="center">Не добавлено параметров</td></tr>';
	}



?>
