<?

session_start();


$a = $_REQUEST['a'];
$id_rubr = $_REQUEST['rubr'];
$id = $_REQUEST['id'];
$id_product = $_REQUEST['id_product'];


$caption=$_REQUEST['caption'];
$query = mysql_query("SELECT * FROM tbl_napr WHERE caption='" .$caption. "'");
$result = mysql_fetch_array($query);
$id_napr = $result['ProductID'];


#Достаем товары презаказанные
if(!empty($_SESSION['factura'])){
	foreach($_SESSION['factura'] as $key => $value){
		$factura .= $value['href'] . '<br>';
		}
	}
else{
	$factura = 'Пока не добавлено ни одного товара...';
	}


	if($_COOKIE['sessid']){
		$query = "select id, is_diller, login, pass, name, last_date from tbl_user where session_id='" .$_COOKIE['sessid']. "'";
		$query = mysql_query($query);
		if(mysql_num_rows($query)){
			$login = @mysql_result($query, '0', 'login');
			$name = @mysql_result($query, '0', 'name');
			$id = @mysql_result($query, '0', 'id');
			$last_date = @mysql_result($query, '0', 'last_date');
			$is_diller = @mysql_result($query, '0', 'is_diller');
						
			$_SESSION['login'] = $login;
			$_SESSION['user_id'] = $id;
			$_SESSION['user_name'] = $name;
			$_SESSION['user_date'] = $last_date;
			$_SESSION['is_diller'] = $is_diller;
						
			$query = "update tbl_user set last_date='" .date('y-m-d H:i:s'). "' where login='" .$login. "'";
			$query = mysql_query($query);	
			}
		}

if(!$_SESSION['user_id']){
	$_SESSION['navigation'][date("d.m.Y H:i:s")]=$_SERVER['REQUEST_URI'];
	}


#Достаем теги страницы
$query = mysql_query("SELECT PageTitle, PageDescription,PageTitleSEO,PageKeywords,PageAuthor,PageRobots,PageContent FROM tbl_page WHERE PageID='" .$currentPage["params"]["idpage"]. "'");
$result = mysql_fetch_array($query);
$PageTitle = $result['PageTitle'];
$PageDescription = $result['PageDescription'];
$PageTitleSEO = $result['PageTitleSEO'];
$PageKeywords = $result['PageKeywords'];
$PageAuthor = $result['PageAuthor'];
$PageRobots = $result['PageRobots'];
$PageContent = $result['PageContent'];


