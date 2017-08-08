<?

if(!$_SESSION['user_id']){
header('Location: /client/enter');
exit();
}

$a = $_REQUEST['a'];
$show = $_REQUEST['show'];
$kkm_akt = $_REQUEST['kkm_akt'];


if($kkm_akt){

	$query = mysql_query("select * from tbl_user where id='" .$_SESSION['user_id']. "'");
	$result = mysql_fetch_array($query);
	$show = $_REQUEST['show'];
	$unn = $result['unn'];
	$dogovor = $result['dogovor'];
	$name = stripslashes(html_entity_decode($result['name']));
	$email = $result['email'];
	$company = stripslashes(html_entity_decode($result['company']));
	$pass = $result['pass'];
	$login = $result['login'];
	$phone = $result['phone'];
	$city = $result['city'];
	
	$adress = $result['adress'];
	$bank_code = $result['bank_code'];
	$bank_name = html_entity_decode($result['bank_name']);
	$bank_adress = $result['bank_adress'];
	$bank_schet = $result['bank_schet'];


	$query = mysql_query("SELECT * FROM tbl_cash_history WHERE sys_id='" .$kkm_akt. "'");
	$result = mysql_fetch_array($query);

	header("Content-Disposition: attachment; filename='akt.xlsx'");
	
	include '../plugins/phpexcel/Classes/PHPExcel/IOFactory.php';
	
	include '../plugins/num2text.php';
	
	//$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	//$cacheSettings = array( 'memoryCacheSize' => '100MB');
	//PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

	$objReader = new PHPExcel_Reader_Excel2007();

	//$objReader->setReadDataOnly(true); 
	
	$objPHPExcel = $objReader->load('../plugins/akt.xlsx');

	$nds = $result['price']/100*20;
	$all_price = $result['price']*1 + $nds;

	$nds = $result['price']/100*20;

	$mt = new ManyToText();
	$all_price_text = $mt->Convert($all_price);
	$nds_text = $mt->Convert($nds);
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', iconv('windows-1251', 'UTF-8', 'Акт № '.$result['akt']. ' от '.date("d.m.Y",strtotime($result['date_to']))))
		->setCellValue('F14', $result['price'])
		->setCellValue('G14', $result['price'])
		->setCellValue('G15', $result['price'])
		->setCellValue('G16', $nds);
		
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('G17', $all_price)
		->setCellValue('B9', trim(iconv('windows-1251', 'UTF-8', $company)))
		->setCellValue('B10', trim(iconv('windows-1251', 'UTF-8', 'Р/сч: '.$bank_schet.' в '.$bank_name.' '.$bank_adress.' код '.$bank_code.', УНП: '.$unn)))
		->setCellValue('B11', trim(iconv('windows-1251', 'UTF-8', 'Адрес: ' .$city. ', '.$adress. ', '.$phone)))
		->setCellValue('B19', trim(iconv('windows-1251', 'UTF-8', 'Всего оказано услуг на сумму: ' .$all_price_text. ', в т.ч.: НДС - ' .$nds_text. '.')));
		

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
	$objWriter->save('php://output'); 
	
	exit();
	}



if($a=='sendmessage'){
	$text = mysql_real_escape_string($_REQUEST['text']);
	$text = nl2br($text);
	if($text){
		mysql_query("INSERT INTO tbl_message SET id_user='" .$_SESSION['user_id']. "', text='" .$text. "', date_add='" .date("Y-m-d H:i:s"). "'");
		$messagenotify = '<strong>Спасибо! Ваше сообщение отправлено.</strong><br>';
		}
	}

