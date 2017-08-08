<?php

// PHPC Control Panel Plugin (50.5) - Plugins Builder v1.2  by Antylevsky Aleksei (Next)

/*

// Main Menu Construction Code
makeMenuGroup("header","pbuilder_menu");
makeMenuItem("pbuilder_scratch","pbuilder.php?action=scratch");
makeMenuItem("pbuilder_tables","pbuilder.php?action=tables");
makeMenuItem("pbuilder_options","pbuilder.php?action=options");
makeMenuItem("pbuilder_template","pbuilder.php?action=template");
makeMenuItem("pbuilder_bundle","pbuilder.php?action=bundle");
makeMenuItem("pbuilder_entity","pbuilder.php?action=entity");
makeMenuItem("pbuilder_form","pbuilder.php?action=form");
makeMenuItem("pbuilder_pictures","pbuilder.php?action=pictures");
makeMenuGroup("footer");

*/

require "global.php";

define("CookiePluginName","cookiepbuildername");
define("ToDoConstant","TODO");

define("DefaultPictureGif","R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==");
define("DefaultPictureJpg","/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wAALCAABAAEBAREA/8QAFAABAAAAAAAAAAAAAAAAAAAAA//EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEAAD8AR//Z");
define("DefaultPicturePng","iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAAAEElEQVR4nGL4//8/A0CAAQAI/AL+M6PimQAAAABJRU5ErkJggg==");

adminLog();
$action=acceptStringParameter("action");

makeAdminPage("header");

/********************************** Samples ***********************************/

$samples=array();

$samples["scratch"]=<<<EOF
<?php

// PHPC Control Panel Plugin (30.5) - Insert Plugin Name Here

// Insert Plugin Description & Copyright Here

/*

// Main Menu Construction Code
makeMenuGroup("header","%s_menu");
if(\$database->isTablePresent("%s")) {
  // Insert Menu Code Here
}
else makeMenuItem("admin_install","%s.php?action=install");
makeMenuGroup("footer");

*/

require "global.php";

adminLog();
\$action=acceptStringParameter("action");

makeAdminPage("header");

/******************************************************************************/

makeAdminPage("footer");

?>
EOF;

$samples["scratchLocale"]=<<<EOF
<?php

\$language["%s_menu"]=%s;

?>
EOF;

$samples["scratchAction"]=<<<EOF
// ############################################################################
// %s
// ############################################################################

/*%s*/

if(\$action==%s) {
  makeTodo();
}
EOF;

$samples["tables"]=<<<EOF
/**************************** Plugin Installation *****************************/

if(\$action=="install") {
  if(\$database->isTablePresent(%s)) {
    makeError("admin_error_already");
    makeBreak();
    makeRefreshMenuLink();
  }
  else {
    makeNotification("admin_installstart");
    \$success=false;
    switch(\$database->type) {
      case "mysql":
      case "dagsql":%s
        \$success=true;
        break;
    }
    if(\$success) {
      // Insert Extra Plugin Installation Code Here
      makeNotification("admin_installsuccess");
      makeBreak();
      makeRefreshMenuLink();
    }
    else makeWrongDBError();
  }
}
EOF;

$samples["tablesStructure"]=<<<EOF
        \$database->customQuery(%s);
        makeNotification("admin_installtable",%s);
EOF;

$samples["options"]=<<<EOF
/********************************* Functions **********************************/

function createDefaultOptions()
{
  return array(%s);
}
EOF;

$samples["templateCommon"]=<<<EOF
/********************************** Samples ***********************************/

\$sampleTemplates=array();
EOF;

$samples["template"]=<<<EOF
\$sampleTemplates[%s]=<<<EOF%sEOF;
EOF;

$samples["bundleCommon"]=<<<EOF
/********************************** Samples ***********************************/

\$sampleBundles=array();
EOF;

$samples["bundle"]=<<<EOF
\$sampleBundles[%s]=<<<EOF%sEOF;
EOF;

$samples["entityMenu"]=<<<EOF
makeMenuItem("%n_add%a2","%n.php?action=add%a1");
makeMenuItem("%n_modify%ap2","%n.php?action=modify%ap1");
EOF;

$samples["entityHandlers"]=<<<EOF
/********************************** Add/Edit **********************************/

if(\$action=="add%a1" || \$action=="edit%a1") {
  \$add=\$action=="add%a1";
  \$formtitle=\$add?"%n_add%a2_form":"%n_edit%a2_form";
  \$matrix=makeMatrix("%t",\$add,%i);
  makeSmartForm(\$formtitle,"%n","do\$action",\$matrix);
}

if(\$action=="doadd%a1" || \$action=="doedit%a1") {
  \$add=\$action=="doadd%a1";%s
}

/*********************************** Remove ***********************************/

if(\$action=="remove%a1") {
  \$%p=acceptIntParameter("%p");
  makePrompt("%n_remove%a2_prompt","%n.php?action=doremove%a1&%p=\$%p");
}

if(\$action=="doremove%a1") {
  \$%p=acceptIntParameter("%p");
  \$database->deleteLine("%t","%p=\$%p");
  makeNotification("%n_remove%a2_success");
  makeBreak();
  \$action="modify%ap1";
}
EOF;

$samples["entityHandlersDefault"]=<<<EOF
/*********************************** Modify ***********************************/

