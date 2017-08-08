<?

#Отзывы

$a = $_REQUEST['a'];
$id = $_REQUEST['id'];

if($a=='del'){
	$query = mysql_query("DELETE FROM tbl_voites WHERE id='" .$id. "'");
	header('Location: /adm/voites');
	exit();
	}


$query = mysql_query("SELECT * FROM tbl_voites ORDER BY is_show, date DESC");
$i=0;
while($result = mysql_fetch_array($query)){
	$i++;
	if($i%2){$cls=" class=TableRow1";}else{$cls=" class=TableRow2";}
	$date_add = date("d.m.Y", strtotime($result['date']));
	
	if(!$result['is_show']){
		$style='style="font-weight:bold"';
		}
	else{
		$style='';
		}
	
	$list_voites .= '
	<tr ' .$cls. ' height=20>
		<td ' .$style. ' nowrap>' .$result['name']. '</td>
		<td ' .$style. ' nowrap>' .$date_add. '</td>
		<td align="center" ' .$cls. ' nowrap><a class=table href="/adm/voites/add?id=' .$result['id']. '"><img border="0" title="Редактировать" src="/cfg/adm/img/edit.png" width=11 height=11></a></td>		
		<td align="center" nowrap><a class=Table href=/adm/voites?a=del&id=' .$result['id']. '" onClick="return confirm(\'Вы уверены, что хотите удалить?\')"><img border="0"  title="Удалить" src="/cfg/adm/img/del.png" width=11 height=10></a></td>	
	</tr>';	
	}

if(!$list_voites){
	$list_voites = '
	<tr class=TableRow1 height=20>
		<td colspan="4" align="center">Не добавлено ни одного отзыва</td>
	</tr>';
	}




?>