if($a=='sociallogin'){
	$s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
	$user = json_decode($s, true);
	$user['first_name'] = iconv('utf8','cp1251',$user['first_name']);
	$user['last_name'] = iconv('utf8','cp1251',$user['last_name']);
	$user['city'] = iconv('utf8','cp1251',$user['city']);
	$name = $user['last_name'].' '.$user['first_name'];
	$city = $user['city'];
	$phone = $user['phone'];
	$email = $user['email'];
	
	$query = mysql_query("select * from tbl_user where network='" .$user['network']. "' AND uid='" .$user['uid']. "'");
	if(mysql_num_rows($query)){
		$login = @mysql_result($query, '0', 'login');
		$name = @mysql_result($query, '0', 'name');
		$id = @mysql_result($query, '0', 'id');
		$last_date = @mysql_result($query, '0', 'last_date');
		$is_diller = @mysql_result($query, '0', 'is_diller');
					
		$_SESSION['login'] = $login;
		$_SESSION['user_id'] = $id;
		$_SESSION['user_name'] = $name;
		$_SESSION['user_date'] = $last_date;
		$_SESSION['is_diller'] = $is_diller;
					
		$query = "update tbl_user set last_date='" .date('y-m-d H:i:s'). "' where login='" .$login. "'";
		$query = mysql_query($query);
	
		$rand_sess_id = rand(111111111,999999999);
		$query = mysql_query("UPDATE tbl_user SET session_id='" .$rand_sess_id. "' WHERE id='" .$_SESSION['user_id']. "'");
		setcookie('sessid', $rand_sess_id, time()+5184000, '/', 'ramok.by');
		}
	else{
			#Достаем последний ID
			$user_id = mysql_query("SELECT MAX(id) AS max_id FROM tbl_user");
			$user_id = mysql_result($user_id, '0', 'max_id');
			$user_id++;
			
			$login = 'user'.$user_id;
			$pass = round(rand(111111, 999999));

		if(!empty($_SESSION['navigation'])){
			foreach($_SESSION['navigation'] as $key => $value){
				$where_was .= '<a href="http://ramok.by'.$value.'">http://ramok.by'.$value.'</a> (' .$key. ')<br>';
				$where_was_text .= 'http://ramok.by' .$value.' (' .$key. ') ';
				}
			}

			$interes2 = $interes.'
IP адрес: ' .$_SERVER['REMOTE_ADDR']. '
Страницы, на которых он был: ' .where_was_text;

			#Сохраняем запись пользователя
			$query = mysql_query("INSERT INTO tbl_user SET id='" .$user_id. "', login='" .$login. "', pass='" .$pass. "', name='" .$name. "', city='" .$city. "', phone='" .$phone. "', email='" .$mail. "', date_register='" .$date_register. "', network='" .$user['network']. "', uid='" .$user['uid']. "', identity='" .$user['identity']. "', profile='" .$user['profile']. "'");
			#Сохраняем реквизиты
			$query = mysql_query("INSERT INTO tbl_user_req SET id_user='" .$user_id. "'");			
			
			$_SESSION['login'] = $login;
			$_SESSION['user_id'] = $user_id;
			$_SESSION['user_name'] = $name;
			$_SESSION['user_date'] = $date_register;	
			
			$guest_banner = '<img border="0" src="/cfg/baner_price.jpg" onclick="show_reg(1)" style="cursor:pointer">';
		
			#Теперь отправляем письмо о регистрации
			include '../includes/class.phpmailer.php';
			$m = new PHPMailer(true);
			$m->Priority = 1;
			$m->AddReplyTo('marketing@ramok.by', 'Сайт УП Рамок');	
			$m->AddAddress($email);
			$m->SetFrom('marketing@ramok.by', 'Сайт УП Рамок');
			$m->Subject = 'Регистрация клиента на сайте Ramok.by';
			$m->Body = 'Здравствуйте!<br>
			Спасибо за регистрацию на сайте УП "Рамок" (<a href="http://ramok.by">www.ramok.by</a>)!<br>
			<br>
			Ваш логин: <strong>' .$login. '</strong><br>
			Ваш пароль: <strong>' .$pass. '</strong><br>
			<br>
			Используя указанные данные, Вы всегда можете зайти на сайт компании, ознакомиться с продукцией, сделать предварительный заказ или заявку на расчет работ.';
			$m->Send();	

			#отправляем админу письмо с данными юзера
			$date_add = date('d.m.Y H:i:s');  
			$m = new PHPMailer(true);
			$m->Priority = '1';
			$m->AddReplyTo('marketing@ramok.by', 'Сайт УП Рамок');	
			$m->AddAddress('marketing@ramok.by');
			$m->AddAddress('upramok@yandex.ru');
			$m->SetFrom('marketing@ramok.by', 'Сайт УП Рамок');
			$m->Subject = 'Регистрация клиента на сайте Ramok.by';
			$m->Body = 'Здравствуйте!<br><br>
			На сайте зарегистрировался посетитель через социальную сеть <strong>' .$date_add. '</strong><br>
			<br>
			Контактное лицо: <strong>' .$name. '</strong><br>
			Город: <strong>' .$city. '</strong><br>
			Телефон: <strong>' .$phone. '</strong><br>
			E-mail: <strong>' .$mail. '</strong><br>
			Социальная сеть: <strong>' .$user['network']. '</strong><br>
			uid соцсети: <strong>' .$user['uid']. '</strong><br>
			Профиль: <strong><a href="' .$user['profile']. '">' .$user['profile']. '</a></strong><br>
			
			<br>
			IP адрес: <strong>' .$_SERVER['REMOTE_ADDR']. '</strong><br>
			Страницы, на которых он был: <br>' .$where_was. '<br>
			';
			$m->Send();	
			
			}
		
	}