if(\$action=="modify%ap1") {
  %i2=\$database->getOrderedLines("%t","%p");
  \$columns=array(%s1
    array("title"=>"%n_modify%ap2_options","width"=>"20%"));
  makeTable("header",\$columns);
  foreach(%i2 as %i1) {
    \$links=array(
      "%n_modify%ap2_edit"=>"%n.php?action=edit%a1&%p=%i1[%p]",
      "%n_modify%ap2_remove"=>"%n.php?action=remove%a1&%p=%i1[%p]");%s2
    makeTableCellLinks(\$links);
  }
  makeTable("footer");
}
EOF;

$samples["entityHandlersOrdered"]=<<<EOF
/*********************************** Order ************************************/

if(\$action=="order%ap1") {
  %i2=\$database->getOrderedLines("%t","%o");
  foreach(%i2 as %i1) {
    \$order=acceptIntParameter(array("order",%i1["%p"]));
    \$database->modifyField("%t","%o",\$order,"%p=%i1[%p]");
  }
  makeNotification("%n_order%ap2_success");
  makeBreak();
  \$action="modify%ap1";
}

/*********************************** Modify ***********************************/

if(\$action=="modify%ap1") {
  %i2=\$database->getOrderedLines("%t","%o");
  \$columns=array(%s1
    array("title"=>"%n_modify%ap2_options","width"=>"20%"),
    array("title"=>"%n_modify%ap2_%o","width"=>"1%"));
  makeTable("header",\$columns,"%n","order%ap1");
  foreach(%i2 as %i1) {
    \$links=array(
      "%n_modify%ap2_edit"=>"%n.php?action=edit%a1&%p=%i1[%p]",
      "%n_modify%ap2_remove"=>"%n.php?action=remove%a1&%p=%i1[%p]");%s2
    makeTableCellLinks(\$links);
    makeTableCellInputOrder("order[%i1[%p]]",%i1["%o"]);
  }
  makeTable("footer");
}
EOF;

$samples["entityHandlersColumn"]=<<<EOF
    "%n_modify%ap2_%s",
EOF;

$samples["entityHandlersCell"]=<<<EOF
    makeTableCellSimple(%i1[%s]);
EOF;

$samples["entityHandlersSimple"]=<<<EOF
  processSmartUpdate("%t",\$add,%i);
  makeNotification(\$add?"%n_add%a2_success":"%n_edit%a2_success");
  makeBreak();
  \$action="modify%ap1";
EOF;

$samples["entityHandlersComplex"]=<<<EOF
  \$success=processSmartUpdate("%t",\$add,%i);
  if(\$success)
    makeNotification(\$add?"%n_add%a2_success":"%n_edit%a2_success");
    else makeError(\$add?"%n_add%a2_failure":"%n_edit%a2_failure");
  makeBreak();
  if(\$success) \$action="modify%ap1";
EOF;

$samples["entityLocale"]=<<<EOF
%s

/******************************************************************************/

%s
EOF;

$samples["formSource"]=<<<EOF
<table border="0" cellpadding="0" cellspacing="4">
<form action="<write:link property=%s>" method="post">
<input type="hidden" name="action" value="%s">%s
<tr><td colspan="2" align="center"><input type="submit" value="<var:language:admin_submit>">
<input type="reset" value="<var:language:admin_reset>"></td></tr>
</form>
</table>
EOF;

$samples["formSourceItem"]=array(
  "input"=>"<tr><td>%n:</td><td><input type=\"text\" name=\"%n\" value=\"%v\"></td></tr>",
  "password"=>"<tr><td>%n:</td><td><input type=\"password\" name=\"%n\" value=\"%v\"></td></tr>",
  "textarea"=>"<tr><td>%n:</td><td><textarea name=\"%n\">%v</textarea></td></tr>",
  "chooser"=>"<tr><td>%n:</td><td><select name=\"%n\"></select></td></tr>",
  "yesno"=>"<tr><td>%n:</td><td>".
    "<input type=\"radio\" name=\"%n\" value=\"1\"%c1>&nbsp;<var:language:admin_yes>&nbsp;\r\n".
    "<input type=\"radio\" name=\"%n\" value=\"0\"%c0>&nbsp;<var:language:admin_no></td></tr>",
  "checkbox"=>"<tr><td>%n:</td><td><input type=\"checkbox\" name=\"%n\"%c1></td></tr>",
  "file"=>"<tr><td>%n:</td><td><input type=\"file\" name=\"%n\"></td></tr>",
  "hidden"=>"<input type=\"hidden\" name=\"%n\" value=\"%v\">");

$samples["formController"]=<<<EOF
\$action=acceptStringParameter("action");

switch(\$action) {
  case %s:%s
    // Insert Data Handling Code Here
    redirect("/");
}

redirect("/");
EOF;

$samples["pictures"]=<<<EOF
/*********************************** Image ************************************/

if(\$action=="image") {
  \$image=acceptStringParameter("image",100);
  switch(\$image) {%s
    default: \$content=%s;
  }
  makeImage(%s,base64_decode(\$content));
}
EOF;

$samples["picturesItem"]=<<<EOF
    case %s: \$content=%s; break;
EOF;

/********************************* Functions **********************************/

