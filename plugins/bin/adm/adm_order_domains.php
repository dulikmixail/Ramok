<?


#Заказы доменов

$query = mysql_query("SELECT * FROM tbl_order_domain ORDER BY is_payed, date_end DESC");
$i=1;
while($result = mysql_fetch_array($query)){
	if($i%2){$cls=" class=TableRow1";}else{$cls=" class=TableRow2";}
	if($result['is_payed']==1){
		$is_payed='оплачен';
		$date_end = date("d.m.Y", strtotime($result['date_end']));
		}
	else{
		$is_payed='<strong>не оплачен</strong>';
		$date_end = 'нет';
		}
	$date_add = date("d.m.Y H:i:s", strtotime($result['date_add']));
	
	$list_domain .= '
	<tr ' .$cls. '>
		<td>' .$result['domain_name']. '</td>
		<td align="center">' .$is_payed. '</td>
		<td align="center">' .$date_add. '</td>
		<td align="center">' .$date_end. '</td>
		<td align="center" ' .$cls. ' nowrap><a class=table href="/adm/order/domains/edit?id=' .$result['id']. '"><img border="0" title="Редактировать" src="/cfg/adm/img/edit.png" width=11 height=11></a></td>
		<td align="center" ' .$cls. ' nowrap><a class=Table href="/adm/order/domains?delete=' .$result['id']. '&page=' .$page. '" onClick="return confirm(\'Вы уверены, что хотите удалить?\')"><img border="0" title="Удалить" src="/cfg/adm/img/del.png" width=11 height=10></a></td>
	</tr>
	';
	$i++;
	}






?>
