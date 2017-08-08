<?php

// PHP Compiler by Antylevsky Aleksei (Next)

function formatAdminText($text)
{
  $text=filterText($text);
  if(strpos($text,"[")===false) return $text;
  $text=preg_replace("{\[(/?[biu])\]}is","<\\1>",$text);
  $text=preg_replace("{\[font=(\w+)\](.*?)\[/font\]}is","<font class=\"\\1\">\\2</font>",$text);
  $text=preg_replace_callback("{\[url(=[^\]]*)?\](.*?)\[/url\]}is",array("Formatter","processTagUrl"),$text);
  $text=preg_replace_callback("{\[email(=[^\]]*)?\](.*?)\[/email\]}is",array("Formatter","processTagEmail"),$text);
  return $text;
}

function checkConstraints($masterTable, $masterKey, $slaveTable, $slaveKey, $slaveField, $zero=false)
{
  global $database;
  $errors=array();
  $masterData=$database->getOrderedLines($masterTable,$masterKey);
  $slaveData=$database->getOrderedLines($slaveTable,$slaveKey);
  $valid=$zero===false?array():array($zero);
  foreach($masterData as $line) $valid[]=$line[$masterKey];
  foreach($slaveData as $line)
    if(!in_array($line[$slaveField],$valid)) $errors[]=$line[$slaveKey];
  return $errors;
}

function checkRecursion($table, $key, $field, $zero=0)
{
  global $database;
  $errors=array();
  $data=$database->getOrderedLines($table,$key);
  $data=extractArrayColumns($data,$key,$field);
  foreach($data as $index=>$value) {
    $visited=array($index);
    while($value!=$zero) {
      if(in_array($value,$visited)) {
        foreach($visited as $position=>$item)
          if($item==$value) break; else unset($visited[$position]);
        $errors[]=min($visited);
        break;
      }
      $visited[]=$value;
      $data[$index]=$zero;
      $index=$value;
      $value=$data[$index];
    }
  }
  return $errors;
}

function adminLog($params="")
{
  global $database;
  $action=acceptStringParameter("action");
  if($action=="" || $action=="image" || $action=="icon") return;
  if(!$database->isTablePresent("adminlog")) return;
  $params=explodeSmart(",",$params);
  foreach($params as $index=>$param) {
    $value=acceptStringParameter($param);
    if($value=="") { unset($params[$index]); continue; }
    $params[$index]="$param=$value";
  }
  $values=array(
    "script"=>basename($_SERVER["PHP_SELF"],".php"),
    "action"=>$action,
    "extra"=>implode(",",$params),
    "dateline"=>phpctime(),
    "ipaddress"=>getClientAddress());
  $database->addLine("adminlog",$values);
}

function adminActions($actions)
{
  if(isAdministrator()) return;
  $actions=explodeSmart(",",$actions);
  $action=acceptStringParameter("action");
  if(substr($action,0,2)=="do") $action=substr($action,2);
  if(in_array($action,$actions)) sendParameter("action","");
}

function processGlobalCache()
{
  global $database, $settings;
  if(!$database->isTablePresent("settings")) return;
  eval($database->getField("settings","value","groupid=0 AND name='globalCache'"));
}

function updateGlobalCache()
{
  global $database;
  $settings=$database->getOrderedLines("settings","groupid,id","groupid!=0");
  $cache="\$settings=array();\r\n";
  foreach($settings as $setting) {
    $name=quoteText($setting["name"]);
    $value=quoteText($setting["value"]);
    if(isTrueInteger($setting["value"])) $value=$setting["value"];
    $cache.="\$settings[$name]=$value;\r\n";
  }
  $database->modifyField("settings","value",$cache,"groupid=0 AND name='globalCache'");
  $messages=$database->getOrderedLines("messages","id","special=0");
  $cache=$database->getLine("messages","name='globalCache' AND special=1");
  foreach($cache as $field=>$value) if(preg_match("{^content\w+\$}",$field))
    $cache[$field]=""; else unset($cache[$field]);
  foreach($messages as $message) foreach($message as $field=>$value) {
    if(!isset($cache[$field])) continue;
    $name=quoteText($message["name"]);
    $value=quoteText($value);
    $cache[$field].="\$language[$name]=$value;\r\n";
  }
  $database->modifyLine("messages",$cache,"name='globalCache' AND special=1");
  processGlobalCache();
}