function getPictureOptions()
{
  return array(
    "gif"=>array("title"=>"GIF","extension"=>".gif","mimetype"=>"image/gif","default"=>DefaultPictureGif),
    "jpg"=>array("title"=>"JPEG","extension"=>".jpg","mimetype"=>"image/jpeg","default"=>DefaultPictureJpg),
    "png"=>array("title"=>"PNG","extension"=>".png","mimetype"=>"image/png","default"=>DefaultPicturePng));
}

function createInheritanceChain($subject, $setid=false)
{
  global $database;
  $result=array();
  if($setid===false) {
    $style=getAdminStyle();
    $setid=$style["{$subject}setid"];
  }
  $sets=$database->getLines("{$subject}sets");
  $sets=extractArrayColumns($sets,"id","parentid");
  while($setid) {
    if(in_array($setid,$result)) break;
    $result[]=$setid;
    $setid=$sets[$setid];
  }
  return $result;
}

function createInheritanceArray($table, $setChain)
{
  global $database;
  $sets=array();
  foreach($setChain as $setid)
    $sets[]=$database->customQuery("SELECT id,name FROM $table WHERE setid=$setid ORDER BY name");
  $result=array();
  foreach($sets as $setIndex=>$set) foreach($set as $item) {
    $name=strtolower($item["name"]);
    if(!isset($result[$name])) {
      $item["inheritance"]=$setIndex?"original":"default";
      $result[$name]=$item;
    }
    else $result[$name]["inheritance"]="inherited";
  }
  ksort($result);
  return $result;
}

function columnsSortCallback($value1, $value2)
{
  $delta=$value1["visible"]-$value2["visible"];
  if($delta) return $delta;
  $delta=$value1["order"]-$value2["order"];
  if($delta) return $delta;
  return strcmp($value1["name"],$value2["name"]);
}

function formatGeneratedCode($text, $scope)
{
  $replace=array(
    "%i1"=>"\$".$scope["item1"],
    "%i2"=>"\$".$scope["item2"],
    "%a1"=>$scope["append1"],
    "%a2"=>$scope["append2"],
    "%ap1"=>$scope["append1p"],
    "%ap2"=>$scope["append2p"],
    "%i"=>$scope["infoName"],
    "%n"=>$scope["plugin"],
    "%o"=>$scope["displayorder"],
    "%p"=>$scope["primary"],
    "%t"=>$scope["table"]);
  return str_replace(array_keys($replace),$replace,$text);
}

function makeTableCellChooserLarger($name, $options, $value=false, $filter=true)
{
  ob_start();
  makeTableCellChooser($name,$options,$value,$filter);
  $content=ob_get_clean();
  $content=preg_replace("{<select class=\"cell\"}","\\0 style=\"width:180px\"",$content);
  echo $content;
}

/********************************** Scratch ***********************************/

if($action=="scratch") {
  $locales=explode(",",PhpcLocalesList);
  $name=acceptStringParameter(CookiePluginName);
  makeForm("header","pbuilder_scratch_form","pbuilder","doscratch");
  makeFormInput("pbuilder_scratch_name","pbuilder_scratch_namedesc","name",$name);
  foreach($locales as $locale) {
    $title=format($language["pbuilder_scratch_title"],$language["locales"][$locale]);
    makeFormInput($title,"","title$locale","",false);
  }
  makeForm("footer");
  makeBreak();
  makeForm("header","pbuilder_scratchaction_form","pbuilder","doscratchaction");
  makeFormInput("pbuilder_scratchaction_action","","actionname");
  makeFormInput("pbuilder_scratchaction_comment","","comment");
  makeFormInput("pbuilder_scratchaction_section","","section");
  makeForm("footer");
}

if($action=="doscratch") {
  $locales=explode(",",PhpcLocalesList);
  $name=acceptStringParameter("name");
  @setcookie(CookiePluginName,$name,0,"/");
  if($name=="") makeAdminError("pbuilder_error_pluginname");
  makeNotification("pbuilder_scratch_success1","admin/$name.php");
  makeBreak();
  makeQuote(str_replace("%s",$name,$samples["scratch"]));
  foreach($locales as $locale) {
    $title=acceptStringParameter("title$locale");
    if($title=="") continue;
    $params=array($language["locales"][$locale],"language/$locale/{$name}_$locale.php");
    makeBreak();
    makeNotification("pbuilder_scratch_success2",$params);
    makeBreak();
    makeQuote(format($samples["scratchLocale"],array($name,quoteText($title))));
  }
}

if($action=="doscratchaction") {
  $actionname=acceptStringParameter("actionname");
  $comment=acceptStringParameter("comment");
  $section=acceptStringParameter("section");
  $comment=str_pad($comment!=""?" $comment ":"",76,"*",STR_PAD_BOTH);
  $section=str_pad($section!=""?" $section ":"",76,"#",STR_PAD_BOTH);
  $parameters=array($section,$comment,quoteText($actionname));
  makeNotification("pbuilder_scratchaction_success");
  makeBreak();
  makeQuote(format($samples["scratchAction"],$parameters));
}

/*********************************** Tables ***********************************/

if($action=="tables") {
  $name=acceptStringParameter(CookiePluginName);
  $tables=$database->getTablesList();
  if(count($tables)) {
    $tables=combineArrays($tables,$tables);
    $maintable=isset($tables[$name])?$name:false;
    makeForm("header","pbuilder_tables_form","pbuilder","dotables");
    makeFormChooser("pbuilder_tables_maintable","pbuilder_tables_maintabledesc","maintable",$maintable,$tables);
    makeFormSelector("pbuilder_tables_tables","pbuilder_tables_tablesdesc","tables[]",$tables);
    makeForm("footer");
  }
  else makeError("pbuilder_error_emptydb");
}

