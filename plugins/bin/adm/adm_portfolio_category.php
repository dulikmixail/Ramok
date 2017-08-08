<?


#Категории портфолио

$a = $_REQUEST['a'];
$delete = $_REQUEST['delete'];


if($delete){
	$query = mysql_query("DELETE FROM tbl_portfoliorubr WHERE id='" .$delete. "'");
	$notify = 'Рубрика удалена<br><br>';
	}


if($a=='save'){
	if($_POST["position"]){
		while(list($id_cat, $val)=each($_POST["position"])){
			$query = mysql_query("UPDATE tbl_portfoliorubr SET position='" .$val. "' WHERE id='" .$id_cat. "'");
			}
		$notify = 'Записи обновлены<br>';
		}
	}

$query = mysql_query("SELECT * FROM tbl_portfoliorubr ORDER BY position");
$i=1;
while($result = mysql_fetch_array($query)){
	if($i%2){$cls=" class=TableRow1";}else{$cls=" class=TableRow2";}
	$i++;
	$list_category .= '
	<tr height=20 ' .$cls. '>
		<td>' .$result['name']. '</td>
		<td align="center"><input type="text" style="width:50px" name="position[' .$result['id']. ']" value="' .$result['position']. '"  class="txt"></td>	
		<td align="center" ' .$cls. '><a class=table href="/adm/portfolio/category/add?id=' .$result['id']. '"><img border="0" title="Редактировать" src="/cfg/adm/img/edit.png" width=11 height=11></a></td>
		<td align="center" ' .$cls. '><a class=Table href="/adm/portfolio/category?delete=' .$result['id']. '" onClick="return confirm(\'Вы уверены, что хотите удалить категорию?\')"><img border="0" title="Удалить" src="/cfg/adm/img/del.png" width=11 height=10></a></td>
	</tr>';
	}



?>