function getAdminStyle()
{
  global $database;
  $style=$database->getLine("styles","foradmin=1");
  if(!$style) makeAdminError("admin_error_noadminstyle");
  return $style;
}

function getTablePagePortion($table, $order, $conditions, $size, &$page, &$total, $all=true)
{
  global $database;
  if($all && acceptStringParameter("page")=="all") {
    $page=$total=false;
    if($order!="")
      return $database->getOrderedLines($table,$order,$conditions);
      else return $database->getLines($table,$conditions);
  }
  $count=$database->getLinesCount($table,$conditions);
  $total=max(ceil($count/$size),1);
  $page=acceptIntParameter("page",1,$total);
  $offset=($page-1)*$size;
  if($order!="")
    return $database->getOrderedLinesRange($table,$order,$offset,$size,$conditions);
    else return $database->getLinesRange($table,$offset,$size,$conditions);
}

function prepareTablePager($script, $action, $append="", $remember=true)
{
  $navigate=$action==acceptStringParameter("action");
  $cookieName="adminpager_{$script}_$action$append";
  $cookieValue=acceptStringParameter($cookieName);
  $page=$navigate?acceptStringParameter("page"):$cookieValue;
  if(!$page && $remember && $cookieValue!="all") $page=$cookieValue;
  if($page!="all") $page=$page?(int)$page:"";
  sendParameter("page",$page);
  phpcsetcookie($cookieName,$page);
}

/******************************************************************************/

function installFolder($folder)
{
  global $fileSystem;
  $path="";
  $folder=rtrim($folder,"/");
  if(char($folder,0)=="/") { $path="/"; $folder=substr($folder,1); }
  $folders=explodeSmart("/",$folder);
  if(!count($folders)) return;
  $success=true;
  foreach($folders as $folderpart) {
    $file=$path.$folderpart;
    if(!$fileSystem->isFileExists($file)) $fileSystem->createFolder($file);
    if(!$fileSystem->isFileExists($file)) { $success=false; break; }
    $path.=$folderpart."/";
  }
  $report="admin_installfolder_".($success?"success":"failure");
  makeNotification($report,$folder);
}

function installOptions($title, $settings, $lang=true)
{
  global $language, $database;
  if($lang) $title=ifset($language[$title],"");
  $displayorder=$database->getMaxField("settinggroups","displayorder")+1;
  $values=compact("title","displayorder");
  $database->addLineStrict("settinggroups",$values);
  $groupid=$database->getCounterValue();
  foreach($settings as $setting) {
    if(!isset($setting["groupid"])) $setting["groupid"]=$groupid;
    if(isset($setting["title"]) && $lang)
      $setting["title"]=ifset($language[$setting["title"]],"");
    if(isset($setting["description"]) && $lang)
      $setting["description"]=ifset($language[$setting["description"]],"");
    if(!isset($setting["kind"])) $setting["kind"]="input";
    if(!isset($setting["visible"])) $setting["visible"]=1;
    $database->addLineStrict("settings",$setting);
  }
  updateGlobalCache();
  makeNotification("admin_installoptions");
}

function installRelations($master, $slave, $operation, $code)
{
  global $database;
  $values=compact("master","slave","operation","code");
  $database->addLineStrict("relations",$values,true);
  makeNotification("admin_installrelations");
}

function installPage($name, $template, $bundles, $title, $visible=1, $parentid=-1, $alias="", $params="", $strict=true)
{
  global $language, $database, $optimizer;
  $locale=$language["locale"];
  $fields=$database->getFieldsList("pages");
  if($parentid==-1)
    $parentid=(int)$database->getField("pages","id","name='general'");
  $values=compact("parentid","name","alias","template","bundles","params","visible");
  if(in_array("title$locale",$fields))
    $values["title$locale"]=ifset($language[$title],"");
  $swapname="{$name}_save";
  if(!$database->addLine("pages",$values)) do {
    if(!$strict) return $database->getField("pages","id","name=".slashes($name));
    $modify=array("name"=>$swapname,"alias"=>"","visible"=>0);
    $success=$database->modifyLine("pages",$modify,"name=".slashes($name));
    if($success) { $database->addLineStrict("pages",$values); break; }
    $swapname=incrementIdentifier($swapname);
  } while(true);
  $result=$database->getCounterValue();
  $optimizer->clearCache();
  makeNotification("admin_installpage",$name);
  return $result;
}

