<?

session_start();

include("../../phpc/config.php");

$db_connection = mysql_connect(DatabaseHost, DatabaseUser, DatabasePass);
mysql_select_db(DatabaseName, $db_connection);
mysql_query("SET NAMES cp1251");


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



if($a=='order'){
	$content = '<table width="100%">
	<tr style="height:40px">
		<td colspan="2" width="60%"><strong>Название</strong></td>
		<td align="center"><strong>Стоимость</strong></td>
		<td align="center"><strong>Количество</strong></td>
		<td align="center"><strong>Сумма</strong></td>
	</tr>';
	}
else{
	$content = '<table width="100%">
	<tr style="height:40px">
		<td colspan="2" width="60%"><strong>Название</strong></td>
		<td align="center"><strong>Стоимость</strong></td>
		<td align="center"><strong>Количество</strong></td>
		<td align="center"><strong>Сумма</strong></td>
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
					<td><img width="70" src="/images/product/Small/' .$result['imgname']. '" border="0" alt=""/></td>
					<td>' .$result['name']. '</td>
					<td align="center">' .$result['price']. ' р.</td>			
					<td align="center">' .$val2. '</td>			
					<td align="center">' .$total_product. ' р.</td>	
				</tr>';
				}
			else{
				$content .= '
				<tr style="height:40px">
					<td><img width="70" src="/images/product/Small/' .$result['imgname']. '" border="0" alt=""/></td>
					<td>' .$result['name']. '</td>
					<td align="center">' .$result['price']. ' р.</td>			
					<td align="center">' .$val2. '</td>			
					<td align="center">' .$total_product. ' р.</td>	
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
					<td><img width="70" src="/images/product/Small/' .$result['imgname']. '" border="0" alt=""/></td>
					<td>' .$result['name']. '</td>
						<td align="center">' .$price_mod. ' р.</td>			
						<td align="center">' .$val_temp. '</td>			
						<td align="center">' .$total_product. ' р.</td>	
					</tr>';
					}
				else{
					$content .= '
					<tr style="height:40px">
					<td><img width="70" src="/images/product/Small/' .$result['imgname']. '" border="0" alt=""/></td>
					<td>' .$result['name']. '</td>
						<td align="center">' .$price_mod. ' р.</td>			
						<td align="center">' .$val_temp. '</td>			
						<td align="center">' .$total_product. ' р.</td>	
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
</table>';

if($total_count==1){$tovar_txt = 'товар';}
if($total_count==2||$total_count==3||$total_count==4){$tovar_txt = 'товара';}
if($total_count>4){$tovar_txt = 'товаров';}

$content_add = 'В Вашем заказе <b>' .$total_count. '</b> ' .$tovar_txt. ' на сумму <strong>' .$total_cart. ' рублей:</strong></b>';

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

<html>
<head>

<title></title>
<meta http-equiv="content-type" content="text/html; charset=windows-1251" />

<style>
td{
	font-size:14px
	}
.buttonbg {
width: 230px;
font-size: 14px;
border: 1px solid #ccc;
border-radius: 10px;
padding: 5px;
margin-top: 20px;
margin-bottom: 30px;
cursor: pointer;
width: 200px;
text-decoration: none;
background-color: #eee;
}
.buttonbg:hover{
	background-color:#ccc;
	}
a{
	color:#333;
	}
</style>
</head>

<body style="font-family: Geneva, Arial, Helvetica, sans-serif; font-size:14px">

<strong style="font-size:16px"><?=$content_add;?></strong>

<br /><br /><br />

<table width="100%">
	<tr>
    	<td align="center" width="50%"><a class="buttonbg" onClick="parent.document.location.href='<?=$_SERVER['HTTP_REFERER'];?>?a=clearcart';return false;" href="<?=$_SERVER['HTTP_REFERER'];?>?a=clear">Очистить заказ</a></td>
    	<td align="center" width="50%"><a class="buttonbg" onClick="parent.document.location.href='/my/cart';return false;" href="/my/cart">Перейти к оформлению</a></td>        
    </tr>
</table>

<br />

<?=$content;?>

</body>
</html>
