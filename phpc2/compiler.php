<?php

// PHP Compiler  by Antylevsky Aleksei (Next)

class Compiler
{
  var $style;
  var $session;
  var $page;
  var $template;
  var $bundles;
  var $plugins;

  function Compiler()
  {
    $this->style=array("folder"=>"");
    $this->session=array("propagate"=>"");
    $this->page=array("name"=>"");
    $this->template="";
    $this->bundles=array();
    $this->plugins=array();
  }

  function getPreloadPlugins()
  {
    $result=explodeSmart(",",PhpcPreloadPlugins);
    foreach($result as $index=>$plugin)
      $result[$index]=format(CompilerPluginFilename,$plugin);
    return $result;
  }

  function getInstalledPlugins()
  {
    global $fileSystem;
    static $cache;
    if(!isset($cache)) {
      $extension=$fileSystem->getFileExtension(CompilerPluginFilename);
      $folder=dirname(CompilerPluginFilename)."/";
      $cache=$fileSystem->getFolder($folder,$extension);
      foreach($cache as $index=>$filename)
        $cache[$index]=basename($filename,$extension);
    }
    return $cache;
  }

  function isPluginInstalled($plugin)
  {
    $plugins=$this->getInstalledPlugins();
    return in_array($plugin,$plugins);
  }

  function getPages()
  {
    global $database;
    static $cache;
    if(!isset($cache)) {
      $cache=array();
      $pages=$database->getLines("pages");
      foreach($pages as $page) {
        $bundles=explodeSmart(",",$page["bundles"]);
        $bundles=combineArrays($bundles,$bundles);
        $page["params"]=explodeAssigns(",",$page["params"]);
        $page["bundles"]=array_change_key_case($bundles);
        $cache[phpcstrtolower($page["name"])]=$page;
      }
    }
    return $cache;
  }

  function getInheritanceChain($subject)
  {
    global $database;
    static $cache=array();
    if(!isset($cache[$subject])) {
      $chain=array();
      $setid=$this->style["{$subject}setid"];
      $sets=$database->getLines("{$subject}sets");
      $sets=extractArrayColumns($sets,"id","parentid");
      while($setid) {
        if(!isset($sets[$setid])) fatalError("fatal_constraints");
        if(in_array($setid,$chain)) fatalError("fatal_recursion","{$subject}sets");
        $chain[]=$setid;
        $setid=$sets[$setid];
      }
      if(!count($chain)) fatalError("fatal_constraints");
      $cache[$subject]=$chain;
    }
    return $cache[$subject];
  }

  function getReplacements()
  {
    global $database;
    static $cache;
    if(!isset($cache)) {
      $cache=array();
      $chain=$this->getInheritanceChain("replacement");
      foreach($chain as $setid) {
        $set=$database->getLines("replacements","setid=$setid");
        foreach($set as $item) {
          $name=phpcstrtolower($item["name"]);
          if(!isset($cache[$name])) $cache[$name]=$item["content"];
        }
        ksort($cache);
      }
    }
    return $cache;
  }

  function getTemplateCallback($matches)
  {
    $pattern="{(?<![\\\\\\\$])\\\$(\w+)=(?![=>&])}";
    $replace="\$_phpc_scope[count(\$_phpc_scope)-1][0][\"\\1\"]=\\0";
    return preg_replace($pattern,$replace,$matches[0]);
  }

  function getTemplate($name, $used=array())
  {
    global $language, $database;
    static $cache=array();
    $namelower=phpcstrtolower($name);
    if(!isset($cache[$namelower])) {
      $cache[$namelower]=false;
      $chain=$this->getInheritanceChain("template");
      $replacements=$this->getReplacements();
      $setsList=implode(",",$chain);
      $templates=$database->getLines("templates","name=".slashes($name)." AND setid IN ($setsList)");
      foreach($chain as $setid) {
        $template=searchArrayLine($templates,"setid",$setid);
        if(!$template) continue;
        $content=$template["content"];
        $patternLines="{(?<=\n)[\r\n]+}s";
        $patternComment="{<\?--.*?(--\?>\r?\n?|\$)}s";
        $content=preg_replace($patternLines,"",$content);
        $content=preg_replace($patternComment,"",$content);
        $patternCode="{<\?.*?\?>}s";
        $callback=array("Compiler","getTemplateCallback");
        $content=preg_replace_callback($patternCode,$callback,$content);
        foreach($replacements as $search=>$replace) {
          $pattern=preg_quote($search);
          $replace=str_replace("\\","\\\\",$replace);
          $replace=str_replace("\$","\\\$",$replace);
          $content=preg_replace("{{$pattern}}i",$replace,$content);
        }
        $template["content"]=$content;
        $content=preg_replace($patternCode,"",$content);
        $patternError="{.*(<\?|\?>).*}";
        if(preg_match($patternError,$content,$matches))
          fatalError("fatal_compile",array(),format($language["fatal_reason_phptag"],array($name,trim($matches[0]))));
        if($template["parent"]!="") {
          if(in_array($namelower,$used)) fatalError("fatal_recursion","templates");
          $used[]=$namelower;
          $parents=$this->getTemplate($template["parent"],$used);
          if(!$parents) fatalError("fatal_compile",array(),format($language["fatal_reason_notemplateparent"],array($template["parent"],$name)));
          $parents[]=$template;
          $cache[$namelower]=$parents;
        }
        else $cache[$namelower]=array($template);
        break;
      }
    }
    return $cache[$namelower];
  }