function installTemplate($name, $content, $parent="", $localize=true, $strict=true)
{
  global $database, $optimizer;
  $style=getAdminStyle();
  $setid=$style["templatesetid"];
  $function="global \$language; return ifset(\$language[\$matches[1]],\"\");";
  $callback=create_function("\$matches",$function);
  $pattern="{<var:language:(\w+) nofilter>}";
  if($localize && PhpcLocale!="")
    $content=preg_replace_callback($pattern,$callback,$content);
  $content=trim($content)."\r\n";
  $values=compact("setid","name","parent","content");
  $swapname="{$name}_save";
  if(!$database->addLine("templates",$values)) do {
    if(!$strict) return;
    $success=$database->modifyField
      ("templates","name",$swapname,"setid=$setid AND name=".slashes($name));
    if($success) { $database->addLineStrict("templates",$values); break; }
    $swapname=incrementIdentifier($swapname);
  } while(true);
  $optimizer->clearCache();
  makeNotification("admin_installtemplate",$name);
}

function installBundle($name, $content, $plugins="", $strict=true)
{
  global $database, $optimizer;
  $style=getAdminStyle();
  $setid=$style["bundlesetid"];
  $content=trim($content)."\r\n";
  $values=compact("setid","name","plugins","content");
  $swapname="{$name}_save";
  if(!$database->addLine("bundles",$values)) do {
    if(!$strict) return;
    $success=$database->modifyField
      ("bundles","name",$swapname,"setid=$setid AND name=".slashes($name));
    if($success) { $database->addLineStrict("bundles",$values); break; }
    $swapname=incrementIdentifier($swapname);
  } while(true);
  $optimizer->clearCache();
  makeNotification("admin_installbundle",$name);
}

function installReplacements($replacements, $strict=true)
{
  global $database, $optimizer;
  $style=getAdminStyle();
  $setid=$style["replacementsetid"];
  foreach($replacements as $name=>$content) {
    $values=compact("setid","name","content");
    $database->addLine("replacements",$values,$strict);
  }
  $optimizer->clearCache();
  makeNotification("admin_installreplacements");
}

function installFormatting($formatting, $lang=true, $strict=true)
{
  global $language, $database, $optimizer;
  $useorder=$database->getMaxField("formatting","useorder");
  foreach($formatting as $entry) {
    if(isset($entry["title"]) && $lang)
      $entry["title"]=ifset($language[$entry["title"]],"");
    if(isset($entry["sample"]) && $lang)
      $entry["sample"]=ifset($language[$entry["sample"]],"");
    $entry["useorder"]=++$useorder;
    $database->addLine("formatting",$entry,$strict);
  }
  $optimizer->clearCache();
  makeNotification("admin_installformatting");
}

function installLinkStyles($linkstyles, $strict=true)
{
  global $database, $optimizer;
  $useorder=$database->getMaxField("linkstyles","useorder");
  foreach($linkstyles as $linkstyle) {
    $linkstyle["useorder"]=++$useorder;
    $database->addLine("linkstyles",$linkstyle,$strict);
  }
  $optimizer->clearCache();
  makeNotification("admin_installlinkstyles");
}

/******************************************************************************/

function updatePageBundlesList($name, $bundles, $remove=false)
{
  global $database, $optimizer;
  if(!is_array($bundles)) $bundles=array($bundles);
  $page=$database->getLine("pages","name=".slashes($name));
  if(!$page) return;
  $list=explodeSmart(",",$page["bundles"]);
  if($remove)
    $list=array_unique(array_diff($list,$bundles));
    else $list=array_unique(array_merge($list,$bundles));
  $list=implode(",",$list);
  $database->modifyField("pages","bundles",$list,"id=$page[id]");
  $optimizer->clearCache();
  makeNotification("admin_installalterpage",$name);
}