if($action=="dotables") {
  $maintable=acceptStringParameter("maintable");
  $tables=acceptArrayParameter("tables");
  if(count($tables)) {
    $content="";
    foreach($tables as $table) {
      $structure=$database->exportTableStructure($table);
      $structure=explode("\r\n",trim($structure,"\r\n;"));
      unset($structure[0]);
      foreach($structure as $index=>$line)
        $structure[$index]=quoteText(trim($line));
      $structure=implode(".\r\n          ",$structure);
      $content.="\r\n".format($samples["tablesStructure"],array($structure,quoteText($table)));
    }
    makeNotification("pbuilder_tables_success");
    makeBreak();
    makeQuote(format($samples["tables"],array(quoteText($maintable),$content)));
  }
  else makeError("pbuilder_error_notables");
}

/********************************** Options ***********************************/

if($action=="options") {
  $name=acceptStringParameter(CookiePluginName);
  if($database->isTablePresent("settings"))
    $groups=$database->getOrderedLines("settinggroups","displayorder");
    else $groups=array();
  if(count($groups)) {
    $groups=extractArrayColumns($groups,"id","title");
    makeForm("header","pbuilder_options_form","pbuilder","dooptions");
    makeFormInput("pbuilder_options_name","","name",$name);
    makeFormChooser("pbuilder_options_group","","groupid",false,$groups);
    makeForm("footer");
  }
  else makeError("pbuilder_options_nogroups");
}

if($action=="dooptions") {
  $locales=explode(",",PhpcLocalesList);
  $name=acceptStringParameter("name");
  $groupid=acceptIntParameter("groupid");
  @setcookie(CookiePluginName,$name,0,"/");
  if($name=="") makeAdminError("pbuilder_error_pluginname");
  $group=$database->getLine("settinggroups","id=$groupid");
  $settings=$database->getOrderedLines("settings","id","groupid=$groupid AND visible!=0");
  if(!count($settings)) makeAdminError("pbuilder_options_noitems");
  $content=array();
  $index=0;
  foreach($settings as $setting) {
    $index++;
    $value=$setting["value"];
    if(isTrueInteger($value)) $value=(int)$value;
      else if(isTrueFloat($value)) $value=(float)$value;
    $entry=array(
      "title"=>"{$name}_defoption$index",
      "description"=>"{$name}_defoption{$index}desc",
      "name"=>$setting["name"],
      "value"=>$value,
      "kind"=>$setting["kind"]);
    if($setting["description"]=="") unset($entry["description"]);
    if($setting["value"]=="") unset($entry["value"]);
    if($setting["kind"]=="input") unset($entry["kind"]);
    foreach($entry as $param=>$value) {
      if(!is_int($value) && !is_float($value)) $value=quoteText($value);
      $entry[$param]=quoteText($param)."=>$value";
    }
    $content[]="    array(".implode(",",$entry).")";
  }
  $content=implode(",\r\n",$content);
  makeNotification("pbuilder_options_success1");
  makeBreak();
  makeQuote(format($samples["options"],"\r\n$content"));
  makeBreak();
  makeNotification("pbuilder_options_success2");
  makeBreak();
  makeQuote("installOptions(\"{$name}_defgroup\",createDefaultOptions());");
  foreach($locales as $locale) {
    $key="{$name}_defgroup";
    $message=$locale==$language["locale"]?$group["title"]:ToDoConstant;
    $content="\$language[\"$key\"]=".quoteText($message).";";
    $index=0;
    foreach($settings as $setting) {
      $index++;
      $key="{$name}_defoption$index";
      $message=$locale==$language["locale"]?$setting["title"]:ToDoConstant;
      $content.="\r\n\$language[\"$key\"]=".quoteText($message).";";
      if($setting["description"]=="") continue;
      $key="{$name}_defoption{$index}desc";
      $message=$locale==$language["locale"]?$setting["description"]:ToDoConstant;
      $content.="\r\n\$language[\"$key\"]=".quoteText($message).";";
    }
    makeBreak();
    makeNotification("pbuilder_options_success3",$language["locales"][$locale]);
    makeBreak();
    makeQuote($content);
  }
}

/****************************** Template/Bundle *******************************/

if($action=="template" || $action=="bundle") {
  $subject=$action;
  if(!$database->isTablePresent("styles"))
    makeAdminError("admin_error_noadminstyle");
  $setChain=createInheritanceChain($subject);
  $items=createInheritanceArray("{$subject}s",$setChain);
  $items=extractArrayColumns($items,"id","name");
  makeForm("header","pbuilder_{$subject}_form","pbuilder","do$action");
  makeFormSelector("pbuilder_{$subject}_items","pbuilder_{$subject}_itemsdesc","items[]",$items);
  makeForm("footer");
}