  function getBundle($name)
  {
    global $database;
    static $cache=array();
    $namelower=phpcstrtolower($name);
    if(!isset($cache[$namelower])) {
      $cache[$namelower]=false;
      $chain=$this->getInheritanceChain("bundle");
      $setsList=implode(",",$chain);
      $bundles=$database->getLines("bundles","name=".slashes($name)." AND setid IN ($setsList)");
      foreach($chain as $setid) {
        $bundle=searchArrayLine($bundles,"setid",$setid);
        if(!$bundle) continue;
        $plugins=explodeSmart(",",$bundle["plugins"]);
        $plugins=combineArrays($plugins,$plugins);
        $bundle["plugins"]=array_change_key_case($plugins);
        $cache[$namelower]=$bundle;
        break;
      }
    }
    return $cache[$namelower];
  }

  function getLinkStyles()
  {
    global $database;
    static $cache;
    if(!isset($cache)) {
      $cache=$database->getOrderedLines("linkstyles","useorder");
      foreach($cache as $index=>$rule) {
        $rule["assign"]=explodeAssigns(",",$rule["assign"]);
        preg_match_all("{\\\$(\w+)}",$rule["pattern"],$matches);
        $rule["params"]=array_merge($matches[1],array_keys($rule["assign"]));
        $cache[$index]=$rule;
      }
    }
    return $cache;
  }

  function getGlobalCache()
  {
    global $database;
    $result=$database->getField("settings","value","groupid=0 AND name='globalCache'");
    if(PhpcLocale!="") return $result;
    $cache=$database->getLine("messages","name='globalCache' AND special=1");
    return $result.getLocalizedField($cache,"content");
  }

  function processStyle(&$request)
  {
    global $database;
    $conditions=isAdministrator()?"":"visible=1";
    $styles=$database->getLines("styles",$conditions);
    $styleid=(int)ifset($_COOKIE[PhpcStyleCookie],0);
    if(class_exists("UsersSupport")) UsersSupport::processStyle($styleid);
    $host=$_SERVER["HTTP_HOST"];
    $folder=preg_match("{^(\w{1,31})(/|\$)}",$request,$matches)?$matches[1]:"";
    $bestWeight=-1;
    foreach($styles as $style) {
      if($style["host"]!="" && $style["host"]!=$host) continue;
      if($style["folder"]!="" && $style["folder"]!=$folder) continue;
      $weight=0;
      if($style["forusers"]) $weight+=1;
      if($style["id"]==$styleid) $weight+=2;
      if($style["folder"]==$folder) $weight+=4;
      if($style["host"]==$host) $weight+=8;
      if($weight>$bestWeight) { $result=$style; $bestWeight=$weight; }
    }
    if($bestWeight<0) fatalError("fatal_nostyle");
    $cutoff=strlen($result["folder"]);
    if($cutoff) $request=(string)substr($request,$cutoff+1);
    return $result;
  }

  function updateStyle($styleid)
  {
    phpcsetcookie(PhpcStyleCookie,$styleid,true);
  }

  function processSession(&$request)
  {
    global $database;
    if(!PhpcSessionEnabled) return array("propagate"=>"");
    $hash=preg_match("{^([\da-f]{32})(/|\$)}",$request,$matches)?$matches[1]:"";
    if($hash!="") $request=(string)substr($request,33);
    if(!PhpcSessionUseURLs) $hash="";
    if($hash=="" && PhpcSessionUseCookies && isset($_COOKIE[PhpcSessionCookie])) {
      $cookieHash=trim(stripSlashesSmart($_COOKIE[PhpcSessionCookie]));
      if(preg_match("{^[\da-f]{32}\$}",$cookieHash)) $hash=$cookieHash;
    }
    $ipaddress=getClientAddress();
    $minimalTime=phpctime()-PhpcSessionTimeout;
    if(random(PhpcSessionGCDivisor)<PhpcSessionGCProbability) {
      if(class_exists("UsersSupport")) {
        $sessions=$database->getLines("sessions","lastactivity<$minimalTime");
        UsersSupport::processSessionCleanup($sessions);
      }
      $database->deleteLines("sessions","lastactivity<$minimalTime");
    }
    $catchConditions="ipaddress=".slashes($ipaddress)." AND lastactivity>=$minimalTime";
    $conditions="hash=".slashes($hash)." AND $catchConditions";
    $session=$hash!=""?$database->getLine("sessions",$conditions):false;
    if(PhpcSessionCatchEnabled && !$session && !count($_COOKIE)) {
      $restrictions=explodeSmart(",",PhpcSessionCatchRestrictions);
      $session=$database->getLine("sessions",$catchConditions);
      if($session) foreach($restrictions as $field)
        if($session[$field]) { $session=false; break; }
    }
    if(!$session) {
      $hash=md5(EncodingPrefix.uniqid(rand(),true));
      $session=compact("hash","ipaddress");
      if(PhpcSessionValidator!="") {
        $callback=PhpcSessionValidator;
        $pattern="{^\w+\.\w+\$}";
        if(preg_match($pattern,$callback)) $callback=explode(".",$callback);
        call_user_func($callback,$session);
      }
      $database->addLine("sessions",$session);
      $session=$database->getLine("sessions","hash=".slashes($session["hash"]));
      $session["phpcNewSession"]=true;
    }
    if(PhpcSessionUseCookies) phpcsetcookie(PhpcSessionCookie,$session["hash"]);
    $propagate=(!PhpcSessionUseCookies || !count($_COOKIE)) && PhpcSessionUseURLs;
    $session["propagate"]=$propagate?$session["hash"]:"";
    return $session;
  }

  function updateSession(&$session, $values)
  {
    global $database;
    if(!isset($session["hash"])) return;
    $database->modifyLine("sessions",$values,"hash=".slashes($session["hash"]));
    $session=array_merge($session,$values);
    $this->session=array_merge($this->session,$values);
  }

