<?

#Услуги

$delete = $_REQUEST['delete'];
$a = $_REQUEST['a'];

if($delete){
	$query = mysql_query("DELETE FROM tbl_services WHERE id='" .$delete. "'");
	$notify = 'Услуга удалена<br><br>';
	}

if($a=='save'){
	$query = mysql_query("SELECT * FROM tbl_services");
	while($result = mysql_fetch_array($query)){
		$sortorder = $_REQUEST['sortorder'.$result['id']];
		$isshow = $_REQUEST['isshow'.$result['id']];
		$query2 = mysql_query("UPDATE tbl_services SET sortorder='" .$sortorder. "', isshow='" .$isshow. "' WHERE id='" .$result['id']. "'");
		$notify = "Записи обновлены<br>";
		}
	}

#Достаем категории
$query = mysql_query("SELECT * FROM tbl_services WHERE id_parent='0' ORDER BY sortorder");
$i=1;
while($result = mysql_fetch_array($query)){
	if($i%2){$cls=" class=TableRow1";}else{$cls=" class=TableRow2";}
	if($result['isshow']==1){
		$isshow = 'checked';
		}
	else{
		$isshow = '';
		}
	$i++;
	if($result['is_main']){$style=' style="background-color:#93FF93" ';}else{$style='';}
	$list_category .= '
	<tr height=20 ' .$cls. '>
		<td ' .$style. '><strong>' .$result['name']. '</strong></td>
		<td><input ' .$isshow. ' type="checkbox" name="isshow' .$result['id']. '" value="1"><input name="sortorder' .$result['id']. '" type="text" class="txt" style="width:25px" value="' .$result['sortorder']. '"></td>
		<td align="center" ' .$cls. '><a class=table href="/adm/services/add?id=' .$result['id']. '"><img border="0" title="Редактировать" src="/cfg/adm/img/edit.png" width=11 height=11></a></td>
		<td align="center" ' .$cls. '><a class=Table href="/adm/services?delete=' .$result['id']. '" onClick="return confirm(\'Вы уверены, что хотите удалить категорию?\')"><img border="0" title="Удалить" src="/cfg/adm/img/del.png" width=11 height=10></a></td>
	</tr>
	';

	#Достаем подкатегории
	$query2 = mysql_query("SELECT * FROM tbl_services WHERE id_parent='" .$result['id']. "' ORDER BY sortorder");
	while($result2 = mysql_fetch_array($query2)){
		if($i%2){$cls=" class=TableRow1";}else{$cls=" class=TableRow2";}
		if($result2['isshow']==1){
			$isshow = 'checked';
			}
		else{
			$isshow = '';
			}
		$i++;	
		if($result2['is_main']){$style=' style="background-color:#93FF93" ';}else{$style='';}
		$list_category .= '
		<tr height=20 ' .$cls. '>
			<td ' .$style. '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .$result2['name']. '</td>
			<td><input ' .$isshow. ' type="checkbox" name="isshow' .$result2['id']. '" value="1"><input name="sortorder' .$result2['id']. '" type="text" class="txt" style="width:25px" value="' .$result2['sortorder']. '"></td>
			<td align="center" ' .$cls. '><a class=table href="/adm/services/add?id=' .$result2['id']. '"><img border="0" title="Редактировать" src="/cfg/adm/img/edit.png" width=11 height=11></a></td>
			<td align="center" ' .$cls. '><a class=Table href="/adm/services?delete=' .$result2['id']. '" onClick="return confirm(\'Вы уверены, что хотите удалить категорию?\')"><img border="0" title="Удалить" src="/cfg/adm/img/del.png" width=11 height=10></a></td>
		</tr>';
		}
	
	}






?>