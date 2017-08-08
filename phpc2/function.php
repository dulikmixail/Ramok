<?php

// PHP Compiler  by Antylevsky Aleksei (Next)

function phpcversion()
{
  return "2.4.5";
}

function random($value)
{
  return rand(0,$value-1);
}

function ifset(&$value, $default=false)
{
  return isset($value)?$value:$default;
}

function swap(&$value1, &$value2)
{
  $value3=$value1;
  $value1=$value2;
  $value2=$value3;
}

function pushVariable(&$variable, $reverse=false)
{
  static $stack=array();
  if($reverse) $variable=array_pop($stack); else $stack[]=$variable;
}

function popVariable(&$variable)
{
  pushVariable($variable,true);
}

function getIncrementalValue()
{
  static $value=0;
  return ++$value;
}

function isTrueInteger($value)
{
  return (string)(int)$value===(string)$value;
}

function isTrueFloat($value)
{
  return (string)(float)$value===(string)$value;
}

if(!function_exists("hex2bin")) {
  function hex2bin($text)
  {
    return pack("H*",$text);
  }
}

if(!function_exists("file_put_contents")) {
  function file_put_contents($filename, $content)
  {
    if(!$file=fopen($filename,"wb")) return false;
    $result=fwrite($file,$content);
    fclose($file);
    return $result;
  }
}

/******************************************************************************/

function phpcstrcasecmp($text1, $text2)
{
  global $language;
  if(class_exists("UTFSupport")) return UTFSupport::strcasecmp($text1,$text2);
  $uppers=$language["charset_uppers"];
  $lowers=$language["charset_lowers"];
  $text1=strtr($text1,$uppers,$lowers);
  $text2=strtr($text2,$uppers,$lowers);
  return strcmp($text1,$text2);
}

function phpcstrncasecmp($text1, $text2, $length)
{
  global $language;
  if(class_exists("UTFSupport")) return UTFSupport::strncasecmp($text1,$text2,$length);
  $uppers=$language["charset_uppers"];
  $lowers=$language["charset_lowers"];
  $text1=strtr($text1,$uppers,$lowers);
  $text2=strtr($text2,$uppers,$lowers);
  return strncmp($text1,$text2,$length);
}

function phpcstrtoupper($text)
{
  global $language;
  if(class_exists("UTFSupport")) return UTFSupport::strtoupper($text);
  $uppers=$language["charset_uppers"];
  $lowers=$language["charset_lowers"];
  return strtr($text,$lowers,$uppers);
}

function phpcstrtolower($text)
{
  global $language;
  if(class_exists("UTFSupport")) return UTFSupport::strtolower($text);
  $uppers=$language["charset_uppers"];
  $lowers=$language["charset_lowers"];
  return strtr($text,$uppers,$lowers);
}

function phpcucfirst($text)
{
  global $language;
  if(class_exists("UTFSupport")) return UTFSupport::ucfirst($text);
  if($text=="") return $text;
  $uppers=$language["charset_uppers"];
  $lowers=$language["charset_lowers"];
  $text[0]=strtr($text[0],$lowers,$uppers);
  return $text;
}

function phpcucwords($text)
{
  global $language;
  if(class_exists("UTFSupport")) return UTFSupport::ucwords($text);
  if($text=="") return $text;
  $uppers=$language["charset_uppers"];
  $lowers=$language["charset_lowers"];
  $text[0]=strtr($text[0],$lowers,$uppers);
  $pattern="{\s[$language[charset_regexp_lowers]]}";
  preg_match_all($pattern,$text,$matches,PREG_OFFSET_CAPTURE);
  foreach($matches[0] as $match)
    $text[$match[1]+1]=strtr($text[$match[1]+1],$lowers,$uppers);
  return $text;
}

/******************************************************************************/

function combineArrays($keys, $values)
{
  if(count($keys)!=count($values)) return false;
  $result=array();
  foreach($keys as $index=>$key) $result[$key]=$values[$index];
  return $result;
}

