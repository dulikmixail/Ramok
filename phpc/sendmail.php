<?php

$a = $_REQUEST['a'];
$receiver = $_REQUEST['receiver'];

if(!$receiver){
    $receiver = 'ramok';
}

$receiver_selected[$receiver] = 'selected';


if($a=='send'){
    $name = mysql_real_escape_string($_REQUEST['name']);
    $description = mysql_real_escape_string($_REQUEST['description']);
    $email = mysql_real_escape_string($_REQUEST['email']);
    $text = mysql_real_escape_string($_REQUEST['text']);

    include('../plugins/captcha.php');

    $success=$captchaSupport->processCheck($currentSession,"captcha");

    if(!$success){
        $error .= '- �� ����� ������������ �����<br>';
    }
    if(!$name||!$description||!$email||!$text){
        $error .= '- �� �� ��������� ��� ����������� ����<br>';
    }

    if(!$error){
        $date_add = date('y-m-d H:i:s');

        if($receiver=='ramok'){$receiver_text='����';}
        if($receiver=='buhgalteriy'){$receiver_text='�����������';}
        if($receiver=='marketing'){$receiver_text='���������';}
        if($receiver=='asu'){$receiver_text='�������������';}
        if($receiver=='ops'){$receiver_text='������� ������������';}
        if($receiver=='cto'){$receiver_text='���';}
        if($receiver=='support'){$receiver_text='������������';}
        if($receiver=='ctoBobruisk'){$receiver_text='��� ��������';}
        if($receiver=='ctoMogilev'){$receiver_text='��� �������';}


        include '../includes/class.phpmailer.php';
        $m = new PHPMailer(true);
        $m->Priority = '1';
        //$m->AddReplyTo($email, $name.' � ����� �� �����');
        //$m->AddAddress('antylevsky@ya.ru');

        if($receiver=='buhgalteriy'){$m->AddAddress('buhgalteriy@ramok.by');}
        else {$m->AddAddress('www@ramok.by');}
        //$m->AddAddress($receiver.'@ramok.by');
        //$m->AddAddress('fwd34ejojwviqio4wc0880c4c0c4@ramok.bitrix24.by');
        $m->SetFrom($email, $name.' � ����� �� �����');
        $m->Subject = '��� ������ '.$receiver_text;
        $m->Body = '������������!<br><br>
		�� ����� ���� ��������� ��������� �� <strong>' .$date_add. '</strong><br>
		<br>
		��������� �����: <strong>' .$receiver_text. '</strong><br>
		���������� ����: <strong>' .$name. '</strong><br>
		���� ���������: <strong>' .$description. '</strong><br>
		E-mail: <strong>' .$email. '</strong><br>
		���������: <strong>' .$text. '</strong><br>
		<br>
		IP �����: <strong>' .$_SERVER['REMOTE_ADDR']. '</strong><br>
		';
        $m->Send();


        $name = '';
        $description = '';
        $email = '';
        $text = '';
        $notify = '�������! ��������� ���� ����������!<br><br>';


    }
    else{
        $error .= '<br>';
    }

}
else{
    if($_SESSION['user_id']){
        $query = mysql_query("select * from tbl_user where id='" .$_SESSION['user_id']. "'");
        $result = mysql_fetch_array($query);
        $fio = $result['name'];
        $phone = $result['phone'];
        $email = $result['email'];
        $adress = $result['city'];
    }

}