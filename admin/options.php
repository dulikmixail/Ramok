<?php

// PHPC Control Panel Plugin (10.5) - Site Options v1.0  by Antylevsky Aleksei (Next)

/*

// Main Menu Construction Code
makeMenuGroup("header","options_menu");
if($database->isTablePresent("settings")) {
  makeMenuItem("options_options","options.php?action=options",true);
  if(isAdministrator()) {
    makeMenuItem("options_add","options.php?action=add");
    makeMenuItem("options_addgroup","options.php?action=addgroup");
    makeMenuItem("options_modify","options.php?action=modify");
  }
  if(PhpcLocale=="") {
    makeMenuGroup("footer");
    makeMenuGroup("header","optionsmessages_menu");
    makeMenuItem("options_add","options.php?action=addmessage");
    makeMenuItem("options_modify","options.php?action=modifymessages");
    if(isAdministrator())
      makeMenuItem("options_languages","options.php?action=modifylanguages");
  }
}
else makeMenuItem("admin_install","options.php?action=install");
makeMenuGroup("footer");

*/

require "global.php";

define("MessagesOnPage",100);

$infoSettings=
  "id:key,groupid:chooser:options_addedit_groupid,".
  "title:input:options_addedit_title,".
  "description:textarea:options_addedit_description:admin_allowcodes,".
  "name:input:options_addedit_name,value:textarea:options_addedit_value,".
  "kind:chooser:options_addedit_kind,visible:yesno:options_addedit_visible";
$infoSettingGroups=
  "id:key,title:input:options_addeditgroup_title,".
  "displayorder:input:options_addeditgroup_order";
$infoMessages=
  "id:key,name:input:options_addeditmessage_name:".
  "options_addeditmessage_namedesc%s";

$localizedFields=array(
  "content"=>array("offset"=>2,"type"=>"textarea","title"=>"options_addeditmessage_content"));

adminLog("id,parent,locale");
adminActions("add,edit,remove,addgroup,editgroup,removegroup");
adminActions("order,modify,addlanguage,removelanguage,modifylanguages");
$action=acceptStringParameter("action");

makeAdminPage("header");

/********************************* Functions **********************************/

function createDefaultOptions()
{
  return array(
    array("groupid"=>0,"title"=>"options_defcache","name"=>"globalCache","kind"=>"textarea","visible"=>0),
    array("title"=>"options_defoption1","description"=>"options_defoption1desc","name"=>"siteOpen","value"=>1,"kind"=>"yesno"),
    array("title"=>"options_defoption2","description"=>"options_defoption2desc","name"=>"siteEnabled","value"=>1,"kind"=>"yesno"),
    array("title"=>"options_defoption3","description"=>"options_defoption3desc","name"=>"siteBlackList"),
    array("title"=>"options_defoption4","description"=>"options_defoption4desc","name"=>"siteAdminEmail"));
}

function getOptionKinds()
{
  global $language;
  return array(
    "input"=>$language["options_kind_input"],
    "textarea"=>$language["options_kind_textarea"],
    "yesno"=>$language["options_kind_yesno"]);
}

function makeOptionElement($kind, $title, $description, $name, $value)
{
  if($kind=="input") makeFormInput($title,$description,$name,$value,false);
  if($kind=="textarea") makeFormTextarea($title,$description,$name,$value,false);
  if($kind=="yesno") makeFormYesNo($title,$description,$name,$value,false);
}

/**************************** Plugin Installation *****************************/

if($action=="install") {
  if($database->isTablePresent("settings")) {
    makeError("admin_error_already");
    makeBreak();
    makeRefreshMenuLink();
  }
  else {
    makeNotification("admin_installstart");
    $success=false;
    switch($database->type) {
      case "mysql":
      case "dagsql":
        $database->customQuery("CREATE TABLE settinggroups (".
          "id INT NOT NULL AUTO_INCREMENT,".
          "title TINYTEXT NOT NULL,".
          "displayorder INT NOT NULL,".
          "PRIMARY KEY (id))");
        makeNotification("admin_installtable","settinggroups");
        $database->customQuery("CREATE TABLE settings (".
          "id INT NOT NULL AUTO_INCREMENT,".
          "groupid INT NOT NULL,".
          "title TINYTEXT NOT NULL,".
          "description LONGTEXT NOT NULL,".
          "name TINYTEXT NOT NULL,".
          "value LONGTEXT NOT NULL,".
          "kind TINYTEXT NOT NULL,".
          "visible TINYINT(1) NOT NULL,".
          "PRIMARY KEY (id))");
        makeNotification("admin_installtable","settings");
        $database->customQuery("CREATE TABLE messages (".
          "id INT NOT NULL AUTO_INCREMENT,".
          "name TINYTEXT NOT NULL,".
          "content$locale LONGTEXT NOT NULL,".
          "special TINYINT(1) NOT NULL,".
          "PRIMARY KEY (id))");
        makeNotification("admin_installtable","messages");
        $database->customQuery("CREATE TABLE relations (".
          "id INT NOT NULL AUTO_INCREMENT,".
          "master TINYTEXT NOT NULL,".
          "slave TINYTEXT NOT NULL,".
          "operation TINYTEXT NOT NULL,".
          "code LONGTEXT NOT NULL,".
          "PRIMARY KEY (id),".
          "UNIQUE KEY master (master(50),slave(50),operation(50)),".
          "KEY operation (master(50),operation(50),id))");
        makeNotification("admin_installtable","relations");
        $success=true;
        break;
    }
    if($success) {
      $values=array("name"=>"globalCache","special"=>1);
      $database->addLineStrict("messages",$values);
      makeNotification("admin_installdata","messages");
      installOptions("options_defgroup",createDefaultOptions());
      makeNotification("admin_installsuccess");
      makeBreak();
      makeRefreshMenuLink();
    }
    else makeWrongDBError();
  }
}

