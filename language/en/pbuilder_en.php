<?php

$language["pbuilder_menu"]="Plugins Builder";
$language["pbuilder_scratch"]="Scratch";
$language["pbuilder_tables"]="Tables";
$language["pbuilder_options"]="Options";
$language["pbuilder_template"]="Template";
$language["pbuilder_bundle"]="Bundle";
$language["pbuilder_entity"]="Entity";
$language["pbuilder_form"]="Form + Controller";
$language["pbuilder_pictures"]="Pictures";

/******************************************************************************/

$language["pbuilder_meaning"]["input"]="Input field";
$language["pbuilder_meaning"]["password"]="Password input field";
$language["pbuilder_meaning"]["datetime"]="Date/time input field";
$language["pbuilder_meaning"]["textarea"]="Multiline text field";
$language["pbuilder_meaning"]["editor"]="Text editor";
$language["pbuilder_meaning"]["chooser"]="Dropdown list";
$language["pbuilder_meaning"]["yesno"]="Boolean (Yes/No)";
$language["pbuilder_meaning"]["hidden"]="Hidden field";
$language["pbuilder_meaning"]["none"]="None";

$language["pbuilder_form_meaning"]["input"]="Input field";
$language["pbuilder_form_meaning"]["password"]="Password input field";
$language["pbuilder_form_meaning"]["textarea"]="Multiline text field";
$language["pbuilder_form_meaning"]["chooser"]="Dropdown list";
$language["pbuilder_form_meaning"]["yesno"]="Boolean (Yes/No)";
$language["pbuilder_form_meaning"]["checkbox"]="Boolean (Checkbox)";
$language["pbuilder_form_meaning"]["file"]="File upload";
$language["pbuilder_form_meaning"]["hidden"]="Hidden field";

$language["pbuilder_form_type"]["string"]="String";
$language["pbuilder_form_type"]["int"]="Integer";
$language["pbuilder_form_type"]["float"]="Float";

/******************************************************************************/

$language["pbuilder_scratch_form"]="Create Plugin Scratch";
$language["pbuilder_scratch_success1"]="Plugin scratch code (%s):";
$language["pbuilder_scratch_success2"]="Localization file code in &quot;%s&quot; (%s):";
$language["pbuilder_scratch_name"]="Plugin Name:";
$language["pbuilder_scratch_namedesc"]="This name will be used as the filename for the plugin and its localization files, table name (if needed) and as an identifier in localization namespace. Use short and clear name - for example, &quot;news&quot; or &quot;guestbook&quot;. Only lowercase letters, digits and underscores are allowed.";
$language["pbuilder_scratch_title"]="Plugin Title in &quot;%s&quot;:";

$language["pbuilder_scratchaction_form"]="Create Plugin Action Scratch";
$language["pbuilder_scratchaction_success"]="Action scratch code:";
$language["pbuilder_scratchaction_action"]="Value of the &quot;action&quot; Parameter:";
$language["pbuilder_scratchaction_comment"]="Action Title (for the comment):";
$language["pbuilder_scratchaction_section"]="Section Title (for the comment):";

$language["pbuilder_tables_form"]="Create Plugin Tables Installer";
$language["pbuilder_tables_success"]="Plugin installer code:";
$language["pbuilder_tables_maintable"]="Primary Table:";
$language["pbuilder_tables_maintabledesc"]="Choose a table, presence of which will indicate a successful instalation of the plugin (usually the same as the name of the plugin itself).";
$language["pbuilder_tables_tables"]="Required Tables:";
$language["pbuilder_tables_tablesdesc"]="Choose one or more tables needed by the plugin (you should create them first using &quot;DB Management&quot; plugin). Use Ctrl+click to select/deselect a table.";

$language["pbuilder_options_form"]="Create Plugin Options";
$language["pbuilder_options_success1"]="Code for the createDefaultOptions function:";
$language["pbuilder_options_success2"]="Code for the plugin installation block:";
$language["pbuilder_options_success3"]="Code for the plugin localization file in &quot;%s&quot;:";
$language["pbuilder_options_name"]="Plugin Name:";
$language["pbuilder_options_group"]="Option Group:";
$language["pbuilder_options_nogroups"]="No option groups found!";
$language["pbuilder_options_noitems"]="Selected option group is empty!";

