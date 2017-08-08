<?

$a = $_GET['a'];
$id_rubr = $_GET['rubr'];
$id_product = $_GET['id_product'];
$caption_catalog = $_GET['caption_catalog'];
$mod = $_REQUEST['mod'];
$no_nds='без НДС';

if($a=='clearcart'){
	unset($_SESSION['cart_product']);
	$_SESSION['cart_count']='';
	$_SESSION['cart_summ']='';			
	}


$addbasket = $_REQUEST['addbasket'];
if($addbasket){
	if(!$mod){
		$query = mysql_query("SELECT price, discount, 
		(SELECT discount FROM tbl_catalog WHERE tbl_products.id_rubr=tbl_catalog.id LIMIT 1) AS discountrubr
		FROM tbl_products WHERE id='" .$addbasket. "'");
		$mod=0;
		}
	else{
		$query = mysql_query("SELECT discount, 
		(SELECT discount FROM tbl_catalog WHERE tbl_products.id_rubr=tbl_catalog.id LIMIT 1) AS discountrubr
		FROM tbl_products WHERE id='" .$addbasket. "'");
		$discount = @mysql_result($query, '0', 'discount');
		$discountrubr = @mysql_result($query, '0', 'discountrubr');
		$query = mysql_query("SELECT price FROM tbl_modification WHERE id='" .$mod. "'");
		}
	if(mysql_num_rows($query)){
		$price = mysql_result($query, '0', 'price');
		if(!$mod){
			$discount = mysql_result($query, '0', 'discount');
			$discountrubr = mysql_result($query, '0', 'discountrubr');
			}
		if(!$discount){
			$discount = $discountrubr;
			}

		$price = $price - $price/100*$discount;
		
		$_SESSION['cart_product'][$addbasket][$mod]++;
		$_SESSION['cart_count']++;
		$_SESSION['cart_summ']+= round($price,2);
		header("Location: /plugins/bin/basket.php");
		exit();
		}
	}
	
$last_catalog_page = 'http://ramok.by'.$_SERVER['REQUEST_URI'];	
	
if(empty($id_rubr) && empty($id_product)){
	#Указываем информацию

	$title = 'Каталог оборудования';
	$page_title = 'УП "Рамок" - Каталог оборудования';
	$page_navigation = 'Каталог';
	
	$content .= '<span style="font-family:Arial, Helvetica, sans-serif; font-size:14px"><em>В нашем каталоге Вы найдете большой выбор оборудования для магазинов и создания успешного бизнеса "под ключ". </em></span><br><br>';
	
	$i = 0;
	#Достаем каталог оборудования 0 уровня
	$query = mysql_query("SELECT * FROM tbl_catalog WHERE id_parent='0' AND IsActive='1' ORDER BY position");
	$content .= '<table width="100%" class="content"><tr>';
	while($result = mysql_fetch_array($query)){
		$i++;
		$content .= '
		<td width="50%" valign="top" style="padding:5px">
		<a href="/catalog/' .$result['caption']. '-' .$result['id']. '"><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:18px"><em>' .$result['name']. '</em></span><br>
		<img align="left" border="0" src="/images/catalog/' .$result['image']. '" hspace="10" vspace="10" alt="' .$result['image_alt']. '"><br>
		<index>' .$result['description']. '</index>...
		</a>
		</td>
		';
		if($i>=2){
			$i=0;
			$content .= '</tr><tr>';
			}
		}
	$content .= '</table>';
	
	#Парсим ключевые слова
	$rubr_keywords = 'Каталог оборудования, продукция компании, каталог продукции, обзор оборудования';
	$rubr_keywords = explode(",", $rubr_keywords);
	$page_tag .= '<strong><span class="title2"><em>У нас ищут:</em></span></strong> ';
	foreach ($rubr_keywords as $key => $value){
		$value = ltrim($value);
		$page_tag .= '<img src="/cfg/img/item2.gif"> &lsaquo;<span style="font-family:Arial, Helvetica, sans-serif; font-size:14px"><em>' .$value. '</em></span>&rsaquo; ';
		}


	}
#
#Выбрана рубрика
#
elseif(!empty($id_rubr) && empty($id_product)){
	#Заносим в статистику факт просмотра это рубрики
	$query = mysql_query("UPDATE tbl_catalog SET count_open=count_open+1 WHERE id='" .$id_rubr. "'");
	#Достаем информацию о выбранном каталоге
	$query = mysql_query("SELECT * FROM tbl_catalog WHERE id='" .$id_rubr. "'");
	if(!mysql_num_rows($query)){
		header('Location: /');
		exit();
		}	
	$result = mysql_fetch_array($query);
	$caption = $result['caption'];
	$rubr_name = $result['name'];
	$rubr_id_parent = $result['id_parent'];
	$id_parent_main = $result['id_parent_main'];
	$rubr_vlog = @mysql_result(mysql_query("SELECT id FROM tbl_catalog WHERE id_parent='" .$id_rubr. "'"), '0', 'id');
	$rubr_image = $result['image'];
	$rubr_description = $result['description'];
	$rubr_tag = $result['CatText'];
	$rubr_static = $result['static'];	
    $rubr_keywords = $result['keywords'];

    $PageTitleSEO = $result['PageTitleSEO'];
    $PageDescription = $result['PageDescription'];
    $PageKeywords = $result['PageKeywords'];
    $PageAuthor = $result['PageAuthor'];
    $PageRobots = $result['PageRobots'];

	if($caption&&$caption_catalog!=$caption){
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: http://www.ramok.by/catalog/' .$caption. '-'.$id_rubr);
		exit();		
		}
	if($id_rubr==42){
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: http://www.ramok.by/catalog/' .$caption. '-129');
		exit();		
		}
	#Достаем данные родительской категории
	if($rubr_id_parent){
		$query = mysql_query("SELECT id, id_parent, caption, name FROM tbl_catalog WHERE id='" .$rubr_id_parent. "'");
		$result = mysql_fetch_array($query);
		$parent_urls = '‹‹ <a href="/catalog/' .$result['caption']. '-' .$result['id']. '" class="link_menu">' .$result['name']. '</a> ';
		if($result['id_parent']){
			$query = mysql_query("SELECT id, id_parent, caption, name FROM tbl_catalog WHERE id='" .$result['id_parent']. "'");
			$result = mysql_fetch_array($query);
			$parent_urls = '‹‹ <a href="/catalog/' .$result['caption']. '-' .$result['id']. '" class="link_menu">' .$result['name']. '</a> '.$parent_urls;
			}
		}

	$content .= '<span style="font-family:Arial, Helvetica, sans-serif; font-size:14px"><em>' .$rubr_tag. '</em></span><br><br>';
	$title = $rubr_name;
	$page_navigation = '<a href="/client/catalog/" class="link_menu">Каталог</a> ' .$parent_urls. ' ‹‹ ' .$rubr_name;;

	if($PageTitleSEO){
		$page_title = $PageTitleSEO;
		}
	else{
		$page_title = $rubr_name. ' - описание, технические характеристики, цены, покупка, продажа';
		}
	
	if($PageDescription){
		$page_description = $PageDescription;
		}
	else{
		$page_description = $rubr_description;
		}
	$page_keywords = $PageKeywords;
	$page_author = $PageAuthor;
	$page_robots = $PageRobots;
	
	
	#Парсим ключевые слова
	$rubr_keywords = explode(",", $rubr_keywords);
	$page_tag .= '<strong><span class="title2"><em>У нас ищут:</em></span></strong> ';
	foreach ($rubr_keywords as $key => $value){
		$value = ltrim($value);
		$page_tag .= '<img src="/cfg/img/item2.gif"> &lsaquo;<span style="font-family:Arial, Helvetica, sans-serif; font-size:14px"><em>' .$value. '</em></span>&rsaquo; ';
		}

	#Теперь формируем контент
	#Если есть подрубрики
	if($rubr_vlog != 0){
		#Если динамичная рубрика
		if($rubr_static == 0){
			$i=0;
			#Достаем рубрики каталога
			$query = mysql_query("SELECT * FROM tbl_catalog WHERE id_parent='" .$id_rubr. "' AND IsActive='1' ORDER BY position");
			$content .= '<table width="100%" class="content"><tr>';
			while($result = mysql_fetch_array($query)){
				$i++;
				$content .= '
				<td width="50%" valign="middle" style="padding:5px; min-height:100px">
				<a href="/catalog/' .$result['caption']. '-' .$result['id']. '"><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:18px"><em>' .$result['name']. '</em></span><br>
				<img align="left" border="0" src="/images/catalog/' .$result['image']. '" hspace="10" vspace="10" alt="' .$result['image_alt']. '"><br>
				<index>' .$result['description']. '</index>
				</a>
				</td>
				';
				if($i == 2){
					$i=0;
					$content .= '</tr><tr>';
					}
				}
			$content .= '</table>';			
			}
		#Если статическая рубрика
		else{
			#достаем контент
			$query = mysql_query("SELECT CatText FROM tbl_catalog WHERE id='" .$id_rubr. "'");
			$content = mysql_result($query, '0', 'CatText');
			}
		}
	#Если нет подрубрик
	#else{
		#Если динамичная рубрика
		if($rubr_static == 0){
			#Достаем количество
			$query = mysql_query("SELECT COUNT(*) AS crows FROM tbl_products WHERE id_rubr='" .$id_rubr. "' and IsActive <> 0 ORDER BY position");
			$count_products = mysql_result($query, 0, 'crows');
			
			#Достаем товары
			$query = mysql_query("SELECT *,
			(SELECT discount FROM tbl_catalog WHERE tbl_products.id_rubr=tbl_catalog.id LIMIT 1) AS discountrubr,
			(SELECT tbl_modification.price FROM tbl_modification WHERE tbl_modification.id_product=tbl_products.id AND tbl_modification.price>0 ORDER BY tbl_modification.price LIMIT 1) AS price_mod,
			(SELECT tbl_modification.price_old FROM tbl_modification WHERE tbl_modification.id_product=tbl_products.id AND tbl_modification.price>0 ORDER BY tbl_modification.price LIMIT 1) AS price_mod_old,
			(SELECT tbl_modification.id FROM tbl_modification WHERE tbl_modification.id_product=tbl_products.id AND tbl_modification.price>0 ORDER BY tbl_modification.sortorder ASC LIMIT 1) AS id_mod,
			(SELECT ProductImg FROM tbl_productimg WHERE tbl_productimg.ProductID=tbl_products.id ORDER BY SortOrder LIMIT 1) AS image FROM tbl_products WHERE id_rubr='" .$id_rubr. "' and IsActive <> 0 ORDER BY position LIMIT 20");
			
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
					$result['price'] = $result['price'] - $result['price']/100*$result['discount'];
					}
				
				
				//if($_SESSION['is_diller']){$result['price']=$result['price_diller'];}
				
				//if(2==2){
				if(!empty($_SESSION['user_id'])||$result['discount']||$show_price){
					if($result['price'] != '0'){
						$price = '<strong><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px"><em>' .number_format($result['price'], 2, ",", " "). ' р. '.$no_nds.'  </em></span></strong>';
						$buy = '<a class="addcart" href="?addbasket=' .$result['id']. '&mod=' .$mod. '"><img align="absmiddle" border="0" src="/cfg/cart.gif" width="20" height="20" alt="Добавить ' .$result['name']. ' в корзину" title="Добавить ' .$result['name']. ' в корзину"></a>';
						if($result['price_old']&&$result['price_old']>$result['price']){
							$price .= '<br><span style="text-decoration:line-through">' .number_format($result['price_old'], 2, ",", " "). ' р. </span>';
							}					
						}
					else{
						if($result['price_usd']){$price_usd = '';}else{$price_usd='';}
						$price = '<strong><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px"><em>Под заказ<br>' .zeroInNull($price_usd). '</em></span></strong>';
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
						<td width="130px" align="right" valign="middle" style="padding:2px; border-top:1px solid #CCCCCC;  background-color:#F9F9F9; text-align: center">' .$price. '</td>
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

			}
		#Если статическая рубрика
		else{
			#Достаем контент
			$query = mysql_query("SELECT CatText FROM tbl_catalog WHERE id='" .$id_rubr. "'");
			$content = mysql_result($query, '0', 'CatText');
			}		
		#}
	
	}
#
#А тут уже идет показ товара
#
elseif(empty($id_rubr) && !empty($id_product)){
	#Заносим в статистику факт просмотра это рубрики
	$query = mysql_query("UPDATE tbl_products SET count_open=count_open+1 WHERE id='" .$id_product. "'");
	#Достаем информацию о товаре	
	$query = mysql_query("SELECT *, (SELECT discount FROM tbl_catalog WHERE tbl_products.id_rubr=tbl_catalog.id LIMIT 1) AS discountrubr	 FROM tbl_products				 
	WHERE id='" .$id_product. "'");
	if(!mysql_num_rows($query)){
		header('Location: /');
		exit();
		}		
	$result = mysql_fetch_array($query);
    $id_rubr = $result['id_rubr'];
	$name = $result['name'];
	$caption = $result['caption'];
	$name2 = $result['name2'];
	$opis1 = $result['opis1'];
	$opis2 = $result['opis2'];
	$price = $result['price'];
	$price_orig = $result['price'];
	$price_old = $result['price_old'];

	$discount = $result['discount'];
	$discountrubr = $result['discountrubr'];

	if($_SESSION['is_diller']){$price=$result['price_diller'];}

	if(!$discount){
		$discount = $discountrubr;
		}
	
	if($discount){
		$price_old = $price;
		$price = $price - $price/100*$discount;
		$price_orig = $price;
		}

	if($caption&&$caption_catalog!=$caption){
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: http://www.ramok.by/product/' .$caption. '-'.$id_product);
		exit();		
		}


	$tech_characteristic = $result['tech_characteristic'];
	$tech_recommend = $result['tech_recommend'];
	$id_also = $result['id_also'];
	$PageTitleSEO = $result['PageTitleSEO'];
	$PageDescription = $result['PageDescription'];
	$PageKeywords = $result['PageKeywords'];
	$PageAuthor = $result['PageAuthor'];
	$PageRobots = $result['PageRobots'];

    #Парсим ключевые слова
    $tag = $result['keywords'];
    $doc['keywords'] = $tag;
    $tag = explode(",", $tag);
    $page_tag = '<strong><span class="title2"><em>У нас ищут:</em></span></strong> ';
    foreach ($tag as $key => $value){
        $value = ltrim($value);
    	$page_tag .= '<img src="/cfg/img/item2.gif"> &lsaquo;<span style="font-family:Arial, Helvetica, sans-serif; font-size:14px"><em>' .$value. '</em></span>&rsaquo; ';
    	}
	
    #Достаем инфу о рубрике
    $query = mysql_query("SELECT id_parent, name, caption FROM tbl_catalog WHERE id='" .$id_rubr. "'");
    $rubr_name = mysql_result($query, '0', 'name');
    $rubr_id_parent = mysql_result($query, '0', 'id_parent');
    $rubr_caption = mysql_result($query, '0', 'caption');

	#Достаем данные родительской категории
	if($rubr_id_parent){
		$query = mysql_query("SELECT id, id_parent, caption, name FROM tbl_catalog WHERE id='" .$rubr_id_parent. "' AND IsActive='1'");
		$result = mysql_fetch_array($query);
		$parent_urls = '‹‹ <a href="/catalog/' .$result['caption']. '-' .$result['id']. '" class="link_menu">' .$result['name']. '</a> ';
		if($result['id_parent']){
			$query = mysql_query("SELECT id, id_parent, caption, name FROM tbl_catalog WHERE id='" .$result['id_parent']. "' AND IsActive='1'");
			$result = mysql_fetch_array($query);
			$parent_urls = '‹‹ <a href="/catalog/' .$result['caption']. '-' .$result['id']. '" class="link_menu">' .$result['name']. '</a> '.$parent_urls;
			}
		}

    #Информация для страницы
	$title = $name;
    $page_navigation = '<a href="/client/catalog/" class="link_menu">Каталог</a> ' .$parent_urls. ' ‹‹ <a href="/catalog/' .$rubr_caption. '-' .$id_rubr. '" class="link_menu">' .$rubr_name. '</a>';

	if($PageTitleSEO){
		$page_title = $PageTitleSEO;
		}
	else{
		$page_title = $name. ' - описание, технические характеристики, цена, покупка, продажа';
		}
	
	if($PageDescription){
		$page_description = $PageDescription;
		}
	else{
		$page_description = $rubr_description;
		}
	$page_keywords = $PageKeywords;
	$page_author = $PageAuthor;
	$page_robots = $PageRobots;


	$query2 = mysql_query("SELECT * FROM tbl_productimg WHERE ProductID='" .$id_product. "' ORDER BY SortOrder");
	$i=0;
	$count_photo = mysql_num_rows($query2);
	while($result2 = mysql_fetch_array($query2)){
		$images .= '<a rel="gal" class="asd" title="' .($i+1). ' из ' .$count_photo. ' ' .$result2['ProductImgName']. '" target="_blank" href="/images/product/' .$result2['ProductImg']. '"><img border="0" align="absmiddle" src="/images/product/Small/' .$result2['ProductImg']. '"></a> ';
		if(!$i){$img1 = $result2['ProductImg'];}
		$i++;
		}

	if(!empty($images)){
		$images = '<b>Фотографии:</b> ' .$images;
		$img = '/images/product/Small/' .$img1;
		}
	else{
		$img = '/image/no-image.gif';
		}




	#Достаем товары сопутствующие
	if($id_also){
			#Достаем товары
			$query = mysql_query("SELECT *,
			(SELECT discount FROM tbl_catalog WHERE tbl_products.id_rubr=tbl_catalog.id LIMIT 1) AS discountrubr,
			(SELECT tbl_modification.price FROM tbl_modification WHERE tbl_modification.id_product=tbl_products.id AND tbl_modification.price>0 ORDER BY tbl_modification.price LIMIT 1) AS price_mod,
			(SELECT tbl_modification.price_old FROM tbl_modification WHERE tbl_modification.id_product=tbl_products.id AND tbl_modification.price>0 ORDER BY tbl_modification.price LIMIT 1) AS price_mod_old,
			(SELECT tbl_modification.id FROM tbl_modification WHERE tbl_modification.id_product=tbl_products.id AND tbl_modification.price>0 ORDER BY tbl_modification.sortorder ASC LIMIT 1) AS id_mod,
			(SELECT ProductImg FROM tbl_productimg WHERE tbl_productimg.ProductID=tbl_products.id ORDER BY SortOrder LIMIT 1) AS image FROM tbl_products WHERE id IN(" .$id_also. ") ORDER BY position");
			
			while($result = mysql_fetch_array($query)){
				if($result['image'] == ''){$result['img'] = '/image/no-image.gif';}
				else{$result['img'] = '/images/product/Small/' .$result['image'];}
				
				if($_SESSION['is_diller']){$result['price']=$result['price_diller'];}


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
					$result['price'] = $result['price'] - $result['price']/100*$result['discount'];
					}
					
				if($result['price_old']&&$result['price_old']>$result['price']){
					$price .= '<br><span style="text-decoration:line-through">' .$result['price_old']. ' р.</span>';
					}
				
				
				
				//if(2==2){
				if(!empty($_SESSION['user_id'])||$show_price){
					if($result['price'] != '0'){
						$result['price'] = number_format($result['price'], 2, ",", " ");
						$price = '<strong><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px"><em>' .$result['price']. ' р. '.$no_nds.'</em></span></strong>';
						$buy = '<a href="?addbasket=' .$result['id']. '"><img align="absmiddle" border="0" src="/cfg/cart.gif" width="20" height="20" alt="Добавить ' .$result['name']. ' в корзину" title="Добавить ' .$result['name']. ' в корзину"></a>';
						if($result['price_old']&&$result['price_old']>$result['price']){
							$price .= '<br><span style="text-decoration:line-through">' .number_format($result['price_old'], 2, ",", " "). ' р.</span>';
							}						
						}
					else{
						if($result['price_usd']){$price_usd = '';}else{$price_usd='';}
						$price = '<strong><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px"><em>Под заказ<br>' .zeroInNull($price_usd). '</em></span></strong>';
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
				
				$list_othergoods .= '<table width="100%" class="content" cellpading="0" cellspacing="0">
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
		}
	


	#Достаем файлы для товара
	$query = mysql_query("SELECT * FROM tbl_files WHERE idtovar LIKE '%[" .$id_product. "]%'");
	if(mysql_num_rows($query)){
		while($result = mysql_fetch_array($query)){
			$list_files .= '<img align="absmiddle" src="/cfg/image/price.gif"> <span class="filedownload"><a download href="/files/' .$result['name']. '">'.$result['caption'].'</a> (' .round(@filesize('../files/' .$result['name'])/1000000, 2). 'Mb)<br>' .$result['opis']. '</span><br>';
			}
		}
	
	#
	#Смотрим модификации
	#
	$query = mysql_query("SELECT * FROM tbl_modification WHERE id_product='" .$id_product. "' AND price>0 ORDER BY price");
	if(mysql_num_rows($query)){
		$i=1;
		while($result = mysql_fetch_array($query)){
			if($i==1){
				if($result['price']){
					if($discount){
						$result['price_old'] = $result['price'];
						$result['price'] =$result['price'] - $result['price']/100*$discount;
						}
					$price_mod = '<strong><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px"><em>' .number_format($result['price'], 2, ",", " "). ' р. '.$no_nds.'</em></span></strong>';
					if($result['price_old']&&$result['price_old']>$result['price']){
						$price_mod .= '<br><span style="text-decoration:line-through">' .number_format($result['price_old'], 2, ",", " "). ' р.</span>';
						}	
					$buy = '<a href="?addbasket=' .$id_product. '&mod=' .$result['id']. '"><img border="0" src="/cfg/cart.gif" width="20" height="20" alt="Добавить ' .$name. ' в корзину" title="Добавить ' .$name. ' в корзину"></a>';
					}
				else{
					if($result['price_usd']){$price_usd = '';}else{$price_usd='';}		
					$price_mod = '<strong><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px"><em>Под заказ<br>' .zeroInNull($price_usd). '</em></span></strong>';
					$buy = '<a href="#order"><img align="absmiddle" border="0" src="/cfg/cart.gif" style="cursor:pointer" width="20" height="20" alt="Добавить ' .$result['name']. ' в корзину" title="Добавить ' .$result['name']. ' в корзину"></a>';
					}
				}
			if($mod==$result['id']){
				$list_modifications .= '<option selected value="' .$result['id']. '">' .$result['name']. '</option>';
				//if(2==2){
				if(!empty($_SESSION['user_id'])||$discount||$show_price){
					if($result['price']){
						if($discount){
							$result['price_old'] = $result['price'];
							$result['price'] = $result['price'] - $result['price']/100*$discount;
							}
						$price_mod = '<strong><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px"><em>' .number_format($result['price'], 2, ",", " "). ' р. '.$no_nds.'</em></span></strong>';
						if($result['price_old']&&$result['price_old']>$result['price']){
							$price_mod .= '<br><span style="text-decoration:line-through">' .number_format($result['price_old'], 2, ",", " "). ' р.</span>';
							}	
						$buy = '<a href="?addbasket=' .$id_product. '&mod=' .$mod. '"><img border="0" src="/cfg/cart.gif" width="20" height="20" alt="Добавить ' .$name. ' в корзину" title="Добавить ' .$name. ' в корзину"></a>';
						}
					else{
						if($result['price_usd']){$price_usd = '';}else{$price_usd='';}
						$price_mod = '<strong><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px"><em>Под заказ<br>' .zeroInNull($price_usd). '</em></span></strong>';
						$buy = '<a href="#order"><img align="absmiddle" border="0" src="/cfg/cart.gif" style="cursor:pointer" width="20" height="20" alt="Добавить ' .$result['name']. ' в корзину" title="Добавить ' .$result['name']. ' в корзину"></a>';
						}
					}
				else{
					$price = '<a href="/client/guest"><strong><div class="title2" style="padding-bottom:10px"><em>Стоимость</em></strong></div></a>';
					}			
				}
			else{
				$list_modifications .= '<option value="' .$result['id']. '">' .$result['name']. '</option>';
				}
			$i++;
			}
		if(!empty($_SESSION['user_id'])||$show_price){
			$modifications = '
			<tr>
				<td style="border-top:1px solid #CCCCCC;  padding:5px; background-color:#F9F9F9" colspan="3" align="right">
				<form method="get">
					Выберите вариант исполнения: <select onchange="this.form.submit();" name="mod">' .$list_modifications. '</select>
				</form>
				</td>
			</tr>
			';
			}
		}


	if(!$price_mod){
		
		//if(!$_SESSION['user_id']&&$price_old){
			//$price = $price_old;
			//}
		
		//if(2==2){
		if(!empty($_SESSION['user_id'])||$price_old||$show_price){
			
			if($price_orig != '0'){
				$price = '<strong><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px"><em>' .number_format($price_orig, 2, ",", " "). ' р. '.$no_nds.'</em></span></strong>';
				$buy = '<a href="?addbasket=' .$id_product. '&mod=' .$mod. '"><img border="0" src="/cfg/cart.gif" width="20" height="20" alt="Добавить ' .$name. ' в корзину" title="Добавить ' .$name. ' в корзину"></a>';
				if($price_old&&$price_old>$price_orig){
					$price .= '<br><span style="text-decoration:line-through">' .number_format($price_old, 2, ",", " "). ' р.</span>';
					}			
				}
			else{
				if($result['price_usd']){$price_usd = '';}else{$price_usd='';}
				$price = '<strong><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px"><em>Под заказ<br>' .zeroInNull($price_usd). '</em></span></strong>';
				$buy = '<a href="#order"><img border="0" style="cursor:pointer" src="/cfg/cart.gif" width="20" height="20" alt="Купить ' .$result['name']. '" title="Добавить ' .$result['name']. ' в корзину"></a>';
				}
			
			}
		else{
			$price = '<a href="/client/guest"><strong><div class="title2" style="padding-bottom:10px"><em>Стоимость</em></strong></div></a>';
			$buy = '<a href="#order"><img align="absmiddle" border="0" src="/cfg/cart.gif" style="cursor:pointer" width="20" height="20" alt="Добавить ' .$result['name']. ' в корзину" title="Добавить ' .$result['name']. ' в корзину"></a>';
			}
		}
	else{
		$price = $price_mod;
		}



	$content .= '
    <table width="100%" class="content" cellpading="0" cellspacing="0">
	    <tr>
	        <td style="border-top:1px solid #CCCCCC; padding-left:5px; background-color:#F9F9F9"><a target="_blank" href="/client/catalog/print/?id=' .$id_product. '"><img border="0" align="absmiddle" src="/cfg/print.gif"></a> <a href="/client/catalog/?id_product=' .$id_product. '"><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:18px"><em>' .$name. '</em></span></a></td>
	        <td width="130px" align="right" valign="middle" style="padding:2px; border-top:1px solid #CCCCCC;  background-color:#F9F9F9; text-align: center">' .$price. '</td>
	        <td width="40px" style="border-top:1px solid #CCCCCC; background-color:#F9F9F9">' .$buy. '</td>
	    </tr>
		' .$modifications. '
	</table>
	<table width="100%" class="content" cellpading="0" cellspacing="0">
	    <tr>
	        <td valign="top" width="100%" style="border-top:1px solid #CCCCCC; padding:7px; font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px">
	        ' .$opis1. '
	        </a>
	        </td>
	    </tr>
	    <tr>
	        <td align="center" colspan="2" width="100%" style="border-bottom:1px solid #CCCCCC;">
			
			' .$images. '

	        </td>
	    </tr>
	</table>
	
	<ul class="ttabs">
		<li class="active"><a id="layer_description" href="">Описание</a></li>';
		if($tech_characteristic){$content .='<li><a id="layer_tech" href="#tech">Технические характеристики</a></li>';}
		if($tech_recommend){$content .='<li><a id="layer_recommend" href="#recommend">Рекоммендации</a></li>';}
		if($list_files){$content .='<li><a id="layer_files" href="#files">Файлы</a></li>';}
		if($list_othergoods){$content .='<li><a id="layer_othergoods" href="#othergoods">Сопутствующие товары</a></li>';}
	
	$content .= '</ul>
	<div style="clear:both"></div>
	
    <div class="ttabslay layer_description">' .$opis2. '</div>';
	
    if($tech_characteristic){$content .='<div class="ttabslay hidden layer_tech">' .$tech_characteristic. '</div>';}
    if($tech_recommend){$content .='<div class="ttabslay hidden layer_recommend">' .$tech_recommend. '</div>';}
    if($list_files){$content .='<div class="ttabslay hidden layer_files" >' .$list_files. '</div>';}
    if($list_othergoods){$content .='<div class="ttabslay hidden layer_othergoods">' .$list_othergoods. '</div>';}

	
	}


function tree($ids,$level,$CatID,$table){ 
	$level++;
	$query = "SELECT * FROM " .$table. " WHERE id_parent=" . $ids . " ORDER BY position";
	$result = mysql_query($query);
	
	if($level==1){
		while($print = mysql_fetch_array($result)){ 
			$ids = $print['id'];
			if($CatID==$ids){
				$left_menu .= '<option selected value="?rubr=' .$print['id']. '">' .$print['name']. '</option>';
				}
			else{
				$left_menu .= '<option value="?rubr=' .$print['id']. '">' .$print['name']. '</option>';
				}
			$left_menu .= tree($ids,$level,$CatID,$table);
			}
		}
	if($level==2){
		while($print = mysql_fetch_array($result)){ 
			$ids = $print['id'];
			if($CatID==$ids){
				$left_menu .= '<option selected value="?rubr=' .$print['id']. '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .$print['name']. '</option>';
				}
			else{
				$left_menu .= '<option value="?rubr=' .$print['id']. '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .$print['name']. '</option>';
				}
			$left_menu .= tree($ids,$level,$CatID,$table);
			}
		}
	if($level==3){
		while($print = mysql_fetch_array($result)){ 
			$ids = $print['id'];
			if($CatID==$ids){
				$left_menu .= '<option selected value="?rubr=' .$print['id']. '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .$print['name']. '</option>';
				}
			else{
				$left_menu .= '<option value="?rubr=' .$print['id']. '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .$print['name']. '</option>';
				}			
			$left_menu .= tree($ids,$level,$CatID,$table);
			}
		}
	if($level==4){
		while($print = mysql_fetch_array($result)){ 
			$ids = $print['id'];
			if($CatID==$ids){
				$left_menu .= '<option selected value="?rubr=' .$print['id']. '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .$print['name']. '</option>';
				}
			else{
				$left_menu .= '<option value="?rubr=' .$print['id']. '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .$print['name']. '</option>';
				}			
			}
		}
			
	return $left_menu;
	}
$list_category = tree(0,0,0,'tbl_catalog');


?>