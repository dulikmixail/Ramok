<?


#домены

$a = $_REQUEST['a'];
$delete = $_REQUEST['delete'];


if($delete){
	$query = mysql_query("DELETE FROM tbl_domain WHERE id='" .$delete. "'");
	$notify = 'Категория удалена<br><br>';
	}


if($a=='add'){
	$name = $_REQUEST['name'];
	$position = $_REQUEST['position'];
	$price_rur = $_REQUEST['price_rur'];
	$price_usd = $_REQUEST['price_usd'];	
	$id_category = $_REQUEST['id_category'];	
	$query = mysql_query("INSERT INTO tbl_domain SET name='" .$name. "', id_category='" .$id_category. "', sortorder='" .$position. "', price_rur='" .$price_rur. "', price_usd='" .$price_usd. "'") or die(mysql_error());
	$notify = 'Запись добавлена<br><br>';
	}


if($a=='save'){
	if($_POST["position"]){
		while(list($id_cat, $val)=each($_POST["position"])){
			$query = mysql_query("UPDATE tbl_domain SET sortorder='" .$val. "', name='" .$_POST["name"][$id_cat]. "', price_rur='" .$_POST["price_rur"][$id_cat]. "', price_usd='" .$_POST["price_usd"][$id_cat]. "' WHERE id='" .$id_cat. "'");
			}
		$notify = 'Записи обновлены<br><br>';
		}
	}

$query2 = mysql_query("SELECT * FROM tbl_category_domain ORDER BY sortorder");
while($result2 = mysql_fetch_array($query2)){
	$query = mysql_query("SELECT * FROM tbl_domain WHERE id_category='" .$result2['id']. "' ORDER BY sortorder");
	$i=1;
	if(mysql_num_rows($query)){
		$list_domain .= '<tr><td colspan="5"><strong>' .$result2['name']. '</strong></td></tr>';
		while($result = mysql_fetch_array($query)){
			if($i%2){$cls=" class=TableRow1";}else{$cls=" class=TableRow2";}
			$i++;
			$list_domain .= '
			<tr height=20 ' .$cls. '>
				<td><input type="text" name="name[' .$result['id']. ']" value="' .$result['name']. '"  class="txt"></td>
				<td><input type="text" style="width:50px" name="price_rur[' .$result['id']. ']" value="' .$result['price_rur']. '"  class="txt"></td>
				<td><input type="text" style="width:50px" name="price_usd[' .$result['id']. ']" value="' .$result['price_usd']. '"  class="txt"></td>		
				<td align="center"><input type="text" style="width:50px" name="position[' .$result['id']. ']" value="' .$result['sortorder']. '"  class="txt"></td>	
				<td align="center" ' .$cls. '><a class=Table href="/adm/domain?delete=' .$result['id']. '" onClick="return confirm(\'Вы уверены, что хотите удалить категорию?\')"><img border="0" title="Удалить" src="/cfg/adm/img/del.png" width=11 height=10></a></td>
			</tr>';
			}
		}
	}


#Достаем категории
$query = mysql_query("SELECT * FROM tbl_category_domain ORDER BY sortorder");
while($result = mysql_fetch_array($query)){
	$list_category .= '<option value="' .$result['id']. '">' .$result['name']. '</option>';
	}

?>
