<?php

// PHP Compiler  by Antylevsky Aleksei (Next)
// PHPC Online Plugin - Syntax Colorer v1.0  by Antylevsky Aleksei (Next)

/****************************** Class Definition ******************************/

class SyntaxColorer
{
  function getPhpcTemplateSyntax()
  {
    return array(
      array("pattern"=>"{<\?--.*?(?:--\?>|\$)}s","classes"=>"phpc_comment"),
      array("pattern"=>"{</?[A-Za-z_][\w\-]*:[\w\-:]*((?:\"(?:\\\\\\\\|\\\\\"|.)*?\"|.)*?)/?>}s",
        "classes"=>"phpc_tag,phpc_param","nested"=>array(
        array("pattern"=>"{\"(?:\\\\\\\\|\\\\\"|.)*?\"}s","classes"=>"phpc_value"))),
      array("pattern"=>"{<\?.*?\?>}s","classes"=>"html_default"),
      array("pattern"=>"{<!--.*?-->}s","classes"=>"html_comment"),
      array("pattern"=>"{<!.*?>}s","classes"=>"html_comment"),
      array("pattern"=>"{</?[A-Za-z_][\w\-]*(.*?)/?>}s",
        "classes"=>"html_tag,html_param","nested"=>array(
        array("pattern"=>"{\".*?\"|'.*?'}s","classes"=>"html_value"))));
  }

  function addTemplateSyntaxPart(&$content, $newpart)
  {
    $content["text"]=substr_replace($content["text"],"",$newpart["offset"],strlen($newpart["text"]));
    $newpart["nested"]=array();
    for($index=0; $index<count($content["nested"]); $index++) {
      $part=$content["nested"][$index];
      if($part["offset"]<=$newpart["offset"]) continue;
      if($part["offset"]>=$newpart["offset"]+strlen($newpart["text"])) continue;
      $part["offset"]-=$newpart["offset"];
      $newpart["nested"][]=$part;
      array_splice($content["nested"],$index--,1);
    }
    $position=false;
    for($index=0; $index<count($content["nested"]); $index++)
      if($content["nested"][$index]["offset"]>$newpart["offset"])
        { $position=$index; break; }
    if($position===false) $position=count($content["nested"]);
    for($index=$position; $index<count($content["nested"]); $index++)
      $content["nested"][$index]["offset"]-=strlen($newpart["text"]);
    array_splice($content["nested"],$position,0,array($newpart));
    return $position;
  }

  function analyseTemplateSyntax(&$content, $info)
  {
    $classes=explodeSmart(",",$info["classes"]);
    preg_match_all($info["pattern"],$content["text"],$matches,PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
    $diff=0;
    foreach($matches as $match) {
      $fragment=$match[0][0];
      $offset=$match[0][1]-$diff;
      $diff+=strlen($fragment);
      $newpart=array("offset"=>$offset,"text"=>$fragment,"class"=>$classes[0]);
      $position=$this->addTemplateSyntaxPart($content,$newpart);
      foreach($match as $blockIndex=>$block) if($blockIndex) {
        $subcontent=&$content["nested"][$position];
        $newpart=array("offset"=>$block[1]-$match[0][1],"text"=>$block[0],"class"=>$classes[$blockIndex]);
        $subposition=$this->addTemplateSyntaxPart($subcontent,$newpart);
        if(isset($info["nested"])) foreach($info["nested"] as $subinfo)
          $this->analyseTemplateSyntax($subcontent["nested"][$subposition],$subinfo);
      }
    }
  }

  function assembleTemplateSyntax($content)
  {
    $workspace=$content["text"];
    $result="";
    for($index=count($content["nested"])-1; $index>=0; $index--) {
      $part=$content["nested"][$index];
      $text=$this->assembleTemplateSyntax($part);
      $result=$text.htmlspecialchars(substr($workspace,$part["offset"])).$result;
      $workspace=substr($workspace,0,$part["offset"]);
    }
    $result=htmlspecialchars($workspace).$result;
    $result="<font class=\"$content[class]\">$result</font>";
    return $result;
  }

  function processPhpcTemplate($text)
  {
    $syntax=$this->getPhpcTemplateSyntax();
    $content=array("text"=>$text,"class"=>"html_default","nested"=>array());
    foreach($syntax as $info) $this->analyseTemplateSyntax($content,$info);
    $text=$this->assembleTemplateSyntax($content);
    $text=preg_replace("{<font class=\"[^\"]*?\"></font>}","",$text);
    return $text;
  }

  function processPhpcBundle($text)
  {
    ini_set("highlight.html","php_html");
    ini_set("highlight.comment","php_comment");
    ini_set("highlight.keyword","php_keyword");
    ini_set("highlight.default","php_default");
    ini_set("highlight.string","php_string");
    $text=highlight_string("<?php\r\n$text?>",true);
    $text=str_replace("\n","",$text);
    $text=str_replace("\r","\r\n",$text);
    $text=str_replace("<br />","",$text);
    $text=preg_replace("{<span style=\"color: ?(\w+)\">}","<font color=\"\\1\">",$text);
    $text=str_replace("</span>","</font>",$text);
    $text=preg_replace("{^<code><font color=\"php_html\">}","",$text);
    $text=preg_replace("{</font></code>\$}","",$text);
    $text=preg_replace("{^<font class=\"php_keyword\">&lt;\?</font><font class=\"php_default\">php}","<font class=\"php_default\">&lt;?php",$text);
    $text=preg_replace("{^<font color=\"php_default\">&lt;\?php\r\n}","<font color=\"php_default\">",$text);
    $text=preg_replace("{\?&gt;</font>\$}","</font>",$text);
    $text=preg_replace("{<font color=\"[^\"]*?\"></font>}","",$text);
    $text=str_replace("<font color=\"","<font class=\"",$text);
    return $text;
  }
}

$syntaxColorer=new SyntaxColorer;

?>
