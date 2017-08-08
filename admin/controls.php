<?php

// PHP Compiler by Antylevsky Aleksei (Next)

$controlState=array();

/*************************** Headers Make Functions ***************************/

function makeHeaders()
{
  global $language;
  @header("Cache-Control: no-store, no-cache, must-revalidate");
  @header("Pragma: no-cache");
  @header("Content-Type: text/html; charset=$language[charset]");
}

/*************************** General Make Functions ***************************/

function makeInstallerPage($what)
{
  global $language;
  if($what=="header") {
    makeHeaders();
    echo "<html>\r\n";
    echo "<head>\r\n";
    echo "<title>$language[admin_installer_title]</title>\r\n";
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".AdminStylesLocation."\">\r\n";
    makeTransparencyPatch();
    echo "</head>\r\n";
    echo "<body>\r\n";
    echo "$language[admin_installer_header]<br><br>\r\n";
  }
  if($what=="footer") {
    echo "</body>\r\n";
    echo "</html>\r\n";
    halt();
  }
}

function makeAdminPage($what, $bodyid="")
{
  global $language;
  if($what=="header") {
    makeHeaders();
    if($bodyid!="") $bodyid=" id=\"$bodyid\"";
    echo "<html>\r\n";
    echo "<head>\r\n";
    echo "<title>$language[admin_title]</title>\r\n";
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".AdminStylesLocation."\">\r\n";
    makeTransparencyPatch();
    echo "</head>\r\n";
    echo "<body$bodyid>\r\n";
  }
  if($what=="footer") {
    echo "</body>\r\n";
    echo "</html>\r\n";
    halt();
  }
}

function makeAdminAuthorization()
{
  global $language;
  $attempt=acceptStringParameter(PhpcPasswordParam)!="";
  if($attempt) sleep(AdminAuthorizationDelay);
  echo "<br><table id=\"auth\" align=\"center\" cellpadding=\"4\" cellspacing=\"0\">\r\n";
  echo "<form action=\"$_SERVER[REQUEST_URI]\" target=\"_self\" method=\"post\">\r\n";
  echo "<tr><td>\r\n";
  echo "&nbsp;$language[admin_auth_prompt]&nbsp;<br>\r\n";
  echo "<input id=\"passwd\" type=\"password\" name=\"".PhpcPasswordParam."\"><br>\r\n";
  echo "</td></tr>\r\n";
  echo "<tr><td align=\"center\">\r\n";
  echo "<input class=\"button\" type=\"submit\" value=\"$language[admin_auth_submit]\"><br>\r\n";
  echo "</td></tr>\r\n";
  echo "</form>\r\n";
  echo "</table>\r\n";
  echo "<script type=\"text/javascript\">document.all.passwd.focus();</script>\r\n";
}

function makeAdminFrames()
{
  global $language;
  makeHeaders();
  echo "<html>\r\n";
  echo "<head>\r\n";
  echo "<title>$language[admin_title]</title>\r\n";
  echo "</head>\r\n";
  echo "<frameset cols=\"".AdminFramesMenuWidth.",*\" framespacing=\"0\" border=\"0\" frameborder=\"0\">\r\n";
  echo "<frame name=\"menu\" src=\"index.php?action=menu\" frameborder=\"0\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"auto\" noresize>\r\n";
  echo "<frameset rows=\"".AdminFramesHeaderHeight.",*\" framespacing=\"0\" border=\"0\" frameborder=\"0\">\r\n";
  echo "<frame name=\"head\" src=\"index.php?action=head\" frameborder=\"0\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"no\" noresize>\r\n";
  echo "<frame name=\"main\" src=\"index.php?action=home\" frameborder=\"0\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"auto\" noresize>\r\n";
  echo "</frameset>\r\n";
  echo "</frameset>\r\n";
  echo "</html>\r\n";
  halt();
}

function makeAdminHeadline()
{
  global $language;
  echo "<table width=\"100%\" height=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\r\n";
  echo "<tr><td>&nbsp;<a href=\"/\" target=\"_blank\">$language[admin_sitehome_admin]</a>&nbsp;</td>\r\n";
  echo "<td align=\"right\">&nbsp;<a href=\"/?".PhpcPasswordParam."=clear\" target=\"_blank\">$language[admin_sitehome_user]</a>&nbsp;</td></tr>\r\n";
  echo "</table>\r\n";
}

function makeTransparencyPatch()
{

}

/************************** Main Menu Make Functions **************************/

function makeMenu($what)
{
  global $language;
  if($what=="header") {
    echo "<a href=\"index.php?action=home\" target=\"main\"><div align=\"center\"><img border=\"0\" src=\"".AdminMenuLogoLocation."\"></a></div>\r\n";
    echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"5\">\r\n";
  }
  if($what=="separator") {
    echo "<tr><td></td></tr>\r\n";
  }
  if($what=="footer") {
    echo "<tr><td><hr></td></tr>\r\n";
    echo "</table>\r\n";
  }
}

function makeMenuGroup($what, $title="", $lang=true)
{
  global $language, $controlState;
  if($what=="header") {
    $title=$lang?$language[$title]:formatAdminText($title);
    echo "<tr><td>\r\n";
    echo "<table id=\"menugroup\" width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\r\n";
    echo "<tr><td>&nbsp;$title&nbsp;</td></tr>\r\n";
    echo "</table>\r\n";
  }
  if($what=="footer") {
    echo "</td></tr>\r\n";
    echo "<tr><td></td></tr>\r\n";
    unset($controlState["menuitem"]);
    unset($controlState["menubreak"]);
  }
}

function makeMenuItem($title, $link, $break=false, $lang=true)
{
  global $language, $controlState;
  $title=$lang?$language[$title]:formatAdminText($title);
  $menubreak=isset($controlState["menubreak"])?"<br>":"";
  unset($controlState["menubreak"]);
  if(isset($controlState["menuitem"])) echo "&nbsp;<br>$menubreak\r\n";
  $controlState["menuitem"]=true;
  if($break) $controlState["menubreak"]=true;
  echo "<nobr>›› <a href=\"$link\" target=\"main\">$title</a></nobr>";
}

/*************************** Toolbar Make Functions ***************************/

function makeToolbar($what, $title="", $lang=true)
{
  global $language, $controlState;
  if($what=="header") {
    echo "<table id=\"toolbar\" width=\"85%\" align=\"center\" cellpadding=\"4\" cellspacing=\"0\">\r\n";
    $controlState["toolbar"]=0;
  }
  if($what=="header" || $what=="separator") {
    $title=$lang?$language[$title]:formatAdminText($title);
    echo "<tr id=\"header\"><td colspan=\"2\">&nbsp;$title&nbsp;</td></tr>\r\n";
  }
  if($what=="footer") {
    echo "</table>\r\n";
    unset($controlState["toolbar"]);
  }
}

