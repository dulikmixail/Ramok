<?


if(!$_SESSION['user_id']){
header('Location: /client/enter');
exit();
}


$show = $_REQUEST['show'];
$id_user = $_REQUEST['id_user'];

#
#Показываем пользователей
#

if($show=='user'){
	$search_content = $_REQUEST['search_content'];
	$user = $_REQUEST['user'];
	$email = $_REQUEST['email'];
	$page = $_REQUEST['page'];

	$where .= " AND account_type<>2 ";	

	if($email){
		$where .= " AND email ='" .$email. "' ";	
		}	
	if($user){
		$where .= " AND id='" .$user. "' ";	
		}
	if($search_content){ 
		$where .= " AND (name LIKE '%" .$search_content. "%' OR company LIKE '%" .$search_content. "%' OR login LIKE '%" .$search_content. "%' OR phone LIKE '%" .$search_content. "%' OR unn LIKE '%" .$search_content. "%') ";	
		}
	
	
	if($where){
		$where = trim($where, ' AND');
		$where = ' WHERE ' .$where;
		}


	#Достаем количество
	$count = mysql_query("SELECT COUNT(id) AS count_user FROM tbl_user " .$where. "");
	$count_user = mysql_result($count, '0', 'count_user');
	$maxpage = ceil($count_user/100);
	if($page>$maxpage){$page=$maxpage;}
	if($page<1){$page=1;}
	$start = ($page-1)*100;
	
	#Генерируем списочек страниц
	$i=1;
	while($i<=$maxpage){
		if($i!=$page){
			$listpages .= ' [<a href="?show=user&page=' .$i. '&user=' .$user. '&search_content=' .$search_content. '&email=' .$email. '">' .$i. '</a>] ';
			}
		else{
			$listpages .= ' <strong>-' .$i. '-</strong> ';
			}
		$i++;
		}
	if($listpages){
		$listpages = '<br>Страницы: '.$listpages;
		}
		
	#Достаем пользователей
	$query = mysql_query("SELECT * FROM tbl_user " .$where. " ORDER BY date_register DESC LIMIT " .$start. ", 100");
	
	$i=1;
	while($result = mysql_fetch_array($query)){
		if($i%2){$cls=' class="TableRow1 hrefrow"';}else{$cls=' class="TableRow2 hrefrow"';}
		$date_add = date("d.m.Y", strtotime($result['date_add']));
		if(!$result['id_user']){
			$result['id_user']='нет';
			}
		if(!$result['city']){$result['city']='не указано';}
		if(!$result['name1']){$result['name1']='не указано';}
		$list_user .= '
		 <tr ' .$cls. ' href="?show=userdetails&id=' .$result['id']. '&page=' .$page. '&user=' .$user. '&search_content=' .$search_content. '" height="20">
		  <td width=20%>' .$result['id']. '</td>
		  <td width=80%>' .$result['name']. '</td>
		  <td width=20%>' .$result['company']. '</td>
		 </tr>	
		';
		$i++;
		}
	
	if(!$list_user){
		$list_user = '
		<tr height="20">
			<td align="center" colspan="6">Не найдено пользователей</td>
		</tr>';
		}
	
	$list_user = '
	<table border=0 cellpadding=0 cellspacing=2 width=100%>
	<tr>
		<td><strong>ID пользователя</strong></td>
		<td><strong>Имя</strong></td>
        <td><strong>Компания</strong></td>
	</tr>

	' .$list_user. '

	</table>';
	
	
	}


if($show=='userdetails'){
	$id = $_REQUEST['id'];
	$search_content = $_REQUEST['search_content'];
	$user = $_REQUEST['user'];
	$email = $_REQUEST['email'];
	$page = $_REQUEST['page'];

	$query = mysql_query("SELECT * FROM tbl_user WHERE id='" .$id. "'");
	$result = mysql_fetch_array($query);
	$name = $result['name'];
	$nick = $result['login'];
	$pass = $result['pass'];
	
	$unn = $result['unn'];
	$dogovor = $result['dogovor'];
	$unn = $result['unn'];
	$company = $result['company'];

	$adress = $result['adress'];
	$bank_code = $result['bank_code'];
	$bank_name = $result['bank_name'];
	$bank_adress = $result['bank_adress'];
	$bank_schet = $result['bank_schet'];
	
	
	$city = $result['city'];
	$phone = $result['phone'];
	$email = $result['email'];
	$date_add = $result['date_register'];
	$last_visit = $result['last_date'];
	$work = $result['work'];
	$special = $result['special'];
	$study = $result['study'];
	
	

	$count = mysql_query("SELECT  COUNT(*) AS count_rows FROM tbl_message WHERE id_user='" .$id. "'");
	$count_message = mysql_result($count, '0', 'count_rows');
	
	$count = mysql_query("SELECT COUNT(*) AS count_rows FROM tbl_orders WHERE id_user='" .$id. "'");
	$count_orders = mysql_result($count, '0', 'count_rows');
	
	}
	
	



