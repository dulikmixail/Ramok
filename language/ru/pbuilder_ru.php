<?php

$language["pbuilder_menu"]="Создание плагинов";
$language["pbuilder_scratch"]="Заготовка";
$language["pbuilder_tables"]="Таблицы";
$language["pbuilder_options"]="Настройки";
$language["pbuilder_template"]="Шаблон";
$language["pbuilder_bundle"]="Пакет";
$language["pbuilder_entity"]="Сущность";
$language["pbuilder_form"]="Форма";
$language["pbuilder_pictures"]="Картинки";

/******************************************************************************/

$language["pbuilder_meaning"]["input"]="Строка ввода";
$language["pbuilder_meaning"]["password"]="Строка ввода пароля";
$language["pbuilder_meaning"]["datetime"]="Строка ввода даты и времени";
$language["pbuilder_meaning"]["textarea"]="Многострочный текст";
$language["pbuilder_meaning"]["editor"]="Текстовый редактор";
$language["pbuilder_meaning"]["chooser"]="Выпадающий список";
$language["pbuilder_meaning"]["yesno"]="Логический тип (Да/Нет)";
$language["pbuilder_meaning"]["hidden"]="Скрытое поле";
$language["pbuilder_meaning"]["none"]="Не используется";

$language["pbuilder_form_meaning"]["input"]="Строка ввода";
$language["pbuilder_form_meaning"]["password"]="Строка ввода пароля";
$language["pbuilder_form_meaning"]["textarea"]="Многострочный текст";
$language["pbuilder_form_meaning"]["chooser"]="Выпадающий список";
$language["pbuilder_form_meaning"]["yesno"]="Логический тип (Да/Нет)";
$language["pbuilder_form_meaning"]["checkbox"]="Логический тип (Галочка)";
$language["pbuilder_form_meaning"]["file"]="Загрузка файла";
$language["pbuilder_form_meaning"]["hidden"]="Скрытое поле";

$language["pbuilder_form_type"]["string"]="Строковый";
$language["pbuilder_form_type"]["int"]="Целочисленный";
$language["pbuilder_form_type"]["float"]="Вещественный";

/******************************************************************************/

$language["pbuilder_scratch_form"]="Создание заготовки для плагина";
$language["pbuilder_scratch_success1"]="Код заготовки плагина (%s):";
$language["pbuilder_scratch_success2"]="Код локализационного файла плагина на языке &quot;%s&quot; (%s):";
$language["pbuilder_scratch_name"]="Имя плагина:";
$language["pbuilder_scratch_namedesc"]="Будет использовано в качестве имени файла для плагина и его локализационных файлов, названия таблицы (если необходимо), а также идентификатора в пространстве имен локализации. Старайтесь придумать короткое имя, например &quot;news&quot; или &quot;guestbook&quot;. Допускаются только латинские буквы, цифры и символ подчеркивания.";
$language["pbuilder_scratch_title"]="Название плагина на языке &quot;%s&quot;:";

$language["pbuilder_scratchaction_form"]="Создание заготовки для действия плагина";
$language["pbuilder_scratchaction_success"]="Код заготовки действия:";
$language["pbuilder_scratchaction_action"]="Значение параметра action:";
$language["pbuilder_scratchaction_comment"]="Название действия (для комментария):";
$language["pbuilder_scratchaction_section"]="Название раздела (для комментария):";

$language["pbuilder_tables_form"]="Инсталлятор таблиц для плагина";
$language["pbuilder_tables_success"]="Код инсталлятора плагина:";
$language["pbuilder_tables_maintable"]="Главная таблица:";
$language["pbuilder_tables_maintabledesc"]="Выберите таблицу, по наличию или отсутствию которой будет определяться факт установки плагина (обычно совпадает с названием самого плагина).";
$language["pbuilder_tables_tables"]="Список таблиц:";
$language["pbuilder_tables_tablesdesc"]="Выберите одну или несколько таблиц, необходимых для работы плагина (возможно, их следует предварительно создать плагином &quot;Управление БД&quot;). Используйте Ctrl+щелчок для установки/снятия пометки.";

$language["pbuilder_options_form"]="Создание настроек для плагина";
$language["pbuilder_options_success1"]="Код функции createDefaultOptions:";
$language["pbuilder_options_success2"]="Код для блока инсталляции плагина:";
$language["pbuilder_options_success3"]="Код для локализационного файла на языке &quot;%s&quot;:";
$language["pbuilder_options_name"]="Имя плагина:";
$language["pbuilder_options_group"]="Группа настроек:";
$language["pbuilder_options_nogroups"]="Не найдено ни одной группы настроек!";
$language["pbuilder_options_noitems"]="В данной группе нет ни одной настройки!";