if($a=='send_order'){
	$f_name = $_REQUEST['f_company_name'];
	$f_contact = $_REQUEST['f_contact'];
	$f_city = $_REQUEST['f_city'];
	$f_phone = $_REQUEST['f_phone'];
	$f_mail = $_REQUEST['f_mail'];
	$f_interes = $_REQUEST['f_interes'];	
	include('../plugins/captcha.php');
	
	$success=$captchaSupport->processCheck($currentSession,"captcha"); 
	
	if(!$success){
		$error .= '<br>Вы ввели неправильную капчу';
		}
	
	if(!eregi("^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.[a-zA-Z]{2,4}$", $f_mail)){
		$error .= '<br>Вы ввели неправильный E-mail';
		}
	if(!$f_name||!$f_city||!$f_phone||!$f_mail||!$f_interes||!!$error){
		$error .= '<br>Вы не заполнили все необходимые поля';
		}
	if($f_mail){
		$query = mysql_query("SELECT id FROM tbl_user WHERE email='" .$f_mail. "'");
		if(mysql_num_rows($query)){
			$error .= '<br>Такой email уже используется';
			}
		}

	if($success){		
		if(!$error){
			
			$date_register = date('y-m-d H:i:s');  
			
			#Достаем последний ID
			#$user_id = mysql_query("SELECT MAX(id) AS max_id FROM tbl_user");
			#$user_id = mysql_result($user_id, '0', 'max_id');
			#$user_id++;
			
			#$login = 'user'.$user_id;
			#$pass = round(rand(111111, 999999));
						
			#$_SESSION['login'] = $login;
			#$_SESSION['user_id'] = $user_id;
			#$_SESSION['user_name'] = $name;
			#$_SESSION['user_date'] = $date_register;	

			#Теперь отправляем письмо о регистрации
			#include '../includes/class.phpmailer.php';
			#$m = new PHPMailer(true);
			#$m->Priority = 1;
			#$m->AddReplyTo('www@ramok.by', 'Сайт УП Рамок');	
			#$m->AddAddress($f_mail);
			#$m->SetFrom('www@ramok.by', 'Сайт УП Рамок');
			#$m->Subject = 'Регистрация клиента на сайте Ramok.by';
			#$m->Body = 'Здравствуйте!<br>
			#Спасибо за регистрацию на сайте УП "Рамок" (<a href="http://ramok.by">www.ramok.by</a>)!<br>
			#<br>
			#Ваш логин: <strong>' .$login. '</strong><br>
			#Ваш пароль: <strong>' .$pass. '</strong><br>
			#<br>
			#Используя указанные данные, Вы всегда можете зайти на сайт компании, ознакомиться с продукцией, сделать предварительный заказ или заявку на расчет работ.';
			#$m->Send();	

			
			#Достаем наименование товара
			if($id_rubr){
				$query = mysql_query("SELECT name FROM tbl_catalog WHERE id='" .$id_rubr. "'");
				$order_prodname = @mysql_result($query, '0', 'name');;
				}
			elseif($id_product){
				$query = mysql_query("SELECT name FROM tbl_products WHERE id='" .$id_product. "'");
				$order_prodname = @mysql_result($query, '0', 'name');;
				}

		if(!empty($_SESSION['navigation'])){
			foreach($_SESSION['navigation'] as $key => $value){
				$where_was .= '<a href="http://ramok.by'.$value.'">http://ramok.by'.$value.'</a> (' .$key. ')<br>';
				$where_was_text .= 'http://ramok.by' .$value.' (' .$key. ') ';
				}
			}

			
			$f_interes2 = $f_interes.'
'.$order_prodname.'IP адрес: ' .$_SERVER['REMOTE_ADDR']. '
Страницы, на которых он был: ' .$where_was_text;

			#Сохраняем запись пользователя
			#$query = mysql_query("INSERT INTO tbl_user SET id='" .$user_id. "', login='" .$login. "', pass='" .$pass. "', name='" .$f_contact. "', company='" .$f_name. "', city='" .$f_city. "',
			#phone='" .$f_phone. "', email='" .$f_mail. "', interes='" .$f_interes2. "', date_register='" .$date_register. "'");
			#Сохраняем реквизиты
			#$query = mysql_query("INSERT INTO tbl_user_req SET id_user='" .$user_id. "'");

			//$query = mysql_query("INSERT INTO tbl_message SET id_user='" .$user_id. "', text='" .$order_prodname. "', date_add='" .$date_register. "'");
				
				
			#отправляем админу письмо с данными юзера
			$date_add = date('d.m.Y H:i:s');
            include '../includes/class.phpmailer.php';
			$m = new PHPMailer(true);
			$m->Priority = '1';
			$m->AddReplyTo('marketing@ramok.by', 'Сайт УП Рамок');
//			$m->AddAddress('marketing@ramok.by');
//			$m->AddAddress('dulik@ramok.by');
			$m->AddAddress('upramok@yandex.ru');
			$m->SetFrom('marketing@ramok.by', 'Сайт УП Рамок');
			$m->Subject = 'Предварительный заказ с сайта Ramok.by';
			$m->Body = 'Здравствуйте!<br><br>
			На сайте был оставлен предварительный заказ на "<strong>' .$order_prodname. '</strong>" от <strong>' .$date_add. '</strong><br>
			<br>
			Название компании: <strong>' .$f_name. '</strong><br>
			Контактное лицо: <strong>' .$f_contact. '</strong><br>
			Город: <strong>' .$f_city. '</strong><br>
			Телефон: <strong>' .$f_phone. '</strong><br>
			E-mail: <strong>' .$f_mail. '</strong><br>
			Комментарий к заявке: <strong>' .$f_interes. '</strong><br>
			<br>
			IP адрес: <strong>' .$_SERVER['REMOTE_ADDR']. '</strong><br>
                Страницы, на которых он был: <br>' .$where_was. '<br>
			';
			$m->Send();

			#Сохраняем данные для SEO анализа
            $query = mysql_query("INSERT INTO tbl_message_seo SET order_prodname='" .$order_prodname. "', date_add='" .$date_add. "', name='" .$f_name. "',contact='" .$f_contact. "',city='" .$f_city. "',phone='" .$f_phone. "',mail='" .$f_mail. "',interes='" .$f_interes. "', where_was='" .$where_was. "'");

			$f_name = '';
			$f_contact = '';
			$f_city = '';
			$f_phone = '';
			$f_mail = '';
			$f_interes = '';	
			$notify = '<br>Спасибо! Заявка успешно отправлена!';
			
			}
		}

	}