function updateTemplateTagParams($name, $tag, $params, $remove=false)
{
  global $database, $optimizer;
  $style=getAdminStyle();
  $setid=$style["templatesetid"];
  $template=$database->getLine("templates","setid=$setid AND name=".slashes($name));
  if(!$template) return;
  $tagStringBlock="\"(?:\\\\\\\\|\\\\\"|.)*?\"";
  $tagPattern="{<$tag\b((?:$tagStringBlock|.)*?)(/?)>}s";
  $tagParamsPattern="{(\w+)(?:=(\w+(?::\w+)*)|=($tagStringBlock))?}s";
  if(!preg_match($tagPattern,$template["content"],$matches)) return;
  $original=$matches[0];
  $trail=$matches[2];
  preg_match_all($tagParamsPattern,$matches[1],$matches);
  $parameters=array();
  for($index=0; $index<count($matches[0]); $index++)
    $parameters[$matches[1][$index]]=$matches[2][$index].$matches[3][$index];
  foreach($params as $param=>$value) if($value===true) $params[$param]="";
  if($remove)
    $parameters=array_diff_assoc($parameters,$params);
    else $parameters=array_merge($parameters,$params);
  foreach($parameters as $param=>$value)
    $tag.=" $param".($value!=""?"=$value":"");
  $content=str_replace($original,"<$tag$trail>",$template["content"]);
  $database->modifyField("templates","content",$content,"id=$template[id]");
  $optimizer->clearCache();
  makeNotification("admin_installaltertemplate",$name);
}

/******************************************************************************/

function makeMatrix($table, $add, $info)
{
  global $database;
  prepareSmartData($add,$info,$items,$conditions);
  $record=$add?array():$database->getLine($table,$conditions);
  $result=array();
  $partialUpdate=false;
  $ignore=$add?array("","none","key"):array("","none");
  foreach($items as $item) if(!in_array($item["type"],$ignore)) {
    $field=$item["field"];
    $title=$item["title"];
    $description=$item["description"];
    $lang=$item["lang"];
    if($add) switch($item["type"]) {
      case "separator": break;
      case "hidden": $record[$field]=""; break;
      case "input": $record[$field]=""; break;
      case "inputorder": $record[$field]=""; break;
      case "password": $record[$field]=""; break;
      case "datetime": $record[$field]=false; break;
      case "file": break;
      case "textarea": $record[$field]=""; break;
      case "editor": $record[$field]=""; break;
      case "htmleditor": $record[$field]=""; break;
      case "tpleditor": $record[$field]=""; break;
      case "phpeditor": $record[$field]=""; break;
      case "chooser": $record[$field]=false; break;
      case "selector": $record[$field]=""; break;
      case "yesno": $record[$field]=1; break;
      case "approval": $record[$field]=ApprovalValueAccept; break;
      default: if(class_exists("CustomControlsSupport"))
        CustomControlsSupport::setDefaultValue($item,$record);
    }
    if(!$add && $item["partial"]) {
      $result[$field."_oldvalue"]=makeMatrixItemHidden($record[$field]);
      $partialUpdate=true;
    }
    switch($item["type"]) {
      case "separator": $result[$field]=makeMatrixSeparator($title,$lang); break;
      case "key": $result[$field]=makeMatrixItemHidden($record[$field]); break;
      case "hidden": $result[$field]=makeMatrixItemHidden($record[$field]); break;
      case "input": $result[$field]=makeMatrixItemInput($title,$description,$record[$field],$lang); break;
      case "inputorder": $result[$field]=makeMatrixItemInputOrder($title,$description,$record[$field],$lang); break;
      case "password": $result[$field]=makeMatrixItemPassword($title,$description,$record[$field],$lang); break;
      case "datetime": $result[$field]=makeMatrixItemDatetime($title,$description,$record[$field],$lang); break;
      case "file": $result[$field]=makeMatrixItemFile($title,$description,$lang); break;
      case "textarea": $result[$field]=makeMatrixItemTextarea($title,$description,$record[$field],$lang); break;
      case "editor": $result[$field]=makeMatrixItemEditor($title,$description,$record[$field],$lang); break;
      case "htmleditor": $result[$field]=makeMatrixItemHTMLEditor($title,$description,$record[$field],$lang); break;
      case "tpleditor": $result[$field]=makeMatrixItemTPLEditor($title,$description,$record[$field],$lang); break;
      case "phpeditor": $result[$field]=makeMatrixItemPHPEditor($title,$description,$record[$field],$lang); break;
      case "chooser": $result[$field]=makeMatrixItemChooser($title,$description,$record[$field],array(),$lang); break;
      case "selector": $result[$field]=makeMatrixItemSelector($title,$description,array(),explodeSmart(",",$record[$field]),$lang); break;
      case "yesno": $result[$field]=makeMatrixItemYesNo($title,$description,$record[$field],$lang); break;
      case "approval": $result[$field]=makeMatrixItemApproval($title,$description,$record[$field],$lang); break;
      default: if(class_exists("CustomControlsSupport"))
        $result[$field]=CustomControlsSupport::makeMatrixItem($item,$record);
    }
  }
  if($partialUpdate) $result["phpcpartialupdate"]=makeMatrixItemHidden(1);
  return $result;
}