$language["pbuilder_template_form"]="Create Plugin Template";
$language["pbuilder_template_success1"]="Templates definition code:";
$language["pbuilder_template_success2"]="Templates installation code:";
$language["pbuilder_template_failure"]="You have not selected any templates.";
$language["pbuilder_template_items"]="Templates List:";
$language["pbuilder_template_itemsdesc"]="Choose one or more templates required by the plugin (you should create them first using &quot;PHPC Control&quot; plugin). Use Ctrl+click to select/deselect a template.";

$language["pbuilder_bundle_form"]="Create Plugin Bundle";
$language["pbuilder_bundle_success1"]="Bundles definition code:";
$language["pbuilder_bundle_success2"]="Bundles installation code:";
$language["pbuilder_bundle_failure"]="You have not selected any bundles.";
$language["pbuilder_bundle_items"]="Bundles List:";
$language["pbuilder_bundle_itemsdesc"]="Choose one or more bundles required by the plugin (you should create them first using &quot;PHPC Control&quot; plugin). Use Ctrl+click to select/deselect a bundle.";

/******************************************************************************/

$language["pbuilder_entity1_form"]="Create Plugin Entity - Step 1 - Data Input";
$language["pbuilder_entity1_name"]="Plugin Name:";
$language["pbuilder_entity1_table"]="Entity Data Table:";
$language["pbuilder_entity1_tabledesc"]="Choose a table containing entity data. The table must have an Auto_Increment primary key defined over a single column.";
$language["pbuilder_entity1_item1"]="Entity Identifier, Singular:";
$language["pbuilder_entity1_item2"]="Entity Identifier, Plural:";
$language["pbuilder_entity1_itemdesc"]="Only letters, digits and underscores are allowed.";
$language["pbuilder_entity1_append1"]="Suffix for appending to Action Names (optional):";
$language["pbuilder_entity1_append2"]="Suffix for appending to Localization Keys (optional):";

$language["pbuilder_entity2_form"]="Create Plugin Entity - Step 2 - Fields Selection";
$language["pbuilder_entity2_separator1"]="Fields To Display";
$language["pbuilder_entity2_separator2"]="Data Display Order";
$language["pbuilder_entity2_column"]="Representation of the field &quot;%s&quot;:";
$language["pbuilder_entity2_columndesc"]="Will be used for adding/editing the entity.";
$language["pbuilder_entity2_display"]="Fields used in the Entity List:";
$language["pbuilder_entity2_displaydesc"]="Choose one or more fields that will be displayed in the entity list. Use Ctrl+click to select/deselect a field.";
$language["pbuilder_entity2_displayorder"]="Display Order Field:";
$language["pbuilder_entity2_displayorderdesc"]="Specifying this field will cause automatic sorting by this field in the Entity List; besides, the small input fields for the order management also will be shown there.";
$language["pbuilder_entity2_nodisplayorder"]="Not used";
$language["pbuilder_entity2_badtable_noprimary"]="Selected table cannot be used for entity creation, because it does not have a primary key.";
$language["pbuilder_entity2_badtable_nocounter"]="Selected table cannot be used for entity creation, because its primary key is not a counter.";
$language["pbuilder_entity2_badtable_complex"]="Selected table cannot be used for entity creation, because its primary key is defined over multiple fields.";
$language["pbuilder_entity2_noitem"]="One of the entity identifiers wasn't specified.";
$language["pbuilder_entity2_sameitems"]="Entity identifiers cannot be equal.";

$language["pbuilder_entity3_header"]="&nbsp;Create Plugin Entity - Step 3 - Fields Order";
$language["pbuilder_entity3_order"]="Order";
$language["pbuilder_entity3_column"]="Field &quot;%s&quot;";
$language["pbuilder_entity3_nodisplay"]="No fields to display specified.";
$language["pbuilder_entity3_nocolumns"]="At least one of the fields should be editable.";

