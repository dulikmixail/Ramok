<?

#������ ��� �������� �������� ���������


include_once 'mail/class.phpmailer.php';

function mailer_send($login, $subject, $body, $files){
	#���������� ������ � �������
	$m = new PHPMailer(true);
	$m->Priority = $priority;
	$m->AddAddress($login);
	$m->SetFrom('marketing@ramok.by', '�� �����');
	
	
//	$m->AddEmbeddedImage('../img/mail/top.jpg', 'my-attach1', $val, 'base64', 'application/octet-stream');
	$m->AddEmbeddedImage('../img/mail/bottom.jpg', 'my-attach2', $val, 'base64', 'application/octet-stream');
	
	if($files){
		while(list($id_arr, $val)=each($files)){
			$m->AddAttachment($files[$id_arr]['name'], $files[$id_arr]['caption']);
			}
		}
	
	#$m->AddAttachment('../img/mail/price.xlsx', '����� ���������.xlsx'); 
	#$m->AddAttachment('../img/mail/protivokraznoe.xlsx', '�������������� ������������.xlsx'); 
	
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
                    <td height="154" colspan="3" align="center">

                        <!--<img width="800" height="154" src="cid:my-attach1">-->
                        <a href="http://www.ramok.by"><img src="http://www.ramok.by/img/mail/top_cr.jpg" align="left"></a>
                        <div style="padding: 16px 0px" >
                            <a href="http://www.ramok.by/napr/kassovoe_oborudovanie_elektronnie_vesi_POS"><img src="http://www.ramok.by/img/mail/top_text/top_01.jpg"></a>
                            <a href="http://www.ramok.by/napr/avtomatizatsiya"><img src="http://www.ramok.by/img/mail/top_text/top_02.jpg"></a>
                            <a href="http://www.ramok.by/napr/oborudovanie_dlya__magazina_butika_torgovoy_tochki"><img src="http://www.ramok.by/img/mail/top_text/top_03.jpg"></a>
                            <a href="http://www.ramok.by/napr/sobstvennoe_proizvodstvo"><img src="http://www.ramok.by/img/mail/top_text/top_04.jpg"></a>
                            <a href="http://www.ramok.by/napr/sistemi_bezopasnosti_i_zashchiti_ot_krazh"><img src="http://www.ramok.by/img/mail/top_text/top_05.jpg"></a>
                            <a href="http://www.ramok.by/napr/Videonabludenie_umniy_dom"><img src="http://www.ramok.by/img/mail/top_text/top_06.jpg"></a>
                        </div>
                    </td>
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

                            ������ ������ ���������� �������� �������. ����� ���������� �� ��������, <a href="http://ramok.by/plugins/subscribe.php?del=' .$login. '">��������� �� ������</a><br>
                            <br>
                            (�) 2012 �� �����. <a href="http://ramok.by"><font face="Arial, Helvetica, sans-serif" size="2" color="#333333"><strong>ramok.by</strong></font></a><br>
                            220036, ��������, �.�����, ��. ����������, 29 <br>
                            (017) 213-67-00 <br>
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