#
#Диалоги
#

if($show=='message'){

	$page = $_REQUEST['page'];

	if($id_user){
		$where .= " AND id_user ='" .$id_user. "' ";	
		}	
	else{
		$where .= " AND id_user <>'' ";	
		}
	if($where){
		$where = trim($where, ' AND');
		$where = ' WHERE ' .$where;
		}

	#Достаем количество
	$count = mysql_query("SELECT id_user FROM tbl_message " .$where. " GROUP BY id_user");
	$count_user = mysql_num_rows($count);
	
	$maxpage = ceil($count_user/50);
	if($page>$maxpage){$page=$maxpage;}
	if($page<1){$page=1;}
	$start = ($page-1)*50;
	
	#Генерируем списочек страниц
	$i=1;
	while($i<=$maxpage){
		if($i!=$page){
			$listpages .= ' [<a href="?show=message&page=' .$i. '">' .$i. '</a>] ';
			}
		else{
			$listpages .= ' <strong>-' .$i. '-</strong> ';
			}
		$i++;
		}
	if($listpages){
		$listpages = '<br>Страницы: '.$listpages;
		}


	$query = mysql_query("SELECT *, COUNT(*) AS crows,
		(SELECT company FROM tbl_user WHERE tbl_user.id=tbl_message.id_user) AS company_name,
		(SELECT name FROM tbl_user WHERE tbl_user.id=tbl_message.id_user) AS name
		FROM tbl_message " .$where. " GROUP BY id_user ORDER BY date_add DESC LIMIT " .$start. ", 50");
	$i=1;
	while($result = mysql_fetch_array($query)){
		if($i%2){$cls=' class="TableRow1 hrefrow"';}else{$cls=' class="TableRow2 hrefrow"';}	
		$list_message .= '
		<tr ' .$cls. ' href="?show=messageshow&id=' .$result['id_user']. '&page=' .$page. '">
			<td>' .$result['company_name']. '</td>
			<td>' .$result['name']. '</td>
			<td align="center">' .$result['crows']. '</td>
			<td align="center">' .date("H:i:s",strtotime($result['date_add'])). '<br>' .date("d.m.Y",strtotime($result['date_add'])). '</td>
		</tr>
		';
		$i++;
		}
	
	
	}



if($show=='messageshow'){

	$page = $_REQUEST['page'];
	$id = $_REQUEST['id'];
	$a = $_REQUEST['a'];

	if($a=='sendmessage'){
		$text = mysql_real_escape_string($_REQUEST['text']);
		$text = nl2br($text);
		if($text){
			mysql_query("INSERT INTO tbl_message SET id_user='" .$id. "', id_admin='" .$_SESSION['user_id']. "', text='" .$text. "', date_add='" .date("Y-m-d H:i:s"). "'");
			$messagenotify = '<strong>Спасибо! Ваше сообщение отправлено.</strong><br>';
			}
		}

	$query = mysql_query("SELECT * FROM tbl_user WHERE id='" .$id. "'");
	
	$result = mysql_fetch_array($query);

	$company_name = $result['company'];
	$fio = $result['name'];
	$adress = $result['city'];
	$phone = $result['phone'];
	$email = $result['email'];

	$adress = $result['adress'];
	$bank_code = $result['bank_code'];
	$bank_name = $result['bank_name'];
	$bank_adress = $result['bank_adress'];
	$bank_schet = $result['bank_schet'];

	
	$list_message = '';
	$query = mysql_query("SELECT *,
	(SELECT name FROM tbl_user WHERE tbl_user.id=tbl_message.id_admin) AS admin_name 
	FROM tbl_message WHERE id_user='" .$id. "' ORDER BY date_add DESC");
	while($result=mysql_fetch_array($query)){
		if($result['id_admin']){
			$list_message .= '<div style="background-color:#ddd; padding:5px; margin-bottom:5px; margin-top:5px;"><strong>' .$result['admin_name']. '</strong> (' .date("d.m.Y H:i:s",strtotime($result['date_add'])). ')</div>';
			}
		else{
			$list_message .= '<div style="background-color:#ddd; padding:5px; margin-bottom:5px; margin-top:5px;"><strong>' .$fio. '</strong> (' .date("d.m.Y H:i:s",strtotime($result['date_add'])). ') </div>';
			}
		$list_message .= '
		' .$result['text']. '<br><br>
		';
		}
	if(!$list_message){
		$list_message = 'В истории пока нет сообщений...';
		}

	
	}




#
#Анкеты
#

if($show=='anketa'){
	
	$query = mysql_query("SELECT *,
		(SELECT name FROM tbl_user WHERE tbl_user.id=tbl_anketa.id_user) AS user_name,
		(SELECT company FROM tbl_user WHERE tbl_user.id=tbl_anketa.id_user) AS company_name
		FROM tbl_anketa ORDER BY id DESC");
	$i=1;
	while($result = mysql_fetch_array($query)){
		if($i%2){$cls=' class="TableRow1 hrefrow"';}else{$cls=' class="TableRow2 hrefrow"';}	
		$ball = 0;
		$ball = $result['qw131']+$result['qw132']+$result['qw133']+$result['qw134']+$result['qw135']+$result['qw136'];
		$list_anketa .= '
		<tr ' .$cls. ' href="?show=anketadetails&id=' .$result['id']. '">
			<td>' .$result['company_name']. '</td>
			<td>' .$result['user_name']. '</td>
			<td align="center">' .$ball. '</td>
			<td align="center">' .date("H:i:s",strtotime($result['date_add'])). '<br>' .date("d.m.Y",strtotime($result['date_add'])). '</td>
		</tr>	
		';
		$i++;
		}
	
	}

if($show=='anketadetails'){
	$id = $_REQUEST['id'];
	
	$query = mysql_query("SELECT *,
	(SELECT name FROM tbl_user WHERE tbl_user.id=tbl_anketa.id_user) AS user_name,
	(SELECT company FROM tbl_user WHERE tbl_user.id=tbl_anketa.id_user) AS company_name,
	(SELECT email FROM tbl_user WHERE tbl_user.id=tbl_anketa.id_user) AS user_email,
	(SELECT phone FROM tbl_user WHERE tbl_user.id=tbl_anketa.id_user) AS user_phone	
	FROM tbl_anketa WHERE id='" .$id. "'");
	
	$result = mysql_fetch_array($query);

	$ball = 0;
	$ball = $result['qw131']+$result['qw132']+$result['qw133']+$result['qw134']+$result['qw135']+$result['qw136'];
	$date_add = date("d.m.Y H:i:s",strtotime($result['date_add']));

	$user_name = $result['user_name'];
	$company_name = $result['company_name'];
	$user_email = $result['user_email'];
	$user_phone = $result['user_phone'];


	$qw1 = $result['qw1'];
	$qw1_add = $result['qw1_add'];
	$qw2 = $result['qw2'];
	$qw3 = $result['qw3'];
	$qw3_add = $result['qw3_add'];
	$qw4 = $result['qw4'];
	$qw4_add = $result['qw4_add'];
	$qw5 = $result['qw5'];
	$qw5_add = $result['qw5_add'];
	$qw6 = $result['qw6'];
	$qw7 = $result['qw7'];
	$qw8 = $result['qw8'];
	$qw91 = $result['qw91'];
	$qw92 = $result['qw92'];
	$qw93 = $result['qw93'];
	$qw94 = $result['qw94'];
	$qw95 = $result['qw95'];
	$qw9_add = $result['qw9_add'];
	$qw10 = $result['qw10'];
	$qw11 = $result['qw11'];
	$qw12 = $result['qw12'];
	$qw131 = $result['qw131'];
	$qw132 = $result['qw132'];
	$qw133 = $result['qw133'];
	$qw134 = $result['qw134'];
	$qw135 = $result['qw135'];
	$qw136 = $result['qw136'];
	$qw14 = $result['qw14'];
	$qw15 = $result['qw15'];
	
	}



#
#Заказы
#

if($show=='order'){

	$page = $_REQUEST['page'];

	if($id_user){
		$where .= " AND id_user ='" .$id_user. "' ";	
		}	
	
	if($where){
		$where = trim($where, ' AND');
		$where = ' WHERE ' .$where;
		}


	
	#Достаем количество новостей
	$count = mysql_query("SELECT COUNT(*) AS count_rows FROM tbl_orders " .$where. "");
	$count_rows = mysql_result($count, '0', 'count_rows');
	$maxpage = ceil($count_rows/20);
	if($page>$maxpage){$page=$maxpage;}
	if($page<1){$page=1;}
	$start = ($page-1)*20;
	
	#Генерируем списочек страниц
	$i=1;
	while($i<=$maxpage){
		if($i!=$page){
			$listpages .= ' [<a href="?show=order&page=' .$i. '&id_category=' .$id_category. '">' .$i. '</a>] ';
			}
		else{
			$listpages .= ' <strong>-' .$i. '-</strong> ';
			}
		$i++;
		}
	
	
	#Достаем новости
	$query = mysql_query("SELECT *,
			(SELECT unn FROM tbl_user WHERE tbl_user.id=tbl_orders.id_user) AS company_unn,
			(SELECT company FROM tbl_user WHERE tbl_user.id=tbl_orders.id_user) AS company_name,
			(SELECT name FROM tbl_user WHERE tbl_user.id=tbl_orders.id_user) AS company_username,
			(SELECT phone FROM tbl_user WHERE tbl_user.id=tbl_orders.id_user) AS company_phone
			FROM tbl_orders " .$where. " ORDER BY date_add DESC LIMIT " .$start. ", 20");
	$i=1;
	while($result = mysql_fetch_array($query)){
		#if($i%2){$cls=" class=TableRow1";}else{$cls=" class=TableRow2";}
		$cls=" class=TableRow2";
		$date_add = date("d.m.Y", strtotime($result['date_add']));
	
		if($result['status']==1){
			$status = 'оплачен';
			}
		else{
			$status = 'не оплачен <a target="_blank" class="agrey2" href="/my/pay?id=' .$result['id']. '">договор</a> | <a style="color:red" class="agrey2" href="?delorder=' .$result['id']. '">удалить</a>';
			}
		$content_orders .= '<tr height="50"><td colspan="5"><b>Заказ №' .$result['id']. ' от ' .$date_add. '</b> (' .$status. ')</td></tr>';
		
		$total_summ = 0;
		$total_count = 0;
		
		$content_orders = '
		<table width="100%" class="details' .$result['id']. '" style="display:none">
		<tr>
			<td width="40%"><strong>Название</strong></td>
			<td align="center"><strong>Стоимость</strong></td>
			<td align="center"><strong>Количество</strong></td>
			<td align="center"><strong>Сумма</strong></td>
		</tr>	
		';
		$query2 =  mysql_query("SELECT *,
		(SELECT tbl_products.name FROM tbl_products WHERE tbl_products.id=tbl_orders_details.id_product) AS product_name FROM tbl_orders_details WHERE id_order='" .$result['id']. "'");
		while($result2 = mysql_fetch_array($query2)){
			$total_summ += $result2['total_price'];
			$total_count += $result2['product_count'];
			$content_orders .= '
			<tr>
				<td>' .$result2['product_name']. '</td>
				<td align="center">' .$result2['price']. ' р.</td>
				<td align="center">' .$result2['product_count']. '</td>
				<td align="center">' .$result2['total_price']. ' р.</td>							
			</tr>
			';
			}
		$content_orders .= '
		<tr>
				<td><b>Всего<b></td>
				<td align="center"></td>
				<td align="center"><strong>' .$total_count. '</strong></td>
				<td align="center"><strong>' .$total_summ. ' р.</strong></td>
				<td align="center"></td>								
		</tr></table>';
	
		$list_order .= '
		<tr>
			<td colspan="5">
				Компания: <strong>' .$result['company_name']. '</strong> (<a href="manager?show=userdetails&id=' .$result['id_user']. '">информация о компании</a>)<br>
				Контактное лицо: <strong>' .$result['company_username']. '</strong><br>
				Телефон,: ' .$result['company_phone']. '<br>
			</td>
		</tr>
		<tr ' .$cls. ' height=20 style="cursor:pointer" onClick="showdetails(' .$result['id']. ')">
			<td>' .$date_add. '</td>
			<td align="center">' .$result['company_unn']. '</td>
			<td align="center">' .$total_summ. '</td>
			<td align="center">' .$total_summ. '</td>
			<td align="center">Сайт</td>
		</tr><tr><td colspan="5">' .$content_orders. '</td></tr>
		<tr>
			<td colspan="5"><hr><a target="_blank" href="/my/pay?id=' .$result['id']. '">Сформировать договор</a><br><br><br></td>
		</tr>
		';
	
	
		$i++;
		}
	
	if(!$list_order){
		$list_order = '<tr height=20><td colspan="4" align="center">Не найдено заказов</td></tr>';
		}
	
	}
	
	
	
if($show=='settings'){
	$query = mysql_query("select * from tbl_user where id='" .$_SESSION['user_id']. "'");
	$result = mysql_fetch_array($query);
	$name = stripslashes($result['name']);
	$email = $result['email'];
	$phone = $result['phone'];
	}
	
	
if($a=='editdetails'){
	$name = $_REQUEST['name'];
	$pass = $_REQUEST['password'];
	$phone = $_REQUEST['phone'];
	$email = $_REQUEST['email'];

	$query = mysql_query("UPDATE tbl_user SET name='" .$name. "', phone='" .$phone. "', email='" .$email. "' where id='" .$_SESSION['user_id']. "'");

	if($pass){
		$query = mysql_query("UPDATE tbl_user SET pass='" .$pass. "' where id='" .$_SESSION['user_id']. "'");
		}

	$detailsnotify = 'Данные сохранены<br>';
	
	}


?>