$save_guest = $_REQUEST['save_guest'];
if($save_guest==1){
	#Проверяем правильность капчи
	$reg_type = $_REQUEST['reg_type'];
	$name = $_REQUEST['company_name'];
	$contact = $_REQUEST['contact'];
	$city = $_REQUEST['city'];
	$phone = $_REQUEST['phone'];
	$mail = $_REQUEST['mail'];
	$interes = $_REQUEST['interes'];	
	include('../plugins/captcha.php');
	$success=$captchaSupport->processCheck($currentSession,"captcha"); 
	
	if(!$success){
		$error .= '<br>Вы ввели неправильную капчу';
		}
	
	if(!eregi("^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.[a-zA-Z]{2,4}$", $mail)){
		$error .= '<br>Вы ввели неправильный E-mail';
		}
	
	if(!$name||!$city||!$phone||!$mail||!$interes||!!$error){
		$error .= '<br>Вы не заполнили все необходимые поля';
		}

	if($f_mail){
		$query = mysql_query("SELECT id FROM tbl_user WHERE email='" .$f_mail. "'");
		if(mysql_num_rows($query)){
			$error .= '<br>Такой email уже используется';
			}
		}

	if($success){
		$date_register = date('y-m-d H:i:s');  
		
		if(!$error){
			#Достаем последний ID
			#$user_id = mysql_query("SELECT MAX(id) AS max_id FROM tbl_user");
			#$user_id = mysql_result($user_id, '0', 'max_id');
			#$user_id++;
			
			#$login = 'user'.$user_id;
			#$pass = round(rand(111111, 999999));

		if(!empty($_SESSION['navigation'])){
			foreach($_SESSION['navigation'] as $key => $value){
				$where_was .= '<a href="http://ramok.by'.$value.'">http://ramok.by'.$value.'</a> (' .$key. ')<br>';
				$where_was_text .= 'http://ramok.by' .$value.' (' .$key. ') ';
				}
			}

			$interes2 = $interes.'
IP адрес: ' .$_SERVER['REMOTE_ADDR']. '
Страницы, на которых он был: ' .where_was_text;

			#Сохраняем запись пользователя
			#$query = mysql_query("INSERT INTO tbl_user SET id='" .$user_id. "', login='" .$login. "', pass='" .$pass. "', name='" .$contact. "', company='" .$name. "', city='" .$city. "', phone='" .$phone. "', email='" .$mail. "', interes='" .$interes. "', date_register='" .$date_register. "'");
			#Сохраняем реквизиты
			#$query = mysql_query("INSERT INTO tbl_user_req SET id_user='" .$user_id. "'");

			//$query = mysql_query("INSERT INTO tbl_message SET id_user='" .$user_id. "', text='" .$interes. "', date_add='" .$date_register. "'");
			
			#$_SESSION['login'] = $login;
			#$_SESSION['user_id'] = $user_id;
			#$_SESSION['user_name'] = $name;
			#$_SESSION['user_date'] = $date_register;	
			
			$guest_banner = '<img border="0" src="/cfg/baner_price.jpg" onclick="show_reg(1)" style="cursor:pointer">';
		
			#Теперь отправляем письмо о регистрации
			#include '../includes/class.phpmailer.php';
			#$m = new PHPMailer(true);
			#$m->Priority = 1;
			#$m->AddReplyTo('www@ramok.by', 'Сайт УП Рамок');	
			//$m->AddAddress($mail);
			#$m->SetFrom('www@ramok.by', 'Сайт УП Рамок');
			#$m->Subject = 'Регистрация клиента на сайте Ramok.by';
			#$m->Body = 'Здравствуйте!<br>
			#Спасибо за регистрацию на сайте УП "Рамок" (<a href="http://ramok.by">www.ramok.by</a>)!<br>
			#<br>
			#Ваш логин: <strong>' .$login. '</strong><br>
			#Ваш пароль: <strong>' .$pass. '</strong><br>
			#<br>
			#Используя указанные данные, Вы всегда можете зайти на сайт компании, ознакомиться с продукцией, сделать предварительный заказ или заявку на расчет работ.';
			#$m->Send();	

			#отправляем админу письмо с данными юзера
			$date_add = date('d.m.Y H:i:s');  
			$m = new PHPMailer(true);
			$m->Priority = '1';
			$m->AddReplyTo('marketing@ramok.by', 'Сайт УП Рамок');	
			$m->AddAddress('marketing@ramok.by');
			$m->AddAddress('upramok@yandex.ru');
			$m->SetFrom('marketing@ramok.by', 'Сайт УП Рамок');
			$m->Subject = 'Регистрация клиента на сайте Ramok.by';
			$m->Body = 'Здравствуйте!<br><br>
			На сайте зарегистрировался гость для просмотра цен и скачивания прайс-листов <strong>' .$date_add. '</strong><br>
			<br>
			Название компании: <strong>' .$name. '</strong><br>
			Контактное лицо: <strong>' .$contact. '</strong><br>
			Город: <strong>' .$city. '</strong><br>
			Телефон: <strong>' .$phone. '</strong><br>
			E-mail: <strong>' .$mail. '</strong><br>
			Интерес: <strong>' .$interes. '</strong><br>
			<br>
			IP адрес: <strong>' .$_SERVER['REMOTE_ADDR']. '</strong><br>
			Страницы, на которых он был: <br>' .$where_was. '<br>
			';
			$m->Send();	
			
			#if($reg_type==1){
				#Перенаправляем на прайс
				if($_SESSION['url_product_redirect']){
					header("Location: ".$_SESSION['url_product_redirect']);
					$_SESSION['url_product_redirect'] = '';
					}
				else{
					header("Location: /client/price/");
					}
				exit();
				#}
			}
		}
	else{
		$error .= '<br>Введен неправильный код с картинки';
		}
	}