/******************************** Edit Options ********************************/

if($action=="options") {
  $groups=$database->getOrderedLines("settinggroups","displayorder");
  $links=array();
  foreach($groups as $index=>$group) {
    $settings=$database->getOrderedLines("settings","id","groupid=$group[id] AND visible=1");
    if(!count($settings)) { unset($groups[$index]); continue; }
    $links[$group["title"]]="#group$group[id]";
    $groups[$index]["settings"]=$settings;
  }
  if(count($links)>1) { makeLinks($links,false); makeBreak(); }
  $already=false;
  foreach($groups as $group) {
    makeAnchor("group$group[id]");
    makeForm($already?"separator":"header",$group["title"],"options","dooptions",false);
    $already=true;
    foreach($group["settings"] as $setting) {
      $title=$setting["title"];
      $description=$setting["description"];
      $name="setting[$setting[id]]";
      $value=$setting["value"];
      makeOptionElement($setting["kind"],$title,$description,$name,$value);
    }
  }
  if($already) makeForm("footer"); else makeError("options_error_empty");
  updateGlobalCache();
}

if($action=="dooptions") {
  $settings=$database->getOrderedLines("settings","id","groupid!=0 AND visible=1");
  foreach($settings as $setting) {
    $value=acceptStringParameter(array("setting",$setting["id"]));
    $database->modifyField("settings","value",$value,"id=$setting[id]");
  }
  updateGlobalCache();
  makeNotification("options_options_success");
}

/****************************** Add/Edit Option *******************************/

if($action=="add" || $action=="edit") {
  $add=$action=="add";
  $formtitle=$add?"options_add_form":"options_edit_form";
  $matrix=makeMatrix("settings",$add,$infoSettings);
  if($add) $matrix["groupid"]["value"]=acceptIntParameter("parent");
  $groups=$database->getOrderedLines("settinggroups","displayorder");
  $matrix["groupid"]["options"]=extractArrayColumns($groups,"id","title");
  $matrix["kind"]["options"]=getOptionKinds();
  if(count($groups))
    makeSmartForm($formtitle,"options","do$action",$matrix);
    else makeError("options_error_nogroups");
}

if($action=="doadd" || $action=="doedit") {
  $add=$action=="doadd";
  processSmartUpdate("settings",$add,$infoSettings);
  updateGlobalCache();
  makeNotification($add?"options_add_success":"options_edit_success");
  makeBreak();
  $action="modify";
}

/******************************* Remove Option ********************************/

if($action=="remove") {
  $id=acceptIntParameter("id");
  makePrompt("options_remove_prompt","options.php?action=doremove&id=$id");
}

if($action=="doremove") {
  $id=acceptIntParameter("id");
  $database->deleteLine("settings","id=$id");
  updateGlobalCache();
  makeNotification("options_remove_success");
  makeBreak();
  $action="modify";
}

/******************************* Add/Edit Group *******************************/

if($action=="addgroup" || $action=="editgroup") {
  $add=$action=="addgroup";
  $formtitle=$add?"options_addgroup_form":"options_editgroup_form";
  $matrix=makeMatrix("settinggroups",$add,$infoSettingGroups);
  if($add) $matrix["displayorder"]["value"]=
    $database->getMaxField("settinggroups","displayorder")+1;
  makeSmartForm($formtitle,"options","do$action",$matrix);
}

if($action=="doaddgroup" || $action=="doeditgroup") {
  $add=$action=="doaddgroup";
  processSmartUpdate("settinggroups",$add,$infoSettingGroups);
  makeNotification($add?"options_addgroup_success":"options_editgroup_success");
  makeBreak();
  $action="modify";
}

/******************************** Remove Group ********************************/

if($action=="removegroup") {
  $id=acceptIntParameter("id");
  makePrompt("options_removegroup_prompt","options.php?action=doremovegroup&id=$id");
}

if($action=="doremovegroup") {
  $id=acceptIntParameter("id");
  $database->deleteLines("settings","groupid=$id");
  $database->deleteLine("settinggroups","id=$id");
  updateGlobalCache();
  makeNotification("options_removegroup_success");
  makeBreak();
  $action="modify";
}