function makeToolbarItem($what, $title="", $description="", $lang=true)
{
  global $language, $controlState;
  if($what=="header") {
    $title=$lang?$language[$title]:formatAdminText($title);
    $description=$lang?$language[$description]:formatAdminText($description);
    $id=(++$controlState["toolbar"]%2)?"firstline":"secondline";
    echo "<tr id=\"$id\"><td><font class=\"title\">$title</font><br>$description</td>\r\n";
    echo "<td align=\"right\" nowrap>";
  }
  if($what=="footer") {
    echo "</td></tr>\r\n";
  }
}

function makeToolbarInput($title, $description, $url, $method, $name, $value="", $hidden=array(), $lang=true)
{
  global $language;
  makeToolbarItem("header",$title,$description,$lang);
  $value=htmlspecialchars($value);
  $target="";
  if(preg_match("{^(?!javascript:)(\w+):(.*)\$}",$url,$matches)) {
    $target=" target=\"$matches[1]\"";
    $url=$matches[2];
  }
  echo "<table align=\"right\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n";
  echo "<form action=\"$url\" method=\"$method\"$target>\r\n";
  echo "<tr><td nowrap>\r\n";
  foreach($hidden as $field=>$fieldvalue) {
    $fieldvalue=htmlspecialchars($fieldvalue);
    echo "<input type=\"hidden\" name=\"$field\" value=\"$fieldvalue\">\r\n";
  }
  echo "<input class=\"toolbar\" type=\"text\" name=\"$name\" value=\"$value\">\r\n";
  echo "<input class=\"button\" type=\"submit\" value=\"$language[admin_submit]\">";
  echo "</td></tr>\r\n";
  echo "</form>\r\n";
  echo "</table>";
  makeToolbarItem("footer");
}

function makeToolbarChooser($title, $description, $value=false, $options=array(), $local=true, $lang=true, $linklang=true)
{
  global $language;
  makeToolbarItem("header",$title,$description,$lang);
  $id=getIncrementalValue();
  $script=$local?
    "document.location.href=toolbar$id.options[toolbar$id.selectedIndex].value;":
    "window.open(toolbar$id.options[toolbar$id.selectedIndex].value,'','location,menubar,resizable,scrollbars,status,toolbar');";
  echo "<select class=\"toolbar\" id=\"toolbar$id\">\r\n";
  if(!isset($options[$value])) $value=false;
  foreach($options as $link=>$option) {
    if($value===false) $value=$link;
    $selected=$link==$value?" selected=\"selected\"":"";
    $option=$linklang?$language[$option]:formatAdminText($option);
    echo "<option value=\"$link\"$selected>$option</option>\r\n";
  }
  echo "</select>\r\n";
  echo "<input class=\"button\" type=\"button\" value=\"$language[admin_submit]\" onclick=\"$script\">";
  makeToolbarItem("footer");
}

/**************************** Form Make Functions *****************************/

function makeForm($what, $title="", $script="", $action="", $lang=true)
{
  global $language, $controlState;
  if($what=="header") {
    $controlState["form"]=0;
    $controlState["formname"]="form".getIncrementalValue();
    if(is_array($action)) {
      $controlState["formsubmit"]=$action[0];
      $controlState["formupdate"]=$action[1];
      $action=$action[0];
    }
    $anchor=preg_match("{#.*}",$script,$matches)?$matches[0]:"";
    $script=preg_replace("{#.*}","",$script).".php";
    echo "<table id=\"form\" width=\"95%\" align=\"center\" cellpadding=\"4\" cellspacing=\"0\">\r\n";
    ob_start();
    echo "<form name=\"$controlState[formname]\" action=\"$script$anchor\" method=\"post\">\r\n";
    echo "<input type=\"hidden\" name=\"action\" value=\"$action\">\r\n";
  }
  if($what=="header" || $what=="separator") {
    $title=$lang?$language[$title]:formatAdminText($title);
    echo "<tr id=\"header\"><td colspan=\"2\">&nbsp;$title&nbsp;</td></tr>\r\n";
  }
  if($what=="footer") {
    echo "<tr id=\"footer\" align=\"center\"><td colspan=\"2\" nowrap>\r\n";
    if(isset($controlState["formupdate"])) {
      $onclick="document.forms['$controlState[formname]'].elements['action'].value='$controlState[formsubmit]';return true";
      echo "<input class=\"button\" type=\"submit\" value=\"$language[admin_submit]\" onclick=\"$onclick\">&nbsp;\r\n";
      $onclick="document.forms['$controlState[formname]'].elements['action'].value='$controlState[formupdate]';return true";
      echo "<input class=\"button\" type=\"submit\" value=\"$language[admin_update]\" onclick=\"$onclick\">&nbsp;\r\n";
    }
    else echo "<input class=\"button\" type=\"submit\" value=\"$language[admin_submit]\">&nbsp;\r\n";
    echo "<input class=\"button\" type=\"reset\" value=\"$language[admin_reset]\"><br>\r\n";
    echo "</td></tr>\r\n";
    echo "</form>\r\n";
    if(isset($controlState["formfile"])) {
      $content=ob_get_clean();
      $content=preg_replace("{^<form}","<form enctype=\"multipart/form-data\"",$content);
      echo $content;
    }
    else ob_end_flush();
    echo "</table>\r\n";
    unset($controlState["form"]);
    unset($controlState["formname"]);
    unset($controlState["formsubmit"]);
    unset($controlState["formupdate"]);
    unset($controlState["formfile"]);
  }
}

function makeFormItem($what, $title="", $description="", $lang=true)
{
  global $language, $controlState;
  if($what=="header") {
    $title=$lang?$language[$title]:formatAdminText($title);
    $description=$lang?$language[$description]:formatAdminText($description);
    $id=(++$controlState["form"]%2)?"firstline":"secondline";
    echo "<tr id=\"$id\"><td><font class=\"title\">$title</font><br>$description</td>\r\n";
    echo "<td align=\"right\">";
  }
  if($what=="footer") {
    echo "</td></tr>\r\n";
  }
}

function makeFormItemWide($what, $title="", $description="", $lang=true)
{
  global $language, $controlState;
  if($what=="header") {
    $title=$lang?$language[$title]:formatAdminText($title);
    $description=$lang?$language[$description]:formatAdminText($description);
    $id=(++$controlState["form"]%2)?"firstline":"secondline";
    echo "<tr id=\"$id\"><td colspan=\"2\"><div style=\"margin-bottom:4px\">";
    echo "<font class=\"title\">$title</font><br>$description</div>\r\n";
  }
  if($what=="footer") {
    echo "</td></tr>\r\n";
  }
}