if(!empty($_SESSION['login'])){
	#Баннер для показа прайс-листа
	$guest_banner = '<a href="/files/price/obshchiy_prays.xls"><img border="0" src="/cfg/baner_price.jpg" style="cursor:pointer"></a>';
	#Форма авторизации
	$login_form = '
	<table cellpadding="0" cellspacing="0">
		<tr>
		<td class="content" style="background-color:#cccccc; padding-left:5px; padding-right:5px">
				Здравствуйте, <b>' .$_SESSION['user_name']. '</b> <a href="/client/"><img align="absmiddle" border="0" src="/cfg/user_profile.png"></a> (<a href="/enter/client/exit/">выйти</a>)
			</td>
		</tr>
	</table>';
	}
else{
	#Баннер для показа прайс-листа
	$guest_banner = '<img border="0" src="/cfg/baner_price.jpg" onclick="show_reg(1)" style="cursor:pointer">';
	#Форма авторизации
	$login_form = '
		<form action="/client/enter/?action=go" method="post">
		Логин: &nbsp;&nbsp;<input name="login" type="text" style="font-size:10; border:1px solid #CCCCCC; color:#006600" size="7">
            Пароль: <input name="pass" type="password" style="font-size:10; border:1px solid #CCCCCC" size="10">
		<input type="submit" value="Войти" style="font-size:10; border:0px solid #CCCCCC; background-color:#FFFFFF; color:#000099">
		</form>';
	}



#
#Достаем категории товаров
#
if($id_product){ 
	$query = mysql_query("SELECT id_rubr FROM tbl_products WHERE id='" .$id_product. "'");
	$id_rubr = @mysql_result($query, '0', 'id_rubr');
	}