/*********************************** Order ************************************/

if($action=="order") {
  $groups=$database->getOrderedLines("settinggroups","displayorder");
  foreach($groups as $group) {
    $order=acceptIntParameter(array("order",$group["id"]));
    $database->modifyField("settinggroups","displayorder",$order,"id=$group[id]");
  }
  makeNotification("options_order_success");
  makeBreak();
  $action="modify";
}

/*********************************** Modify ***********************************/

if($action=="modify") {
  $groups=$database->getOrderedLines("settinggroups","displayorder");
  makeTree("header","options_modify_tree");
  foreach($groups as $group) {
    $settings=$database->getOrderedLines("settings","id","groupid=$group[id]");
    $links=array(
      "options_modify_groupedit"=>"options.php?action=editgroup&id=$group[id]",
      "options_modify_groupremove"=>"options.php?action=removegroup&id=$group[id]",
      "options_modify_groupadd"=>"options.php?action=add&parent=$group[id]");
    makeTreeGroupSimple("header",$group["title"],$links);
    foreach($settings as $setting) {
      $title="$setting[title] ($setting[name])";
      if(!$setting["visible"]) $title="[font=disabled]{$title}[/font]";
      $links=array(
        "options_modify_itemedit"=>"options.php?action=edit&id=$setting[id]",
        "options_modify_itemremove"=>"options.php?action=remove&id=$setting[id]");
      makeTreeItemSimple($title,$links);
    }
    makeTreeGroupSimple("footer");
  }
  makeTree("footer");
  if(count($groups)) {
    makeBreak();
    makeForm("header","options_order_form","options","order");
    foreach($groups as $group)
      makeFormInputOrder($group["title"],"","order[$group[id]]",$group["displayorder"],false);
    makeForm("footer");
  }
}

/****************************** Add/Edit Message ******************************/

if($action=="addmessage" || $action=="editmessage") {
  $add=$action=="addmessage";
  $formtitle=$add?"options_addmessage_form":"options_editmessage_form";
  $info=localizeSmartInfo("messages",$infoMessages,$localizedFields);
  $matrix=makeMatrix("messages",$add,$info);
  makeSmartForm($formtitle,"options","do$action",$matrix);
}

if($action=="doaddmessage" || $action=="doeditmessage") {
  $add=$action=="doaddmessage";
  $info=localizeSmartInfo("messages",$infoMessages,$localizedFields);
  processSmartUpdate("messages",$add,$info);
  updateGlobalCache();
  makeNotification($add?"options_addmessage_success":"options_editmessage_success");
  makeBreak();
  $action="modifymessages";
}

/******************************* Remove Message *******************************/

if($action=="removemessage") {
  $id=acceptIntParameter("id");
  makePrompt("options_removemessage_prompt","options.php?action=doremovemessage&id=$id");
}

if($action=="doremovemessage") {
  $id=acceptIntParameter("id");
  $database->deleteLine("messages","id=$id");
  updateGlobalCache();
  makeNotification("options_removemessage_success");
  makeBreak();
  $action="modifymessages";
}

/****************************** Modify Messages *******************************/

if($action=="modifymessages") {
  $locale=getTableMatchingLocale("messages",$localizedFields);
  $messages=getTablePagePortion("messages","name","special=0",MessagesOnPage,$page,$total);
  $columns=array(
    array("title"=>"options_modifymessages_name","width"=>"20%"),
    array("title"=>"options_modifymessages_content","width"=>"60%"),
    "options_modifymessages_options");
  makeTable("header",$columns);
  foreach($messages as $message) {
    $content=chopText(optimizeTextStrict($message["content$locale"]),100);
    makeTableCellSimple($message["name"]);
    makeTableCellSimple($content,array("wrap"=>true));
    $links=array(
      "options_modifymessages_edit"=>"options.php?action=editmessage&id=$message[id]",
      "options_modifymessages_remove"=>"options.php?action=removemessage&id=$message[id]");
    makeTableCellLinks($links);
  }
  makeTablePager($page,$total,"options.php?action=modifymessages&page=%s");
  makeTable("footer");
}

/***************************** Messages Languages *****************************/

if($action=="addlanguage" || $action=="doaddlanguage") {
  $success=makeTableLocalesAdd("messages",$localizedFields,"options",$action);
  if($success) $action="modifylanguages";
  updateGlobalCache();
}

if($action=="removelanguage" || $action=="doremovelanguage") {
  $success=makeTableLocalesRemove("messages",$localizedFields,"options",$action);
  if($success) $action="modifylanguages";
  updateGlobalCache();
}

if($action=="modifylanguages") {
  $addlink="options.php?action=addlanguage";
  $removelink="options.php?action=removelanguage&locale=%s";
  makeTableLocalesList("messages",$localizedFields,$addlink,$removelink);
}

/******************************************************************************/

makeAdminPage("footer");

?>
