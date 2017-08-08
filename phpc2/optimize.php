<?php

// PHP Compiler  by Antylevsky Aleksei (Next)

class Optimizer
{
  function addTemplate($styleid, $name, $content)
  {
    global $database;
    $content=preg_replace("{\?>\r\n(?!\r\n)}","?>",$content);
    $content=preg_replace("{<\?php[ \r\n]+}","<?php ",$content);
    $content=preg_replace("{\?><\?php[ \r\n]+}"," ",$content);
    $compressed=CompilerCacheEnabled && CompilerCacheCompressionEnabled && function_exists("gzcompress");
    if($compressed) $content=gzcompress($content,CompilerCacheCompressionLevel);
    $template=$name;
    $result=compact("styleid","template","content","compressed");
    if(CompilerCacheEnabled) $database->addLine("cache",$result);
    return $result;
  }

  function getTemplate($styleid, $name)
  {
    global $database;
    if(!CompilerCacheEnabled) return false;
    $conditions="styleid=$styleid AND template=".slashes($name);
    return $database->getLine("cache",$conditions);
  }

  function executeTemplate($handle, $scope)
  {
    if(!$handle) return;
    $content=$handle["content"];
    eval($handle["compressed"]?gzuncompress($content):$content);
  }

  function addBundles($styleid, $names, $compilation)
  {
    return $compilation;
  }

  function getBundles($styleid, $names)
  {
    return count($names)?false:array("plugins"=>array(),"content"=>"");
  }

  function getBundlesPlugins($handle)
  {
    return $handle["plugins"];
  }

  function executeBundles($_phpc_handle, $_phpc_scope)
  {
    if(!$_phpc_handle) return array();
    extract($_phpc_scope);
    eval("unset(\$this,\$_phpc_handle,\$_phpc_scope);$_phpc_handle[content]");
    return get_defined_vars();
  }

  function clearCache()
  {
    global $database;
    $database->clearTable("cache");
  }

  function processFileCache($name, $time, $callback, $params=array())
  {
    global $fileSystem;
    if(!is_array($params)) $params=array($params);
    if(!FileCacheEnabled) return call_user_func_array($callback,$params);
    $filename=format(FileCacheFilename,$name);
    $content=$fileSystem->openFile($filename);
    if($content) $content=unserialize($content);
    if($content && $content["time"]>=phpctime()) return $content["data"];
    $result=call_user_func_array($callback,$params);
    $content=array("time"=>phpctime()+$time,"data"=>$result);
    $success=$fileSystem->saveFile($filename,serialize($content));
    if(!$success) fatalError("fatal_filecache");
    return $result;
  }

  function clearFileCache()
  {
    global $fileSystem;
    $extension=$fileSystem->getFileExtension(FileCacheFilename);
    $folder=dirname(FileCacheFilename)."/";
    $cache=$fileSystem->getFolder($folder,$extension);
    foreach($cache as $entry) $fileSystem->deleteFile($folder.$entry);
  }
}

?>