$language["pbuilder_entity4_success1"]="Code for the Main Menu:";
$language["pbuilder_entity4_success2"]="Code for the Entity Container:";
$language["pbuilder_entity4_success3"]="Code for the Action Handlers:";
$language["pbuilder_entity4_success4"]="Code for the plugin localization file in &quot;%s&quot;:";
$language["pbuilder_entity4_add"]="Add";
$language["pbuilder_entity4_modify"]="Modify";
$language["pbuilder_entity4_addform"]="Add New Item";
$language["pbuilder_entity4_addsuccess"]="Item added!";
$language["pbuilder_entity4_addfailure"]="Unable to add item.";
$language["pbuilder_entity4_editform"]="Edit Item";
$language["pbuilder_entity4_editsuccess"]="Item updated!";
$language["pbuilder_entity4_editfailure"]="Unable to update item.";
$language["pbuilder_entity4_addeditfield"]="Value of the Field &quot;%s&quot;:";
$language["pbuilder_entity4_removeprompt"]="Are you sure you want to delete this item?";
$language["pbuilder_entity4_removesuccess"]="Item deleted!";
$language["pbuilder_entity4_ordersuccess"]="Order updated!";
$language["pbuilder_entity4_modifyfield"]="%s";
$language["pbuilder_entity4_modifyoptions"]="Options";
$language["pbuilder_entity4_modifyorder"]="Order";
$language["pbuilder_entity4_modifyedit"]="Edit";
$language["pbuilder_entity4_modifyremove"]="Delete";

/******************************************************************************/

$language["pbuilder_form1_form"]="Create Form/Controller Pair";
$language["pbuilder_form1_count"]="Number of Form Fields:";
$language["pbuilder_form1_page"]="Controller Page:";
$language["pbuilder_form1_pagedesc"]="Name of the page (e.g. actionGuestbook) the controller will be linked to. A controller is a bundle processing the form's data.";
$language["pbuilder_form1_action"]="Value of the &quot;action&quot; Parameter:";
$language["pbuilder_form1_actiondesc"]="A controller can perform different actions, depending on this parameter's value.";

$language["pbuilder_form2_nopage"]="Controller page not specified.";
$language["pbuilder_form2_noaction"]="Action parameter not specified.";
$language["pbuilder_form2_field"]="Field Name";
$language["pbuilder_form2_meaning"]="Meaning";
$language["pbuilder_form2_type"]="Type";
$language["pbuilder_form2_limits"]="Limits*";
$language["pbuilder_form2_default"]="Default Value";
$language["pbuilder_form2_note"]="Note *. For integer and float types, you can specify either the minimal value of the parameter, or the minimal and maximal values separated by comma. For string types, you can specify the maximal length in symbols.";

$language["pbuilder_form3_success1"]="HTML code for the form:";
$language["pbuilder_form3_success2"]="Code for the controller:";
$language["pbuilder_form3_wrongfield"]="Name of one or more fields specified improperly.";
$language["pbuilder_form3_nofields"]="No form fields specified.";

/******************************************************************************/

$language["pbuilder_pictures1_form"]="Create Plugin Pictures";
$language["pbuilder_pictures1_count"]="Number of Pictures:";
$language["pbuilder_pictures1_type"]="File Format:";
$language["pbuilder_pictures1_typedesc"]="For simplifying the code generation, all pictures should be in the same format.";

$language["pbuilder_pictures2_form"]="Internal Picture Names";
$language["pbuilder_pictures2_separator"]="Picture Data Files";
$language["pbuilder_pictures2_name"]="Name for the Picture %d:";
$language["pbuilder_pictures2_file"]="Data File for the Picture %d:";

$language["pbuilder_pictures3_success"]="Code for pictures generation:";
$language["pbuilder_pictures3_nofile"]="One of the files not selected or was not uploaded properly.";
$language["pbuilder_pictures3_wrongformat"]="One of the files has wrong format.";

/******************************************************************************/

$language["pbuilder_error_pluginname"]="Plugin name not specified.";
$language["pbuilder_error_emptydb"]="No tables found in the database.";
$language["pbuilder_error_notables"]="You have not selected any tables.";

?>
