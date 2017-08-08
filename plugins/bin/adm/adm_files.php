<?

#Файлы

#Админка - пакет статей

$a = $_REQUEST['a'];
$delete = $_REQUEST['delete'];
$id_category = $_REQUEST['id_category'];
$id_thematic = $_REQUEST['id_thematic'];
$page = $_REQUEST['page'];



$_SESSION['backurl_filesadd'] = $_SERVER['REQUEST_URI'];


if($delete){
	$query = mysql_query("DELETE FROM tbl_files WHERE id='" .$delete. "'");
	}

if($a=='add'){
	$name = $_REQUEST['name'];
	$filename = $_REQUEST['filename'];
	if($name&&$filename){
		$query = mysql_query("INSERT INTO tbl_files SET rubr='" .$id_category. "', caption='" .$caption. "', name='" .$name. "'");
		$notify = 'Запись добавлена';
		}
	}



if($id_category){
	$where = " rubr='" .$id_category. "' ";
	}

if($where){
	$where = trim($where,'AND');
	$where = ' WHERE '.$where;
	}


#Достаем количество новостей
$count = mysql_query("SELECT COUNT(id) AS count_rows FROM tbl_files " .$where. "");
$count_rows = mysql_result($count, '0', 'count_rows');
$maxpage = ceil($count_rows/20);
if($page>$maxpage){$page=$maxpage;}
if($page<1){$page=1;}
$start = ($page-1)*20;

#Генерируем списочек страниц
$i=1;
while($i<=$maxpage){
	if($i!=$page){
		$listpages .= ' [<a href="/adm/files?page=' .$i. '&id_category=' .$id_category. '">' .$i. '</a>] ';
		}
	else{
		$listpages .= ' <strong>-' .$i. '-</strong> ';
		}
	$i++;
	}


#Достаем новости
$query = mysql_query("SELECT * FROM tbl_files " .$where. " ORDER BY name DESC LIMIT " .$start. ", 20");
$i=1;
while($result = mysql_fetch_array($query)){
	if($i%2){$cls=" class=TableRow1";}else{$cls=" class=TableRow2";}
	$date = date("d.m.Y", strtotime($result['date']));
	if($result['isactive']==1){$result['isactive']='checked';}else{$result['isactive']='';}
	$result['caption'] = wordwrap($result['caption'], 90, "<br>", 1);
	$result['name'] = wordwrap($result['name'], 40, " ", 1);
	$list_news .= '
	 <tr height=20>
      <td ' .$cls. ' nowrap width=100%>' .$result['caption']. '<br><i>' .$result['name']. '</i></td>
	  <td align="center" ' .$cls. ' nowrap><a class=Table href="/adm/files/add?id=' .$result['id']. '"><img border="0" title="Редактировать" src="/cfg/adm/img/edit.png" width=11 height=10></a></td>
	  <td align="center" ' .$cls. ' nowrap><a class=Table href="/adm/files?delete=' .$result['id']. '&page=' .$page. '&id_category=' .$id_category. '" onClick="return confirm(\'Вы уверены, что хотите удалить?\')"><img border="0" title="Удалить" src="/cfg/adm/img/del.png" width=11 height=10></a></td>
	 </tr>	
	';
	$i++;
	}

if(!$list_news){
	$list_news = '<tr height=20><td colspan="4" align="center">Не найдено файлов</td></tr>';
	}




#Достаем категории

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