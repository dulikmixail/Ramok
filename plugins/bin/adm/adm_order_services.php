<?


#Заказы услуг

$a = $_REQUEST['a'];
$id = $_REQUEST['id'];

if($a=='delete'){
	$query = mysql_query("DELETE FROM tbl_catorder WHERE id='" .$id. "'");
	$query = mysql_query("DELETE FROM tbl_catparams WHERE id_order='" .$id. "'");
	header('Location: /adm/order/services');
	exit();
	}


$query = mysql_query("SELECT *,
	(SELECT name FROM tbl_services WHERE tbl_services.id=tbl_catorder.id_category) AS cat_name FROM tbl_catorder WHERE is_ready='0' ORDER BY date_add DESC");
$i=1;
while($result = mysql_fetch_array($query)){
	if($i%2){$cls=" class=TableRow1";}else{$cls=" class=TableRow2";}
	$date_add = date("d.m.Y H:i:s", strtotime($result['date_add']));
	$list_order .= '
	<tr ' .$cls. ' height="20">
		<td>' .$date_add. '</td>
		<td>' .$result['cat_name']. '</td>
		<td>' .$result['user_name']. '</td>
		<td align="center" ' .$cls. ' nowrap><a class=table href="/adm/order/services/edit?id=' .$result['id']. '"><img border="0" title="Редактировать" src="/cfg/adm/img/edit.png" width=11 height=11></a></td>
		<td align="center" ' .$cls. ' nowrap><a class=Table href="/adm/order/services?a=delete&id=' .$result['id']. '" onClick="return confirm(\'Вы уверены, что хотите удалить заказ?\')"><img border="0" title="Удалить" src="/cfg/adm/img/del.png" width=11 height=10></a></td>
	</tr>
	';
	$i++;
	}







?>