if(!$id_rubr && $currentPage["params"]["idpage"]==1){
	$prod_link[8] = '<a title="Автоматизация дома, офиса, гостинницы" style="margin-top:-20px; float:right; color:#2f4876" target="_blank" rel="nofollow" href="/redirect?url=ydom.by">www.ydom.by</a>';
	$prod_link[6] = '<a title="Автоматизация торговли, складов, ресторанов" style="margin-top:-20px; float:right; color:#00A651" target="_blank" rel="nofollow" href="/redirect?url=bizsoft.by">www.bizsoft.by</a>';
	$query0 = mysql_query("SELECT ProductID, ProductName, caption, id_subcat FROM tbl_napr WHERE IsActive='1' ORDER BY SortOrder");
	while($result0=mysql_fetch_array($query0)){
		if($result0['id_subcat']){
			$catalog_main_list .= '<div style="color:#666; font-size:12px; background-color:#eee; padding:5px; margin-right:3px; margin-left:-25px; margin-bottom:5px; margin-top:5px"><a class="showblock" tag="' .$result0['ProductID']. '" style="text-decoration:none;padding-top:4px" href="/napr/' .$result0['caption']. '">' .$result0['ProductName']. '' .$prod_link[$result0['ProductID']]. '</a></div>
			<span class="blockcats" id="block' .$result0['ProductID']. '" style="display:none">';
			$query = mysql_query("SELECT * FROM tbl_catalog WHERE id IN (" .$result0['id_subcat']. ") AND IsActive='1' ORDER BY position_main, position_main");
			while($result=mysql_fetch_array($query)){
				$catalog_main_list .= '<div><a class="main" href="/catalog/' .$result['caption']. '-' .$result['id']. '">' .$result['name']. '</a></div>';
				#Достаем подкатегории
				$query2 = mysql_query("SELECT * FROM tbl_catalog WHERE id_parent='" .$result['id']. "' AND IsActive='1' ORDER BY position");
				if(!mysql_num_rows($query2)){
					$catalog_main_list .= '<div class="submaincats">';
					while($result2=mysql_fetch_array($query2)){
						$catalog_main_list .= '<a href="/catalog/' .$result2['caption']. '-' .$result2['id']. '">' .$result2['name']. '</a>';
						}
					$catalog_main_list .= '</div>';
					}
				}
			$catalog_main_list .= '</span>';
			}
		}
	}