function makeFormHidden($name, $value="")
{
  $value=htmlspecialchars($value);
  echo "<input type=\"hidden\" name=\"$name\" value=\"$value\">\r\n";
}

function makeFormInput($title, $description, $name, $value="", $lang=true)
{
  makeFormItem("header",$title,$description,$lang);
  $value=htmlspecialchars($value);
  echo "<input type=\"text\" name=\"$name\" value=\"$value\">";
  makeFormItem("footer");
}

function makeFormInputOrder($title, $description, $name, $value="", $lang=true)
{
  makeFormItem("header",$title,$description,$lang);
  $value=htmlspecialchars($value);
  echo "<input class=\"order\" type=\"text\" name=\"$name\" value=\"$value\">";
  makeFormItem("footer");
}

function makeFormPassword($title, $description, $name, $value="", $lang=true)
{
  makeFormItem("header",$title,$description,$lang);
  $value=htmlspecialchars($value);
  echo "<input type=\"password\" name=\"$name\" value=\"$value\">";
  makeFormItem("footer");
}

function makeFormDatetime($title, $description, $name, $value=false, $lang=true)
{
  makeFormItem("header",$title,$description,$lang);
  if($value===false) $value=phpctime();
  $value=$value?htmlspecialchars(timestamp2datetime($value)):"";
  echo "<input type=\"text\" name=\"$name\" value=\"$value\">";
  makeFormItem("footer");
}

function makeFormFile($title, $description, $name, $lang=true)
{
  global $controlState;
  makeFormItem("header",$title,$description,$lang);
  echo "<input type=\"file\" name=\"$name\">";
  makeFormItem("footer");
  $controlState["formfile"]=true;
}

function makeFormTextarea($title, $description, $name, $value="", $lang=true)
{
  makeFormItem("header",$title,$description,$lang);
  $value=htmlspecialchars($value);
  echo "<textarea name=\"$name\">$value</textarea>";
  makeFormItem("footer");
}

function makeFormEditor($title, $description, $name, $value="", $lang=true)
{
  makeFormItemWide("header",$title,$description,$lang);
  $value=htmlspecialchars($value);
  echo "<textarea class=\"editor\" name=\"$name\" wrap=\"off\">$value</textarea>";
  makeFormItemWide("footer");
}

function makeFormExternalEditor($class, $title, $description, $name, $value="", $lang=true)
{
  $classQuoted=quoteText($class);
  eval("\$isSupported=class_exists($classQuoted) && $class::isSupported();");
  if($isSupported) {
    $params=array("width"=>ExternalEditorWidth,"height"=>ExternalEditorHeight);
    eval("\$code=$class::getEditorCode(\$name,\$value,\$params);");
    makeFormItemWide("header",$title,$description,$lang);
    echo $code;
    makeFormItemWide("footer");
  }
  else makeFormEditor($title,$description,$name,$value,$lang);
}

function makeFormHTMLEditor($title, $description, $name, $value="", $lang=true)
{
  makeFormExternalEditor("HTMLEditorSupport",$title,$description,$name,$value,$lang);
}

function makeFormTPLEditor($title, $description, $name, $value="", $lang=true)
{
  makeFormExternalEditor("TPLEditorSupport",$title,$description,$name,$value,$lang);
}

function makeFormPHPEditor($title, $description, $name, $value="", $lang=true)
{
  makeFormExternalEditor("PHPEditorSupport",$title,$description,$name,$value,$lang);
}

function makeFormChooser($title, $description, $name, $value=false, $options=array(), $lang=true, $filter=true)
{
  makeFormItem("header",$title,$description,$lang);
  echo "<select name=\"$name\">\r\n";
  if(!isset($options[$value])) $value=false;
  foreach($options as $key=>$option) {
    if($value===false) $value=$key;
    $selected=$key==$value?" selected=\"selected\"":"";
    $key=htmlspecialchars($key);
    if($filter) $option=formatAdminText($option);
    echo "<option value=\"$key\"$selected>$option</option>\r\n";
  }
  echo "</select>";
  makeFormItem("footer");
}

function makeFormSelector($title, $description, $name, $options=array(), $selected=array(), $lang=true, $filter=true)
{
  makeFormItem("header",$title,$description,$lang);
  echo "<select class=\"selector\" name=\"$name\" multiple=\"multiple\">\r\n";
  foreach($options as $key=>$option) {
    $select=in_array($key,$selected)?" selected=\"selected\"":"";
    $key=htmlspecialchars($key);
    if($filter) $option=formatAdminText($option);
    echo "<option value=\"$key\"$select>$option</option>\r\n";
  }
  echo "</select>";
  makeFormItem("footer");
}

function makeFormYesNo($title, $description, $name, $value=1, $lang=true)
{
  global $language;
  makeFormItem("header",$title,$description,$lang);
  $value=$value?1:0;
  $selected=array("","",$value=>" checked=\"checked\"");
  echo "<input class=\"radio\" type=\"radio\" name=\"$name\" value=\"1\"$selected[1]> $language[admin_yes]&nbsp;\r\n";
  echo "<input class=\"radio\" type=\"radio\" name=\"$name\" value=\"0\"$selected[0]> $language[admin_no]&nbsp;";
  makeFormItem("footer");
}

function makeFormApproval($title, $description, $name, $value=ApprovalValueAccept, $lang=true)
{
  global $language;
  makeFormItem("header",$title,$description,$lang);
  $value=(int)$value;
  if($value!=ApprovalValueReject && $value!=ApprovalValueDelete) $value=ApprovalValueAccept;
  $selected=array(
    ApprovalValueAccept=>"",
    ApprovalValueReject=>"",
    ApprovalValueDelete=>"",
    $value=>" checked=\"checked\"");
  echo "<input class=\"radio\" type=\"radio\" name=\"$name\" value=\"".ApprovalValueAccept."\"".$selected[ApprovalValueAccept]."> $language[admin_accept]&nbsp;\r\n";
  echo "<input class=\"radio\" type=\"radio\" name=\"$name\" value=\"".ApprovalValueReject."\"".$selected[ApprovalValueReject]."> $language[admin_reject]&nbsp;\r\n";
  echo "<input class=\"radio\" type=\"radio\" name=\"$name\" value=\"".ApprovalValueDelete."\"".$selected[ApprovalValueDelete]."> $language[admin_delete]&nbsp;";
  makeFormItem("footer");
}

/**************************** Table Make Functions ****************************/

