<?php

$content.='<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>';


require_once '../plugins/vendor/autoload.php';

if($_GET['a']=='finish'){
    $tmp_path = $_SERVER['DOCUMENT_ROOT'].'/templates/forma_zajavki_na_podkljuchenie.docx';
    $content.= $tmp_path;

    $phpWord = new \PhpOffice\PhpWord\PhpWord();

    $document=$phpWord->loadTemplate($tmp_path);


    foreach ($_POST as $field=>$value) {
        $document->setValue($field, iconv("WINDOWS-1251","UTF-8",$value));
    }

    $tmp_path = $_SERVER['DOCUMENT_ROOT'].'/export_doc/test.docx';

    $content.= $tmp_path;

    $document->saveAs($tmp_path);
}

$content .='
<form action="SKNO?a=finish" method="post">
<div class="skno_form">

<label for="unp">УНП</label><input type="number" name="unp" id="unp" placeholder="123456789" value="123456789"><br>
<label for="company">Наименование</label><input type="text" name="company" id="company" placeholder="ИП Рога и копыта" value="ИП Рога и копыта"><br>
<label for="fio">ФИО ответственного за КО</label><input type="text" name="fio" id="fio" placeholder="Петров Иван Иваныч" value="Петров Иван Иваныч"><br>
<label for="tel">Телефон</label><input type="text" name="tel" id="tel" placeholder="+375-(17)-200-10-20" value="+375-(17)-200-10-20"><br>
<label for="fax">Факс</label><input type="text" name="fax" id="fax" placeholder="+375-(17)-200-20-30" value="+375-(17)-200-20-30"><br>
<label for="mob">Мобильный телефон</label><input type="text" name="mob" id="mob" placeholder="+375-(29)-610-10-20" value="+375-(29)-610-10-20"><br>
<label for="email">Email</label><input type="email" name="email" id="email" placeholder="example@google.com" value="example@google.com"><br>
<label for="address">Почтовый адрес</label><input type="text" name="address" id="address" placeholder="ул. Пушкина 1" value="ул. Пушкина 1"><br><br>


<label for="using">Вид использования КО</label>
<select id="using" name="using">
  <option selected>Постоянное</option>
  <option>Резервное</option>
</select><br>

<label for="tariff">Выбранный тарифный план</label>
<select id="tariff" name="tariff">
  <option selected>Месячный</option>
  <option>Суточный</option>
</select><br>

<label for="diff_accounting">Дифференцированный учет данных о товаре:</label>
<select id="diff_accounting" name="diff_accounting">
  <option>Да</option>
  <option selected>Нет</option>
</select><br>

<label for="condition">Новое КО, или ранее использованное</label>
<select id="condition" name="condition"> 
  <option selected>Новое</option>
  <option>Ранее использовалось</option>
</select><br>

<label for="ro_type">Тип торгового объекта:</label>
<input type="text" name="ro_type" id="ro_type" placeholder="Магазин" value="Магазин"><br><br>

<label for="ro_name">Наименование торгового объекта</label>
<input type="text" name="ro_name" id="ro_name" placeholder="Мебельный магазин Ромашка" value="Мебельный магазин Ромашка"><br><br>

<label for="ro_address">Адрес торгового объекта:</label>
<input type="text" name="ro_address" id="ro_address" placeholder="Минский обл., Минский рн., г.Минск., ул. Пушкина 1/50, Офис 32" value="Минский обл., Минский рн., г.Минск., ул. Пушкина 1/50, Офис 32"><br><br>

<label for="ro_time">Время работы торгового объекта</label>
<input type="text" name="ro_time" id="ro_time" placeholder="08:00 - 18:00" value="08:00 - 18:00"><br><br>

<input type="submit" value="Отправить">

</div>
</form>';


$content.='</body>
</html>';

echo $content;





//
//
////$section = $phpWord->addSection($sectionStyle);
////$text = "PHPWord is a library written in pure PHP that provides a set of classes to write to and read from different document file formats.";
////
////$section->addText(htmlspecialchars($text),
////    array('name'=>'Arial','size'=>36,'color'=>'075776','bold'=>TRUE,'italic'=>TRUE),
////    array('align'=>'right','spaceBefore'=>10)
////);
////
////$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord,'Word2007');
////$objWriter->save('doc.docx');