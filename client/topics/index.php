<?
include "../../setup_mysql.php";

session_start();

if(!empty($_SESSION['factura'])){
	foreach($_SESSION['factura'] as $key => $value){
		$doc['factura'] .= $value['href'] . '<br>';	
		}
	}
else{
	$doc['factura'] = 'Пока не добавлено ни одного товара...';
	}
	
$doc['title'] = 'УП "Рамок" - Полезные статьи';
$doc['hotkey'] = 'касовый аппарат. Касби 4. Всё для магазина. Информация о торговле';
$lnk['topics'] = 'green';

$topic = $_GET['topic'];

if(!empty($topic)){
	#Если выбрана статья
	$query = 'SELECT * FROM tbl_topics WHERE id=' .$topic;
	$query = mysql_query($query);
	$id = mysql_result($query, '0', 'id');
	$name = mysql_result($query, '0', 'name');
	$text = mysql_result($query, '0', 'text');
	$rubr = mysql_result($query, '0', 'rubr');
	$doc['keywords'] = mysql_result($query, '0', 'keywords');
	
	$doc['center'] .= '
	<div align="center" class="title"><strong><em>' .$name. '</em></strong></div>
	<br>';
	
	$doc['title'] .= ' - ' .$name;
	
	$doc['center'] .= $text;
	
	$doc['nav'] = '<a href="/client/" class="link_menu">Клиентам ‹‹</a> <a href="/client/topics/" class="link_menu">Полезные статьи ‹‹</a> ' .$name;
	$doc['center_title'] = 'Полезные статьи';
	
	
	#Добавляем дополнительную информацию
	$query = mysql_query("SELECT * FROM tbl_topics WHERE id<>'" .$_GET['topic']. "' and rubr='" .$rubr. "' order by id");
	$doc['block1_title'] = 'Другие статьи';
	$doc['block1'] = '<table width="100%" cellpadding="0" cellspacing="0" class="content">';
	while($result = mysql_fetch_array($query)){
		$doc['block1'] .= '<tr><td valign="top" style="padding-top:4px; padding-right:5px;"><img width="8" height="8" src="/cfg/img/item.gif" alt="' .$result['name'] .' "></td><td width="100%" valign="top"><a href="/client/topics/?topic=' .$result['id']. '">' .$result['name'] .'</a></a></td></tr>';
		}
	$doc['block1'] .= '</table>';

	}
