<?


#Работы портфолио


$delete = $_REQUEST['delete'];
$a = $_REQUEST['a'];
$id_category = $_REQUEST['id_category'];
$page = $_REQUEST['page'];

$_SESSION['backurl_workadd'] = $_SERVER['REQUEST_URI'];


if($a=='edit'){
	if($_POST["position"]){
		while(list($id_cat, $val)=each($_POST["position"])){
			$query = mysql_query("UPDATE tbl_portfolio SET position='" .$val. "' WHERE id='" .$id_cat. "'");
			}
		$notify = 'Записи обновлены<br>';
		}
	}


if($delete){
	#Удаляем картинки
	$query = mysql_query("SELECT img, imgmain FROM tbl_portfolio WHERE id='" .$delete. "'");
	$img = mysql_result($query, '0', 'img');
	$imgmain = mysql_result($query, '0', 'imgmain');
	@unlink('../images/portfolio/' .$img);
	@unlink('../images/portfoliomain/' .$imgmain);
	
	$query = mysql_query("DELETE FROM tbl_portfolio WHERE id='" .$delete. "'");
	header('Location: /adm/portfolio/work?id_category='.$id_category);
	exit();
	}


if($id_category){
	$where = " id_category='" .$id_category. "' ";
	}
if($where){
	$where = trim($where,'AND');
	$where = ' WHERE '.$where;
	}


#Достаем количество новостей
$count = mysql_query("SELECT COUNT(id) AS count_rows FROM tbl_portfolio " .$where. "");
$count_rows = mysql_result($count, '0', 'count_rows');
$maxpage = ceil($count_rows/20);
if($page>$maxpage){$page=$maxpage;}
if($page<1){$page=1;}
$start = ($page-1)*20;

#Генерируем списочек страниц
$i=1;
while($i<=$maxpage){
	if($i!=$page){
		$listpages .= ' [<a href="/adm/portfolio/work?page=' .$i. '&id_category=' .$id_category. '">' .$i. '</a>] ';
		}
	else{
		$listpages .= ' <strong>-' .$i. '-</strong> ';
		}
	$i++;
	}


#Достаем новости
$query = mysql_query("SELECT * FROM tbl_portfolio " .$where. " ORDER BY date DESC LIMIT " .$start. ", 20");
$i=1;
while($result = mysql_fetch_array($query)){
	if($i%2){$cls=" class=TableRow1";}else{$cls=" class=TableRow2";}
	$date = date("d.m.Y", strtotime($result['date']));
	$list_work .= '
	 <tr height=20>
      <td ' .$cls. '>' .$date. '</td>
      <td ' .$cls. '>' .$result['name']. '</td>
	  <td align="center" ' .$cls. ' nowrap><a class=table href="/adm/portfolio/work/add?id=' .$result['id']. '"><img border="0" title="Редактировать" src="/cfg/adm/img/edit.png" width=11 height=11></a></td>
	  <td align="center" ' .$cls. ' nowrap><a class=Table href="/adm/portfolio/work?delete=' .$result['id']. '&page=' .$page. '" onClick="return confirm(\'Вы уверены, что хотите удалить?\')"><img border="0" title="Удалить" src="/cfg/adm/img/del.png" width=11 height=10></a></td>
	 </tr>	
	';
	$i++;
	}

if(!$list_work){
	$list_work = '<tr height=20><td colspan="4" align="center">Не найдено работ</td></tr>';
	}









?>
