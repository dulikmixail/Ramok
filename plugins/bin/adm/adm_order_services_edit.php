<?

#Скрипт для редактирования заказа из каталога в админке

$id = $_REQUEST['id'];
$id_category = $_REQUEST['id_category'];
$a = $_REQUEST['a'];

if($a=='save'){
	$name = $_REQUEST['name'];
	$phone = $_REQUEST['phone'];
	$email = $_REQUEST['email'];	

	$query = mysql_query("UPDATE tbl_catorder SET user_name='" .$name. "', phone='" .$phone. "', email='" .$email. "' WHERE id='" .$id. "'");

	#Обрабатываем дополниельные поля формы
	$query = mysql_query("SELECT * FROM tbl_form_param WHERE id_category='" .$id_category. "'");
	while($result=mysql_fetch_array($query)){
		$value = $_REQUEST['param'.$result['id']];
		$type = $result['type'];
		if($type==2){
			if($value){$value='Да';}else{$value='Нет';}
			}
		$query2 = mysql_query("UPDATE tbl_catparams SET param_value='" .$value. "' WHERE id_order='" .$id. "' AND id_param='" .$result['id']. "'");		
		}
	
	header('Location: /adm/order/services');
	exit();	
	}


#Достаем информацию о заказе
$query = mysql_query("SELECT * FROM tbl_catorder WHERE id='" .$id. "'");

$result = mysql_fetch_array($query);
$name = $result['user_name'];
$phone = $result['phone'];
$email = $result['email'];
$id_category = $result['id_category'];
$cat_name = $result['cat_name'];

#Формируем поля
#Достаем параметры формы
$query = mysql_query("SELECT *, 
	(SELECT param_value FROM tbl_catparams WHERE tbl_catparams.id_order='" .$id. "' AND tbl_catparams.id_param=tbl_form_param.id) AS param_value FROM tbl_form_param WHERE id_category='" .$id_category. "'");
$i=0;
while($result = mysql_fetch_array($query)){
	if($i%2){$cls=' class="TableRow1"';}else{$cls=' class="TableRow2"';}
	
	$param_value = $result['param_value'];
	
	if($result['type'] == 1){
		$input .= '<input class="txt" type="text" name="param' .$result['id']. '" value="' .$param_value. '">';
		}
	elseif($result['type'] == 2){
		if($param_value=='Да'){$param_value='checked';}else{$param_value='';}
		$input .= '<input class="txt" style="width: 350px;" type="checkbox" ' .$param_value. ' value="1" name="param' .$result['id']. '">';		
		}
	elseif($result['type'] == 3){
		$param_value_arr = split(",", $result['value']);
		$input .= '<select class="edit" style="width: 350px;" name="param' .$result['id']. '">
		<option style="color:green" value="0">Выберите</option>';
		$j=0;
		foreach ($param_value_arr as $key => $value){
			$value = ltrim($value);
			if($param_value==$j){
				$input .= '<option selected value="' .$j. '">' .$value. '</option>';
				}
			else{
				$input .= '<option value="' .$j. '">' .$value. '</option>';
				}
			$j++;
			}
		$input .= '</select>';
		}	
	$form_params .= '
	<tr ' .$cls. ' height="25">
		<td>' .$result['name']. ':</td>
		<td>' .$input. '</td>
	</tr>';
	$input='';
	$i++;
	}

?>