if($action=="dotemplate" || $action=="dobundle") {
  $subject=substr($action,2);
  $subjectUpper=ucfirst($subject);
  $items=acceptArrayParameter("items");
  if(count($items)) {
    $result1=$samples["{$subject}Common"];
    $result2="";
    foreach($items as $id) {
      $item=$database->getLine("{$subject}s","id=$id");
      $content="\r\n".trim($item["content"])."\r\n";
      $content=str_replace("\\","\\\\",$content);
      $content=str_replace("\$","\\\$",$content);
      $params=array(quoteText($item["name"]),$content);
      $result1.="\r\n\r\n".format($samples[$subject],$params);
      $params=array();
      $params[]=quoteText($item["name"]);
      $params[]="\$sample{$subjectUpper}s[$params[0]]";
      $extra=$subject=="template"?"parent":"plugins";
      if($item[$extra]!="") $params[]=quoteText($item[$extra]);
      $result2.="\r\ninstall$subjectUpper(".implode(",",$params).");";
    }
    makeNotification("pbuilder_{$subject}_success1");
    makeBreak();
    makeQuote($result1);
    makeBreak();
    makeNotification("pbuilder_{$subject}_success2");
    makeBreak();
    makeQuote(trim($result2));
  }
  else makeError("pbuilder_{$subject}_failure");
}

/*********************************** Entity ***********************************/

if($action=="entity") {
  $name=acceptStringParameter(CookiePluginName);
  $tables=$database->getTablesList();
  if(count($tables)) {
    $tables=combineArrays($tables,$tables);
    $maintable=isset($tables[$name])?$name:false;
    makeForm("header","pbuilder_entity1_form","pbuilder","entity2");
    makeFormInput("pbuilder_entity1_name","","name",$name);
    makeFormChooser("pbuilder_entity1_table","pbuilder_entity1_tabledesc","table",$maintable,$tables);
    makeFormInput("pbuilder_entity1_item1","pbuilder_entity1_itemdesc","item1");
    makeFormInput("pbuilder_entity1_item2","pbuilder_entity1_itemdesc","item2");
    makeFormInput("pbuilder_entity1_append1","pbuilder_entity1_itemdesc","append1");
    makeFormInput("pbuilder_entity1_append2","pbuilder_entity1_itemdesc","append2");
    makeForm("footer");
  }
  else makeError("pbuilder_error_emptydb");
}

if($action=="entity2") {
  $name=acceptStringParameter("name");
  $table=acceptStringParameter("table");
  $item1=acceptStringParameter("item1");
  $item2=acceptStringParameter("item2");
  $append1=acceptStringParameter("append1");
  $append2=acceptStringParameter("append2");
  @setcookie(CookiePluginName,$name,0,"/");
  if($name=="") makeAdminError("pbuilder_error_pluginname");
  $info=$database->getTableInformation($table);
  if(!isset($info["uniques"]["PRIMARY"]))
    makeAdminError("pbuilder_entity2_badtable_noprimary");
  if(count($info["uniques"]["PRIMARY"])>1)
    makeAdminError("pbuilder_entity2_badtable_complex");
  $primary=$info["uniques"]["PRIMARY"][0]["name"];
  $column=searchArrayLine($info["columns"],"name",$primary);
  if(!($column["attrs"]&ColumnAttributeCounter))
    makeAdminError("pbuilder_entity2_badtable_nocounter");
  if($item1=="" || $item2=="") makeAdminError("pbuilder_entity2_noitem");
  if($item1==$item2) makeAdminError("pbuilder_entity2_sameitems");
  makeForm("header","pbuilder_entity2_form","pbuilder","entity3");
  makeFormHidden("name",$name);
  makeFormHidden("table",$table);
  makeFormHidden("item1",$item1);
  makeFormHidden("item2",$item2);
  makeFormHidden("append1",$append1);
  makeFormHidden("append2",$append2);
  $meaningsTable=array(
    "input"=>array("input","password","chooser","hidden","none"),
    "textarea"=>array("textarea","editor","hidden","none"),
    "int"=>array("input","chooser","datetime","hidden","none"),
    "float"=>array("input","hidden","none"),
    "yesno"=>array("yesno","hidden","none"));
  $meaningsExceptions=array(
    "{^dateline\$}i"=>"datetime",
    "{id\$}i"=>"chooser");
  $order=1000;
  foreach($info["columns"] as $index=>$column) {
    if($column["name"]==$primary) {
      makeFormHidden("meaning$column[name]","key");
      makeFormHidden("order$column[name]",++$order);
      unset($info["columns"][$index]);
      continue;
    }
    $title=format($language["pbuilder_entity2_column"],$column["name"]);
    $description=$language["pbuilder_entity2_columndesc"];
    $type=$column["type"].($column["size"]?"($column[size])":"");
    $meaning=$database->getColumnMeaning($type);
    $info["columns"][$index]["meaning"]=$meaning;
    $meanings=isset($meaningsTable[$meaning])?$meaningsTable[$meaning]:array();
    $options=array();
    foreach($meanings as $meaning)
      $options[$meaning]=$language["pbuilder_meaning"][$meaning];
    $value=false;
    foreach($meaningsExceptions as $pattern=>$meaning)
      if(preg_match($pattern,$column["name"])) $value=$meaning;
    if(!isset($options[$value])) $value=false;
    makeFormChooser($title,$description,"meaning$column[name]",$value,$options,false);
    makeFormHidden("order$column[name]",++$order);
  }
  $options=$database->getFieldsList($table);
  $options=combineArrays($options,$options);
  makeForm("separator","pbuilder_entity2_separator1");
  makeFormSelector("pbuilder_entity2_display","pbuilder_entity2_displaydesc","display[]",$options);
  $options=array(""=>$language["pbuilder_entity2_nodisplayorder"]);
  $value=false;
  foreach($info["columns"] as $column)
    if($column["meaning"]=="int" || $column["meaning"]=="float") {
      $options[$column["name"]]=$column["name"];
      if($value===false && preg_match("{order\$}i",$column["name"]))
        $value=$column["name"];
    }
  if(count($options)>1) {
    makeForm("separator","pbuilder_entity2_separator2");
    makeFormChooser("pbuilder_entity2_displayorder","pbuilder_entity2_displayorderdesc","displayorder",$value,$options);
  }
  else makeFormHidden("displayorder");
  makeForm("footer");
}

