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
        $error .= '- Вы ввели неправильную капчу<br>';
    }
    if(!$name||!$description||!$email||!$text){
        $error .= '- Вы не заполнили все необходимые поля<br>';
    }

    if(!$error){
        $date_add = date('y-m-d H:i:s');

        if($receiver=='ramok'){$receiver_text='Офис';}
        if($receiver=='buhgalteriy'){$receiver_text='Бухгалтерия';}
        if($receiver=='marketing'){$receiver_text='Маркетинг';}
        if($receiver=='asu'){$receiver_text='Автоматизация';}
        if($receiver=='ops'){$receiver_text='Системы безопасности';}
        if($receiver=='cto'){$receiver_text='ЦТО';}
        if($receiver=='support'){$receiver_text='Техподдержка';}
        if($receiver=='ctoBobruisk'){$receiver_text='ЦТО Бабруйск';}
        if($receiver=='ctoMogilev'){$receiver_text='ЦТО Могилев';}


        include '../includes/class.phpmailer.php';
        $m = new PHPMailer(true);
        $m->Priority = '1';
        //$m->AddReplyTo($email, $name.' с сайта УП Рамок');
        //$m->AddAddress('antylevsky@ya.ru');

        if($receiver=='buhgalteriy'){$m->AddAddress('buhgalteriy@ramok.by');}
        else {$m->AddAddress('www@ramok.by');}
        //$m->AddAddress($receiver.'@ramok.by');
        //$m->AddAddress('fwd34ejojwviqio4wc0880c4c0c4@ramok.bitrix24.by');
        $m->SetFrom($email, $name.' с сайта УП Рамок');
        $m->Subject = 'Для отдела '.$receiver_text;
        $m->Body = 'Здравствуйте!<br><br>
		На сайте было оставлено сообщение от <strong>' .$date_add. '</strong><br>
		<br>
		Выбранный отдел: <strong>' .$receiver_text. '</strong><br>
		Контактное лицо: <strong>' .$name. '</strong><br>
		Тема сообщения: <strong>' .$description. '</strong><br>
		E-mail: <strong>' .$email. '</strong><br>
		Сообщение: <strong>' .$text. '</strong><br>
		<br>
		IP адрес: <strong>' .$_SERVER['REMOTE_ADDR']. '</strong><br>
		';
        $m->Send();


        $name = '';
        $description = '';
        $email = '';
        $text = '';
        $notify = 'Спасибо! Сообщение было отправлено!<br><br>';


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