function makeTableScripts()
{
  static $already=false;
  if($already) return; else $already=true;
  echo "<script type=\"text/javascript\">\r\n";
  echo "function processcheck(formname, itemname, state)\r\n";
  echo "{\r\n";
  echo "  var items=document.forms[formname].elements[itemname];\r\n";
  echo "  if(typeof(items)!=\"undefined\") {\r\n";
  echo "    if(typeof(items.length)!=\"undefined\")\r\n";
  echo "      for(index=0; index<items.length; index++) items[index].checked=state;\r\n";
  echo "      else items.checked=state;\r\n";
  echo "  }\r\n";
  echo "  checkboxtop=document.forms[formname].elements[formname+\"top\"];\r\n";
  echo "  checkboxbottom=document.forms[formname].elements[formname+\"bottom\"];\r\n";
  echo "  if(typeof(checkboxtop)!=\"undefined\") checkboxtop.checked=state;\r\n";
  echo "  if(typeof(checkboxbottom)!=\"undefined\") checkboxbottom.checked=state;\r\n";
  echo "  return true;\r\n";
  echo "}\r\n";
  echo "</script>\r\n";
}

function makeTable($what, $columns=array(), $script="", $action="", $lang=true)
{
  global $language, $controlState;
  if($what=="header" || $what=="headeraction") {
    makeTableScripts();
    echo "<table id=\"table\" width=\"95%\" align=\"center\" cellpadding=\"4\" cellspacing=\"1\">\r\n";
    $controlState["table"]["columns"]=0;
    $controlState["table"]["checkbox"]=array();
    $controlState["table"]["line"]=0;
    if($script!="") $controlState["table"]["form"]=true;
  }
  if(($what=="header" || $what=="headeraction") && isset($controlState["table"]["form"])) {
    $formname="form".getIncrementalValue();
    $anchor=preg_match("{#.*}",$script,$matches)?$matches[0]:"";
    $script=preg_replace("{#.*}","",$script).".php";
    echo "<form name=\"$formname\" action=\"$script$anchor\" method=\"post\">\r\n";
    if($action!="") echo "<input type=\"hidden\" name=\"action\" value=\"$action\">\r\n";
    $controlState["table"]["name"]=$formname;
  }
  if($what=="header" || $what=="headeraction") {
    echo "<tr id=\"header\">";
    if(!is_array($columns)) $columns=array($columns);
    $separator="";
    foreach($columns as $column) {
      if(!is_array($column)) $column=array("title"=>$column);
      $title=$column["title"];
      $colspan=ifset($column["colspan"],1);
      if(substr($title,0,6)=="table:") {
        $parts=explode(":",$title);
        switch($parts[1]) {
          case "checkbox":
            $formname=$controlState["table"]["name"];
            $controlState["table"]["checkbox"][$controlState["table"]["columns"]]=$parts[2];
            $content="<input id=\"{$formname}top\" class=\"checkbox\" type=\"checkbox\" onclick=\"processcheck('$formname','$parts[2]',this.checked)\">";
            break;
        }
      }
      else $content=$lang?$language[$title]:formatAdminText($title);
      if(isset($column["link"])) {
        $selected=isset($column["selected"]) && $column["selected"];
        $class=$selected?" class=\"selected\"":"";
        $content="<a$class href=\"$column[link]\">$content</a>";
      }
      $controlState["table"]["columns"]+=$colspan;
      $colspan=$colspan!=1?" colspan=\"$colspan\"":"";
      $width=isset($column["width"])?" width=\"$column[width]\"":"";
      $align=ifset($column["align"],"center");
      echo "$separator<td$colspan$width align=\"$align\" nowrap>$content</td>";
      $separator="\r\n";
    }
    echo "</tr>\r\n";
    $controlState["table"]["index"]=0;
  }
  if($what=="footer" && isset($controlState["table"]["form"])) {
    $colspan=$controlState["table"]["columns"];
    echo "<tr id=\"footer\" align=\"center\"><td colspan=\"$colspan\" nowrap>\r\n";
    echo "<input class=\"button\" type=\"submit\" value=\"$language[admin_submit]\">&nbsp;\r\n";
    echo "<input class=\"button\" type=\"reset\" value=\"$language[admin_reset]\"><br>\r\n";
    echo "</td></tr>\r\n";
    echo "</form>\r\n";
  }
  if($what=="footeraction") {
    $prompt=$columns;
    $actions=$script;
    $colspan=$controlState["table"]["columns"];
    $onchange=$controlState["table"]["name"].".submit()";
    $onchange=AdminTableActionAutostart?" onchange=\"$onchange\"":"";
    echo "<tr id=\"footer\" align=\"center\"><td colspan=\"$colspan\" nowrap>\r\n";
    echo "$prompt <select class=\"goselect\" name=\"action\"$onchange>\r\n";
    foreach($actions as $key=>$action) {
      $key=htmlspecialchars($key);
      echo "<option value=\"$key\">$action</option>\r\n";
    }
    echo "</select>\r\n";
    echo "<input class=\"gobutton\" type=\"submit\" value=\"$language[admin_go]\"><br>\r\n";
    echo "</td></tr>\r\n";
    echo "</form>\r\n";
  }
  if($what=="footer" || $what=="footeraction") {
    echo "</table>\r\n";
    unset($controlState["table"]);
  }
}

function makeTableAction($what, $columns=array(), $script="", $prompt="", $actions=array(), $lang=true)
{
  global $language, $controlState;
  if($what=="header") {
    makeTable("headeraction",$columns,$script,"",$lang);
    if($prompt=="")
      $prompt=$language["admin_action"];
      else $prompt=$lang?$language[$prompt]:formatAdminText($prompt);
    foreach($actions as $key=>$action)
      $actions[$key]=$lang?$language[$action]:formatAdminText($action);
    $controlState["table"]["prompt"]=$prompt;
    $controlState["table"]["actions"]=$actions;
  }
  if($what=="footer") {
    $prompt=$controlState["table"]["prompt"];
    $actions=$controlState["table"]["actions"];
    makeTable("footeraction",$prompt,$actions);
  }
}

function makeTableArrangement($what)
{
  global $controlState;
  if($what=="header") {
    $columns=$controlState["table"]["columns"];
    $width=floor(100/$columns);
    echo "<tr id=\"header\">";
    for($index=1; $index<$columns; $index++)
      echo "<td width=\"$width%\" height=\"0\" style=\"padding:0px\"></td>\r\n";
    echo "<td height=\"0\" style=\"padding:0px\"></td></tr>\r\n";
  }
  if($what=="footer") {
    while($controlState["table"]["index"]) makeTableCellSimple();
  }
}