$language["pbuilder_template_form"]="Создание шаблона для плагина";
$language["pbuilder_template_success1"]="Код констант шаблонов:";
$language["pbuilder_template_success2"]="Код инсталляции шаблонов:";
$language["pbuilder_template_failure"]="Вы не выбрали ни один шаблон.";
$language["pbuilder_template_items"]="Список шаблонов:";
$language["pbuilder_template_itemsdesc"]="Выберите один или несколько шаблонов, необходимых для работы плагина (возможно, их следует предварительно создать плагином &quot;Управление PHPC&quot;). Используйте Ctrl+щелчок для установки/снятия пометки.";

$language["pbuilder_bundle_form"]="Создание пакета для плагина";
$language["pbuilder_bundle_success1"]="Код констант пакетов:";
$language["pbuilder_bundle_success2"]="Код инсталляции пакетов:";
$language["pbuilder_bundle_failure"]="Вы не выбрали ни один пакет.";
$language["pbuilder_bundle_items"]="Список пакетов:";
$language["pbuilder_bundle_itemsdesc"]="Выберите один или несколько пакетов, необходимых для работы плагина (возможно, их следует предварительно создать плагином &quot;Управление PHPC&quot;). Используйте Ctrl+щелчок для установки/снятия пометки.";

/******************************************************************************/

$language["pbuilder_entity1_form"]="Создание сущности - Шаг 1 - Ввод данных";
$language["pbuilder_entity1_name"]="Имя плагина:";
$language["pbuilder_entity1_table"]="Таблица с данными:";
$language["pbuilder_entity1_tabledesc"]="Укажите таблицу, которая хранит данные сущности. Таблица должна иметь простой первичный ключ (из одного поля) с атрибутом Auto_Increment.";
$language["pbuilder_entity1_item1"]="Идентификатор сущности в единственном числе:";
$language["pbuilder_entity1_item2"]="Идентификатор сущности во множественном числе:";
$language["pbuilder_entity1_itemdesc"]="Допускаются только латинские буквы, цифры и символ подчеркивания.";
$language["pbuilder_entity1_append1"]="Суффикс для добавления в названия действий (необязательно):";
$language["pbuilder_entity1_append2"]="Суффикс для добавления в ключи локализации (необязательно):";

$language["pbuilder_entity2_form"]="Создание сущности - Шаг 2 - Выбор полей";
$language["pbuilder_entity2_separator1"]="Просмотр списка";
$language["pbuilder_entity2_separator2"]="Порядок отображения данных";
$language["pbuilder_entity2_column"]="Способ представления поля %s:";
$language["pbuilder_entity2_columndesc"]="Используется при создании/редактировании сущности.";
$language["pbuilder_entity2_display"]="Поля, отображаемые в списке:";
$language["pbuilder_entity2_displaydesc"]="Отметьте одно или несколько полей, значения которых будут отображаться в списке сущности. Для установки/снятия пометки используйте Ctrl+щелчок.";
$language["pbuilder_entity2_displayorder"]="Поле, отвечающее за порядок отображения:";
$language["pbuilder_entity2_displayorderdesc"]="Если указать здесь имя поля, список сущности будет автоматически сортироваться по этому полю; кроме того, для каждого элемента будет отображаться небольшая строка ввода для управления порядком.";
$language["pbuilder_entity2_nodisplayorder"]="Не используется";
$language["pbuilder_entity2_badtable_noprimary"]="Данная таблица не подходит для создания сущности, так как в ней не определен первичный ключ.";
$language["pbuilder_entity2_badtable_nocounter"]="Данная таблица не подходит для создания сущности, так как ее первичный ключ не является счетчиком.";
$language["pbuilder_entity2_badtable_complex"]="Данная таблица не подходит для создания сущности, так как ее первичный ключ является составным.";
$language["pbuilder_entity2_noitem"]="Не указан один из идентификаторов.";
$language["pbuilder_entity2_sameitems"]="Идентификаторы должны различаться.";

$language["pbuilder_entity3_header"]="&nbsp;Создание сущности - Шаг 3 - Порядок полей";
$language["pbuilder_entity3_order"]="Порядок";
$language["pbuilder_entity3_column"]="Поле %s";
$language["pbuilder_entity3_nodisplay"]="Не указано ни одного поля для просмотра списка сущности.";
$language["pbuilder_entity3_nocolumns"]="Хотя бы одно поле сущности должно быть доступным для редактирования.";