if($action=="entity3" || $action=="entity4") {
  $plugin=acceptStringParameter("name");
  $table=acceptStringParameter("table");
  $item1=acceptStringParameter("item1");
  $item2=acceptStringParameter("item2");
  $append1=acceptStringParameter("append1");
  $append2=acceptStringParameter("append2");
  $append1p=$append1.(preg_match("{[^s\W]\$}i",$append1)?"s":"");
  $append2p=$append2.(preg_match("{[^s\W]\$}i",$append2)?"s":"");
  if($append1==$item1) $append1p=$item2;
  if($append2==$item1) $append2p=$item2;
  $display=acceptArrayParameter("display");
  if(!count($display)) makeAdminError("pbuilder_entity3_nodisplay");
  $displayorder=acceptStringParameter("displayorder");
  $info=$database->getTableInformation($table);
  $columns=array();
  $invisibleTotal=0;
  foreach($info["columns"] as $column) {
    $name=$column["name"];
    $meaning=acceptStringParameter("meaning$name");
    $oldorder=acceptIntParameter("oldorder$name");
    $order=acceptIntParameter("order$name");
    $visible=!in_array($meaning,array("key","hidden","none"));
    $columns[]=compact("name","meaning","oldorder","order","visible");
    if(!$visible) $invisibleTotal++;
  }
  usort($columns,"columnsSortCallback");
  if($invisibleTotal==count($columns))
    makeAdminError("pbuilder_entity3_nocolumns");
  $changed=false;
  foreach($columns as $index=>$column) {
    $order=$index-$invisibleTotal+1;
    if($column["order"]!=$order || $column["oldorder"]!=$order) $changed=true;
    $columns[$index]["order"]=$order;
  }
  if($invisibleTotal==count($columns)-1) { $changed=false; $action="entity4"; }
  if($changed) {
    $tableColumns=array(
      array("title"=>"pbuilder_entity3_header","colspan"=>2,"align"=>"left"),
      array("title"=>"pbuilder_entity3_order","width"=>"1%"));
    makeTable("header",$tableColumns,"pbuilder","entity4");
    makeFormHidden("name",$plugin);
    makeFormHidden("table",$table);
    makeFormHidden("item1",$item1);
    makeFormHidden("item2",$item2);
    makeFormHidden("append1",$append1);
    makeFormHidden("append2",$append2);
    foreach($display as $column) makeFormHidden("display[]",$column);
    makeFormHidden("displayorder",$displayorder);
    foreach($columns as $column) {
      makeFormHidden("meaning$column[name]",$column["meaning"]);
      makeFormHidden("oldorder$column[name]",$column["order"]);
      if($column["visible"]) {
        $title=format($language["pbuilder_entity3_column"],$column["name"]);
        $meaning=$language["pbuilder_meaning"][$column["meaning"]];
        makeTableCellSimple($title);
        makeTableCellSimple($meaning);
        makeTableCellInputOrder("order$column[name]",$column["order"]);
      }
      else makeFormHidden("order$column[name]",$column["order"]);
    }
    makeTable("footer");
    $action="";
  }
}