function conjunctArrays($scheme)
{
  $result=array();
  if(!count($scheme)) return $result;
  $keys=array_keys($scheme);
  $indexes=array_keys($scheme[$keys[0]]);
  foreach($indexes as $index) {
    $line=array();
    foreach($keys as $key) $line[$key]=$scheme[$key][$index];
    $result[]=$line;
  }
  return $result;
}

function groupArrayByColumn($array, $column)
{
  $result=array();
  foreach($array as $line) {
    $key=$line[$column];
    if(!isset($result[$key])) $result[$key]=array();
    $result[$key][]=$line;
  }
  return $result;
}

function areArraysEqual($array1, $array2)
{
  foreach($array1 as $value) {
    $key=array_search($value,$array2);
    if($key===false) return false;
    unset($array2[$key]);
  }
  return !count($array2);
}

function extractArrayLines($array, $column)
{
  $result=array();
  foreach($array as $line) $result[$line[$column]]=$line;
  return $result;
}

function extractArrayColumn($array, $column)
{
  $result=array();
  foreach($array as $key=>$line) $result[$key]=$line[$column];
  return $result;
}

function extractArrayColumns($array, $column1, $column2)
{
  $result=array();
  foreach($array as $line) $result[$line[$column1]]=$line[$column2];
  return $result;
}

function isArrayFieldPresent($array, $column, $needle, $caseless=false)
{
  if($caseless) foreach($array as $line)
    if(!phpcstrcasecmp($line[$column],$needle)) return true;
  if(!$caseless) foreach($array as $line)
    if($line[$column]==$needle) return true;
  return false;
}

function searchArrayKey($array, $column, $needle, $caseless=false)
{
  if($caseless) foreach($array as $key=>$line)
    if(!phpcstrcasecmp($line[$column],$needle)) return $key;
  if(!$caseless) foreach($array as $key=>$line)
    if($line[$column]==$needle) return $key;
  return false;
}

function searchArrayField($array, $column1, $column2, $needle, $caseless=false)
{
  if($caseless) foreach($array as $line)
    if(!phpcstrcasecmp($line[$column1],$needle)) return $line[$column2];
  if(!$caseless) foreach($array as $line)
    if($line[$column1]==$needle) return $line[$column2];
  return false;
}

function searchArrayLine($array, $column, $needle, $caseless=false)
{
  if($caseless) foreach($array as $line)
    if(!phpcstrcasecmp($line[$column],$needle)) return $line;
  if(!$caseless) foreach($array as $line)
    if($line[$column]==$needle) return $line;
  return false;
}

/******************************************************************************/

function char($text, $index)
{
  if($index<0) return "\x00";
  if($index>=strlen($text)) return "\x00";
  return $text[$index];
}

function slashes($text, $symbol="'")
{
  return $symbol.addslashes($text).$symbol;
}

function addSlashesSmart($text)
{
  static $gpc, $sybase;
  if(!isset($gpc)) $gpc=get_magic_quotes_gpc();
  if(!isset($sybase)) $sybase=ini_get("magic_quotes_sybase");
  $text=is_scalar($text)?(string)$text:"";
  if(!$gpc) return $text;
  return $sybase?str_replace("'","''",$text):addslashes($text);
}

function stripSlashesSmart($text)
{
  static $gpc, $sybase;
  if(!isset($gpc)) $gpc=get_magic_quotes_gpc();
  if(!isset($sybase)) $sybase=ini_get("magic_quotes_sybase");
  $text=is_scalar($text)?(string)$text:"";
  if(!$gpc) return $text;
  return $sybase?str_replace("''","'",$text):stripslashes($text);
}

function explodeSmart($separator, $text)
{
  return $text==""?array():explode($separator,$text);
}

function explodeAssigns($separator, $text)
{
  $result=array();
  $elements=explodeSmart($separator,$text);
  foreach($elements as $element) {
    list($name,$value)=explode("=",$element,2);
    $result[$name]=$value;
  }
  return $result;
}

function implodeAssigns($glue, $pieces)
{
  foreach($pieces as $key=>$piece) $pieces[$key]="$key=$piece";
  return implode($glue,$pieces);
}

