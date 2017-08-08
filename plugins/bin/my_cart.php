<?


include('../plugins/captcha.php');

$query = mysql_query("select * from tbl_user where id='" .$_SESSION['user_id']. "'");
$result = mysql_fetch_array($query);


$a = $_REQUEST['a'];
$unn = $result['unn'];
$dogovor = $result['dogovor'];
$name = stripslashes($result['name']);
$email = $result['email'];
$company = stripslashes($result['company']);
$pass = $result['pass'];
$login = $result['login'];
$phone = $result['phone'];
$city = $result['city'];
$adress = $result['adress'];
$bank_code = $result['bank_code'];
$bank_name = $result['bank_name'];
$bank_adress = $result['bank_adress'];
$bank_schet = $result['bank_schet'];


#Корзина
$a = $_REQUEST['a'];
$del = $_REQUEST['del'];
$action = $_REQUEST['action'];
$goto = $_REQUEST['goto'];

if($a=='save'&&$_SESSION['cart_count']){
	$cart_temp = $_SESSION['cart_product'];
	while(list($id_product, $val)=each($cart_temp)){
		if($val[0]){
			if($_REQUEST['count'][$id_product]){
				$_SESSION['cart_product'][$id_product][0]=$_REQUEST['count'][$id_product];
				}
			else{
				unset($_SESSION['cart_product'][$id_product]);
				}
			}
		else{
			while(list($id_mod_temp, $val_temp)=each($val)){
				if($_REQUEST['countmod'][$id_mod_temp]){
					$_SESSION['cart_product'][$id_product][$id_mod_temp]=$_REQUEST['countmod'][$id_mod_temp];
					}
				else{
					unset($_SESSION['cart_product'][$id_product][$id_mod_temp]);
					}
				}
			}
		}
	if($goto=='save'){
		header('Location: /my/cart?a=order');
		exit();
		}
	}

if($del){
	unset($_SESSION['cart_product'][$del]);
	}
	
if($a=='clear'){
	unset($_SESSION['cart_product']);
	$_SESSION['cart_count']='';
	$_SESSION['cart_summ']='';			
	}



#
#Сохранение заказа
#

