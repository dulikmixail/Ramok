<?php

$language["locale"]="ru";
$language["charset"]="windows-1251";
$language["charset_mysql"]="cp1251";

$language["charset_uppers"]="ABCDEFGHIJKLMNOPQRSTUVWXYZАБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯЎЁЄЇ";
$language["charset_lowers"]="abcdefghijklmnopqrstuvwxyzабвгдежзийклмнопрстуфхцчшщъыьэюяўёєї";
$language["charset_regexp"]="A-Za-zА-Яа-яЎўЁёЄєЇї";
$language["charset_regexp_uppers"]="A-ZА-ЯЎЁЄЇ";
$language["charset_regexp_lowers"]="a-zа-яўёєї";

$language["common_yes"]="Да";
$language["common_no"]="Нет";

$language["weekday"][]="Воскресенье";
$language["weekday"][]="Понедельник";
$language["weekday"][]="Вторник";
$language["weekday"][]="Среда";
$language["weekday"][]="Четверг";
$language["weekday"][]="Пятница";
$language["weekday"][]="Суббота";

$language["weekday_short"][]="Вс";
$language["weekday_short"][]="Пн";
$language["weekday_short"][]="Вт";
$language["weekday_short"][]="Ср";
$language["weekday_short"][]="Чт";
$language["weekday_short"][]="Пт";
$language["weekday_short"][]="Сб";

$language["month"][1]="Январь";
$language["month"][2]="Февраль";
$language["month"][3]="Март";
$language["month"][4]="Апрель";
$language["month"][5]="Май";
$language["month"][6]="Июнь";
$language["month"][7]="Июль";
$language["month"][8]="Август";
$language["month"][9]="Сентябрь";
$language["month"][10]="Октябрь";
$language["month"][11]="Ноябрь";
$language["month"][12]="Декабрь";

$language["month_gen"][1]="января";
$language["month_gen"][2]="февраля";
$language["month_gen"][3]="марта";
$language["month_gen"][4]="апреля";
$language["month_gen"][5]="мая";
$language["month_gen"][6]="июня";
$language["month_gen"][7]="июля";
$language["month_gen"][8]="августа";
$language["month_gen"][9]="сентября";
$language["month_gen"][10]="октября";
$language["month_gen"][11]="ноября";
$language["month_gen"][12]="декабря";

$language["month_short"][1]="Янв";
$language["month_short"][2]="Фев";
$language["month_short"][3]="Мар";
$language["month_short"][4]="Апр";
$language["month_short"][5]="Май";
$language["month_short"][6]="Июн";
$language["month_short"][7]="Июл";
$language["month_short"][8]="Авг";
$language["month_short"][9]="Сен";
$language["month_short"][10]="Окт";
$language["month_short"][11]="Ноя";
$language["month_short"][12]="Дек";

$language["format_decimals"]=2;
$language["format_separator"]=",";
$language["format_thousands"]=" ";

$language["format_datetime"]["date"]="j f Y";
$language["format_datetime"]["time"]="H:i";
$language["format_datetime"]["datetime"]="j f Y, H:i";

$language["fatal_title"]="Фатальная ошибка PHPC:";
$language["fatal_error"]="Описание ошибки:";
$language["fatal_query"]="Запрос, вызвавший ошибку:";

$language["fatal_connection"]="Невозможно подключиться к БД.";
$language["fatal_wrongquery"]="Неверный SQL-запрос.";
$language["fatal_constraints"]="Обнаружены нарушения целостности БД.";
$language["fatal_recursion"]="Обнаружена рекурсия в таблице &quot;%s&quot;.";
$language["fatal_nostyle"]="Не найден подходящий стиль для отображения сайта.";
$language["fatal_no404"]="Не найдена страница 404. Создайте ее в панели управления.";
$language["fatal_compile"]="Ошибка компиляции.";
$language["fatal_filecache"]="Не удалось сохранить файловый кеш. Возможно, каталог &quot;cache&quot; не имеет прав на запись.";
$language["fatal_gzip"]="Невозможно запустить GZIP-сжатие. Обнаружен лишний вывод текста в файле &quot;%s&quot;, строка %s.";

$language["fatal_reason_notemplate"]="Не найден шаблон \"%s\", используемый на странице \"%s\".";
$language["fatal_reason_notemplateparent"]="Не найден шаблон \"%s\", от которого наследуется шаблон \"%s\".";
$language["fatal_reason_notemplateinsert"]="Не найден шаблон \"%s\", используемый для вставки в шаблон \"%s\", фрагмент \"%s\".";
$language["fatal_reason_nobundle"]="Не найден пакет \"%s\", используемый на странице \"%s\".";
$language["fatal_reason_phptag"]="Обнаружен одиночный PHP-тег в шаблоне \"%s\", фрагмент \"%s\".";
$language["fatal_reason_area"]="Нарушен синтаксис тега \"area\" в шаблоне \"%s\", фрагмент \"%s\".";
$language["fatal_reason_areaalready"]="Повторное использование тега \"area\" с тем же идентификатором в шаблоне \"%s\", фрагмент \"%s\".";
$language["fatal_reason_areamissing"]="Пропущен открывающий тег \"area\" в шаблоне \"%s\", фрагмент \"%s\".";
$language["fatal_reason_areaunclosed"]="Отсутствует закрывающий тег \"area\" в шаблоне \"%s\", фрагмент \"%s\".";
$language["fatal_reason_areanoparent"]="Не найден соответствующий тег \"area\" в предке шаблона \"%s\", фрагмент \"%s\".";
$language["fatal_reason_tag"]="Нарушен синтаксис специального тега в шаблоне \"%s\", фрагмент \"%s\".";
$language["fatal_reason_tagmissing"]="Пропущен открывающий специальный тег в шаблоне \"%s\", фрагмент \"%s\".";
$language["fatal_reason_tagunclosed"]="Отсутствует закрывающий специальный тег в шаблоне \"%s\", фрагмент \"%s\".";
$language["fatal_reason_varref"]="В параметрах тега \"var\" использована переменная - шаблон \"%s\", фрагмент \"%s\".";
$language["fatal_reason_unknownlogic"]="Обнаружен неизвестный тег \"logic\" в шаблоне \"%s\", фрагмент \"%s\".";
$language["fatal_reason_unknownwrite"]="Обнаружен неизвестный тег \"write\" в шаблоне \"%s\", фрагмент \"%s\".";

?>