if($show=='message'){
	$query = mysql_query("SELECT *,
	(SELECT AdminFullName FROM tbl_admin WHERE tbl_admin.AdminID=tbl_message.id_admin) AS admin_name FROM tbl_message WHERE id_user='" .$_SESSION['user_id']. "' ORDER BY date_add DESC");
	while($result=mysql_fetch_array($query)){
		if($result['id_admin']){
			$list_message .= '<div style="background-color:#ddd; padding:5px; margin-bottom:5px; margin-top:5px;"><strong>' .$result['admin_name']. '</strong> (' .date("d.m.Y H:i:s",strtotime($result['date_add'])). ')</div>';
			}
		else{
			$list_message .= '<div style="background-color:#ddd; padding:5px; margin-bottom:5px; margin-top:5px;"><strong>Вы</strong> (' .date("d.m.Y H:i:s",strtotime($result['date_add'])). ')</div>';
			}
		$list_message .= '
		' .$result['text']. '<br><br>
		';
		}
	if(!$list_message){
		$list_message = 'В истории пока нет сообщений...';
		}
	}

if($show=='kassa'){
	$query = mysql_query("SELECT * FROM tbl_cash_history WHERE unn='" .$_SESSION['user_unn']. "'");
	while($result=mysql_fetch_array($query)){
		//$list_cash .= '<strong>' .$result['num_cushregister'].'</strong> <a href="http://ramok.by/plugins/phpword/doc/akt.php?id=' .$result['sys_id']. '">Скачать акт от ' .date("d.m.Y",strtotime($result['date_to'])). '</a><br>';
		$list_cash .= '<strong>' .$result['num_cushregister'].'</strong> <a href="?kkm_akt=' .$result['sys_id']. '">Скачать акт от ' .date("d.m.Y",strtotime($result['date_to'])). '</a><br>';
		}
	
	if(!$list_cash){
		$list_cash = 'Кассовых аппаратов не найдено.';
		}
	
	}
	
if($a=='saveanketa'){
	$qw1 = mysql_real_escape_string($_REQUEST['qw1']);
	$qw1_add = mysql_real_escape_string($_REQUEST['qw1_add']);
	$qw2 = mysql_real_escape_string($_REQUEST['qw2']);
	$qw3 = mysql_real_escape_string($_REQUEST['qw3']);
	$qw3_add = mysql_real_escape_string($_REQUEST['qw3_add']);
	$qw4 = mysql_real_escape_string($_REQUEST['qw4']);
	$qw4_add = mysql_real_escape_string($_REQUEST['qw4_add']);
	$qw5 = mysql_real_escape_string($_REQUEST['qw5']);
	$qw5_add = mysql_real_escape_string($_REQUEST['qw5_add']);
	$qw6 = mysql_real_escape_string($_REQUEST['qw6']);
	$qw7 = mysql_real_escape_string($_REQUEST['qw7']);
	$qw8 = mysql_real_escape_string($_REQUEST['qw8']);
	$qw91 = mysql_real_escape_string($_REQUEST['qw91']);
	$qw92 = mysql_real_escape_string($_REQUEST['qw92']);
	$qw93 = mysql_real_escape_string($_REQUEST['qw93']);
	$qw94 = mysql_real_escape_string($_REQUEST['qw94']);
	$qw95 = mysql_real_escape_string($_REQUEST['qw95']);
	$qw9_add = mysql_real_escape_string($_REQUEST['qw9_add']);
	$qw10 = mysql_real_escape_string($_REQUEST['qw10']);
	$qw11 = mysql_real_escape_string($_REQUEST['qw11']);
	$qw12 = mysql_real_escape_string($_REQUEST['qw12']);
	$qw131 = mysql_real_escape_string($_REQUEST['qw131']);
	$qw132 = mysql_real_escape_string($_REQUEST['qw132']);
	$qw133 = mysql_real_escape_string($_REQUEST['qw133']);
	$qw134 = mysql_real_escape_string($_REQUEST['qw134']);
	$qw135 = mysql_real_escape_string($_REQUEST['qw135']);
	$qw136 = mysql_real_escape_string($_REQUEST['qw136']);
	$qw14 = mysql_real_escape_string($_REQUEST['qw14']);
	$qw15 = mysql_real_escape_string($_REQUEST['qw15']);
	
	$query = mysql_query("INSERT INTO tbl_anketa SET
	qw1='" .$qw1. "', qw1_add='" .$qw1_add. "', qw2='" .$qw2. "', qw3='" .$qw3. "', qw3_add='" .$qw3_add. "', qw4='" .$qw4. "',
	qw4_add='" .$qw4_add. "', qw5='" .$qw5. "', qw5_add='" .$qw5_add. "', qw6='" .$qw6. "', qw7='" .$qw7. "',
	qw8='" .$qw8. "', qw91='" .$qw91. "', qw92='" .$qw92. "', qw93='" .$qw93. "', qw94='" .$qw94. "',
	qw95='" .$qw95. "', qw9_add='" .$qw9_add. "', qw10='" .$qw10. "', qw11='" .$qw11. "', qw12='" .$qw12. "',
	qw131='" .$qw131. "', qw132='" .$qw132. "', qw133='" .$qw133. "', qw134='" .$qw134. "', qw135='" .$qw135. "',
	qw136='" .$qw136. "', qw14='" .$qw14. "', qw15='" .$qw15. "', 
	id_user='" .$_SESSION['user_id']. "', date_add='" .date("Y-m-d H:i;s"). "'	
	");

	$query = mysql_query("select * from tbl_user where id='" .$_SESSION['user_id']. "'");
	$result = mysql_fetch_array($query);
	$name = stripslashes($result['name']);
	$email = $result['email'];
	$company = stripslashes($result['company']);
	$phone = $result['phone'];

	$ball = $qw131+$qw132+$qw133+$qw134+$qw135+$qw136;

	#Теперь отправляем письмо о регистрации
	include '../includes/class.phpmailer.php';
	$m = new PHPMailer(true);
	$m->Priority = '1';
	$m->AddReplyTo('website@ramok.by', 'Сайт УП Рамок');	
	$m->AddAddress('website@ramok.by');
	#$m->AddAddress('antylevsky@ya.ru');
	$m->SetFrom('website@ramok.by', 'Сайт УП Рамок');
	$m->Subject = 'Прохождение анкетирования';
	$m->Body = 'Здравствуйте!<br>
	На сайте была заполнена анкета:<br>
	Дата: <b>' .date("Y-m-d H:i:s"). '</b><br>
	Название компании: <b>' .$company. '</b><br>
	Имя: <b>' .$name. '</b><br>
	Телефон: <b>' .$phone. '</b><br>
	Email: <b>' .$email. '</b><br>
	Оценка, баллов: <b>' .$ball. '</b><br>
	Подробное описание Вы можете увидеть в администраторской панели';
	
	if($file_name){
		$m->AddAttachment($file, $file_name);
		}
	
	$m->Send();	

	header('Location: /my');
	exit();
	}


$query = mysql_query("SELECT id FROM tbl_anketa WHERE id_user='" .$_SESSION['user_id']. "'");
if(mysql_num_rows($query)>0){
	$is_anketa_ready = 1;
	}

if($a!='anketa'){
	$is_anketa_ready = 1;
	}

$query = mysql_query("select * from tbl_user where id='" .$_SESSION['user_id']. "'");
$result = mysql_fetch_array($query);
$show = $_REQUEST['show'];
$unn = $result['unn'];
$dogovor = $result['dogovor'];
$name = stripslashes(html_entity_decode($result['name']));
$email = $result['email'];
$company = stripslashes(html_entity_decode($result['company']));
$pass = $result['pass'];
$login = $result['login'];
$phone = $result['phone'];
$city = $result['city'];

$adress = $result['adress'];
$bank_code = $result['bank_code'];
$bank_name = html_entity_decode($result['bank_name']);
$bank_adress = $result['bank_adress'];
$bank_schet = $result['bank_schet'];

$delorder = $_REQUEST['delorder'];

if($delorder){
	$query = mysql_query("SELECT id_user, status FROM tbl_orders WHERE id='" .$delorder. "'");
	$result = mysql_fetch_array($query);
	if($result['id_user']==$_SESSION['user_id'] && $result['status']==0){
		$query = mysql_query("DELETE FROM tbl_orders WHERE id='" .$delorder. "'");
		$query = mysql_query("DELETE FROM tbl_orders_details WHERE id_order='" .$delorder. "'");
		}
	$a = 'orders';
	}



if($a=='makekvitance'){
	$query = mysql_query("SELECT * FROM tbl_dolg WHERE unn='" .$unn. "'");
	$result = mysql_fetch_array($query);
	print '
	<html>
	<body style="background:url(/kvit.png); background-repeat:no-repeat">
	
	<div style="padding-left:135px; padding-top:118px; font-size:10px">УНН: ' .$unn. ', ' .$result['name']. '</div>
	<div style="padding-left:565px; padding-top:48px; font-size:14px">' .$result['amount']. '</div>
	<div style="padding-left:565px; padding-top:56px; font-size:14px">' .$result['amount']. '</div>
	
	
	<div style="padding-left:135px; padding-top:118px; font-size:10px">УНП: ' .$unn. ', ' .$result['name']. '</div>
	<div style="padding-left:565px; padding-top:50px; font-size:14px">' .$result['amount']. '</div>
	<div style="padding-left:565px; padding-top:56px; font-size:14px">' .$result['amount']. '</div>
	
	
	</body>
	</html>';
	exit();
	}

if($a=='editdetails'){
	$unn = $_REQUEST['unn'];
	$dogovor = $_REQUEST['dogovor'];
	$name = $_REQUEST['name'];
	$company = $_REQUEST['company'];
	$pass = $_REQUEST['pass'];
	$login = $_REQUEST['login'];
	$phone = $_REQUEST['phone'];
	$city = $_REQUEST['city'];
	$email = $_REQUEST['email'];

	$adress = $_REQUEST['adress'];
	$bank_code = $_REQUEST['bank_code'];
	$bank_name = $_REQUEST['bank_name'];
	$bank_adress = $_REQUEST['bank_adress'];
	$bank_schet = $_REQUEST['bank_schet'];

	$query = mysql_query("UPDATE tbl_user SET name='" .$name. "', company='" .$company. "', phone='" .$phone. "', city='" .$city. "', email='" .$email. "',
	adress='" .$adress. "', bank_code='" .$bank_code. "', bank_name='" .$bank_name. "', bank_adress='" .$bank_adress. "', bank_schet='" .$bank_schet. "', unn='" .$unn. "' where id='" .$_SESSION['user_id']. "'");

	$name = stripslashes($name);
	$company = stripslashes($company);

	$detailsnotify = 'Данные сохранены<br>';
	
	}

if($unn){
	$query = mysql_query("SELECT * FROM tbl_dolg WHERE unn='" .$unn. "'");
	if(mysql_num_rows($query)){

		$is_unn=1;
		$result = mysql_fetch_array($query);
		$c_amount = $result['amount'];
		$content = 'Компания: ' .$result['name']. '<br>
		Долг: <b>' .$result['amount']. '</b> рублей';

		if($a=='sendact'){
			$dolg = $_REQUEST['dolg'];
			$c_name = stripcslashes($_REQUEST['c_name']);
			$c_adress = stripcslashes($_REQUEST['c_adress']);
			$c_fax = stripcslashes($_REQUEST['c_fax']);
			$c_phone = stripcslashes($_REQUEST['c_phone']);
			$comment = stripcslashes($_REQUEST['comment']);
			
			$file = $_FILES["act"]["tmp_name"];
			$file_name = $_FILES["act"]["name"];

			if($dolg&&$c_phone&&$c_name){
				#Теперь отправляем письмо о регистрации
				include '../includes/class.phpmailer.php';
				$m = new PHPMailer(true);
				$m->Priority = '1';
				$m->AddReplyTo('website@ramok.by', 'Сайт УП Рамок');	
				$m->AddAddress('website@ramok.by');
				$m->SetFrom('website@ramok.by', 'Сайт УП Рамок');
				$m->Subject = 'Запрос акта сверки клиентом';
				$m->Body = 'Здравствуйте!<br>
				На сайте был запрошен акт сверки:<br>
				УНП: <b>' .$unn. '</b><br>
				Название компании: <b>' .$c_name. '</b><br>
				Адрес: <b>' .$c_adress. '</b><br>
				Телефон: <b>' .$c_phone. '</b><br>
				Факс: <b>' .$c_fax. '</b><br>
				Сумма долга по мнению клиента:  <b>' .$dolg. '</b> рублей<br>
				Сумма долга из последней выгрузки: <b>' .$c_amount. '</b> рублей<br>
				Комментарий клиента: <b>' .$comment. '</b><br>';
				
				if($file_name){
					$m->AddAttachment($file, $file_name);
					}
				
				$m->Send();	
				$dolg = '';
				$c_name = '';
				$c_adress = '';
				$c_fax = '';
				$c_phone = '';
				$comment = '';				
				$notify = '<span style="color:green">Ваше сообщение отправлено!</span><br><br>';
				}
			else{
				$notify = '<span style="color:red">Заполните необходимые поля</span><br><br>';
				}
			}

		$c_name = $result['name'];
		
		}
	else{
		$content = '<span style="color:green">Задолженности по УНП не найдено.</span>';
		}
	
	
	}


	#Проверяем ремонты
	$query = mysql_query("SELECT * FROM tbl_repair WHERE unn='" .$unn. "'");
	if(mysql_num_rows($query)){
		while($result = mysql_fetch_array($query)){
			$date = date("d.m.Y H:i:s", strtotime($result['date']));
			if($result['status']==1){
				$kkm_found .= 'Кассовый аппарат <b>№' .$result['cashregister']. '</b> - ремонт закончен (' .$date. '). Стоимость ремонта с НДС: ' .$result['price']. 'р. <a href="/files/repair/'.$result['unn'].'+'.$result['cashregister'].'.doc"><b>Скачать акт</b></a><br><br>';
				}
			else{
				$kkm_found .= 'Кассовый аппарат <b>№' .$result['cashregister']. '</b> - в ремонте (дата принятия ' .$date. ')<br><br>';
				}
			}
		}
	else{
		$kkm_found .= '<span style="color:green">кассовых аппаратов в ремонте не найдено...</span><br><br>';
		}

$query = mysql_query("SELECT * FROM tbl_orders WHERE id_user='" .$_SESSION['user_id']. "' ORDER BY date_add DESC");
if(mysql_num_rows($query)){
	$content_orders = '<table width="100%">
	<tr>
		<td width="40%"><strong>Название</strong></td>
		<td align="center"><strong>Стоимость</strong></td>
		<td align="center"><strong>Количество</strong></td>
		<td align="center"><strong>Сумма</strong></td>
	</tr>	
	';
	while($result = mysql_fetch_array($query)){
		$date_add = date("Y-m-d H:i:s", strtotime($result['date_add']));
		if($result['status']==1){
			$status = 'оплачен';
			}
		else{
			$status = 'не оплачен <a target="_blank" class="agrey2" href="/my/pay?id=' .$result['id']. '">договор</a> | <a style="color:red" class="agrey2" href="?delorder=' .$result['id']. '">удалить</a>';
			}
		$content_orders .= '<tr height="50"><td colspan="5"><b>Заказ №' .$result['id']. ' от ' .$date_add. '</b> (' .$status. ')</td></tr>';
		
		$total_summ = 0;
		$total_count = 0;
		
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
		</tr>';
		}
	$content_orders .= '</table>';
	}
else{
	$content_orders = 'Нет заказов';
	}



?>