if($action=='order'){
	$name = $_REQUEST['name'];
	$company = $_REQUEST['company'];
	$phone = $_REQUEST['phone'];
	$email = $_REQUEST['email'];
	$city = $_REQUEST['city'];
	$unn = $_REQUEST['unn'];
	$adress = $_REQUEST['adress'];
	$bank_code = $_REQUEST['bank_code'];
	$bank_name = $_REQUEST['bank_name'];
	$bank_adress = $_REQUEST['bank_adress'];
	$bank_schet = $_REQUEST['bank_schet'];
	
	if(!$name){$error .= '- Введите Ваше ФИО<br>';}
	if(!$company){$error .= '- Введите название Вашей компании<br>';}
	if(!$email){$error .= '- Введите email<br>';}
	if(!$phone){$error .= '- Введите телефон<br>';}
	if(!$city){$error .= '- Введите город<br>';}
	if(!$unn){$error .= '- Введите УНН компании<br>';}
	if(!$adress){$error .= '- Введите адрес компании<br>';}
	if(!$bank_code){$error .= '- Введите код банка<br>';}
	if(!$bank_name){$error .= '- Введите название банка<br>';}
	if(!$bank_adress){$error .= '- Введите адрес банка<br>';}
	if(!$bank_schet){$error .= '- Введите счет в банке<br>';}
	
				
	if(!$error&&$_SESSION['user_id']&&$_SESSION['cart_count']){
		$date_add = date("Y-m-d H:i:s");
		$query = mysql_query("INSERT INTO tbl_orders SET id_user='" .$_SESSION['user_id']. "', name='" .$order_name. "', adress='" .$order_adress. "', phone='" .$order_phone. "', comment='" .$order_comment. "', date_add='" .$date_add. "'");
		$id_order = mysql_insert_id();

		$query = mysql_query("UPDATE tbl_user SET name='" .$name. "', company='" .$company. "', phone='" .$phone. "', city='" .$city. "', email='" .$email. "', unn='" .$unn. "',
		adress='" .$adress. "', bank_code='" .$bank_code. "', bank_name='" .$bank_name. "', bank_adress='" .$bank_adress. "', bank_schet='" .$bank_schet. "' where id='" .$_SESSION['user_id']. "'");

		$content_letter = '
		ФИО: <b>' .$name. '</b><br>
		Название компании:	<b>' .$company. '</b><br>
		E-mail: <b>' .$email. '</b><br>
		Телефон: <b>' .$phone. '</b><br>
		Город: <b>' .$city. '</b><br>
		УНП: <b>' .$unn. '</b><br>
		Договор: <b>' .$dogovor. '</b><br>
		Адрес компании: <b>' .$adress. '</b><br>
		Код банка: <b>' .$bank_code. '</b><br>
		Название банка: <b>' .$bank_name. '</b><br>
		Адрес банка: <b>' .$bank_adress. '</b><br>
		Счет в банке: <b>' .$bank_schet. '</b><br>
		
		<br><br>
		
		<table width="100%">
		<tr style="height:40px">
			<td width="40%"><strong>Название</strong></td>
			<td align="center"><strong>Стоимость</strong></td>
			<td align="center"><strong>Количество</strong></td>
			<td align="center"><strong>Сумма</strong></td>
		</tr>';
	
		while(list($id_product, $val)=each($_SESSION['cart_product'])){
			if($val[0]){
				$query = mysql_query("SELECT price, discount,
				(SELECT discount FROM tbl_catalog WHERE tbl_products.id_rubr=tbl_catalog.id LIMIT 1) AS discountrubr
				FROM tbl_products WHERE id='" .$id_product. "'");
				$result = mysql_fetch_array($query);
				if(!$result['discount']){
					$result['discount'] = $result['discountrubr'];
					}
				if($result['discount']){
					$result['price'] = round($result['price'] - $result['price']/100*$result['discount'],2);
					}
				$total_product = $result['price']*$val[0];
				mysql_query("INSERT INTO tbl_orders_details SET id_order='" .$id_order. "', id_product='" .$id_product. "', product_count='" .$val[0]. "', price='" .$result['price']. "', total_price='" .$total_product. "'");
				
				
				$query = mysql_query("SELECT id, name, price, discount,
				(SELECT discount FROM tbl_catalog WHERE tbl_products.id_rubr=tbl_catalog.id LIMIT 1) AS discountrubr,
				(SELECT tbl_productimg.ProductImg FROM tbl_productimg WHERE tbl_productimg.ProductID=tbl_products.id ORDER BY tbl_productimg.SortOrder LIMIT 1) AS imgname
				FROM tbl_products WHERE id='" .$id_product. "'");
				$result = mysql_fetch_array($query);
				if(!$result['discount']){
					$result['discount'] = $result['discountrubr'];
					}
				if($result['discount']){
					$result['price'] = round($result['price'] - $result['price']/100*$result['discount'],2);
					}
				$total_product = $result['price']*$val[0];
				$total_cart += $total_product;
				$total_count += $val[0];
				$content_letter .= '
				<tr style="height:40px">
					<td><img width="70" align="absmiddle" src="http://ramok.by/images/product/Small/' .$result['imgname']. '" border="0" alt=""/> ' .$result['name']. '</td>
					<td align="center">' .$result['price']. ' р.</td>			
					<td align="center">' .$val[0]. '</td>			
					<td align="center">' .$total_product. ' р.</td>	
					<td align="center"></td>			
				</tr>';
				}
			else{
				while(list($id_mod_temp, $val_temp)=each($val)){
					$query2 = mysql_query("SELECT price, name FROM tbl_modification WHERE id='" .$id_mod_temp. "'");
					$result2 = mysql_fetch_array($query2);
					$name_mod = $result2['name'];
					$price_mod = $result2['price'];
					$total_product = $price_mod*$val_temp;
					mysql_query("INSERT INTO tbl_orders_details SET id_order='" .$id_order. "', id_product='" .$id_product. "', id_mod='" .$id_mod_temp. "', product_count='" .$val_temp. "', price='" .$price_mod. "', total_price='" .$total_product. "'");
					
					$query = mysql_query("SELECT id, name, price,
					(SELECT tbl_productimg.ProductImg FROM tbl_productimg WHERE tbl_productimg.ProductID=tbl_products.id ORDER BY tbl_productimg.SortOrder LIMIT 1) AS imgname
					FROM tbl_products WHERE id='" .$id_product. "'");
					$result = mysql_fetch_array($query);
					$total_product = $price_mod*$val_temp;
					$total_cart += $total_product;
					$total_count += $val_temp;
					$content_letter .= '
					<tr style="height:40px">
						<td><img width="70" align="absmiddle" src="http://ramok.by/images/product/Small/' .$result['imgname']. '" border="0" alt=""/> ' .$result['name']. ' (' .$name_mod. ')</td>
						<td align="center">' .$price_mod. ' р.</td>			
						<td align="center">' .$val_temp. '</td>			
						<td align="center">' .$total_product. ' р.</td>	
						<td align="center"></td>			
					</tr>';
					
					}
				}
				
			}
	
	$content_letter .= '
	<tr style="height:40px">
		<td><strong>Всего:</strong></td>
		<td></td>
		<td align="center"><strong>' .$total_count. '</strong></td>
		<td align="center"><strong>' .$total_cart. ' р.</strong></td>
		<td></td>	
	</tr>
	</table>';

		
		#Вставояем хеш
		$hash = md5(rand(111111111,999999999));
		$query = mysql_query("INSERT INTO tbl_hash SET text='" .$hash. "'");


		#Теперь отправляем письмо о регистрации
		include '../includes/class.phpmailer.php';
		$m = new PHPMailer(true);
		$m->Priority = 1;
		$m->AddReplyTo('www@ramok.by', 'Сайт УП Рамок');	
		$m->AddAddress('www@ramok.by');
		$m->AddAddress('upramok@yandex.ru');
		$m->SetFrom('www@ramok.by', 'Сайт УП Рамок');
		$m->Subject = 'Новый заказ на сайте ramok.by';
		$m->Body = '
Здравствуйте!<br>
<br><br>
На сайте оставлен новый заказ:<br>
<br>
' .$content_letter;

		$m->Send();	



		#Удаляем заказ из сессии
		unset($_SESSION['cart_product']);
		$_SESSION['cart_count']=''; 
		$_SESSION['cart_summ']='';

		
		header("Location: /my?show=shop");
		exit();
		}
	
	}

if($a=='order'){
	$content = '<table width="100%">
	<tr style="height:40px">
		<td width="40%"><strong>Название</strong></td>
		<td align="center"><strong>Стоимость</strong></td>
		<td align="center"><strong>Количество</strong></td>
		<td align="center"><strong>Сумма</strong></td>
	</tr>';
	}
else{
	$content = '<table width="100%">
	<tr style="height:40px">
		<td width="40%"><strong>Название</strong></td>
		<td align="center"><strong>Стоимость</strong></td>
		<td align="center"><strong>Количество</strong></td>
		<td align="center"><strong>Сумма</strong></td>
		<td align="center"><strong>Действия</strong></td>
	</tr>';
	}
	
if($_SESSION['cart_count']){
	while(list($id_product, $val)=each($_SESSION['cart_product'])){
		$query = mysql_query("SELECT id, name, price, discount,
		(SELECT discount FROM tbl_catalog WHERE tbl_products.id_rubr=tbl_catalog.id LIMIT 1) AS discountrubr,
		(SELECT tbl_productimg.ProductImg FROM tbl_productimg WHERE tbl_productimg.ProductID=tbl_products.id ORDER BY tbl_productimg.SortOrder LIMIT 1) AS imgname
		FROM tbl_products WHERE id='" .$id_product. "'");
		$result = mysql_fetch_array($query);
		if(!$result['discount']){
			$result['discount'] = $result['discountrubr'];
			}
		if($result['discount']){
			$result['price'] = round($result['price'] - $result['price']/100*$result['discount'],2);
			}
		if($val[0]){
			$val2=$val[0];
			$total_product = round($result['price']*$val2,2);
			$total_cart += $total_product;
			$total_count += $val2;
			if($a=='order'){
				$content .= '
				<tr style="height:40px">
					<td><img width="70" align="absmiddle" src="/images/product/Small/' .$result['imgname']. '" border="0" alt=""/> ' .$result['name']. '</td>
					<td align="center">' .$result['price']. ' р.</td>			
					<td align="center">' .$val2. '</td>			
					<td align="center">' .$total_product. ' р.</td>	
					<td align="center"></td>			
				</tr>';
				}
			else{
				$content .= '
				<tr style="height:40px">
					<td><img width="70" align="absmiddle" src="/images/product/Small/' .$result['imgname']. '" border="0" alt=""/> ' .$result['name']. '</td>
					<td align="center">' .$result['price']. ' р.</td>			
					<td align="center"><input name="count[' .$id_product. ']" size="3" maxlength="3" type="text" value="' .$val2. '"></td>			
					<td align="center">' .$total_product. ' р.</td>	
					<td align="center"><a style="color:red" class="agrey" href="?del=' .$id_product. '">удалить</a></td>			
				</tr>';
				}			
			}
		else{
			while(list($id_mod_temp, $val_temp)=each($val)){
				$query2 = mysql_query("SELECT price, name FROM tbl_modification WHERE id='" .$id_mod_temp. "'");
				$price_mod = @mysql_result($query2, 0, 'price');
				$name_mod = @mysql_result($query2, 0, 'name');
				if($result['discount']){
					$price_mod = $price_mod - $price_mod/100*$result['discount'];
					}
				$total_product = $price_mod*$val_temp;
				$total_cart += $total_product;
				$total_count += $val_temp;
				if($a=='order'){
					$content .= '
					<tr style="height:40px">
						<td><img width="70" align="absmiddle" src="/images/product/Small/' .$result['imgname']. '" border="0" alt=""/> ' .$result['name']. ' (' .$name_mod. ')</td>
						<td align="center">' .$price_mod. ' р.</td>			
						<td align="center">' .$val_temp. '</td>			
						<td align="center">' .$total_product. ' р.</td>	
						<td align="center"></td>			
					</tr>';
					}
				else{
					$content .= '
					<tr style="height:40px">
						<td><img width="70" align="absmiddle" src="/images/product/Small/' .$result['imgname']. '" border="0" alt=""/> ' .$result['name']. ' (' .$name_mod. ')</td>
						<td align="center">' .$price_mod. ' р.</td>			
						<td align="center"><input name="countmod[' .$id_mod_temp. ']" size="3" maxlength="3" type="text" value="' .$val_temp. '"></td>			
						<td align="center">' .$total_product. ' р.</td>	
						<td align="center"><a style="color:red" class="agrey" href="?del=' .$id_product. '">удалить</a></td>			
					</tr>';
					}
				}
			}

		}
	$_SESSION['cart_count']=$total_count;
	$_SESSION['cart_summ']= round($total_cart,2);
	}
else{
	$_SESSION['cart_count']='';
	$_SESSION['cart_summ']='';		
	}

$content .= '
<tr style="height:40px">
	<td><strong>Всего:</strong></td>
	<td></td>
	<td align="center"><strong>' .$total_count. '</strong></td>
	<td align="center"><strong>' .$total_cart. ' р.</strong></td>
	<td></td>	
</tr>
</table>';

if(!$_SESSION['cart_count']){
	header("Location: /");
	exit();
	}


#Достаем данные для корзины
if($_SESSION['user_id']&&$a=='order'){
	$query = mysql_query("SELECT * FROM tbl_user WHERE id='" .$_SESSION['user_id']. "'");
	$result = mysql_fetch_array($query);
	$order_name = $result['name'];
	$order_phone = $result['phone'];
	$order_adress = $result['adress'];
	if($_REQUEST['order_name']){
		$order_name = $_REQUEST['order_name'];
		}
	if($_REQUEST['order_phone']){
		$order_phone = $_REQUEST['order_phone'];
		}
	if($_REQUEST['order_adress']){
		$order_adress = $_REQUEST['order_adress'];
		}		
	}



?>