  function processRequest(&$request)
  {
    if(phpcstrtolower($request)==CompilerIndexPage) return 0;
    $linkStyles=$this->getLinkStyles();
    foreach($linkStyles as $rule) {
      $pattern=preg_quote($rule["pattern"]);
      $pattern=preg_replace("{\\\{\\\\\\\$\w+\\\}}","([\w\-%/]+)",$pattern);
      $pattern=preg_replace("{\\\\\\\$\w+}","([\w\-%]+)",$pattern);
      if(!preg_match("{^$pattern\$}i",$request,$matches)) continue;
      $values=array_slice($matches,1);
      preg_match_all("{\\\$(\w+)}",$rule["pattern"],$matches);
      foreach($matches[1] as $index=>$param)
        sendParameter($param,urldecode($values[$index]));
      foreach($rule["assign"] as $param=>$value) sendParameter($param,$value);
      return $rule["pageid"];
    }
    return 0;
  }

  function processPage(&$request)
  {
    if($request=="") $request=CompilerIndexPage;
    $pages=$this->getPages();
    $page=searchArrayLine($pages,"alias",$request,true);
    if(!$page) {
      $pageid=$this->processRequest($request);
      if($pageid) {
        $page=searchArrayLine($pages,"id",$pageid);
        if(!$page) fatalError("fatal_constraints");
      }
    }
    $requestlower=phpcstrtolower($request);
    if(!$page && isset($pages[$requestlower]) &&
      $pages[$requestlower]["alias"]=="") $page=$pages[$requestlower];
    if(!$page || !$page["visible"]) {
      if(!isset($pages[CompilerErrorPage])) fatalError("fatal_no404");
      $page=$pages[CompilerErrorPage];
    }
    $page=array($page);
    $chain=array($page[0]["id"]);
    while($page[0]["parentid"]) {
      $parent=searchArrayLine($pages,"id",$page[0]["parentid"]);
      if(!$parent || in_array($parent["id"],$chain)) fatalError("fatal_constraints");
      array_unshift($page,$parent);
      $chain[]=$parent["id"];
    }
    $result=$page[0];
    $complexInherit=explodeSmart(",",CompilerComplexInherit);
    foreach($page as $index=>$item) if($index)
      foreach($item as $key=>$value) if(in_array($key,$complexInherit))
        $result[$key]=array_merge($result[$key],$value);
        else if($value!="") $result[$key]=$value;
    $result["bundles"]=array_values($result["bundles"]);
    $result["request"]=$request;
    $result["link"]=$this->createLink($result["name"]);
    localizeField($result,"title");
    return $result;
  }

  function processUpdate()
  {
    if(!isset($this->session["hash"])) return;
    $values=array();
    $pattern="{^[\w\-/]{1,50}\$}";
    foreach($_GET as $param=>$value) {
      if(!is_string($param) || !is_string($value)) continue;
      $value=acceptStringParameter($param);
      if(!preg_match($pattern,$param) || !preg_match($pattern,$value)) continue;
      $values[$param]=$value;
    }
    $values=array_merge($values,$this->page["params"]);
    ksort($values);
    do {
      $params=implodeAssigns(",",$values);
      if(strlen($params)<=PhpcSessionParamsLimit) break;
      array_pop($values);
    } while(true);
    $values=array("lastactivity"=>phpctime(),"pageid"=>$this->page["id"],"params"=>$params);
    if(class_exists("UsersSupport"))
      UsersSupport::processSessionUpdate($this->session,$values);
    $this->updateSession($this->session,$values);
  }

  function processRelations($master, $operation, $id)
  {
    global $language, $settings, $fileSystem, $mailSystem, $database, $formatter, $optimizer, $compiler;
    static $cache=array();
    if(!$id) return;
    $cachekey="$master.$operation";
    if(!isset($cache[$cachekey])) {
      $rules=$database->getOrderedLines("relations","id",
        "master=".slashes($master)." AND operation=".slashes($operation));
      $cache[$cachekey]="";
      foreach($rules as $rule) $cache[$cachekey].=$rule["code"]."\r\n";
    }
    eval($cache[$cachekey]);
  }

  function createLink($page, $params=array())
  {
    if($page=="/") $page=CompilerIndexPage;
    $pagelower=phpcstrtolower($page);
    $pages=$this->getPages();
    if(!isset($pages[$pagelower])) {
      $result=$page;
      if(count($params)) {
        foreach($params as $param=>$value) $params[$param]=urlencode($value);
        $result.="?".implodeAssigns("&",$params);
      }
      if(strpos($page,":")===false) $result="/".$result;
      return $result;
    }
    $page=$pages[$pagelower];
    $result=$page["alias"]!=""?$page["alias"]:$page["name"];
    if(phpcstrtolower($result)==CompilerIndexPage) $result="";
    if($result!="" || count($params)) {
      $linkStyles=$this->getLinkStyles();
      $paramNames=array_keys($params);
      foreach($linkStyles as $rule) if($rule["pageid"]==$page["id"]) {
        if(count(array_diff($rule["params"],$paramNames))) continue;
        $paramsCopy=$params;
        $success=true;
        foreach($rule["assign"] as $param=>$value) {
          if($paramsCopy[$param]!=$value) { $success=false; break; }
          unset($paramsCopy[$param]);
        }
        if(!$success) continue;
        $result=$rule["pattern"];
        foreach($paramsCopy as $param=>$value)
          if(in_array($param,$rule["params"])) {
            $pattern="{\\{\\\$$param\}|\\\$$param\b}";
            $value=str_replace("%2F","/",urlencode($value));
            $result=preg_replace($pattern,$value,$result);
            unset($paramsCopy[$param]);
          }
        $params=$paramsCopy;
        break;
      }
      if(count($params)) {
        foreach($params as $param=>$value) $params[$param]=urlencode($value);
        $result.="?".implodeAssigns("&",$params);
      }
    }
    $glue=($result!="" && $this->session["propagate"]!="")?"/":"";
    $result=$this->session["propagate"].$glue.$result;
    $glue=($result!="" && $this->style["folder"]!="")?"/":"";
    $result="/".$this->style["folder"].$glue.$result;
    return $result;
  }