function makeTableTotals($columns=array(), $firstnote=true)
{
  global $language, $controlState;
  if(!is_array($columns)) $columns=array($columns);
  $columns=array_values($columns);
  foreach($columns as $index=>$column)
    if(!is_array($column)) $columns[$index]=array("value"=>$column);
  if(count($columns)==1 && count($columns[0])==1) {
    $colspan=$controlState["table"]["columns"];
    while($colspan>1 && isset($controlState["table"]["checkbox"][$colspan-1])) $colspan--;
    $columns[0]["colspan"]=$colspan;
  }
  echo "<tr id=\"footer\">";
  $separator="";
  foreach($columns as $index=>$column) {
    if(!is_array($column)) $column=array("value"=>$column);
    $value=formatAdminText($column["value"]);
    if(!$index && $firstnote) {
      $value=$value==""?
        $language["admin_total"]:format($language["admin_totalvalue"],$value);
      $column["align"]="left";
    }
    $colspan=ifset($column["colspan"],1);
    $controlState["table"]["index"]+=$colspan;
    $colspan=$colspan!=1?" colspan=\"$colspan\"":"";
    $width=isset($column["width"])?" width=\"$column[width]\"":"";
    $align=ifset($column["align"],"right");
    echo "$separator<td$colspan$width align=\"$align\" nowrap>$value</td>";
    $separator="\r\n";
  }
  $rowsleft=0;
  for($index=$controlState["table"]["index"]; $index<$controlState["table"]["columns"]; $index++)
    if(isset($controlState["table"]["checkbox"][$index])) {
      $formname=$controlState["table"]["name"];
      $itemname=$controlState["table"]["checkbox"][$index];
      $content="<input id=\"{$formname}bottom\" class=\"checkbox\" type=\"checkbox\" onclick=\"processcheck('$formname','$itemname',this.checked)\">";
      $colspan=$rowsleft>1?" colspan=\"$rowsleft\"":"";
      if($rowsleft) { echo "$separator<td$colspan></td>"; $separator="\r\n"; }
      echo "$separator<td align=\"center\" nowrap>$content</td>";
      $rowsleft=0;
      $separator="\r\n";
    }
    else $rowsleft++;
  $colspan=$rowsleft>1?" colspan=\"$rowsleft\"":"";
  if($rowsleft) echo "$separator<td$colspan></td>";
  echo "</tr>\r\n";
  $controlState["table"]["index"]=0;
}

function makeTablePager($page, $total, $link, $all=true)
{
  global $language, $controlState;
  if($page===false || $total<=1) return;
  $fancy=FancyNavigationEnabled &&
    preg_match(FancyNavigationPattern,$_SERVER["HTTP_USER_AGENT"]);
  $symbols=$fancy?FancyNavigationSymbols1:FancyNavigationSymbols2;
  $symbols=explode(",",$symbols);
  $class=$fancy?"navigation1":"navigation2";
  $colspan=$controlState["table"]["columns"];
  echo "<tr id=\"footer\" align=\"center\"><td colspan=\"$colspan\">\r\n";
  echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n";
  echo "<tr align=\"center\">";
  if($page>1) {
    $firstLink=format($link,1);
    $prevLink=format($link,$page-1);
    echo "<td width=\"100\" align=\"left\" nowrap>&nbsp;<input class=\"$class\" type=\"button\" value=\"$symbols[0]\" title=\"$language[admin_pages_first]\" onclick=\"document.location.href='$firstLink'\">\r\n";
    echo "&nbsp;<input class=\"$class\" type=\"button\" value=\"$symbols[1]\" title=\"$language[admin_pages_previous]\" onclick=\"document.location.href='$prevLink'\"></td>\r\n";
  }
  else echo "<td width=\"100\"></td>\r\n";
  $pages=array();
  for($index=1; $index<=$total; $index++) if($index!=$page) {
    $pageLink=format($link,$index);
    $pages[]="<a href=\"$pageLink\">$index</a>";
  }
  else $pages[]="[$index]";
  $allpagesLink=format($link,"all");
  if($all) $pages[]="<a href=\"$allpagesLink\">$language[admin_pages_all]</a>";
  $pages=implode("\r\n",$pages);
  echo "<td>$language[admin_pages] $pages</td>";
  if($page<$total) {
    $nextLink=format($link,$page+1);
    $lastLink=format($link,$total);
    echo "\r\n<td width=\"100\" align=\"right\" nowrap><input class=\"$class\" type=\"button\" value=\"$symbols[2]\" title=\"$language[admin_pages_next]\" onclick=\"document.location.href='$nextLink'\">&nbsp;\r\n";
    echo "<input class=\"$class\" type=\"button\" value=\"$symbols[3]\" title=\"$language[admin_pages_last]\" onclick=\"document.location.href='$lastLink'\">&nbsp;</td>";
  }
  else echo "\r\n<td width=\"100\"></td>";
  echo "</tr>\r\n";
  echo "</table></td></tr>\r\n";
}

function makeTableCell($what, $style=array())
{
  global $controlState;
  if($what=="header") {
    if(!$controlState["table"]["index"]) {
      $id=(++$controlState["table"]["line"]%2)?"firstline":"secondline";
      echo "<tr id=\"$id\">";
    }
    $colspan=ifset($style["colspan"],1);
    $controlState["table"]["index"]+=$colspan;
    $colspan=$colspan>1?" colspan=\"$colspan\"":"";
    $align=ifset($style["align"],"left");
    $valign=ifset($style["valign"],"middle");
    $wrap=isset($style["wrap"])?"":" nowrap";
    echo "<td$colspan align=\"$align\" valign=\"$valign\"$wrap>";
  }
  if($what=="footer") {
    echo "</td>";
    if($controlState["table"]["index"]==$controlState["table"]["columns"]) {
      $controlState["table"]["index"]=0;
      echo "</tr>";
    }
    echo "\r\n";
  }
}

function makeTableCellExact($content="", $style=array("wrap"=>true))
{
  makeTableCell("header",$style);
  echo $content;
  makeTableCell("footer");
}

function makeTableCellSimple($title="", $style=array(), $lang=false)
{
  global $language;
  makeTableCell("header",$style);
  echo $lang?$language[$title]:formatAdminText($title);
  makeTableCell("footer");
}

function makeTableCellPattern($pattern)
{
  makeTableCell("header");
  $pattern=htmlspecialchars($pattern);
  echo "<font class=\"pattern\">$pattern</font>";
  makeTableCell("footer");
}

function makeTableCellYesNo($value)
{
  global $language;
  makeTableCell("header",array("align"=>"center"));
  echo $value?"<b>$language[admin_yes]</b>":$language["admin_no"];
  makeTableCell("footer");
}