function makeMatrixSeparator($title, $lang=true)
{
  $type="separator";
  return compact("type","title","lang");
}

function makeMatrixItemHidden($value="")
{
  $type="hidden";
  return compact("type","value");
}

function makeMatrixItemInput($title, $description, $value="", $lang=true)
{
  $type="input";
  return compact("type","title","description","value","lang");
}

function makeMatrixItemInputOrder($title, $description, $value="", $lang=true)
{
  $type="inputorder";
  return compact("type","title","description","value","lang");
}

function makeMatrixItemPassword($title, $description, $value="", $lang=true)
{
  $type="password";
  return compact("type","title","description","value","lang");
}

function makeMatrixItemDatetime($title, $description, $value=false, $lang=true)
{
  $type="datetime";
  return compact("type","title","description","value","lang");
}

function makeMatrixItemFile($title, $description, $lang=true)
{
  $type="file";
  return compact("type","title","description","lang");
}

function makeMatrixItemTextarea($title, $description, $value="", $lang=true)
{
  $type="textarea";
  return compact("type","title","description","value","lang");
}

function makeMatrixItemEditor($title, $description, $value="", $lang=true)
{
  $type="editor";
  return compact("type","title","description","value","lang");
}

function makeMatrixItemHTMLEditor($title, $description, $value="", $lang=true)
{
  $type="htmleditor";
  return compact("type","title","description","value","lang");
}

function makeMatrixItemTPLEditor($title, $description, $value="", $lang=true)
{
  $type="tpleditor";
  return compact("type","title","description","value","lang");
}

function makeMatrixItemPHPEditor($title, $description, $value="", $lang=true)
{
  $type="phpeditor";
  return compact("type","title","description","value","lang");
}

function makeMatrixItemChooser($title, $description, $value=false, $options=array(), $lang=true)
{
  $type="chooser";
  return compact("type","title","description","value","options","lang");
}

function makeMatrixItemSelector($title, $description, $options=array(), $selected=array(), $lang=true)
{
  $type="selector";
  return compact("type","title","description","options","selected","lang");
}

function makeMatrixItemYesNo($title, $description, $value=1, $lang=true)
{
  $type="yesno";
  return compact("type","title","description","value","lang");
}

function makeMatrixItemApproval($title, $description, $value=ApprovalValueAccept, $lang=true)
{
  $type="approval";
  return compact("type","title","description","value","lang");
}

/******************************************************************************/

function localizeSmartInfo($table, $info, $localizedFields)
{
  global $language;
  $locales=getTableLocales($table,$localizedFields);
  $insert=array();
  foreach($localizedFields as $field=>$data) $insert[$field]="";
  foreach($locales as $locale) {
    $name=$language["locales"][$locale];
    foreach($localizedFields as $field=>$data) {
      if(!isset($data["title"])) $data["title"]="";
      if(!isset($data["description"])) $data["description"]="";
      $title=format(ifset($language[$data["title"]],""),$name);
      $title=str_replace(",","&#44;",$title);
      $title=str_replace(":","&#58;",$title);
      $description=format(ifset($language[$data["description"]],""),$name);
      $description=str_replace(",","&#44;",$description);
      $description=str_replace(":","&#58;",$description);
      $insert[$field].=",$field$locale:$data[type]:$title:$description:0";
    }
  }
  return format($info,array_values($insert));
}

