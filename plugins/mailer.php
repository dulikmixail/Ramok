<?

#Скрипт для красивой отправки сообщения


include_once 'mail/class.phpmailer.php';

function mailer_send($login, $subject, $body, $files){
	#Отправляем письмо с паролем
	$m = new PHPMailer(true);
	$m->Priority = $priority;
	$m->AddAddress($login);
	$m->SetFrom('marketing@ramok.by', 'УП Рамок');
	
	
	$m->AddEmbeddedImage('../img/mail/top.jpg', 'my-attach1', $val, 'base64', 'application/octet-stream');
	$m->AddEmbeddedImage('../img/mail/bottom.jpg', 'my-attach2', $val, 'base64', 'application/octet-stream');
	
	if($files){
		while(list($id_arr, $val)=each($files)){
			$m->AddAttachment($files[$id_arr]['name'], $files[$id_arr]['caption']);
			}
		}
	
	#$m->AddAttachment('../img/mail/price.xlsx', 'Общий прайслист.xlsx'); 
	#$m->AddAttachment('../img/mail/protivokraznoe.xlsx', 'Противокражное оборудование.xlsx'); 
	
	$m->Subject = $subject;	
	$m->Body = '
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
</head>
<body width="810">

<table class="pad_null" cellpadding="0" cellspacing="0" align="center" class="pad_null" width="800" height="600" bgcolor="#fff" border="1">
<tr>
<td align="center">

<table cellpadding="0" cellspacing="0" align="center" class="pad_null" width="800" height="600" bgcolor="#fff">
	<tr height="154">
    	<td height="154" colspan="3" align="center"><img width="800" height="154" src="cid:my-attach1"></td>
	</tr>
 	<tr>
    	<td colspan="3" height="10"></td>
	</tr>   
	<tr>
   		<td width="50"></td>
    	<td colspan="2" align="left" valign="top">
         	<font face="Arial, Helvetica, sans-serif" size="2" color="#333333">
				' .$body. '
            </font>       
        </td>
	</tr>
	<tr>
    	<td width="50"></td>
    	<td align="left" valign="middle">
        	<font face="Arial, Helvetica, sans-serif" size="2" color="#333333">
			
			Данное письмо отправлено почтовым роботом. Чтобы отписаться от рассылки, <a href="http://ramok.by/plugins/subscribe.php?del=' .$login. '">перейдите по ссылке</a><br>
			<br>
            (с) 2012 УП Рамок. <a href="http://ramok.by"><font face="Arial, Helvetica, sans-serif" size="2" color="#333333"><strong>ramok.by</strong></font></a><br>
			220036, Беларусь, г.Минск, ул. Лермонтова, 29 <br>
			(017) 213-67-00 <br>
			(017) 210-22-80 <br>
			+375 (33) 313-67-00<br>
			+375 (29) 613-67-00<br>
			<br>
            </font>
       		
        </td>
        <td><img width="233" height="78" align="right" src="cid:my-attach2"></td>
	</tr>    
</table>
</td>
</tr>
</table>

</body>
</html>';
	
	$m->Send();
	return true;
	}


?>
