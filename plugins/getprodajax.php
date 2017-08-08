<?

include("../phpc/config.php");

$db_connection = mysql_connect(DatabaseHost, DatabaseUser, DatabasePass);
mysql_select_db(DatabaseName, $db_connection);
mysql_query("SET NAMES utf8");

$id_rubr = mysql_real_escape_string($_REQUEST['id_rubr']);
$start = mysql_real_escape_string($_REQUEST['start']);


#Достаем товары
$query = mysql_query("SELECT *,
(SELECT discount FROM tbl_catalog WHERE tbl_products.id_rubr=tbl_catalog.id LIMIT 1) AS discountrubr,
(SELECT tbl_modification.price FROM tbl_modification WHERE tbl_modification.id_product=tbl_products.id AND tbl_modification.price>0 ORDER BY tbl_modification.price LIMIT 1) AS price_mod,
(SELECT tbl_modification.price_old FROM tbl_modification WHERE tbl_modification.id_product=tbl_products.id AND tbl_modification.price>0 ORDER BY tbl_modification.price LIMIT 1) AS price_mod_old,
(SELECT tbl_modification.id FROM tbl_modification WHERE tbl_modification.id_product=tbl_products.id AND tbl_modification.price>0 ORDER BY tbl_modification.sortorder ASC LIMIT 1) AS id_mod,
(SELECT ProductImg FROM tbl_productimg WHERE tbl_productimg.ProductID=tbl_products.id ORDER BY SortOrder LIMIT 1) AS image FROM tbl_products WHERE id_rubr='" .$id_rubr. "' and IsActive <> 0 ORDER BY position LIMIT " .$start. ", 20");

while($result = mysql_fetch_array($query)){
	if($result['image'] == ''){$result['img'] = '/image/no-image.gif';}
	else{$result['img'] = '/images/product/Small/' .$result['image'];}

	if(!$result['discount']){
		$result['discount'] = $result['discountrubr'];
		}

	if($result['price_mod']){
		$result['price'] = $result['price_mod'];
		$result['price_old'] = $result['price_mod_old'];
		$mod = $result['id_mod'];
		}
	
	if($result['discount']){
		$result['price_old'] = $result['price'];
		$result['price'] = ceil($result['price'] - $result['price']/100*$result['discount']);
		}
	
	//if($_SESSION['is_diller']){$result['price']=$result['price_diller'];}
	
	if(2==2){
	//if(!empty($_SESSION['user_id'])||$result['discount']){
		if($result['price'] != '0'){
			$price = '<strong><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px"><em>' .number_format($result['price'], 0, "", "."). ' р.</em></span></strong>';
			$buy = '<a class="addcart" href="?addbasket=' .$result['id']. '&mod=' .$mod. '"><img align="absmiddle" border="0" src="/cfg/cart.gif" width="20" height="20" alt="Добавить ' .$result['name']. ' в корзину" title="Добавить ' .$result['name']. ' в корзину"></a>';
			if($result['price_old']&&$result['price_old']>$result['price']){
				$price .= '<br><span style="text-decoration:line-through">' .number_format($result['price_old'], 0, "", "."). ' р.</span>';
				}					
			}
		else{
			if($result['price_usd']){$price_usd = '';}else{$price_usd='';}
			$price = '<strong><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px"><em>Под заказ<br>' .$price_usd. '</em></span></strong>';
			$buy = '<a href="/product/' .$result['caption']. '-' .$result['id']. '#order"><img align="absmiddle" border="0" src="/cfg/cart.gif" style="cursor:pointer" width="20" height="20" alt="Добавить ' .$result['name']. ' в корзину" title="Добавить ' .$result['name']. ' в корзину"></a>';
			}
		
		}
	else{
		$price = '<a href="/client/guest"><span style="cursor:pointer" title="Открыть цены"><strong><span class="title2"><em>Стоимость</em></span></strong></span></a>';
		$buy = '<a href="/product/' .$result['caption']. '-' .$result['id']. '#order"><img align="absmiddle" border="0" src="/cfg/cart.gif" style="cursor:pointer" width="20" height="20" alt="Добавить ' .$result['name']. ' в корзину" title="Добавить ' .$result['name']. ' в корзину"></a>';
		}
	
	$href1 = '';
	$href2 = '';
	
	if($result['opis2'] != ''){
		$opis = '<br><a href="/product/' .$result['caption']. '-' .$result['id']. '"><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:12px"><em><strong>Подробное описание ...</strong></em></span>';
		$href1 = '<a target="_blank" href="/client/catalog/print/?id=' .$result['id']. '"><img alt="Версия для печати" border="0" align="absmiddle" src="/cfg/print.gif"></a> <a href="/product/' .$result['caption']. '-' .$result['id']. '"><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:18px"><em>' .$result['name']. '</em></span>';
		$href2 = '<a href="/product/' .$result['caption']. '-' .$result['id']. '">
		<img border="0" alt="' .$result['name']. '" src="' .$result['img']. '"></a>';
		}
	else{
		$opis = '&nbsp;';
		$href1 = '<span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:18px; color:#000000"><em>' .$result['name']. '</em></span></a>';
		$href2 = '<img border="0" alt="' .$result['name']. '" src="' .$result['img']. '">';
		}
	
	$content .= '<table width="100%" class="content proditem" cellpading="0" cellspacing="0">
		<tr>
			<td style="border-top:1px solid #CCCCCC; padding-left:5px; background-color:#F9F9F9">' .$href1. '</td>
			<td width="120px" align="right" valign="middle" style="padding:2px; border-top:1px solid #CCCCCC;  background-color:#F9F9F9">' .$price. '</td>
			<td width="40px" style="border-top:1px solid #CCCCCC;  background-color:#F9F9F9">' .$buy. '</td>
		</tr>
	</table>
	<table width="100%" class="content" cellpading="0" cellspacing="0">
		<tr>
			<td align="center" valign="top"  style="background-color:#F9F9F9">
				<img width="140" height="0">
				' .$href2. '<br>
			</td>
			<td valign="top" width="100%" style="border-top:1px solid #CCCCCC; border-left:1px solid #CCCCCC; padding:7px; font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px">
				' .$result['opis1']. ' ' .$opis. '
				</a>
			</td>
		</tr>
	</table>';
	}

print $content;


?>