function prepareSmartData($add, $info, &$items, &$conditions)
{
  $items=explodeSmart(",",$info);
  foreach($items as $index=>$item) {
    $split=explode(":",$item);
    $partial=count($split)>2 && $split[1]=="key";
    if($partial) array_splice($split,1,1);
    $items[$index]=array(
      "field"=>$split[0],
      "type"=>ifset($split[1],"none"),
      "title"=>ifset($split[2],""),
      "description"=>ifset($split[3],""),
      "lang"=>(boolean)ifset($split[4],true),
      "partial"=>$partial);
  }
  $conditions=array();
  if(!$add) {
    $partialUpdate=acceptIntParameter("phpcpartialupdate",0,1);
    foreach($items as $item) if($item["type"]=="key" || $item["partial"]) {
      $field=$item["field"];
      if($item["partial"] && $partialUpdate) $field.="_oldvalue";
      $value=acceptStringParameter($field);
      $conditions[]="$item[field]=".slashes($value);
    }
  }
  $conditions=implode(" AND ",$conditions);
}

function makeSmartForm($title, $script, $action, $matrix)
{
  makeForm("header",$title,$script,$action);
  foreach($matrix as $field=>$item) {
    if(!isset($item["type"]) || $item["type"]=="") continue;
    $title=ifset($item["title"]);
    $description=ifset($item["description"]);
    $value=ifset($item["value"]);
    $lang=ifset($item["lang"]);
    switch($item["type"]) {
      case "separator": makeForm("separator",$title,"","",$lang); break;
      case "hidden": makeFormHidden($field,$value); break;
      case "input": makeFormInput($title,$description,$field,$value,$lang); break;
      case "inputorder": makeFormInputOrder($title,$description,$field,$value,$lang); break;
      case "password": makeFormPassword($title,$description,$field,$value,$lang); break;
      case "datetime": makeFormDatetime($title,$description,$field,$value,$lang); break;
      case "file": makeFormFile($title,$description,$field,$lang); break;
      case "textarea": makeFormTextarea($title,$description,$field,$value,$lang); break;
      case "editor": makeFormEditor($title,$description,$field,$value,$lang); break;
      case "htmleditor": makeFormHTMLEditor($title,$description,$field,$value,$lang); break;
      case "tpleditor": makeFormTPLEditor($title,$description,$field,$value,$lang); break;
      case "phpeditor": makeFormPHPEditor($title,$description,$field,$value,$lang); break;
      case "chooser": makeFormChooser($title,$description,$field,$value,$item["options"],$lang); break;
      case "selector": makeFormSelector($title,$description,"{$field}[]",$item["options"],$item["selected"],$lang); break;
      case "yesno": makeFormYesNo($title,$description,$field,$value,$lang); break;
      case "approval": makeFormApproval($title,$description,$field,$value,$lang); break;
      default: if(class_exists("CustomControlsSupport"))
        CustomControlsSupport::makeFormItem($field,$item);
    }
  }
  makeForm("footer");
}

function acceptSmartForm($add, $info, &$conditions)
{
  prepareSmartData($add,$info,$items,$conditions);
  $values=array();
  $ignore=array("","separator","none","key","file");
  foreach($items as $item) if(!in_array($item["type"],$ignore)) {
    $field=$item["field"];
    switch($item["type"]) {
      case "hidden": $values[$field]=acceptStringParameter($field); break;
      case "input": $values[$field]=acceptStringParameter($field); break;
      case "inputorder": $values[$field]=acceptIntParameter($field); break;
      case "password": $values[$field]=acceptStringParameter($field); break;
      case "datetime": $values[$field]=datetime2timestamp(acceptStringParameter($field)); break;
      case "textarea": $values[$field]=acceptStringParameter($field); break;
      case "editor": $values[$field]=acceptStringParameter($field,false,false); break;
      case "htmleditor": $values[$field]=acceptStringParameter($field,false,false); break;
      case "tpleditor": $values[$field]=acceptStringParameter($field,false,false); break;
      case "phpeditor": $values[$field]=acceptStringParameter($field,false,false); break;
      case "chooser": $values[$field]=acceptStringParameter($field); break;
      case "selector": $values[$field]=implode(",",acceptArrayParameter($field)); break;
      case "yesno": $values[$field]=acceptIntParameter($field,0,1); break;
      case "approval": $values[$field]=acceptIntParameter($field,0,2); break;
      default: if(class_exists("CustomControlsSupport"))
        CustomControlsSupport::acceptFormValue($item,$values);
    }
  }
  return $values;
}

function makeSmartUpdate($table, $add, $values, $conditions)
{
  global $database;
  if($add)
    return $database->addLine($table,$values);
    else return $database->modifyLine($table,$values,$conditions);
}

function processSmartUpdate($table, $add, $info)
{
  $values=acceptSmartForm($add,$info,$conditions);
  return makeSmartUpdate($table,$add,$values,$conditions);
}