else{

	if($id_rubr){
		#Определяем все ступени категорий
		$query = mysql_query("SELECT id, id_parent, name FROM tbl_catalog WHERE id='" .$id_rubr. "' AND IsActive='1'");
		$result = mysql_fetch_array($query);
		if($result['id_parent']){
			$query2 = mysql_query("SELECT id, id_parent, name FROM tbl_catalog WHERE id='" .$result['id_parent']. "' AND IsActive='1'");
			$result2 = mysql_fetch_array($query2);
			$id_rubr2 = $result2['id'];
			if($result2['id_parent']){
				$query3 = mysql_query("SELECT id, id_parent, name FROM tbl_catalog WHERE id='" .$result2['id_parent']. "' AND IsActive='1'");
				$result3 = mysql_fetch_array($query3);
				$id_rubr3 = $result3['id'];
				}
			}
		}

	$prod_link[8] = '<a title="Автоматизация дома, офиса, гостинницы" style="margin-top:-20px; float:right; color:#2f4876" target="_blank" rel="nofollow" href="/redirect?url=ydom.by">www.ydom.by</a>';
	$prod_link[6] = '<a title="Автоматизация торговли, складов, ресторанов" style="margin-top:-20px; float:right; color:#00A651" target="_blank" rel="nofollow" href="/redirect?url=bizsoft.by">www.bizsoft.by</a>';
	
	$query0 = mysql_query("SELECT ProductID, ProductName, id_subcat, caption FROM tbl_napr WHERE IsActive='1' ORDER BY SortOrder");
	while($result0=mysql_fetch_array($query0)){
		#Достаем категории товаров
		if($result0['id_subcat']){
			if($result0['ProductID']==$id_napr){
				$napr_vis = '';
				}
			else{
				$napr_vis = 'style="display:none"';
				}
			$catalog_list .= '<div style="color:#666; font-size:12px; background-color:#eee; padding:5px; margin-right:3px; margin-left:-25px; margin-bottom:5px; margin-top:5px"><a class="showblock" tag="' .$result0['ProductID']. '" style="text-decoration:none;padding-top:4px" href="/napr/' .$result0['caption']. '">' .$result0['ProductName']. '</a>' .$prod_link[$result0['ProductID']]. '</div>
			<span class="blockcats" id="block' .$result0['ProductID']. '" ' .$napr_vis. '>';
			$query = mysql_query("SELECT * FROM tbl_catalog WHERE id IN (" .$result0['id_subcat']. ") AND IsActive='1' ORDER BY position_main");
			while($result=mysql_fetch_array($query)){
				#Достаем подкатегории
				if($result['id']==$id_rubr||$result['id']==$id_rubr2||$result['id']==$id_rubr3){
					$remove_hidden = $result0['ProductID'];
					#if(!$result['id_parent']){
						$catalog_list .= '<div><a class="main" href="/catalog/' .$result['caption']. '-' .$result['id']. '"><strong>' .$result['name']. '</strong></a></div>';
						#}
					#else{
						#$catalog_list .= '<div><a class="main" href="/catalog/' .$result['caption']. '-' .$result['id']. '">' .$result['name']. '</a></div>';
						#}
					if($result['id']==$id_rubr){
						$id_rubr2 = '';
						$id_rubr3 = '';
						}
						
					$query2 = mysql_query("SELECT * FROM tbl_catalog WHERE id_parent='" .$result['id']. "' AND IsActive='1' ORDER BY position");
					if(mysql_num_rows($query2)){
						$catalog_list .= '<div class="submaincats">';
						while($result2=mysql_fetch_array($query2)){
							if($result2['id']==$id_rubr||$result2['id']==$id_rubr2||$result2['id']==$id_rubr3){
								$catalog_list .= '<a class="red" href="/catalog/' .$result2['caption']. '-' .$result2['id']. '">' .$result2['name']. '</a>';
								$query3 = mysql_query("SELECT * FROM tbl_catalog WHERE id_parent='" .$result2['id']. "' AND IsActive='1' ORDER BY position");
								if(mysql_num_rows($query3)){
									$catalog_list .= '<div class="subcats">';
									while($result3=mysql_fetch_array($query3)){
										if($result3['id']==$id_rubr||$result3['id']==$id_rubr2||$result3['id']==$id_rubr3){
											$catalog_list .= '<a class="red" href="/catalog/' .$result3['caption']. '-' .$result3['id']. '">' .$result3['name']. '</a>';
											}
										else{
											$catalog_list .= '<a href="/catalog/' .$result3['caption']. '-' .$result3['id']. '">' .$result3['name']. '</a>';
											}
										}
									$catalog_list .= '</div>';
									}
								}
							else{
								$catalog_list .= '<a href="/catalog/' .$result2['caption']. '-' .$result2['id']. '">' .$result2['name']. '</a>';
								}
							}
						$catalog_list .= '</div>';
						}
					
					}
				else{
					$catalog_list .= '<div><a class="main" href="/catalog/' .$result['caption']. '-' .$result['id']. '">' .$result['name']. '</a></div>';
					}
				}
			}
		$catalog_list .= '</span>';
		}
	}
$catalog_list = str_replace('id="block' .$remove_hidden. '" style="display:none"','id="block' .$remove_hidden. '" style="display:block"',$catalog_list);


#Достаем услуги
$query = mysql_query("SELECT * FROM tbl_services WHERE IsActive='1' ORDER BY position");
while($result=mysql_fetch_array($query)){
	if($currentPage["params"]["idpage"]==2&&$id==$result['id']){
		$services_list .= '<div><a class="main" href="/services/?id=' .$result['id']. '"><strong>' .$result['name']. '</strong></a></div>';
		}
	else{
		$services_list .= '<div><a class="main" href="/services/?id=' .$result['id']. '">' .$result['name']. '</a></div>';		
		}
	}