function makeTableCellTitle($title, $link=false, $style=array(), $lang=false)
{
  makeTableCell("header",$style);
  makeLinksFormatted(array($title=>$link),"%s","title","title",false,$lang);
  makeTableCell("footer");
}

function makeTableCellLink($title, $link=false, $style=array(), $lang=false)
{
  makeTableCell("header",$style);
  makeLinksFormatted(array($title=>$link),"%s","","nolink",false,$lang);
  makeTableCell("footer");
}

function makeTableCellLinks($links=array(), $style=array("align"=>"center"), $linklang=true)
{
  makeTableCell("header",$style);
  makeLinksArray($links,$linklang);
  makeTableCell("footer");
}

function makeTableCellInput($name, $value="")
{
  makeTableCell("header");
  $value=htmlspecialchars($value);
  echo "<input class=\"cell\" type=\"text\" name=\"$name\" value=\"$value\">";
  makeTableCell("footer");
}

function makeTableCellInputOrder($name, $value="")
{
  makeTableCell("header");
  $value=htmlspecialchars($value);
  echo "<input class=\"order\" type=\"text\" name=\"$name\" value=\"$value\">";
  makeTableCell("footer");
}

function makeTableCellChooser($name, $options, $value=false, $filter=true)
{
  makeTableCell("header");
  echo "<select class=\"cell\" name=\"$name\">\r\n";
  if(!isset($options[$value])) $value=false;
  foreach($options as $key=>$option) {
    if($value===false) $value=$key;
    $selected=$key==$value?" selected=\"selected\"":"";
    $key=htmlspecialchars($key);
    if($filter) $option=formatAdminText($option);
    echo "<option value=\"$key\"$selected>$option</option>\r\n";
  }
  echo "</select>";
  makeTableCell("footer");
}

function makeTableCellCheckbox($name, $value=0)
{
  makeTableCell("header",array("align"=>"center"));
  $checked=$value?" checked=\"checked\"":"";
  echo "<input class=\"checkbox\" type=\"checkbox\" name=\"$name\" value=\"1\"$checked>";
  makeTableCell("footer");
}

function makeTableCellCheckboxArray($name, $value, $checked=false)
{
  makeTableCell("header",array("align"=>"center"));
  $value=htmlspecialchars($value);
  $checked=$checked?" checked=\"checked\"":"";
  echo "<input class=\"checkbox\" type=\"checkbox\" name=\"$name\" value=\"$value\"$checked>";
  makeTableCell("footer");
}

function makeTableCellImage($image, $title="", $style=array("align"=>"center"))
{
  makeTableCell("header",$style);
  if($title!="") $title=" title=\"".htmlspecialchars($title)."\"";
  echo "<img border=\"0\" src=\"$image\"$title>";
  makeTableCell("footer");
}

function makeTableCellImageSize($width, $height, $image, $title="", $style=array("align"=>"center"))
{
  makeTableCell("header",$style);
  if($title!="") $title=" title=\"".htmlspecialchars($title)."\"";
  echo "<img width=\"$width\" height=\"$height\" border=\"0\" src=\"$image\"$title>";
  makeTableCell("footer");
}

/**************************** Tree Make Functions *****************************/

function makeTree($what, $title="", $lang=true)
{
  global $language, $controlState;
  if($what=="header") {
    $title=$lang?$language[$title]:formatAdminText($title);
    echo "<table id=\"tree\" width=\"95%\" align=\"center\" cellpadding=\"4\" cellspacing=\"0\">\r\n";
    echo "<tr id=\"header\"><td>&nbsp;$title&nbsp;</td></tr>\r\n";
    $controlState["tree"]=0;
  }
  if($what=="footer") {
    echo "</table>\r\n";
    unset($controlState["tree"]);
  }
}

function makeTreeGroup($what)
{
  global $controlState;
  if($what=="header") {
    $id=(++$controlState["tree"]%2)?"firstline":"secondline";
    echo "<tr id=\"$id\"><td><font class=\"group\">";
  }
  if($what=="separator") {
    echo "</font>\r\n";
    echo "<ul>";
  }
  if($what=="footer") {
    echo "</ul></td></tr>\r\n";
  }
}

function makeTreeGroupSimple($what, $title="", $links=array(), $lang=false, $linklang=true)
{
  global $language;
  makeTreeGroup($what);
  if($what=="header") {
    if($title!="") $title=$lang?$language[$title]:formatAdminText($title);
    if($title!="") echo "$title ";
    makeLinksArray($links,$linklang);
    makeTreeGroup("separator");
  }
}

function makeTreeItem($what)
{
  if($what=="header") {
    echo "<li>";
  }
  if($what=="footer") {
    echo "</li>\r\n";
  }
}

function makeTreeItemSimple($title="", $links=array(), $lang=false, $linklang=true)
{
  global $language;
  makeTreeItem("header");
  if($title!="") $title=$lang?$language[$title]:formatAdminText($title);
  if($title!="") echo "$title ";
  makeLinksArray($links,$linklang);
  makeTreeItem("footer");
}

/************************ Complex Tree Make Functions *************************/

function makeComplexTreeScripts()
{
  static $already=false;
  if($already) return; else $already=true;
  $script=basename($_SERVER["PHP_SELF"]);
  $plusimage="$script?action=image&image=treeplus";
  $minusimage="$script?action=image&image=treeminus";
  echo "<style type=\"text/css\">\r\n";
  echo "td.complextreeplus { background-image:url($plusimage); }\r\n";
  echo "td.complextreeminus { background-image:url($minusimage); }\r\n";
  echo "div.complextreevisible { visibility:visible; display:block; }\r\n";
  echo "div.complextreehidden { visibility:hidden; display:none; }\r\n";
  echo "</style>\r\n";
  echo "<script type=\"text/javascript\">\r\n";
  echo "var complextreedata=new Array();\r\n";
  echo "function complextreeclick(id)\r\n";
  echo "{\r\n";
  echo "  var obj=document.all[\"complextreeswitch\"+id];\r\n";
  echo "  if(obj) if(obj.className==\"complextreeplus\")\r\n";
  echo "    obj.className=\"complextreeminus\";\r\n";
  echo "    else obj.className=\"complextreeplus\";\r\n";
  echo "  var obj=document.all[\"complextreeblock\"+id];\r\n";
  echo "  if(obj) if(obj.className==\"complextreevisible\")\r\n";
  echo "    obj.className=\"complextreehidden\";\r\n";
  echo "    else obj.className=\"complextreevisible\";\r\n";
  echo "}\r\n";
  echo "function complextreeaction(action, treeindex)\r\n";
  echo "{\r\n";
  echo "  if(!treeindex) treeindex=0;\r\n";
  echo "  ids=complextreedata[treeindex];\r\n";
  echo "  if(!ids[0] || !ids[1]) return;\r\n";
  echo "  for(id=ids[0]; id<=ids[1]; id++) {\r\n";
  echo "    var obj=document.all[\"complextreeswitch\"+id];\r\n";
  echo "    if(action==\"expand\" && obj.className==\"complextreeplus\") complextreeclick(id);\r\n";
  echo "    if(action==\"contract\" && obj.className==\"complextreeminus\") complextreeclick(id);\r\n";
  echo "  }\r\n";
  echo "}\r\n";
  echo "</script>\r\n";
}