$language["pbuilder_entity4_success1"]="Код формирования главного меню:";
$language["pbuilder_entity4_success2"]="Код строки-представления сущности:";
$language["pbuilder_entity4_success3"]="Код обработчиков действий:";
$language["pbuilder_entity4_success4"]="Код для локализационного файла на языке &quot;%s&quot;:";
$language["pbuilder_entity4_add"]="Создать";
$language["pbuilder_entity4_modify"]="Обзор";
$language["pbuilder_entity4_addform"]="Создание нового элемента";
$language["pbuilder_entity4_addsuccess"]="Элемент добавлен!";
$language["pbuilder_entity4_addfailure"]="Не удалось добавить элемент.";
$language["pbuilder_entity4_editform"]="Редактирование элемента";
$language["pbuilder_entity4_editsuccess"]="Элемент изменен!";
$language["pbuilder_entity4_editfailure"]="Не удалось изменить элемент.";
$language["pbuilder_entity4_addeditfield"]="Значение поля %s:";
$language["pbuilder_entity4_removeprompt"]="Вы действительно хотите удалить этот элемент?";
$language["pbuilder_entity4_removesuccess"]="Элемент удален!";
$language["pbuilder_entity4_ordersuccess"]="Порядок изменен!";
$language["pbuilder_entity4_modifyfield"]="%s";
$language["pbuilder_entity4_modifyoptions"]="Управление";
$language["pbuilder_entity4_modifyorder"]="Порядок";
$language["pbuilder_entity4_modifyedit"]="Свойства";
$language["pbuilder_entity4_modifyremove"]="Удалить";

/******************************************************************************/

$language["pbuilder_form1_form"]="Создание пары &quot;Форма-Контроллер&quot;";
$language["pbuilder_form1_count"]="Количество полей в форме:";
$language["pbuilder_form1_page"]="Страница контроллера:";
$language["pbuilder_form1_pagedesc"]="Название страницы (например, actionGuestbook), с которой будет связан контроллер - пакет, обрабатывающий данные формы.";
$language["pbuilder_form1_action"]="Значение параметра action:";
$language["pbuilder_form1_actiondesc"]="Один контроллер может выполнять несколько различных действий, в зависимости от значения этого параметра.";

$language["pbuilder_form2_nopage"]="Не указана страница контроллера.";
$language["pbuilder_form2_noaction"]="Не указано значение параметра action.";
$language["pbuilder_form2_field"]="Имя поля";
$language["pbuilder_form2_meaning"]="Представление";
$language["pbuilder_form2_type"]="Тип";
$language["pbuilder_form2_limits"]="Ограничения*";
$language["pbuilder_form2_default"]="По умолчанию";
$language["pbuilder_form2_note"]="Примечание *. Для целочисленных и вещественных типов вы можете указать здесь минимальное значение параметра, либо минимальное и максимальное значения через запятую. Для строковых типов вы можете указать максимальную длину в символах.";

$language["pbuilder_form3_success1"]="HTML-код формы:";
$language["pbuilder_form3_success2"]="Код контроллера:";
$language["pbuilder_form3_wrongfield"]="Имя одного или нескольких полей указано неверно.";
$language["pbuilder_form3_nofields"]="Не указано ни одного поля формы.";

/******************************************************************************/

$language["pbuilder_pictures1_form"]="Создание картинок для плагина";
$language["pbuilder_pictures1_count"]="Количество картинок:";
$language["pbuilder_pictures1_type"]="Формат файла:";
$language["pbuilder_pictures1_typedesc"]="Для простоты генерации, все картинки должны быть в одном формате.";

$language["pbuilder_pictures2_form"]="Внутренние названия для картинок";
$language["pbuilder_pictures2_separator"]="Файлы с данными картинок";
$language["pbuilder_pictures2_name"]="Название картинки %d:";
$language["pbuilder_pictures2_file"]="Файл с картинкой %d:";

$language["pbuilder_pictures3_success"]="Код формирования картинок:";
$language["pbuilder_pictures3_nofile"]="Не указан или не загрузился один из файлов.";
$language["pbuilder_pictures3_wrongformat"]="Один из файлов имеет неверный формат.";

/******************************************************************************/

$language["pbuilder_error_pluginname"]="Не указано имя плагина.";
$language["pbuilder_error_emptydb"]="В базе данных не обнаружено ни одной таблицы.";
$language["pbuilder_error_notables"]="Вы не выбрали ни одной таблицы.";

?>