if($currentPage["params"]["idpage"]==1){
	#Достаем картинки в шапку
	$query = mysql_query("SELECT * FROM tbl_headimage ORDER BY sortorder");
	while($result = mysql_fetch_array($query)){
		$head_image .= '<li><a href="' .$result['url']. '"><img src="/images/header/' .$result['src']. '" alt=""/></a></li>';
		}
	
	#Достаем направления деятельности
	$query = mysql_query("SELECT ProductID,ProductName,ProductTeaser,caption FROM tbl_napr WHERE IsActive='1' ORDER BY SortOrder");
	$n=0;
	$v=$_REQUEST['v'];
	if(!$v){
		$arr_otstyp=array(45,45,45,45);
		}
	
	while($result = mysql_fetch_array($query)){
		$query2 = mysql_query("SELECT * FROM tbl_naprimg WHERE ProductID='" .$result['ProductID']. "' ORDER BY SortOrder");
		$napr_photo = '';
		$i=0;
		while($result2 = mysql_fetch_array($query2)){
			$napr_photo .= '<img src="/images/napr/' .$result2['ProductImg']. '" />';
			if(!$i){$napr_photo_f = '/images/napr/' .$result2['ProductImg'];}
			$i++;
			}
		
		$list_napr .= '
		<table class="napr_deyat" id="naprs" src="' .$result['ProductID']. '">
		<tr>
			<td style="padding-left:35px; background:none">
			<a href="/napr/' .$result['caption']. '">
		  <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="104" 	height="102">
			  <param name="movie" value="/images/mainteaser/' .$result['ProductID']. '.swf">
			  <param name="quality" value="high">
			  <param name="wmode" value="transparent" />
			  <embed autostart="false" src="/images/mainteaser/' .$result['ProductID']. '.swf" quality="high" wmode="transparent"  pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="104" height="102"></embed>
			</object>			
			</a>
		   </td>
			<td style="padding-left:7px; background:none"><a style="text-decoration:none" href="/napr/' .$result['caption']. '">
				<div style="padding-bottom:5px; font-size:17px"><strong>' .$result['ProductName']. '</strong></div>
				' .$result['ProductTeaser']. '</a><br />
				<a href="/napr/' .$result['caption']. '">подробнее</a>
			</td>
		</tr>	
		</table>
		';
		$n++;
		}
	#<div style="height:' .$arr_otstyp[$n]. 'px"></div>
	#Достаем последние новости
	$query = mysql_query("SELECT NewsID, NewsTitle, NewsTeaser, NewsImg  FROM tbl_news ORDER BY NewsDate DESC LIMIT 5");
	while($result = mysql_fetch_array($query)){
		$date = date("d.m.Y", strtotime($result['NewsDate']));
		if($result['NewsImg']){$result['NewsImg']='<img src="/images/NewsImg/Small/' .$result['NewsImg']. '" align="left" style="padding-right:5px"/>';}
		$list_last_news .= '
		<tr>
			<td style="padding-bottom:18px">
				<a style="text-decoration:none" href="/client/news/?id=' .$result['NewsID']. '">' .$result['NewsImg']. '
				<div style="padding-bottom:5px"><strong>' .$result['NewsTitle']. '</strong></div>
				' .$result['NewsTeaser']. '</a>
			</td>
		</tr>		
		';
		}	
	#Достаем последние статьи
	$query = mysql_query("SELECT id, name, caption, teaser, img FROM tbl_topics ORDER BY date DESC LIMIT 5");
	while($result = mysql_fetch_array($query)){
		$date = date("d.m.Y", strtotime($result['NewsDate']));
		if($result['img']){$result['img']='<img src="/images/topics/small/' .$result['img']. '" align="left" style="padding-right:5px"/>';}
		if($result['id']<47&&!$result['caption']){
			$url = '/client/topics/?topic=' .$result['id'];
			}
		else{
			$url = '/articles/' .$result['caption'].'-'.$result['id'];
			}
		$list_last_topics .= '
		<tr>
			<td style="padding-bottom:18px">
				<a style="text-decoration:none" href="' .$url. '">' .$result['img']. '
				<div style="padding-bottom:5px"><strong>' .$result['name']. '</strong></div>
				' .$result['teaser']. '</a>
			</td>
		</tr>		
		';
		}	
	
	
	}
else{
	$query = mysql_query("SELECT * FROM tbl_slideimage ORDER BY RAND() LIMIT 1");
	while($result = mysql_fetch_array($query)){
		if($result['url']){
			$head_image = '<a href="' .$result['url']. '"><img src="/images/header/' .$result['src']. '" alt="" style="height:152px;width:1000px;" /></a>';
			}
		else{
			$head_image = '<img src="/images/header/' .$result['src']. '" alt="" style="height:152px;width:1000px;" />';
			}
		}	
	}

if($_SESSION['openlk']==1){
	$openlk = "
	<script>
	window.open('/my?a=anketa', '_blank');
	</script>	
	";
	$_SESSION['openlk']=0;
	}


#Достаем картинки в баннер
$query = mysql_query("SELECT * FROM tbl_bannerimage ORDER BY sortorder");
$i=1;
while($result = mysql_fetch_array($query)){
	$banner_image .= '
	<a href="' .$result['url']. '"><img class="bannerimg ban' .$i. '" src="/images/banner/' .$result['src']. '" /></a>';
	$i++;
	}


$query = mysql_query("SELECT * FROM tbl_settings");
$result = mysql_fetch_array($query);
$copyright = $result['copyright'];
$copyright = stripslashes($copyright);
$kurs = $result['kurs'];
$kurs = stripslashes($kurs);
$show_price = $result['show_price'];
$show_price = stripslashes($show_price);



?>