function makeComplexTreeContent($nodes, $flags=array())
{
  global $controlState;
  static $id=0;
  $script=basename($_SERVER["PHP_SELF"]);
  $width=$controlState["complextreewidth"];
  $height=$controlState["complextreeheight"];
  foreach($nodes as $index=>$node) {
    $complex=count($node["items"]);
    if($complex) {
      $nodeid=++$id;
      $controlState["complextreerange"]+=array("min"=>$nodeid,"max"=>$nodeid);
      $controlState["complextreerange"]["min"]=
        min($controlState["complextreerange"]["min"],$nodeid);
      $controlState["complextreerange"]["max"]=
        max($controlState["complextreerange"]["max"],$nodeid);
    }
    if($index) echo "\r\n";
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n";
    echo "<tr>";
    foreach($flags as $flag) {
      $image=$flag?"$script?action=image&image=treeud":"$script?action=image";
      echo "<td><img width=\"$width\" height=\"$height\" border=\"0\" src=\"$image\"></td>\r\n";
    }
    $image="tree";
    if(count($flags) || $index) $image.="u";
    if($index<count($nodes)-1) $image.="d";
    if(count($flags) || count($nodes)>1 || $complex) $image.="r";
    $image=$image!="tree"?"$script?action=image&image=$image":"";
    $image=$image!=""?" background=\"$image\"":"";
    echo "<td$image>";
    if($complex) {
      $halfheight1=floor(($height-10)/2);
      $halfheight2=$height-$halfheight1-10;
      $class=$node["expand"]?"complextreeminus":"complextreeplus";
      echo "<img width=\"$width\" height=\"$halfheight1\" border=\"0\" src=\"$script?action=image\"><br>\r\n";
      echo "<table align=\"center\" width=\"10\" height=\"10\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n";
      echo "<tr><td id=\"complextreeswitch$nodeid\" class=\"$class\">\r\n";
      echo "<img style=\"cursor:pointer\" width=\"10\" height=\"10\" border=\"0\" src=\"$script?action=image\" onclick=\"complextreeclick($nodeid)\"></td></tr>\r\n";
      echo "</table>\r\n";
      echo "<img width=\"$width\" height=\"$halfheight2\" border=\"0\" src=\"$script?action=image\">";
    }
    else echo "<img width=\"$width\" height=\"$height\" border=\"0\" src=\"$script?action=image\">";
    echo "</td>\r\n";
    echo "<td><img width=\"$width\" height=\"$height\" border=\"0\" src=\"$node[icon]\"></td>\r\n";
    echo "<td style=\"padding-left:5px\" nowrap>$node[header]</td>";
    echo "</tr>\r\n";
    echo "</table>";
    if($complex) {
      $class=$node["expand"]?"complextreevisible":"complextreehidden";
      echo "\r\n<div id=\"complextreeblock$nodeid\" class=\"$class\">\r\n";
      $subflags=$flags;
      $subflags[]=$index<count($nodes)-1;
      makeComplexTreeContent($node["items"],$subflags);
      echo "</div>";
    }
  }
}

function makeComplexTreeSize($what, $width=0, $height=0, $title="", $lang=true)
{
  global $language, $controlState;
  if($what=="header") {
    makeComplexTreeScripts();
    $title=$lang?$language[$title]:formatAdminText($title);
    echo "<table id=\"tree\" width=\"95%\" align=\"center\" cellpadding=\"4\" cellspacing=\"0\">\r\n";
    echo "<tr id=\"header\"><td>&nbsp;$title&nbsp;</td></tr>\r\n";
    echo "<tr id=\"firstline\"><td>";
    $controlState["complextree"]=array();
    $controlState["complextreewidth"]=$width;
    $controlState["complextreeheight"]=$height;
    $controlState["complextreedepth"]=0;
    $controlState["complextreerange"]=array();
    if(!isset($controlState["complextreeindex"]))
      $controlState["complextreeindex"]=0;
  }
  if($what=="footer") {
    makeComplexTreeContent($controlState["complextree"]);
    $range=$controlState["complextreerange"]+array("min"=>0,"max"=>0);
    echo "</td></tr>\r\n";
    echo "</table>\r\n";
    echo "<script type=\"text/javascript\">";
    echo "complextreedata[$controlState[complextreeindex]]=[$range[min],$range[max]];</script>\r\n";
    unset($controlState["complextree"]);
    unset($controlState["complextreewidth"]);
    unset($controlState["complextreeheight"]);
    unset($controlState["complextreedepth"]);
    unset($controlState["complextreerange"]);
    $controlState["complextreeindex"]++;
  }
}

function makeComplexTree($what, $title="", $lang=true)
{
  $width=ComplexTreeDefaultWidth;
  $height=ComplexTreeDefaultHeight;
  makeComplexTreeSize($what,$width,$height,$title,$lang);
}

function makeComplexTreeNode($what, $icon="", $header="", $expand=false)
{
  global $controlState;
  if($what=="header") {
    $locator=&$controlState["complextree"];
    for($index=0; $index<$controlState["complextreedepth"]; $index++) {
      if($expand) $locator[count($locator)-1]["expand"]=true;
      $locator=&$locator[count($locator)-1]["items"];
    }
    $items=array();
    $locator[]=compact("icon","header","expand","items");
    $controlState["complextreedepth"]++;
  }
  if($what=="footer") {
    $controlState["complextreedepth"]--;
  }
}

function makeComplexTreeNodeSimple($what, $icon="", $title="", $links=array(), $expand=false, $lang=false, $linklang=true)
{
  global $language;
  if($what=="header") {
    ob_start();
    if($title!="") $title=$lang?$language[$title]:formatAdminText($title);
    if($title!="") echo "$title ";
    makeLinksArray($links,$linklang);
    $header=ob_get_clean();
  }
  else $header="";
  makeComplexTreeNode($what,$icon,$header,$expand);
}

/*************************** Prompt Make Functions ****************************/

