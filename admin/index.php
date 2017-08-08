<?php

// PHP Compiler  by Antylevsky Aleksei (Next)

session_start();

if($_REQUEST['key']=='666'){
	$_SESSION['entered']=1;
	}

if(!$_SESSION['entered']){
	exit();
	}

require "global.php";

$action=acceptStringParameter("action");

/********************************* Functions **********************************/

function comparePluginWeights($plugin1, $plugin2)
{
  if($plugin1["weight"]!=$plugin2["weight"])
    return $plugin1["weight"]<$plugin2["weight"]?-1:1;
    else return strcmp($plugin1["filename"],$plugin2["filename"]);
}

function getPlugins()
{
  global $fileSystem;
  $result=array();
  $signature="PHPC Control Panel Plugin";
  $pattern=
    "{^<\?php\s+// $signature \(([\d.]+)\)[^\\n]*\\n".
    "\s*(?://[^\\n]*\\n\s*)*/\*(.*?)\*/\s*(/\*(.*?)\*/)?}s";
  $folder=$fileSystem->getFolder(".",".php");
  foreach($folder as $filename) {
    if(!isAdministrator(basename($filename,".php"))) continue;
    $file=$fileSystem->openFile($filename);
    if(!preg_match($pattern,$file,$matches)) continue;
    if(!isset($matches[4])) $matches[4]="";
    $result[]=array(
      "weight"=>(float)$matches[1],
      "filename"=>$filename,
      "menucode"=>trim($matches[2])."\r\n",
      "homecode"=>trim($matches[4])."\r\n");
  }
  usort($result,"comparePluginWeights");
  return $result;
}

/*********************************** Frames ***********************************/

if($action=="" || $action=="index") {
  makeAdminFrames();
}

/********************************* Main Menu **********************************/

if($action=="menu") {
  $plugins=getPlugins();
  makeAdminPage("header","menu");
  makeMenu("header");
  $prevweight=-1;
  foreach($plugins as $plugin) {
    $weight=floor($plugin["weight"]);
    if($weight!=$prevweight) makeMenu("separator");
    $prevweight=$weight;
    eval($plugin["menucode"]);
  }
  makeMenu("footer");
  makeAdminPage("footer");
}

/*********************************** Header ***********************************/

if($action=="head") {
  makeAdminPage("header","head");
  makeAdminHeadline();
  makeAdminPage("footer");
}

/************************************ Home ************************************/

if($action=="home") {
  adminLog();
  $plugins=getPlugins();
  $links=array(
    "http://www.phpc.ru/"=>"admin_toolbar_link1",
    "http://www.phpc.ru/manual"=>"admin_toolbar_link2",
    "http://www.php.net/"=>"admin_toolbar_link3",
    "http://www.php.net/docs.php"=>"admin_toolbar_link4",
    "http://www.mysql.com/"=>"admin_toolbar_link5",
    "http://dev.mysql.com/doc/mysql/"=>"admin_toolbar_link6");
  $languages=array();
  $defaultLanguage=false;
  if(PhpcLocale=="") foreach($language["locales"] as $locale=>$name) {
    $link="index.php?action=dolanguage&locale=$locale";
    if($locale==$language["locale"]) $defaultLanguage=$link;
    $languages[$link]=$name;
  }
  
  
  makeAdminPage("header");
  makeNotification("admin_welcome");


  print '<br><div align="center"></div>';
  
  
  makeBreak(3);
  foreach($plugins as $plugin) eval($plugin["homecode"]);
  if(count($languages)) makeToolbarChooser("admin_toolbar_language","",
    $defaultLanguage,$languages,true,true,false);
  makeToolbar("footer");
  makeAdminPage("footer");
}

/****************************** Choose Language *******************************/

if($action=="dolanguage") {
  adminLog("locale");
  setPhpcLocale(acceptStringParameter("locale"));
  makeAdminPage("header");
  makeNotification("admin_toolbar_langsuccess");
  makeBreak();
  makeRefreshMenuLink();
  makeAdminPage("footer");
}

?>