  function prepare()
  {
    global $language, $settings, $optimizer;
    eval($this->getGlobalCache());
    $request=stripSlashesSmart($_SERVER["REQUEST_URI"]);
    $position=strpos($request,"?");
    if($position!==false) $request=substr($request,0,$position);
    $request=trim($request,"/");
    $this->style=$this->processStyle($request);
    $this->session=$this->processSession($request);
    $this->page=$this->processPage($request);
    $this->processUpdate();
    $styleid=$this->style["id"];
    $bundles=$this->page["bundles"];
    $this->template=$this->page["template"];
    $this->bundles=$optimizer->getBundles($styleid,$bundles);
    if(!$this->bundles) $this->bundles=
      $optimizer->addBundles($styleid,$bundles,$this->compileBundles($bundles));
    $this->plugins=$optimizer->getBundlesPlugins($this->bundles);
    foreach($this->plugins as $index=>$name) {
      $filename=format(CompilerPluginFilename,$name);
      $this->plugins[$index]=$filename;
    }
    if(class_exists("StatisticsSupport"))
      StatisticsSupport::processVisitor($this->session,$request);
  }

  function processTemplateAreas($template)
  {
    global $language;
    $areaPattern="{(</?area:.*?>)\r?\n?}s";
    $areaPatternStrict="{^<(/?)area:([\w\-]+)\s*(/?)>\$}s";
    foreach($template as $itemIndex=>$item) {
      $split=preg_split($areaPattern,$item["content"],-1,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
      foreach($split as $index=>$part) if(preg_match($areaPattern,$part)) {
        if(!preg_match($areaPatternStrict,$part,$matches))
          fatalError("fatal_compile",array(),format($language["fatal_reason_area"],array($item["name"],$part)));
        if($matches[1]!="" && $matches[3]!="")
          fatalError("fatal_compile",array(),format($language["fatal_reason_area"],array($item["name"],$part)));
        $split[$index]=array(
          "name"=>$matches[2],
          "opening"=>$matches[1]=="",
          "closing"=>$matches[1]!="" || $matches[3]!="",
          "content"=>$part);
      }
      $used=array();
      $nesting=array();
      foreach($split as $index=>$part) if(is_array($part)) {
        $name=$part["name"];
        if($part["opening"]) {
          if(in_array($name,$used))
            fatalError("fatal_compile",array(),format($language["fatal_reason_areaalready"],array($item["name"],$part["content"])));
          $used[]=$name;
          $nesting[]=compact("name","index");
        }
        if($part["closing"]) {
          $match=array_pop($nesting);
          if(!$match || $match["name"]!=$name)
            fatalError("fatal_compile",array(),format($language["fatal_reason_areamissing"],array($item["name"],$part["content"])));
          $split[$match["index"]]["delta"]=$index-$match["index"];
        }
      }
      foreach($split as $part)
        if(is_array($part) && $part["opening"] && !isset($part["delta"]))
          fatalError("fatal_compile",array(),format($language["fatal_reason_areaunclosed"],array($item["name"],$part["content"])));
      $template[$itemIndex]=array("id"=>$item["id"],"name"=>$item["name"],"content"=>$split);
    }
    $result=$template[0];
    foreach($template as $itemIndex=>$item) if($itemIndex) {
      $result["id"]=$item["id"];
      $result["name"]=$item["name"].".".$result["name"];
      $index=0;
      while($index<count($item["content"])) {
        $part=$item["content"][$index];
        if(!is_array($part)) { $index++; continue; }
        $position=false;
        foreach($result["content"] as $fragmentIndex=>$fragment)
          if(is_array($fragment) && $fragment["name"]==$part["name"])
            { $position=$fragmentIndex; break; }
        if($position===false) fatalError("fatal_compile",array(),
          format($language["fatal_reason_areanoparent"],array($item["name"],$part["content"])));
        $replacement=array_slice($item["content"],$index,$part["delta"]+1);
        array_splice($result["content"],$position,$fragment["delta"]+1,$replacement);
        $index+=$part["delta"]+1;
      }
    }
    foreach($result["content"] as $itemIndex=>$item)
      if(is_array($item)) unset($result["content"][$itemIndex]);
    $result["content"]=implode("",$result["content"]);
    $globals=array("language"=>true,"settings"=>true,"compiler"=>true);
    preg_match_all("{<\?.*?\?>}s",$result["content"],$matches);
    $fragments=$matches[0];
    foreach($fragments as $fragment) {
      $pattern="{(?<![\\\\\\\$])\\\$(\w+)->}";
      preg_match_all($pattern,$fragment,$matches);
      foreach($matches[1] as $global) $globals[$global]=true;
    }
    $result["globals"]=array_keys($globals);
    return $result;
  }

  function processTemplateTags($template)
  {
    global $language;
    $tagStringBlock="\"(?:\\\\\\\\|\\\\\"|.)*?\"";
    $tagParamsBlock="[A-Za-z_]\w*(?:=[A-Za-z_]\w*(?::\w+)*|=$tagStringBlock)?";
    $tagPattern="{(</?(?:insert|logic):(?:$tagStringBlock|.)*?>)\r?\n?}s";
    $tagPatternStrict="{^<(/?)(insert|logic):([\w\-]+)\s*((?:$tagParamsBlock\s*)*)(/?)>\$}s";
    $tagParamsPattern="{(\w+)(?:=(\w+(?::\w+)*)|=($tagStringBlock))?}s";
    $result=preg_split($tagPattern,$template["content"],-1,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
    foreach($result as $index=>$part) if(preg_match($tagPattern,$part)) {
      if(!preg_match($tagPatternStrict,$part,$matches))
        fatalError("fatal_compile",array(),format($language["fatal_reason_tag"],array($template["name"],$part)));
      if($matches[1]!="" && $matches[5]!="")
        fatalError("fatal_compile",array(),format($language["fatal_reason_tag"],array($template["name"],$part)));
      $tagInfo=array(
        "type"=>$matches[2],
        "name"=>$matches[3],
        "typename"=>$matches[2].":".$matches[3],
        "opening"=>$matches[1]=="",
        "closing"=>$matches[1]!="" || $matches[5]!="",
        "params"=>array(),
        "content"=>$part);
      preg_match_all($tagParamsPattern,$matches[4],$matches);
      $tagInfo["params"]=array_slice($matches,1);
      if(!$tagInfo["opening"] && count($tagInfo["params"][0]))
        fatalError("fatal_compile",array(),format($language["fatal_reason_tag"],array($template["name"],$part)));
      if($tagInfo["type"]=="logic" &&
        !in_array($tagInfo["name"],$this->getLogicTags()))
          fatalError("fatal_compile",array(),format($language["fatal_reason_unknownlogic"],array($template["name"],$part)));
      $result[$index]=$tagInfo;
    }
    $nesting=array();
    foreach($result as $index=>$part) if(is_array($part)) {
      $typename=$part["typename"];
      if($part["opening"]) $nesting[]=compact("typename","index");
      if($part["closing"]) {
        $match=array_pop($nesting);
        if(!$match || $match["typename"]!=$typename)
          fatalError("fatal_compile",array(),format($language["fatal_reason_tagmissing"],array($template["name"],$part["content"])));
        $result[$match["index"]]["delta"]=$index-$match["index"];
      }
    }
    foreach($result as $part)
      if(is_array($part) && $part["opening"] && !isset($part["delta"]))
        fatalError("fatal_compile",array(),format($language["fatal_reason_tagunclosed"],array($template["name"],$part["content"])));
    return $result;
  }

  function processTemplateContents($fragment)
  {
    $contentPattern="{(<var:content\s*>)\r?\n?}s";
    $result=preg_split($contentPattern,$fragment,-1,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
    foreach($result as $index=>$part)
      if(preg_match($contentPattern,$part)) $result[$index]=array();
    return $result;
  }

  function processTemplateVars($name, $fragment)
  {
    global $language;
    $tagStringBlock="\"(?:\\\\\\\\|\\\\\"|.)*?\"";
    $tagParamsBlock="[A-Za-z_]\w*(?:=[A-Za-z_]\w*(?::\w+)*|=$tagStringBlock)?";
    $tagPattern="{(<(?:var|const|write):(?:$tagStringBlock|.)*?>)}s";
    $tagPatternStrict="{^<(var|const|write):(\w+(?::\w+)*)\s*((?:$tagParamsBlock\s*)*)>\$}s";
    $tagParamsPattern="{(\w+)(?:=(\w+(?::\w+)*)|=($tagStringBlock))?}s";
    $result=preg_split($tagPattern,$fragment,-1,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
    foreach($result as $index=>$part) if(preg_match($tagPattern,$part)) {
      if(!preg_match($tagPatternStrict,$part,$matches))
        fatalError("fatal_compile",array(),format($language["fatal_reason_tag"],array($name,$part)));
      $tagInfo=array(
        "type"=>$matches[1],
        "name"=>$matches[2],
        "params"=>array(),
        "content"=>$part);
      preg_match_all($tagParamsPattern,$matches[3],$matches);
      $tagInfo["params"]=array_slice($matches,1);
      if($tagInfo["type"]=="write" &&
        !in_array($tagInfo["name"],$this->getWriteTags()))
          fatalError("fatal_compile",array(),format($language["fatal_reason_unknownwrite"],array($name,$part)));
      $result[$index]=$tagInfo;
    }
    return $result;
  }

  function compileTemplateFragment($fragment)
  {
    if($fragment=="") return "";
    $result="?>\r\n$fragment<?php\r\n";
    $useecho=strlen($fragment)<=CompilerEchoLimit && strpos($fragment,"<?")===false;
    if($useecho) $result="echo".quoteText($fragment).";";
    return $result;
  }

  function compileTemplateParams($params)
  {
    $prefix="\$_phpc_args=array();";
    $result="";
    foreach($params[0] as $paramIndex=>$param) {
      if($param=="currentScope") {
        $prefix="\$_phpc_args=\$_phpc_scope[count(\$_phpc_scope)-1][0];";
        continue;
      }
      $param=quoteText($param);
      $valueRef=$params[1][$paramIndex];
      $valueStr=$params[2][$paramIndex];
      if($valueRef!="") {
        $parts=explode(":",$valueRef);
        $subject="\$".array_shift($parts);
        foreach($parts as $part) {
          if(!isTrueInteger($part)) $part=quoteText($part);
          $subject.="[$part]";
        }
        $result.="if(isset($subject))\$_phpc_args[$param]=$subject;";
      }
      else {
        $subject=$valueStr!=""?$valueStr:"true";
        $result.="\$_phpc_args[$param]=$subject;";
      }
    }
    return $prefix.$result;
  }

  function compileTemplateVars($name, $fragment)
  {
    global $language;
    $result="";
    $fragments=$this->processTemplateVars($name,$fragment);
    foreach($fragments as $fragment) if(is_array($fragment)) {
      if($fragment["type"]=="var" || $fragment["type"]=="const") {
        $const=$fragment["type"]=="const";
        $parts=explode(":",$fragment["name"]);
        if($const && count($parts)>1)
          fatalError("fatal_compile",array(),format($language["fatal_reason_tag"],array($name,$fragment["content"])));
        $subject=($const?"":"\$").array_shift($parts);
        foreach($parts as $part) $subject.="[".quoteText($part)."]";
        $params=array();
        foreach($fragment["params"][0] as $paramIndex=>$param) {
          $param=quoteText($param);
          if($fragment["params"][1][$paramIndex]!="")
            fatalError("fatal_compile",array(),format($language["fatal_reason_varref"],array($name,$fragment["content"])));
          $value=$fragment["params"][2][$paramIndex];
          if($value=="") $value="true";
          eval("\$params[$param]=$value;");
        }
        $method=$const?"defined":"isset";
        $source=$const?quoteText($subject):$subject;
        $filter=true;
        $suspicious=false;
        foreach($params as $param=>$value) switch($param) {
          case "integer": $subject="formatInteger($subject)"; break;
          case "float": $subject="formatFloat($subject)"; break;
          case "length": $subject="strlen($subject)"; break;
          case "count": $subject="count($subject)"; break;
          case "email": $subject="antispamText($subject)"; break;
          case "uppercase": $subject="phpcstrtoupper($subject)"; break;
          case "lowercase": $subject="phpcstrtolower($subject)"; break;
          case "titlecase": $subject="phpcucfirst($subject)"; break;
          case "wordscase": $subject="phpcucwords($subject)"; break;
          case "trim":
          case "ltrim":
          case "rtrim":
            $charlist=$value!==true?",".quoteText($value):"";
            $subject="$param($subject$charlist)";
            break;
          case "suspicious": $suspicious=true; break;
          case "textarea": $subject="htmlspecialchars($subject)"; $filter=false; break;
          case "nofilter": $filter=false; break;
          default: if(isset($language["format_datetime"][$param])) {
            $param=quoteText($param);
            $subject="phpcdate(\$language[\"format_datetime\"][$param],$subject)";
          }
        }
        $suspicious=$suspicious?",true":"";
        if($filter) $subject="filterText($subject$suspicious)";
        $result.="if($method($source))echo $subject;";
        if(isset($params["default"]))
          $result.="else echo".quoteText($params["default"]).";";
      }
      else {
        $result.=$this->compileTemplateParams($fragment["params"]);
        $templateName=quoteText($name);
        $tagName=quoteText($fragment["name"]);
        $result.="\$compiler->processWrite($templateName,$tagName,\$_phpc_args);";
      }
    }
    else $result.=$this->compileTemplateFragment($fragment);
    return $result;
  }

  function compileTemplateContents($tid, $name, $fragment)
  {
    $result="";
    $fragments=$this->processTemplateContents($fragment);
    foreach($fragments as $fragment) if(is_array($fragment)) {
      $result.=
        "\$_phpc_stack=\$_phpc_scope;".
        "\$_phpc_name=array_pop(\$_phpc_stack);".
        "\$_phpc_name=\"_phpc{$tid}_exec\$_phpc_name[1]\";".
        "\$_phpc_name(\$_phpc_stack);";
    }
    else $result.=$this->compileTemplateVars($name,$fragment);
    return $result;
  }

  function compileTemplateTags($tid, $id, $task, &$queue, &$queueMap)
  {
    global $language;
    if(!count($task["body"])) return "function _phpc{$tid}_exec$id(\$scope){}\r\n";
    $globals="\$".implode(",\$",$task["globals"]);
    $result=
      "function _phpc{$tid}_exec$id(\$_phpc_scope){".
      "global $globals;extract(\$_phpc_scope[count(\$_phpc_scope)-1][0]);";
    $index=0;
    while($index<count($task["body"])) {
      $fragment=$task["body"][$index];
      if(!is_array($fragment)) {
        $result.=$this->compileTemplateContents($tid,$task["name"],$fragment);
        $index++;
        continue;
      }
      $result.=$this->compileTemplateParams($fragment["params"]);
      $nestingBody=array_slice($task["body"],$index+1,max($fragment["delta"]-1,0));
      if(count($nestingBody)) {
        $nextTask=array(
          "name"=>$task["name"],
          "body"=>$nestingBody,
          "globals"=>$task["globals"],
          "content"=>$task["content"]);
        $queue[]=$nextTask;
        $contentHandler=count($queue);
      }
      else $contentHandler=0;
      if($fragment["type"]=="insert") {
        $name=$fragment["name"];
        if(!isset($queueMap[$name])) {
          $template=$this->getTemplate($name);
          if(!$template) fatalError("fatal_compile",array(),format($language["fatal_reason_notemplateinsert"],array($name,$task["name"],$fragment["content"])));
          $template=$this->processTemplateAreas($template);
          $nextTask=array(
            "name"=>$template["name"],
            "body"=>$this->processTemplateTags($template),
            "globals"=>$template["globals"],
            "content"=>$contentHandler);
          $queue[]=$nextTask;
          $handler=count($queue);
          $queueMap[$name]=$handler;
        }
        else $handler=$queueMap[$name];
        $result.=
          "\$_phpc_stack=\$_phpc_scope;".
          "\$_phpc_stack[]=array(\$_phpc_args,$contentHandler);".
          "_phpc{$tid}_exec$handler(\$_phpc_stack);";
      }
      else {
        $templateName=quoteText($task["name"]);
        $tagName=quoteText($fragment["name"]);
        $contentExecutor=quoteText("_phpc{$tid}_exec$contentHandler");
        $result.="\$compiler->processLogic($templateName,$tagName,\$_phpc_args,\$_phpc_scope,$contentExecutor);";
      }
      $index+=$fragment["delta"]+1;
    }
    $result.="}\r\n";
    return $result;
  }

  function compileTemplate($name)
  {
    global $language;
    $template=$this->getTemplate($name);
    if(!$template) fatalError("fatal_compile",array(),format($language["fatal_reason_notemplate"],array($name,$this->page["name"])));
    $template=$this->processTemplateAreas($template);
    $tid=$template["id"];
    $result=
      "if(function_exists(\"_phpc{$tid}_exec1\"))".
      "{_phpc{$tid}_exec1(array(array(\$scope,0)));return;}\r\n".
      "function _phpc{$tid}_exec0(\$scope){}\r\n";
    $firstTask=array(
      "name"=>$template["name"],
      "body"=>$this->processTemplateTags($template),
      "globals"=>$template["globals"],
      "content"=>0);
    $queue=array($firstTask);
    $queueMap=array($name=>1);
    $done=0;
    while($done<count($queue)) $result.=
      $this->compileTemplateTags($tid,$done+1,$queue[$done++],$queue,$queueMap);
    $result.="_phpc{$tid}_exec1(array(array(\$scope,0)));";
    return $result;
  }

  function processHeaders()
  {
    global $language;
    if(ini_get("expose_php")) @header("X-Generated-By: PHPC/".phpcversion());
    if($this->page["name"]==CompilerErrorPage) sendStatus(404);
    if(CompilerNoCacheHeaders) {
      @header("Cache-Control: no-store, no-cache, must-revalidate");
      @header("Pragma: no-cache");
    }
    @header("Content-Type: text/html; charset=$language[charset]");
  }

  function processTemplate($name, $scope=array())
  {
    global $optimizer;
    if($name=="") return;
    if(!isset($this->style["id"])) fatalError("fatal_nostyle");
    $styleid=$this->style["id"];
    $handle=$optimizer->getTemplate($styleid,$name);
    if(!$handle) $handle=$optimizer->addTemplate($styleid,$name,$this->compileTemplate($name));
    gzipCompressionStart();
    $this->processHeaders();
    $optimizer->executeTemplate($handle,$scope);
    halt();
  }

  function captureTemplate($name, $scope=array())
  {
    global $optimizer;
    if(!isset($this->style["id"])) fatalError("fatal_nostyle");
    $styleid=$this->style["id"];
    $handle=$optimizer->getTemplate($styleid,$name);
    if(!$handle) $handle=$optimizer->addTemplate($styleid,$name,$this->compileTemplate($name));
    ob_start();
    $optimizer->executeTemplate($handle,$scope);
    return ob_get_clean();
  }

  function interceptTemplate($name, $scope=array())
  {
    return $this->captureTemplate($name,$scope);
  }

  function compileBundles($bundles)
  {
    global $language;
    $classes="language,settings,fileSystem,mailSystem,database,formatter,optimizer,compiler";
    $classes=explode(",",$classes);
    $globals=array();
    foreach($classes as $class) $globals[$class]=true;
    $result=array("plugins"=>array(),"content"=>array());
    foreach($bundles as $name) {
      $bundle=$this->getBundle($name);
      if(!$bundle) fatalError("fatal_compile",array(),format($language["fatal_reason_nobundle"],array($name,$this->page["name"])));
      $pattern="{(?<![\\\\\\\$])\\\$(\w+)->}";
      preg_match_all($pattern,$bundle["content"],$matches);
      foreach($matches[1] as $global) $globals[$global]=true;
      $result["plugins"]=array_merge($result["plugins"],$bundle["plugins"]);
      $result["content"][]=$bundle["content"];
    }
    $globals="\$".implode(",\$",array_keys($globals));
    $header="global $globals;\r\n";
    $footer="\r\nunset($globals);";
    $result["plugins"]=array_values($result["plugins"]);
    $result["content"]=$header.implode("\r\n",$result["content"]).$footer;
    return $result;
  }

  function processBundles()
  {
    global $optimizer;
    $scope=array(
      "currentStyle"=>$this->style,
      "currentSession"=>$this->session,
      "currentPage"=>$this->page);
    return $optimizer->executeBundles($this->bundles,$scope);
  }

  function getWriteTags()
  {
    $tags="link,anchor,cycle,options,format,trace";
    return explode(",",$tags);
  }

  function getLogicTags()
  {
    $tags=
      "present,notPresent,empty,notEmpty,equal,notEqual,less,greater,".
      "lessEqual,greaterEqual,regexp,notRegexp,test,iterator,admin,notAdmin,".
      "local,notLocal";
    return explode(",",$tags);
  }

  function processWrite($templateName, $tagName, $params)
  {
    global $formatter;
    if($tagName=="link" || $tagName=="anchor") {
      $page=ifset($params["property"],"/");
      $filter=!isset($params["nofilter"]);
      unset($params["property"],$params["nofilter"]);
      if($tagName=="anchor") {
        $target=ifset($params["target"],"");
        $content=ifset($params["content"],"");
        unset($params["target"],$params["content"]);
      }
      if(isset($params["params"]) && is_array($params["params"])) {
        $extra=$params["params"];
        unset($params["params"]);
        $params+=$extra;
      }
      $link=$this->createLink($page,$params);
      if($filter) $link=filterText($link,true);
      if($tagName=="anchor") $link=$target!=""?
        format(PredefinedLinkTarget,array($link,$target,$content)):
        format(PredefinedLinkDefault,array($link,$content));
      echo URL_base . $link;
      return;
    }
    if($tagName=="cycle") {
      static $cycleCache=array();
      $property=(string)ifset($params["property"],"");
      unset($params["property"]);
      if(!isset($cycleCache[$property]))
        $cycleCache[$property]=0; else $cycleCache[$property]++;
      if(!count($params)) return;
      $params=array_values($params);
      echo $params[$cycleCache[$property]%count($params)];
      return;
    }
    if($tagName=="options") {
      $property=ifset($params["property"],array());
      if(!is_array($property)) $property=array($property);
      $selected=ifset($params["selected"]);
      $indent=(int)ifset($params["indent"],0);
      $indent=str_repeat(" ",$indent);
      $nokeys=isset($params["nokeys"]);
      $filter=!isset($params["nofilter"]);
      $separator="";
      foreach($property as $key=>$value) {
        if($nokeys) $key=$value;
        if($filter) $value=filterText($value);
        if($selected===false) $selected=$key;
        $text=$key==$selected?PredefinedOptionSelected:PredefinedOptionDefault;
        $text=format($text,array(htmlspecialchars($key),$value));
        echo $separator.$indent.$text;
        $separator="\r\n";
      }
      return;
    }
    if($tagName=="format") {
      $text=ifset($params["property"],"");
      $classes=ifset($params["class"],"");
      if(isset($params["strict"])) $text=optimizeTextStrict($text);
      if(isset($params["limit"])) $text=chopText($text,(int)$params["limit"]);
      $text=$formatter->processClasses($text,$classes,$params);
      if(isset($params["wrap"]))
        $text=wordwrap($text,(int)$params["wrap"],"\r\n");
      echo $text;
      return;
    }
    if($tagName=="trace") {
      $property=ifset($params["property"]);
      trace($property);
    }
  }

  function processLogic($templateName, $tagName, $params, $paramsStack, $contentExecutor)
  {
    $tags=array("present","notPresent","empty","notEmpty");
    if(in_array($tagName,$tags)) {
      switch($tagName) {
        case "present": $success=isset($params["property"]); break;
        case "notPresent": $success=!isset($params["property"]); break;
        case "empty": $success=!isset($params["property"]) || !$params["property"]; break;
        case "notEmpty": $success=isset($params["property"]) && $params["property"]; break;
      }
      if($success) {
        if(isset($params["then"])) echo $params["then"];
        $contentExecutor($paramsStack);
      }
      else if(isset($params["else"])) echo $params["else"];
      return;
    }
    $tags=array("equal","notEqual","less","greater","lessEqual","greaterEqual","regexp","notRegexp");
    if(in_array($tagName,$tags)) {
      if(isset($params["property"]) && isset($params["value"]))
        switch($tagName) {
          case "equal": $success=$params["property"]==$params["value"]; break;
          case "notEqual": $success=$params["property"]!=$params["value"]; break;
          case "less": $success=$params["property"]<$params["value"]; break;
          case "greater": $success=$params["property"]>$params["value"]; break;
          case "lessEqual": $success=$params["property"]<=$params["value"]; break;
          case "greaterEqual": $success=$params["property"]>=$params["value"]; break;
          case "regexp": $success=@preg_match($params["value"],$params["property"]); break;
          case "notRegexp": $success=!@preg_match($params["value"],$params["property"]); break;
        }
        else $success=false;
      if($success) {
        if(isset($params["then"])) echo $params["then"];
        $contentExecutor($paramsStack);
      }
      else if(isset($params["else"])) echo $params["else"];
      return;
    }
    if($tagName=="test") {
      if(!function_exists("phpcLogicTestHandler")) {
        function phpcLogicTestHandler($condition)
          { return @eval("return (boolean)($condition);"); }
      }
      $success=isset($params["property"]) &&
        phpcLogicTestHandler($params["property"]);
      if($success) {
        if(isset($params["then"])) echo $params["then"];
        $contentExecutor($paramsStack);
      }
      else if(isset($params["else"])) echo $params["else"];
      return;
    }
    if($tagName=="iterator") {
      $source=ifset($params["property"],array());
      if(!is_array($source)) $source=array($source);
      $sourceKeys=array_keys($source);
      $count=count($sourceKeys);
      if(isset($params["offset"])) $offset=(int)$params["offset"];
      else if(isset($params["chunk"]) && isset($params["count"]))
        $offset=(int)$params["chunk"]*(int)$params["count"];
      else if(isset($params["start"])) {
        for($index=0; $index<$count; $index++)
          if($sourceKeys[$index]==$params["start"]) break;
        $offset=$index;
      }
      else $offset=0;
      $limit=(int)ifset($params["count"],$count);
      for($processed=0; $processed<$limit; $processed++) {
        $index=$offset+$processed;
        if($index<0) continue;
        if($index>=$count) break;
        $key=$sourceKeys[$index];
        if(isset($params["stop"]) && $params["stop"]==$key) break;
        $paramsStackRef=&$paramsStack[count($paramsStack)-1][0];
        if(isset($params["item"])) $paramsStackRef[$params["item"]]=$source[$key];
        if(isset($params["key"])) $paramsStackRef[$params["key"]]=$key;
        if(isset($params["index"])) $paramsStackRef[$params["index"]]=$index;
        if(isset($params["index0"])) $paramsStackRef[$params["index0"]]=$index?$index:"";
        if(isset($params["index1"])) $paramsStackRef[$params["index1"]]=$index+1;
        if($processed && isset($params["separator"])) echo $params["separator"];
        $contentExecutor($paramsStack);
      }
      return;
    }
    if($tagName=="admin" || $tagName=="notAdmin") {
      $success=(isAdministrator() xor $tagName=="notAdmin");
      if($success) {
        if(isset($params["then"])) echo $params["then"];
        $contentExecutor($paramsStack);
      }
      else if(isset($params["else"])) echo $params["else"];
      return;
    }
    if($tagName=="local" || $tagName=="notLocal") {
      $success=(isLocalhost() xor $tagName=="notLocal");
      if($success) {
        if(isset($params["then"])) echo $params["then"];
        $contentExecutor($paramsStack);
      }
      else if(isset($params["else"])) echo $params["else"];
      return;
    }
  }

  function standardError($message)
  {
    $this->processTemplate("standardError",compact("message"));
  }

  function standardRedirect($message, $link="/")
  {
    $this->processTemplate("standardRedirect",compact("message","link"));
  }
}

?>
