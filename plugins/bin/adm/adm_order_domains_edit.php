<?

#Редактирование домена

$update = $_REQUEST['update'];
$id = $_REQUEST['id'];
$a = $_REQUEST['a'];


if($update){
    $head_text = 'Редактирование домена';

	$domain_name = $_REQUEST['domain_name'];
	$date_end = $_REQUEST['date_end'];
	$is_payed = $_REQUEST['is_payed'];
	$ns1 = $_REQUEST['ns1'];
	$ns2 = $_REQUEST['ns2'];		
	$ip1 = $_REQUEST['ip1'];		
	$ip2 = $_REQUEST['ip2'];		
	
	$date_end = date("Y-m-d", strtotime($date_end));
	
	if($id){
		$query = mysql_query("UPDATE tbl_order_domain SET domain_name='" .$domain_name. "', date_end='" .$date_end. "', is_payed='" .$is_payed. "', ns1='" .$ns1. "', ns2='" .$ns2. "', ip1='" .$ip1. "', ip2='" .$ip2. "' WHERE id='" .$id. "'");
		header('Location: /adm/order/domains/edit?a=ready&id='.$id);
		exit();
		}
	}
else{
	if($id){
        $head_text = 'Редактирование домена';
		#Достаем информацию о домене
		$query = mysql_query("SELECT * FROM tbl_order_domain WHERE id='" .$id. "'");
		$result = mysql_fetch_array($query);
		
		$id_domain = $result['id_domain'];
		$id_user = $result['id_user'];
		$domain_name = $result['domain_name'];
		$date_end = $result['date_end'];
		$is_payed = $result['is_payed'];
		$ns1 = $result['ns1'];
		$ns2 = $result['ns2'];		
		$ip1 = $result['ip1'];		
		$ip2 = $result['ip2'];		

		$date_end = date("d.m.Y", strtotime($date_end));

		if($is_payed){
			$chk_is_payed = ' checked ';
			}
		else{
			$date_end = '';
			}
		
		#Достаем информацию о юзере
		$query = mysql_query("SELECT * FROM tbl_user WHERE id='" .$id. "'");
		$result = mysql_fetch_array($query);
		$user_name = $result['name'];
		$user_email = $result['login'];

		#Достаем информацию о домене
		$query = mysql_query("SELECT * FROM tbl_domain WHERE id='" .$id_domain. "'");
		$result = mysql_fetch_array($query);
		$price_rur = $result['price_rur'];
		$price_usd = $result['price_usd'];


		}
	}







?>
