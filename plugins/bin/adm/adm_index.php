<?


#Главная админка

$a = $_REQUEST['a'];


if($a=='sendmailtest2'){
	$email = $_REQUEST['email'];
	
	include_once '../plugins/mailer.php';
	$send_title = 'привет';
	$send_text_ready = '
	Уважаемый пользователь, Имя Напоминаем Вам, что у Вас открыт на сайте www.RAMOK.by для всех за регистрирующихся пользователей личный кабинет.	
	Ваше имя для входа  :«имя», (1 раз номер договора, затем заменяем на email). Пароль: «пароль», (УНП)
	Войти в личный кабинет по ссылке.
	В Вашем кабинете Вы можете просмотреть: за должность, Акты, состояние оборудования в ремонте, покупки, оформить заказ, запросить необходимые документы, вызвать мастера, выслать Акт-сверки. В данном кабинете вы будете видеть вашу персональную скидку на товар.
	
	Сделать ссылки, на последние новости, которые были сделаны за последний месяц.
	
	Ссылки на действующие Акции, распродажи.	
	
	«Напоминаем Вам что Ваша за должность составляет: указать сумму рублей с НДС 20%» «Благодарим Вас за своевременную оплату» 
	
	При заказе товаров через сайт с Вашего личного кабинета Вы получите скидку (мы сами ее устанавливаем, она может меняться).	
	
	
	';
	mailer_send('antylevsky@ya.ru', $send_title, $send_text_ready, $files);
	
	}


	
if($a=='importprice'){

	$date_add = date("Y-m-d");
	
	//mysql_query("UPDATE tbl_products SET price='0'");
	//mysql_query("UPDATE tbl_modification SET price='0'");


	include '../plugins/phpexcel/Classes/PHPExcel/IOFactory.php';
	$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	$cacheSettings = array( 'memoryCacheSize' => '100MB');
	PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
	
	
	$objReader = new PHPExcel_Reader_Excel2007();
		
	$objReader->setReadDataOnly(true); 
	
	$objPHPExcel = $objReader->load($_FILES['price']['tmp_name']);
	
	$objWorksheet = $objPHPExcel->getActiveSheet();
	
	$n=0;
	$c=0;
	foreach ($objWorksheet->getRowIterator() as $row) {
		$n++;
		
		if($n>1){
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false); 
			
			$i = 0;
			foreach ($cellIterator as $cell) {
				$i++;
				if($i==1){$artikul = $cell->getValue();}
				if($i==5){$price_client = $cell->getValue();}
				if($i==6){$price_diller = $cell->getValue();}
				if($i==7){$count = $cell->getValue();}
				}
			 
			$price_client = str_replace(',', '.', $price_client);
			$price_diller = str_replace(',', '.', $price_diller);
			
			$artikul = mb_convert_encoding($artikul,'cp1251','utf8');
			$artikul = trim($artikul);

			if($artikul && $artikul<>''){
				
				$query = mysql_query("SELECT id, price FROM tbl_products WHERE artikul='" .$artikul. "'");
				if(mysql_num_rows($query)){
                    while ($result = mysql_fetch_array($query)) {
                        if($result['price']>$price_client){
                            $price_old = $result['price'];
                            mysql_query("UPDATE tbl_products SET price='" .$price_client. "', price_old='" .$price_old. "', price_old_date='" .$date_add. "' WHERE id='" .$result['id']. "'");
                        }
                        else{
                            mysql_query("UPDATE tbl_products SET price='" .$price_client. "' WHERE id='" .$result['id']. "'");
                        }
                        $notify2 .= 'Стоимость для товара <strong>' .$artikul. '</strong> изменена (' .$price_client. ')<br>';
                        $ids1 .= $result['id'].',';
                    }
					}
				else{
					$query = mysql_query("SELECT id, price, name FROM tbl_modification WHERE artikul='" .$artikul. "' LIMIT 1");
					if(mysql_num_rows($query)){
						$result = mysql_fetch_array($query);
						$n++;
						$found .= '<strong>' .$articul.'</strong> ' .$result['name']. '<br>';
						if($result['price']>$price_client){
							$price_old = $result['price'];
							mysql_query("UPDATE tbl_modification SET price='" .$price_client. "', price_old='" .$price_old. "', price_old_date='" .$date_add. "' WHERE id='" .$result['id']. "'");
							}
						else{
							mysql_query("UPDATE tbl_modification SET price='" .$price_client. "' WHERE id='" .$result['id']. "'");
							}
						$notify2 .= 'Стоимость для товара <strong>' .$artikul. '</strong> изменена (' .$price_client. ')<br>';
						$ids2 .= $result['id'].',';
						}
					else{
						$notify2 .= 'Не найден товар с артикулом <strong>' .$artikul. '</strong><br>';
						}			
					
					
					
					
					}
				
				}
				
			}
			
			
		}
		
		
	mysql_query("UPDATE tbl_products SET price='0' WHERE id NOT IN (" .$ids1. ")");
	mysql_query("UPDATE tbl_modification SET price='0' WHERE id NOT IN (" .$ids2. ")");
		
		
	}


?>