function makePrompt($title, $link, $lang=true)
{
  global $language;
  $title=$lang?$language[$title]:formatAdminText($title);
  echo "<table id=\"prompt\" width=\"95%\" align=\"center\" cellpadding=\"4\" cellspacing=\"0\">\r\n";
  echo "<tr id=\"header\"><td>&nbsp;$title&nbsp;</td></tr>\r\n";
  echo "<tr id=\"footer\" align=\"center\"><td nowrap>\r\n";
  echo "<input class=\"button\" type=\"button\" value=\"$language[admin_yes]\" onclick=\"document.location.href='$link'\">&nbsp;\r\n";
  echo "<input class=\"button\" type=\"button\" value=\"$language[admin_no]\" onclick=\"history.back(1)\"><br>\r\n";
  echo "</td></tr>\r\n";
  echo "</table>\r\n";
}

function makePromptForm($what, $title="", $script="", $action="", $lang=true)
{
  global $language;
  if($what=="header") {
    $title=$lang?$language[$title]:formatAdminText($title);
    $anchor=preg_match("{#.*}",$script,$matches)?$matches[0]:"";
    $script=preg_replace("{#.*}","",$script).".php";
    echo "<table id=\"prompt\" width=\"95%\" align=\"center\" cellpadding=\"4\" cellspacing=\"0\">\r\n";
    echo "<form action=\"$script$anchor\" method=\"post\">\r\n";
    echo "<input type=\"hidden\" name=\"action\" value=\"$action\">\r\n";
    echo "<tr id=\"header\"><td>&nbsp;$title&nbsp;</td></tr>\r\n";
  }
  if($what=="footer") {
    echo "<tr id=\"footer\" align=\"center\"><td nowrap>\r\n";
    echo "<input class=\"button\" type=\"submit\" value=\"$language[admin_yes]\">&nbsp;\r\n";
    echo "<input class=\"button\" type=\"button\" value=\"$language[admin_no]\" onclick=\"history.back(1)\"><br>\r\n";
    echo "</td></tr>\r\n";
    echo "</form>\r\n";
    echo "</table>\r\n";
  }
}

/************************ Miscellaneous Make Functions ************************/

function makeLinksFormatted($links, $format, $class1, $class2, $wrap, $linklang)
{
  global $language;
  if($class1!="") $class1=" class=\"$class1\"";
  if($class2!="") $class2=" class=\"$class2\"";
  $parts=array();
  foreach($links as $key=>$link) {
    if($link===true) { $parts[]="<br>"; continue; }
    $key=format($format,$linklang?$language[$key]:formatAdminText($key));
    if($link===false) { $parts[]="<nobr><font$class2>$key</font></nobr>"; continue; }
    $target="";
    if(preg_match("{^(?!javascript:)(\w+):(.*)\$}",$link,$matches)) {
      $target=" target=\"$matches[1]\"";
      $link=$matches[2];
    }
    $parts[]="<nobr><a$class1 href=\"$link\"$target>$key</a></nobr>";
  }
  $content=implode(" ",$parts);
  $content=str_replace(" <br> ","<br>\r\n",$content);
  echo $content;
}

function makeLinksArray($links, $linklang=true)
{
  makeLinksFormatted($links,"[%s]","","nolink",false,$linklang);
}

function makeLinks($links, $linklang=true)
{
  echo "<div align=\"center\">";
  makeLinksArray($links,$linklang);
  echo "</div>\r\n";
}

function makeRefreshMenuLink()
{
  makeLinks(array("admin_refreshmenu"=>"_top:."));
}

function makeBreak($count=1)
{
  echo str_repeat("<br>",$count)."\r\n";
}

function makeNotification($title, $params=array(), $lang=true)
{
  global $language;
  $title=$lang?$language[$title]:formatAdminText($title);
  if(!is_array($params)) $params=array($params);
  foreach($params as $index=>$param) $params[$index]=formatAdminText($param);
  $message=format($title,$params);
  echo "$message<br>\r\n";
}

function makeWarning($title, $params=array(), $lang=true)
{
  global $language;
  $title=$lang?$language[$title]:formatAdminText($title);
  if(!is_array($params)) $params=array($params);
  foreach($params as $index=>$param) $params[$index]=formatAdminText($param);
  $message=format($title,$params);
  $message=format($language["admin_warning"],$message);
  echo "$message<br>\r\n";
}

function makeError($title, $params=array(), $lang=true)
{
  global $language;
  $title=$lang?$language[$title]:formatAdminText($title);
  if(!is_array($params)) $params=array($params);
  foreach($params as $index=>$param) $params[$index]=formatAdminText($param);
  $message=format($title,$params);
  $message=format($language["admin_error"],$message);
  echo "$message<br>\r\n";
}

function makeAdminError($title, $params=array(), $lang=true)
{
  makeError($title,$params,$lang);
  makeAdminPage("footer");
}

function makeWrongDBError()
{
  global $database;
  makeError("admin_error_wrongdb",$database->title);
}

function makeTodo()
{
  makeNotification("admin_error_todo");
}

function makeHeadline($title, $lang=true)
{
  global $language;
  $title=$lang?$language[$title]:formatAdminText($title);
  echo "<div align=\"center\"><b>$title</b></div>\r\n";
}

function makeAnchor($name)
{
  echo "<a name=\"$name\"></a>";
}

function makeFormattedText($text)
{
  echo "<div id=\"formatted\"><pre><hr>$text<hr></pre></div>\r\n";
}

function makeQuote($text)
{
  $text=htmlspecialchars($text);
  echo "<table id=\"quote\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\r\n";
  echo "<tr><td><pre>$text</pre></td></tr>\r\n";
  echo "</table>\r\n";
}

function makeImage($mimetype, $content)
{
  gzipCompressionSkip();
  @header("Cache-Control: max-age=".OneYear.", private");
  @header("Pragma: cache");
  @header("Content-Type: $mimetype");
  @header("Content-Length: ".strlen($content));
  echo $content;
  halt();
}

function makeNotificationScript($title, $delay=500, $lang=true)
{
  global $language;
  $title=$lang?$language[$title]:formatAdminText($title);
  $title=quoteText($title);
  $id=getIncrementalValue();
  echo "<script type=\"text/javascript\">\r\n";
  echo "function response$id() { alert($title); }\r\n";
  echo "window.setTimeout(\"response$id()\",$delay);\r\n";
  echo "</script>\r\n";
}

function makeRedirectScript($link, $delay=1500)
{
  $link=quoteText($link);
  $id=getIncrementalValue();
  echo "<script type=\"text/javascript\">\r\n";
  echo "function response$id() { document.location.href=$link; }\r\n";
  echo "window.setTimeout(\"response$id()\",$delay);\r\n";
  echo "</script>\r\n";
}

?>