/******************************************************************************/

function getTableLocales($table, $localizedFields)
{
  global $database;
  $result=array();
  $names=array_keys($localizedFields);
  $locales=explode(",",PhpcLocalesList);
  $fields=$database->getFieldsList($table);
  $offset=$localizedFields[$names[0]]["offset"];
  while($offset<count($fields)) {
    if(!preg_match("{^$names[0](\w+)\$}",$fields[$offset],$matches)) break;
    if(!in_array($matches[1],$locales)) break;
    $result[]=$matches[1];
    $offset++;
  }
  $starting=0;
  $success=true;
  foreach($localizedFields as $field=>$info) {
    $offset=$info["offset"]+$starting*count($result);
    foreach($result as $locale) if($offset>=count($fields) ||
      $fields[$offset++]!=$field.$locale) $success=false;
    $starting++;
  }
  if(!$success) makeAdminError("admin_error_structure",$table);
  return $result;
}

function getTableMatchingLocale($table, $localizedFields)
{
  global $language;
  $locales=getTableLocales($table,$localizedFields);
  if(!count($locales)) makeAdminError("admin_error_nolocales");
  $locale=$language["locale"];
  return in_array($locale,$locales)?$locale:$locales[0];
}

function makeTableLocalesList($table, $localizedFields, $addlink, $removelink)
{
  global $language;
  $locales=getTableLocales($table,$localizedFields);
  $columns=array(
    array("title"=>"admin_modifylocales_locale","width"=>"50%"),
    "admin_modifylocales_options");
  makeTable("header",$columns);
  foreach($locales as $locale) {
    makeTableCellSimple($language["locales"][$locale]);
    $links=array("admin_modifylocales_remove"=>format($removelink,$locale));
    makeTableCellLinks($links);
  }
  makeTable("footer");
  makeBreak();
  makeLinks(array("admin_modifylocales_add"=>$addlink));
}

function makeTableLocalesAdd($table, $localizedFields, $script, $action, $echo=true)
{
  global $language, $database;
  $finish=substr($action,0,2)=="do";
  if(!$finish) {
    $current=getTableLocales($table,$localizedFields);
    $available=array_diff(explode(",",PhpcLocalesList),$current);
    $locales=array();
    $positions=array($language["admin_addlocale_first"]);
    $methods=array(""=>$language["admin_addlocale_empty"]);
    foreach($available as $locale)
      $locales[$locale]=$language["locales"][$locale];
    foreach($current as $locale) {
      $name=$language["locales"][$locale];
      $positions[]=format($language["admin_addlocale_after"],$name);
      $methods[$locale]=format($language["admin_addlocale_copy"],$name);
    }
    if(count($available)) {
      makeForm("header","admin_addlocale_form",$script,"do$action");
      makeFormChooser("admin_addlocale_locale","","locale",false,$locales);
      makeFormChooser("admin_addlocale_position","","position",count($positions)-1,$positions);
      makeFormChooser("admin_addlocale_method","","method","",$methods);
      makeForm("footer");
    }
    else makeError("admin_error_nofreelocales");
  }
  else {
    $locales=explode(",",PhpcLocalesList);
    $current=getTableLocales($table,$localizedFields);
    $locale=(int)array_search(acceptStringParameter("locale"),$locales);
    $position=acceptIntParameter("position",0,count($current));
    $method=array_search(acceptStringParameter("method"),$locales);
    $starting=0;
    foreach($localizedFields as $field=>$info) {
      $name=$field.$locales[$locale];
      $type=$database->getColumnType($info["type"]);
      $offset=$info["offset"]+$starting*(count($current)+1)+$position;
      $database->addColumn($table,$name,$type,0,$offset);
      if($method!==false) {
        $oldname=$field.$locales[$method];
        $database->customQuery("UPDATE $table SET $name=$oldname");
      }
      $starting++;
    }
    $database->optimizeTable($table);
    if($echo) makeNotification("admin_addlocale_success");
    if($echo) makeBreak();
  }
  return $finish;
}

