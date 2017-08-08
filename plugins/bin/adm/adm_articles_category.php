<?


#Категории доменов

$a = $_REQUEST['a'];
$delete = $_REQUEST['delete'];

include('../plugins/_translit.php');


if($delete){
	$query = mysql_query("DELETE FROM tbl_articles_category WHERE id='" .$delete. "'");
	$notify = 'Категория удалена<br><br>';
	}


if($a=='add'){
	$name = $_REQUEST['name'];
	$caption = encodestring($_REQUEST['caption']);
	$position = $_REQUEST['position'];
	$query = mysql_query("INSERT INTO tbl_articles_category SET name='" .$name. "', caption='" .$caption. "',  sortorder='" .$position. "'");
	$notify = 'Запись обновлена<br><br>';
	}


if($a=='save'){
	if($_POST["position"]){
		while(list($id_cat, $val)=each($_POST["position"])){
			$caption = encodestring($_POST["name"][$id_cat]);
			$query = mysql_query("UPDATE tbl_articles_category SET sortorder='" .$val. "', name='" .$_POST["name"][$id_cat]. "', caption='" .$caption. "' WHERE id='" .$id_cat. "'");
			}
		$notify = 'Записи обновлены<br><br>';
		}
	}

$query = mysql_query("SELECT * FROM tbl_articles_category ORDER BY sortorder");
$i=1;
while($result = mysql_fetch_array($query)){
	if($i%2){$cls=" class=TableRow1";}else{$cls=" class=TableRow2";}
	$i++;
	$list_category .= '
	<tr height=20 ' .$cls. '>
		<td><input type="text" name="name[' .$result['id']. ']" value="' .$result['name']. '"  class="txt"></td>
		<td align="center"><input type="text" style="width:50px" name="position[' .$result['id']. ']" value="' .$result['sortorder']. '"  class="txt"></td>	
		<td align="center" ' .$cls. '><a class=Table href="/adm/articles/category?delete=' .$result['id']. '" onClick="return confirm(\'Вы уверены, что хотите удалить категорию?\')"><img border="0" title="Удалить" src="/cfg/adm/img/del.png" width=11 height=10></a></td>
	</tr>';
	}



?>