if($action=="entity4") {
  $locales=explode(",",PhpcLocalesList);
  $primary=searchArrayField($columns,"meaning","name","key");
  $infoName="\$info".ucfirst($item2);
  $infoData=array();
  foreach($columns as $column) if($column["meaning"]!="none") {
    $block="$column[name]:$column[meaning]";
    if($column["visible"]) $block.=":{$plugin}_addedit{$append2}_$column[name]";
    if(!count($infoData)) { $infoData[]=$block; continue; }
    $index=count($infoData)-1;
    $infoData[$index].=",";
    $append=strlen($infoData[$index].$block)<75;
    if($append) $infoData[$index].=$block; else $infoData[]=$block;
  }
  foreach($infoData as $index=>$line) $infoData[$index]=quoteText($line);
  $menu=formatGeneratedCode($samples["entityMenu"],get_defined_vars());
  $infoData="$infoName=\r\n  ".implode(".\r\n  ",$infoData).";";
  $complexScheme=count($info["uniques"])>1;
  $fragment1="entityHandlers".($displayorder!=""?"Ordered":"Default");
  $fragment2="entityHandlers".($complexScheme?"Complex":"Simple");
  $insert1=$insert2="";
  foreach($display as $column) {
    $line1=str_replace("%s",$column,$samples["entityHandlersColumn"]);
    $line2=str_replace("%s",quoteText($column),$samples["entityHandlersCell"]);
    $insert1.="\r\n".$line1;
    $insert2.="\r\n".$line2;
  }
  $handlers=str_replace("%s1",$insert1,$samples[$fragment1]);
  $handlers=str_replace("%s2",$insert2,$handlers);
  $handlers=$samples["entityHandlers"]."\r\n\r\n".$handlers;
  $handlers=str_replace("%s","\r\n".$samples[$fragment2],$handlers);
  $handlers=formatGeneratedCode($handlers,get_defined_vars());
  makeNotification("pbuilder_entity4_success1");
  makeBreak();
  makeQuote($menu);
  makeBreak();
  makeNotification("pbuilder_entity4_success2");
  makeBreak();
  makeQuote($infoData);
  makeBreak();
  makeNotification("pbuilder_entity4_success3");
  makeBreak();
  makeQuote($handlers);
  foreach($locales as $locale) {
    $contents=array(
      "menu"=>array(
        "%n_add%a2"=>"pbuilder_entity4_add",
        "%n_modify%ap2"=>"pbuilder_entity4_modify"),
      "addedit"=>array(
        "%n_add%a2_form"=>"pbuilder_entity4_addform",
        "%n_add%a2_success"=>"pbuilder_entity4_addsuccess",
        "%n_add%a2_failure"=>"pbuilder_entity4_addfailure",
        "%n_edit%a2_form"=>"pbuilder_entity4_editform",
        "%n_edit%a2_success"=>"pbuilder_entity4_editsuccess",
        "%n_edit%a2_failure"=>"pbuilder_entity4_editfailure"),
      "remove"=>array(
        "%n_remove%a2_prompt"=>"pbuilder_entity4_removeprompt",
        "%n_remove%a2_success"=>"pbuilder_entity4_removesuccess"),
      "order"=>array(
        "%n_order%ap2_success"=>"pbuilder_entity4_ordersuccess"),
      "modify"=>array());
    foreach($display as $column) {
      $message="pbuilder_entity4_modifyfield:".ucfirst($column);
      $contents["modify"]["%n_modify%ap2_$column"]=$message;
    }
    $contents["modify"]+=array(
      "%n_modify%ap2_options"=>"pbuilder_entity4_modifyoptions",
      "%n_modify%ap2_$displayorder"=>"pbuilder_entity4_modifyorder",
      "%n_modify%ap2_edit"=>"pbuilder_entity4_modifyedit",
      "%n_modify%ap2_remove"=>"pbuilder_entity4_modifyremove");
    foreach($columns as $column) if($column["visible"]) {
      $name=$column["name"];
      $value="pbuilder_entity4_addeditfield:$name";
      $contents["addedit"]["%n_addedit%a2_$name"]=$value;
    }
    if(!$complexScheme) {
      unset($contents["addedit"]["%n_add%a2_failure"]);
      unset($contents["addedit"]["%n_edit%a2_failure"]);
    }
    if($displayorder=="") {
      unset($contents["order"]);
      unset($contents["modify"]["%n_modify%ap2_$displayorder"]);
    }
    foreach($contents as $index=>$content) {
      foreach($content as $key=>$message) {
        $keyFormatted=quoteText(formatGeneratedCode($key,get_defined_vars()));
        $params=explodeSmart(":",$message);
        $message=array_shift($params);
        $message=format($language[$message],$params);
        $message=$locale==$language["locale"]?$message:ToDoConstant;
        $content[$key]="\$language[$keyFormatted]=".quoteText($message).";";
      }
      $contents[$index]=implode("\r\n",$content);
    }
    $first=array_shift($contents);
    $contents=array($first,implode("\r\n\r\n",$contents));
    makeBreak();
    makeNotification("pbuilder_entity4_success4",$language["locales"][$locale]);
    makeBreak();
    makeQuote(format($samples["entityLocale"],$contents));
  }
}

/************************************ Form ************************************/

if($action=="form") {
  makeForm("header","pbuilder_form1_form","pbuilder","form2");
  makeFormInput("pbuilder_form1_count","","count");
  makeFormInput("pbuilder_form1_page","pbuilder_form1_pagedesc","page");
  makeFormInput("pbuilder_form1_action","pbuilder_form1_actiondesc","actionname");
  makeForm("footer");
}

if($action=="form2") {
  $count=acceptIntParameter("count",1,100);
  $page=acceptStringParameter("page");
  $actionname=acceptStringParameter("actionname");
  if($page=="") makeAdminError("pbuilder_form2_nopage");
  if($actionname=="") makeAdminError("pbuilder_form2_noaction");
  $columns=array(
    "pbuilder_form2_field",
    array("title"=>"pbuilder_form2_meaning","width"=>"50%"),
    array("title"=>"pbuilder_form2_type","width"=>"50%"),
    "pbuilder_form2_limits",
    "pbuilder_form2_default");
  makeTable("header",$columns,"pbuilder","form3");
  makeFormHidden("count",$count);
  makeFormHidden("page",$page);
  makeFormHidden("actionname",$actionname);
  for($index=0; $index<$count; $index++) {
    makeTableCellInput("field[$index]");
    makeTableCellChooserLarger("meaning[$index]",$language["pbuilder_form_meaning"]);
    makeTableCellChooserLarger("type[$index]",$language["pbuilder_form_type"]);
    makeTableCellInput("limits[$index]");
    makeTableCellInput("default[$index]");
  }
  makeTable("footer");
  makeBreak();
  makeNotification("pbuilder_form2_note");
}