function makeTableLocalesRemove($table, $localizedFields, $script, $action, $echo=true)
{
  global $database;
  $finish=substr($action,0,2)=="do";
  if(!$finish) {
    $locale=acceptStringParameter("locale");
    makePromptForm("header","admin_removelocale_prompt",$script,"do$action");
    makeFormHidden("locale",$locale);
    makePromptForm("footer");
  }
  else {
    $locale=acceptStringParameter("locale");
    foreach($localizedFields as $field=>$info)
      $database->deleteColumn($table,$field.$locale);
    if($echo) makeNotification("admin_removelocale_success");
    if($echo) makeBreak();
  }
  return $finish;
}

/******************************************************************************/

function getNestedSetObject()
{
  $props=getNestedSetProperties();
  $autoOrder=ifset($props["autoOrder"]);
  $enablePaths=ifset($props["enablePaths"]);
  $nestedSet=new NestedSet($props["table"],$autoOrder,$enablePaths);
  return $nestedSet;
}

function getNestedSetTree()
{
  $nestedSet=getNestedSetObject();
  return $nestedSet->getTree();
}

function recalculateNestedSetTree()
{
  $nestedSet=getNestedSetObject();
  $nestedSet->recalculate();
}

function createNestedSetParentOptions($root=false, $denyid=0)
{
  global $language, $database;
  $props=getNestedSetProperties();
  $deny=$database->getLine($props["table"],"id=$denyid");
  $conditions=$deny?"itemleft<$deny[itemleft] OR itemright>$deny[itemright]":"";
  $nodes=$database->getOrderedLines($props["table"],"itemleft",$conditions);
  $result=$root?array($language[$props["noParentText"]]):array();
  foreach($nodes as $node) {
    $prefix=str_repeat("--",max($node["itemlevel"],1)-1);
    $result[$node["id"]]=trim("$prefix $node[title]");
  }
  return $result;
}

function createNestedSetOrderOptions($id)
{
  global $language, $database;
  $props=getNestedSetProperties();
  $current=$database->getLine($props["table"],"id=$id");
  if(!$current) return array($language[$props["orderLastText"]]);
  $result=array(1=>$language[$props["orderFirstText"]]);
  $nodes=$database->getOrderedLines($props["table"],"itemorder","parentid=$current[parentid]");
  foreach($nodes as $node) if($node["id"]!=$id)
    $result[]=format($language[$props["orderAfterText"]],$node["title"]);
  $result[count($result)]=$language[$props["orderLastText"]];
  return $result;
}

function checkNestedSetOrder($id, $oldNode)
{
  global $database;
  $props=getNestedSetProperties();
  $newNode=$database->getLine($props["table"],"id=$id");
  if(!$newNode) return;
  $simpleEdit=$oldNode && $oldNode["parentid"]==$newNode["parentid"];
  if($simpleEdit) {
    $oldOrder=$oldNode["itemorder"];
    $newOrder=$newNode["itemorder"];
    if($newOrder!=$oldOrder) {
      $minOrder=min($oldOrder,$newOrder);
      $maxOrder=max($oldOrder,$newOrder);
      $conditions="parentid=$newNode[parentid] AND itemorder>=$minOrder AND itemorder<=$maxOrder";
      $delta=$newOrder<$oldOrder?"+1":"-1";
      $database->customQuery("UPDATE $props[table] SET itemorder=itemorder$delta WHERE $conditions");
      $database->modifyField($props["table"],"itemorder",$newOrder,"id=$id");
    }
  }
  else {
    $order=$database->getMaxField($props["table"],"itemorder","parentid=$newNode[parentid]")+1;
    $database->modifyField($props["table"],"itemorder",$order,"id=$id");
  }
}

function checkNestedSetVisibility($id, $oldNode)
{
  global $database;
  $props=getNestedSetProperties();
  $newNode=$database->getLine($props["table"],"id=$id");
  if(!$newNode) return;
  $parent=$database->getLine($props["table"],"id=$newNode[parentid]");
  $parentHidden=($parent && !$parent["visible"]) || !$newNode["visible"];
  $makeVisible=$oldNode && !$oldNode["visible"] && $newNode["visible"];
  if($parentHidden) {
    $conditions="itemleft>=$newNode[itemleft] AND itemright<=$newNode[itemright]";
    $database->modifyLines($props["table"],array("visible"=>false),$conditions);
  }
  if($makeVisible) {
    $conditions="itemleft<$newNode[itemright] AND itemright>$newNode[itemleft]";
    $database->modifyLines($props["table"],array("visible"=>true),$conditions);
  }
}

?>