function incrementIdentifier($text)
{
  preg_match("{^(.*?)([0-9]*)\$}",$text,$matches);
  return $matches[1].($matches[2]+1);
}

function formatInteger($value)
{
  global $language;
  return number_format((int)$value,0,$language["format_separator"],$language["format_thousands"]);
}

function formatFloat($value)
{
  global $language;
  return number_format((float)$value,$language["format_decimals"],$language["format_separator"],$language["format_thousands"]);
}

function format($text, $arguments=array())
{
  if(!is_array($arguments)) $arguments=array($arguments);
  $pattern="{(%[dfs])}";
  $split=preg_split($pattern,$text,-1,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
  $index=0;
  foreach($split as $key=>$item) if(preg_match($pattern,$item)) {
    $argument=$index<count($arguments)?$arguments[$index++]:false;
    switch($item) {
      case "%d": $split[$key]=formatInteger($argument); break;
      case "%f": $split[$key]=formatFloat($argument); break;
      case "%s": $split[$key]=(string)$argument; break;
    }
  }
  return implode("",$split);
}

function encode($text, $seed="")
{
  $hash=EncodingPrefix.$seed;
  for($index=0; $index<strlen($text); $index++) {
    if(($index&15)==0) $hash=hex2bin(md5($hash));
    $text[$index]=chr(ord($text[$index])+ord($hash[$index&15]));
  }
  return bin2hex($text);
}

function decode($text, $seed="")
{
  $text=hex2bin($text);
  $hash=EncodingPrefix.$seed;
  for($index=0; $index<strlen($text); $index++) {
    if(($index&15)==0) $hash=hex2bin(md5($hash));
    $text[$index]=chr(ord($text[$index])-ord($hash[$index&15]));
  }
  return $text;
}

/******************************************************************************/

function optimizeText($text)
{
  $text=trim(strtr($text,"\t\r","  "));
  $text=preg_replace("{(?<! ) +\n}","\n",$text);
  $text=preg_replace("{(?<!\n) +}"," ",$text);
  $text=str_replace("\n","\r\n",$text);
  return $text;
}

function optimizeTextStrict($text)
{
  return preg_replace("{[ \t\r\n]+}"," ",trim($text));
}

function quoteText($text)
{
  $text=str_replace("\\","\\\\",$text);
  $text=str_replace("\"","\\\"",$text);
  $text=str_replace("\$","\\\$",$text);
  $text=str_replace("\t","\\t",$text);
  $text=str_replace("\r","\\r",$text);
  $text=str_replace("\n","\\n",$text);
  return "\"$text\"";
}

function filterText($text, $suspicious=false)
{
  $text=htmlspecialchars($text,ENT_QUOTES);
  if($suspicious) {
    $text=str_replace("&amp;#039;","&#039;",$text);
    if(strpos($text,"&amp;")!==false)
      $text=preg_replace("{&amp;(\w+;)}","&\\1",$text);
    $text=preg_replace("{[a-z]*script:}i","noscript:",$text);
  }
  else if(strpos($text,"&amp;")!==false)
    $text=preg_replace("{&amp;(#?\w+;)}","&\\1",$text);
  $text=str_replace("\r\n",PredefinedNewline."\r\n",$text);
  return $text;
}

function antispamText($text, $method="hex")
{
  if($method=="hex" || $method=="url") {
    $search=array("&lt;","&gt;","&quot;","&#039;","&amp;");
    $replace=array("<",">","\"","'","&");
    $text=str_replace($search,$replace,$text);
    $prefix=$method=="hex"?"&#x":"%";
    $suffix=$method=="hex"?";":"";
    for($index=strlen($text)-1; $index>=0; $index--) {
      if(ord($text[$index])>=128) continue;
      $replace=$prefix.bin2hex($text[$index]).$suffix;
      $text=substr_replace($text,$replace,$index,1);
    }
    return $text;
  }
  if(class_exists("AntispamSupport"))
    return AntispamSupport::processText($text,$method);
  return $text;
}

function chopText($text, $limit, $words=true)
{
  global $language;
  if(class_exists("UTFSupport")) return UTFSupport::chopText($text,$limit,$words);
  if(strlen($text)<=$limit) return $text;
  $saved=$limit=max($limit-strlen(PredefinedDots),0);
  if($words) {
    $pattern="{^[$language[charset_regexp]]+\$}";
    while($limit>0 && preg_match($pattern,substr($text,$limit-1,2))) $limit--;
    if(!$limit) $limit=$saved;
  }
  return rtrim(substr($text,0,$limit),PredefinedChop).PredefinedDots;
}

/******************************************************************************/

function phpcmicrotime()
{
  $microtime=microtime();
  if(is_float($microtime)) return $microtime;
  $microtime=explode(" ",$microtime);
  return array_sum($microtime);
}

function phpctime()
{
  global $timeOffsetServer;
  return time()+round($timeOffsetServer*OneHour);
}

function phpcdate($format, $time=false)
{
  global $language, $timeOffsetClient;
  if($time===false) $time=phpctime();
  $time+=round($timeOffsetClient*OneHour);
  $index=0;
  while($index<strlen($format)) {
    if($format[$index]=="\\") { $index+=2; continue; }
    $replacement=false;
    switch($format[$index]) {
      case "l": $replacement=$language["weekday"][@date("w",$time)]; break;
      case "D": $replacement=$language["weekday_short"][@date("w",$time)]; break;
      case "F": $replacement=$language["month"][@date("n",$time)]; break;
      case "f": $replacement=$language["month_gen"][@date("n",$time)]; break;
      case "M": $replacement=$language["month_short"][@date("n",$time)]; break;
    }
    if($replacement!==false) {
      $replacement=preg_replace("{.}","\\\\\\0",$replacement);
      $format=substr_replace($format,$replacement,$index,1);
      $index+=strlen($replacement);
    }
    else $index++;
  }
  return @date($format,$time);
}

function timestamp2datetime($time)
{
  return @date("Y-m-d H:i:s",$time);
}

function datetime2timestamp($datetime)
{
  $pattern="{^(\d+)-(\d+)-(\d+)\s*(\d*):?(\d*):?(\d*)\$}";
  if(!preg_match($pattern,$datetime,$matches)) return 0;
  $year=(int)$matches[1];
  $month=(int)$matches[2];
  $day=(int)$matches[3];
  $hour=(int)$matches[4];
  $minute=(int)$matches[5];
  $second=(int)$matches[6];
  return @mktime($hour,$minute,$second,$month,$day,$year);
}

/******************************************************************************/

function sendParameter($name, $value)
{
  $value=addSlashesSmart($value);
  $_GET[$name]=$value;
  $_REQUEST[$name]=$value;
}

function acceptParameter($name)
{
  if(!is_array($name)) $name=array($name);
  $result=$_REQUEST;
  foreach($name as $part) {
    if(!isset($result[$part])) return "";
    $result=$result[$part];
  }
  $result=stripSlashesSmart($result);
  if(class_exists("PrefilterSupport"))
    $result=PrefilterSupport::processText($result);
  return $result;
}

function acceptIntParameter($name, $min=false, $max=false)
{
  $value=(int)acceptParameter($name);
  if($max!==false && $value>$max) $value=$max;
  if($min!==false && $value<$min) $value=$min;
  return $value;
}

function acceptFloatParameter($name, $min=false, $max=false)
{
  $value=acceptParameter($name);
  $value=(float)str_replace(",",".",$value);
  if($max!==false && $value>$max) $value=$max;
  if($min!==false && $value<$min) $value=$min;
  return $value;
}

function acceptStringParameter($name, $limit=false, $optimize=true)
{
  $value=(string)acceptParameter($name);
  if($optimize) $value=optimizeText($value);
  if($limit!==false) $value=chopText($value,$limit);
  return $value;
}

function acceptArrayParameter($name, $keys=false)
{
  if(!is_array($name)) $name=array($name);
  $result=$_REQUEST;
  foreach($name as $part) {
    if(!isset($result[$part])) return array();
    $result=$result[$part];
  }
  if(!is_array($result)) return array();
  $result=$keys?array_keys($result):array_values($result);
  $prefilter=class_exists("PrefilterSupport");
  foreach($result as $index=>$value) {
    $value=stripSlashesSmart($value);
    if($prefilter) $value=PrefilterSupport::processText($value);
    $result[$index]=$value;
  }
  return $result;
}

/******************************************************************************/

function halt()
{
  while(ob_get_level()) ob_end_flush();
  exit;
}

function gzipCompressionStart()
{
  global $fileSystem;
  if(defined("GzipCompressionSkip")) return;
  if(headers_sent($file,$line)) {
    $fileSystem->localize($file);
    fatalError("fatal_gzip",array($file,$line));
  }
  $buffer="";
  while(ob_get_level()) $buffer=ob_get_clean().$buffer;
  ob_start("gzipCompressionHandler");
  echo $buffer;
}

function gzipCompressionSkip($erase=true)
{
  define("GzipCompressionSkip",true);
  if($erase) while(ob_get_level()) ob_end_clean();
}

function gzipCompressionHandler($content)
{
  if(defined("GzipCompressionSkip")) return $content;
  if(GzipCompressionEnabled) {
    $encoding=ifset($_SERVER["HTTP_ACCEPT_ENCODING"],"");
    preg_match_all("{[\w\-]+}",$encoding,$matches);
    $encoding=false;
    if(in_array("x-gzip",$matches[0])) $encoding="x-gzip";
    if(in_array("gzip",$matches[0])) $encoding="gzip";
    if($encoding && function_exists("gzcompress")) {
      $header="\x1f\x8b\x08\x00\x00\x00\x00\x00";
      $compressed=substr(gzcompress($content,GzipCompressionLevel),0,-4);
      $trailer=pack("V",crc32($content)).pack("V",strlen($content));
      $content=$header.$compressed.$trailer;
      @header("Content-Encoding: $encoding");
    }
  }
  @header("Content-Length: ".strlen($content));
  return $content;
}

/******************************************************************************/

function getServerAddress()
{
  return $_SERVER["SERVER_ADDR"];
}

function getClientAddress()
{
  return $_SERVER["REMOTE_ADDR"];
}

function sendStatus($code)
{
  $statuses=array(
    200=>"OK",
    401=>"Unauthorized",
    403=>"Forbidden",
    404=>"Not Found");
  $status=ifset($statuses[$code],"Unknown");
  @header("HTTP/1.0 $code $status");
  @header("HTTP/1.1 $code $status");
  @header("Status: $code $status");
}

function redirect($page, $params=array(), $anchor="")
{
  global $compiler;
  gzipCompressionSkip();
  $link=$compiler->createLink($page,$params);
  @header("Location: $link$anchor");
  halt();
}

function redirectBack($default="/")
{
  gzipCompressionSkip();
  $host=$_SERVER["HTTP_HOST"];
  $referer=ifset($_SERVER["HTTP_REFERER"],"");
  $pattern="{^https?://([^/]+)(/.*)\$}i";
  $success=preg_match($pattern,$referer,$matches) && $matches[1]==$host;
  $link=$success?$matches[2]:$default;
  @header("Location: $link");
  halt();
}

function httpAuthentication()
{
  $username=ifset($_SERVER["PHP_AUTH_USER"],"");
  $password=ifset($_SERVER["PHP_AUTH_PW"],"");
  return array(
    "username"=>trim(stripSlashesSmart($username)),
    "password"=>trim(stripSlashesSmart($password)));
}

function httpAuthenticate($realm="Restricted Area", $message="")
{
  global $language;
  gzipCompressionSkip();
  sendStatus(401);
  $realm=str_replace("\"","''",$realm);
  @header("WWW-Authenticate: Basic realm=\"$realm\"");
  @header("Content-Type: text/html; charset=$language[charset]");
  echo $message;
  halt();
}

function imageDisposition($filename, $mimetype, $content)
{
  global $language;
  gzipCompressionSkip();
  @header("Cache-Control: max-age=".OneYear.", private");
  @header("Pragma: cache");
  @header("Content-Type: $mimetype; charset=$language[charset]");
  @header("Content-Length: ".strlen($content));
  @header("Content-Disposition: inline; filename=$filename");
  echo $content;
  halt();
}

function contentDisposition($filename, $mimetype, $content)
{
  global $language;
  gzipCompressionSkip();
  @header("Cache-Control: no-store, no-cache, must-revalidate");
  @header("Pragma: no-cache");
  @header("Content-Type: $mimetype; charset=$language[charset]");
  @header("Content-Length: ".strlen($content));
  @header("Content-Disposition: attachment; filename=$filename");
  echo $content;
  halt();
}

/******************************************************************************/

function phpcsetcookie($name, $value="", $permanent=false)
{
  $expire=$permanent?phpctime()+OneYear:0;
  @setcookie($name,$value,$expire,"/");
}

function isLocalhost()
{
  return getServerAddress()=="127.0.0.1";
}

function isClientBanned($blackList)
{
  $ipaddress=getClientAddress();
  preg_match_all("{\d[\d.]*}",$blackList,$matches);
  foreach($matches[0] as $mask)
    if(!strncmp($ipaddress,$mask,strlen($mask))) return true;
  return false;
}

function isAdministrator($script="")
{
  global $adminAccessRights;
  static $password;
  if(!isset($password)) {
    $password="";
    if(isset($_COOKIE[PhpcPasswordCookie]))
      $password=decode(stripSlashesSmart($_COOKIE[PhpcPasswordCookie]),getClientAddress());
    $passsave=$password;
    if(isset($_POST[PhpcPasswordParam])) $password=trim(stripSlashesSmart($_POST[PhpcPasswordParam]));
    if(isset($_GET[PhpcPasswordParam])) $password=trim(stripSlashesSmart($_GET[PhpcPasswordParam]));
    if(strlen($password)>100 || !isset($adminAccessRights[$password])) $password="";
    if($password!=$passsave)
      phpcsetcookie(PhpcPasswordCookie,encode($password,getClientAddress()));
  }
  if($password=="") return false;
  if($adminAccessRights[$password]=="*") return true;
  if($script=="") return false;
  return in_array($script,explode(",",$adminAccessRights[$password]));
}

/******************************************************************************/

function fatalError($title, $params=array(), $error="", $query="")
{
  global $language;
  gzipCompressionSkip();
  @header("Cache-Control: no-store, no-cache, must-revalidate");
  @header("Pragma: no-cache");
  @header("Content-Type: text/html; charset=$language[charset]");
  $title=format($language[$title],$params);
  if($error!="") {
    $error=optimizeTextStrict($error);
    if(strlen($error)>500) $error=substr($error,0,500).PredefinedDots;
    $error=str_replace("{text}",htmlspecialchars($error),FatalReportError);
    $error=str_replace("{header}",$language["fatal_error"],$error);
  }
  if($query!="") {
    $query=optimizeTextStrict($query);
    if(strlen($query)>500) $query=substr($query,0,500).PredefinedDots;
    $query=str_replace("{text}",htmlspecialchars($query),FatalReportQuery);
    $query=str_replace("{header}",$language["fatal_query"],$query);
  }
  $report=str_replace("{text}",$title,FatalReport);
  $report=str_replace("{header}",$language["fatal_title"],$report);
  $report=str_replace("{error}",$error,$report);
  $report=str_replace("{query}",$query,$report);
  echo $report;
  halt();
}

function variableToString($value, $prefix="", $offset=0)
{
  $result=str_pad("",$offset);
  if(is_bool($value)) $value=$value?"true":"false";
  $array=is_array($value);
  $object=is_object($value);
  if(!$array && !$object) { $result.=$prefix.$value; return $result; }
  $result.=$prefix.($object?"object":"array")."(";
  if($object) $value=get_object_vars($value);
  if(count($value)) $result.="\r\n";
  foreach($value as $key=>$item)
    $result.=variableToString($item,"$key=>",$offset+2)."\r\n";
  if(count($value)) $result.=str_pad("",$offset);
  $result.=")";
  return $result;
}

function trace($variable)
{
  global $language;
  gzipCompressionSkip();
  @header("Cache-Control: no-store, no-cache, must-revalidate");
  @header("Pragma: no-cache");
  @header("Content-Type: text/plain; charset=$language[charset]");
  echo variableToString($variable);
  halt();
}

/******************************************************************************/

function generatePassword($length, $digits=true, $small=true, $caps=true)
{
  $source="";
  if($digits) for($code=ord("0"); $code<=ord("9"); $code++) $source.=chr($code);
  if($small) for($code=ord("a"); $code<=ord("z"); $code++) $source.=chr($code);
  if($caps) for($code=ord("A"); $code<=ord("Z"); $code++) $source.=chr($code);
  return substr(str_shuffle($source),0,$length);
}

function createSimpleCompareFunction($column, $caseless=false)
{
  $a="\$a[".quoteText($column)."]";
  $b="\$b[".quoteText($column)."]";
  if($caseless)
    $content="return phpcstrcasecmp($a,$b);";
    else $content="if($a==$b) return 0; return $a<$b?-1:1;";
  return create_function("\$a,\$b",$content);
}

function createComplexCompareFunction($columns)
{
  if(!is_array($columns)) $columns=explodeSmart(",",$columns);
  $content="";
  foreach($columns as $column=>$options) {
    if(!is_array($options)) { $column=$options; $options=array(); }
    $a="\$a[".quoteText($column)."]";
    $b="\$b[".quoteText($column)."]";
    $minus=isset($options["desc"])?"-":"";
    $operator=isset($options["desc"])?">":"<";
    if(isset($options["caseless"]))
      $content.="\$c={$minus}phpcstrcasecmp($a,$b);";
      else $content.="\$c=$a==$b?0:($a$operator$b?-1:1);";
    $content.="if(\$c) return \$c;";
  }
  $content.="return 0;";
  return create_function("\$a,\$b",$content);
}

function createSimpleNavigation($min, $max, $current, $start=1)
{
  $result=array("range"=>array());
  for($index=$min; $index<=$max; $index++) {
    $item=array("value"=>$index,"label"=>$start++);
    if($index==$current) $item["current"]=true;
    $result["range"][]=$item;
  }
  $result["current"]=$current;
  if($current>$min) $result["prev"]=$current-1;
  if($current<$max) $result["next"]=$current+1;
  if($current>$min) $result["first"]=$min;
  if($current<$max) $result["last"]=$max;
  $result["count"]=max($max-$min+1,0);
  return $result;
}

function createComplexNavigation($min, $max, $current, $limit=9, $start=1)
{
  $result=array("range"=>array());
  $offset=floor(($limit-1)/2);
  $mindisp=max(min($current-$offset,$max-$limit+1),$min);
  $maxdisp=min($mindisp+$limit-1,$max);
  $start+=$mindisp-$min;
  for($index=$mindisp; $index<=$maxdisp; $index++) {
    $item=array("value"=>$index,"label"=>$start++);
    if($index==$current) $item["current"]=true;
    $result["range"][]=$item;
  }
  $result["current"]=$current;
  if($current>$min) $result["prev"]=$current-1;
  if($current<$max) $result["next"]=$current+1;
  if($mindisp>$min) $result["prevmore"]=true;
  if($maxdisp<$max) $result["nextmore"]=true;
  if($current>$min) $result["first"]=$min;
  if($current<$max) $result["last"]=$max;
  $result["count"]=max($max-$min+1,0);
  return $result;
}

function parsePhpcManual($filename, $link)
{
  global $fileSystem;
  $result=array();
  $content=$fileSystem->openFile($filename);
  $link=format($link,"\\1");
  $pattern="{<section:(\w+)>(.*?)</section:\\1>}s";
  preg_match_all($pattern,$content,$matches,PREG_SET_ORDER);
  foreach($matches as $fragment) {
    $text=trim($fragment[2]);
    $text=preg_replace("{\{section:(\w+)\}}",$link,$text);
    $text=preg_replace("{\{const:(\w+)\}}e","\\1",$text);
    $result[$fragment[1]]=$text;
  }
  return $result;
}

?>