if($action=="form3") {
  $count=acceptIntParameter("count",1,100);
  $page=acceptStringParameter("page");
  $actionname=acceptStringParameter("actionname");
  $fields=array();
  $uploads=false;
  for($index=0; $index<$count; $index++) {
    $field=acceptStringParameter(array("field",$index));
    if($field=="") continue;
    if(!preg_match("{^[a-z_]\w*\$}i",$field))
      makeAdminError("pbuilder_form3_wrongfield");
    $meaning=acceptStringParameter(array("meaning",$index));
    if($meaning=="file") $uploads=true;
    $type=acceptStringParameter(array("type",$index));
    $limits=acceptStringParameter(array("limits",$index));
    $limits=explodeSmart(",",$limits);
    foreach($limits as $limitIndex=>$limit) $limits[$limitIndex]=(float)$limit;
    $default=acceptStringParameter(array("default",$index));
    $checked=$default=="" || $default;
    $html="\r\n".$samples["formSourceItem"][$meaning];
    $html=str_replace("%n",htmlspecialchars($field),$html);
    $html=str_replace("%v",htmlspecialchars($default),$html);
    $html=str_replace("%c0",$checked?"":" checked",$html);
    $html=str_replace("%c1",$checked?" checked":"",$html);
    $html=str_replace(" value=\"\"","",$html);
    if($type=="int" || $type=="float") {
      $function="accept".ucfirst($type)."Parameter";
      $limit1=isset($limits[0])?",$limits[0]":"";
      $limit2=isset($limits[1])?",$limits[1]":"";
      $php="\$$field=$function(".quoteText($field)."$limit1$limit2);";
    }
    else {
      $limit=isset($limits[0])?",$limits[0]":"";
      $php="\$$field=acceptStringParameter(".quoteText($field)."$limit);";
    }
    $exceptions=array(
      "yesno"=>"\$$field=acceptIntParameter(".quoteText($field).",0,1);",
      "checkbox"=>"\$$field=acceptIntParameter(".quoteText($field).",0,1);",
      "file"=>"\$$field=\$fileSystem->getUploadedFile(".quoteText($field).");");
    if(isset($exceptions[$meaning])) $php=$exceptions[$meaning];
    $php="\r\n    ".$php;
    $fields[$field]=compact("html","php");
  }
  if(count($fields)) {
    $parameters=array(
      quoteText($page),
      htmlspecialchars($actionname),
      implode("",extractArrayColumn($fields,"html")));
    $result1=format($samples["formSource"],$parameters);
    if($uploads) $result1=
      str_replace("<form ","<form enctype=\"multipart/form-data\" ",$result1);
    if(PhpcLocale!="") {
      $pattern="{<var:language:(\w+)>}e";
      $replace="htmlspecialchars(\$language[\"\\1\"])";
      $result1=preg_replace($pattern,$replace,$result1);
    }
    $parameters=array(
      quoteText($actionname),
      implode("",extractArrayColumn($fields,"php")));
    $result2=format($samples["formController"],$parameters);
    makeNotification("pbuilder_form3_success1");
    makeBreak();
    makeQuote($result1);
    makeBreak();
    makeNotification("pbuilder_form3_success2");
    makeBreak();
    makeQuote($result2);
  }
  else makeError("pbuilder_form3_nofields");
}

/********************************** Pictures **********************************/

if($action=="pictures") {
  $options=getPictureOptions();
  $options=extractArrayColumn($options,"title");
  makeForm("header","pbuilder_pictures1_form","pbuilder","pictures2");
  makeFormInput("pbuilder_pictures1_count","","count");
  makeFormChooser("pbuilder_pictures1_type","pbuilder_pictures1_typedesc","type",false,$options);
  makeForm("footer");
}

if($action=="pictures2") {
  $count=acceptIntParameter("count",1,100);
  $type=acceptStringParameter("type");
  makeForm("header","pbuilder_pictures2_form","pbuilder","pictures3");
  makeFormHidden("count",$count);
  makeFormHidden("type",$type);
  for($index=0; $index<$count; $index++) {
    $title=format($language["pbuilder_pictures2_name"],$index+1);
    makeFormInput($title,"","name$index","",false);
  }
  makeForm("separator","pbuilder_pictures2_separator");
  for($index=0; $index<$count; $index++) {
    $title=format($language["pbuilder_pictures2_file"],$index+1);
    makeFormFile($title,"","file$index",false);
  }
  makeForm("footer");
}

if($action=="pictures3") {
  $count=acceptIntParameter("count",1,100);
  $type=acceptStringParameter("type");
  $options=getPictureOptions();
  $pictures="";
  for($index=0; $index<$count; $index++) {
    $name=acceptStringParameter("name$index");
    $file=$fileSystem->getUploadedFile("file$index");
    if(!$file) makeAdminError("pbuilder_pictures3_nofile");
    $extension=$fileSystem->getFileExtension($file["filename"]);
    if($extension!=$options[$type]["extension"])
      makeAdminError("pbuilder_pictures3_wrongformat");
    $parameters=array(
      quoteText($name),
      quoteText(base64_encode($file["content"])));
    $pictures.="\r\n".format($samples["picturesItem"],$parameters);
  }
  $parameters=array(
    $pictures,
    quoteText($options[$type]["default"]),
    quoteText($options[$type]["mimetype"]));
  $result=format($samples["pictures"],$parameters);
  makeNotification("pbuilder_pictures3_success");
  makeBreak();
  makeQuote($result);
}

/******************************************************************************/

makeAdminPage("footer");

?>