else{
	# Если не выбрана статья
	# форируем список рубрик 
	$query = mysql_query("SELECT * FROM rubr_topic order by id");
	
	$doc['rubr'] .= '<table width="100%"><tr>';
	$i=0;
	while($result = mysql_fetch_array($query)){
		$i++;
		if($result['id'] != $_GET['r']){
			$doc['rubr'] .= '<td align="center"><a href="?r=' .$result['id']. '"><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:16px"><strong>' .$result['rubr_name']. '</strong></span><br><img hspace="15" vspace="15" src="/image/rubr_topic/' .$result['id']. '.gif" border="0"></a></td>';
			}
		else{
			$rubr_name = $result['rubr_name'];
			$doc['keywords'] = $result['keywords'];
			}

		if($i==2){$doc['rubr'] .= '</tr><tr>'; $i=0;}
		}
	$doc['rubr'] .= '</table>';
	
	#Достаем все статьи
	$query = 'select * from tbl_topics order by id DESC';
	$query = mysql_query($query);
	
	$doc['rubr'] .= '<br>
	<table cellpadding="0" cellspacing="0" class="content" width="100%">
	<tr>
		<td class="topic"><strong>Название статьи</strong></td>
	</tr>';
	
	while($result = mysql_fetch_array($query)){
		$date = strtotime($result['date']);
		$date = date("d.m.Y", $date);
		$doc['rubr'] .= '
		<tr>
			<td height="25" valign="center" class="topic"><a style="font-size:13px" href="/client/topics/?topic=' .$result['id']. '" class="link_menu">
			<img border="0" width="14" height="13" src="/cfg/image/info.gif"> ' .$result['name']. '</a><br>
			</td>
		</tr>
		';
		}	
	$doc['rubr'] .= '</table>';	
	
		
	if(!empty($_GET['r'])){
		#Если выбрана рубрика
		
		$doc['nav'] = '<a href="/client/" class="link_menu">Клиентам ‹‹</a> <a href="/client/topics/" class="link_menu">Полезные статьи ‹‹</a>' .$rubr_name;
		$doc['center_title'] = 'Статьи: ' .$rubr_name;
		$doc['title'] = 'УП "Рамок" - Полезные статьи - ' .$rubr_name;
		$doc['center'] .= '<br>
		<img src="/cfg/image/true2.gif"><span style="font-family:Arial, Helvetica, sans-serif; font-size:14px"><em>
		На данной странице мы привели список торгового оборудования:<br>
		</em></span>';

		#Достаем статьи
		$query = 'select * from tbl_topics where rubr=' .$_GET['r']. ' order by id DESC';
		$query = mysql_query($query);
		
		$doc['center'] .= '	<br><br>
		<table cellpadding="0" cellspacing="0" class="content" width="100%">
		<tr>
			<td class="topic"><strong>Название статьи</strong></td>
		</tr>';
		
		while($result = mysql_fetch_array($query)){
			$date = strtotime($result['date']);
			$date = date("d.m.Y", $date);
			$doc['center'] .= '
			<tr>
				<td height="25" valign="center" class="topic"><a style="font-size:13px" href="/client/topics/?topic=' .$result['id']. '" class="link_menu">
				<img border="0" width="14" height="13" src="/cfg/image/info.gif"> ' .$result['name']. '</a><br>
				</td>
			</tr>
			';
			}	
		$doc['center'] .= '</table>';
		
		#Добавляем дополнительную информацию
		$query = mysql_query("SELECT * FROM rubr_topic WHERE id<>'" .$_GET['r']. "' order by id");
		$doc['block1_title'] = 'Рубрики';
		$doc['block1'] = '<table width="100%" cellpadding="0" cellspacing="0" class="content">';
		while($result = mysql_fetch_array($query)){
			$doc['block1'] .= '<tr><td valign="top" style="padding-top:4px; padding-right:5px;"><img width="8" height="8" src="/cfg/img/item.gif" alt="' .$result['name'] .' "></td><td width="100%" valign="top"><a href="/client/topics/?r=' .$result['id']. '">' .$result['rubr_name'] .'</a></a></td></tr>';
			}
		$doc['block1'] .= '</table>';
		}
	else{
		$doc['nav'] = '<a href="/client/" class="link_menu">Клиентам ‹‹</a> Полезные статьи';
		$doc['center_title'] = 'Полезные статьи';
		$doc['title'] = 'УП "Рамок" - Раздел для клиентов - Полезные статьи';
	
		$doc['center'] .= '<br>
		<img src="/cfg/image/true2.gif"><span style="font-family:Arial, Helvetica, sans-serif; font-size:14px"><em>
		Мы собрали в этом разделе интересные статьи и разместили их в следующих рубриках:<br>
		Мы надеемся, что собранная информация окажется полезной для Вас!
		</em></span><br>
		<br>
		' .$doc['rubr'];
		
		#Добавляем дополнительную информацию
		$doc['block1_title'] = 'Смотрите';
		$doc['block1'] = '<table width="100%" cellpadding="0" cellspacing="0" class="content">';
		$doc['block1'] .= '
		<tr>
			<td valign="top" style="padding-top:4px; padding-right:5px;"><img width="8" height="8" src="/cfg/img/item.gif""></td>
			<td><a href="/client/news/">Наши новости</a></td>
		</tr><tr>	
			<td valign="top" style="padding-top:4px; padding-right:5px;"><img width="8" height="8" src="/cfg/img/item.gif""></td>
			<td><a href="/client/price/">Наши цены</a></td>
		</tr><tr>		
			<td valign="top" style="padding-top:4px; padding-right:5px;"><img width="8" height="8" src="/cfg/img/item.gif""></td>
			<td><a href="/client/order/">Расчет стоимости</a></td>

		</tr>';
		$doc['block1'] .= '</table>';
		
		#Список ключевых слов
		$doc['keywords'] = 'Статьи, интересные статьи, публикации нашей компании';
		}
	}

#Парсим ключевые слова
$tag = split(",", $doc['keywords']);
$doc['tag'] .= '<strong><span class="title2"><em>У нас ищут:</em></span></strong> ';
foreach ($tag as $key => $value){
	$value = ltrim($value);
	$doc['tag'] .= '<img src="/cfg/img/item2.gif"> &lsaquo;<span style="font-family:Arial, Helvetica, sans-serif; font-size:14px"><em>' .$value. '</em></span>&rsaquo; ';
	}

include '../../blank_main.php';

?>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
