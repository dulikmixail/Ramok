<?php

// PHP Compiler  by Antylevsky Aleksei (Next)

class FileSystem
{
  function normalize(&$filename)
  {
    $filename=str_replace("\\","/",$filename);
    if(char($filename,0)=="/") $filename=$_SERVER["DOCUMENT_ROOT"].$filename;
  }

  function localize(&$filename)
  {
    $filename=str_replace("\\","/",$filename);
    $path=str_replace("\\","/",$_SERVER["DOCUMENT_ROOT"]);
    if(!phpcstrncasecmp($filename,$path,strlen($path)))
      $filename=substr($filename,strlen($path));
  }

  function validateFilename($filename)
  {
    $extension=phpcstrtolower($this->getFileExtension($filename));
    $filename=substr($filename,0,strlen($filename)-strlen($extension));
    $filename=str_replace("'","",$filename);
    $filename=preg_replace("{[^a-zA-Z0-9\-]}"," ",$filename);
    $filename=preg_replace("{ +}","_",trim($filename));
    if($filename=="") $filename="noname";
    if(!preg_match("{^\.[a-z0-9]+\$}",$extension)) $extension="";
    return $filename.$extension;
  }

  function incrementFilename($filename)
  {
    $extension=$this->getFileExtension($filename);
    $filename=substr($filename,0,strlen($filename)-strlen($extension));
    $filename=incrementIdentifier($filename);
    return $filename.$extension;
  }

  function openFile($filename, $normalize=true)
  {
    if($normalize) $this->normalize($filename);
    return (string)@file_get_contents($filename);
  }

  function saveFile($filename, $content, $normalize=true)
  {
    if($normalize) $this->normalize($filename);
    return @file_put_contents($filename,$content)==strlen($content);
  }

  function renameFile($oldfilename, $newfilename, $normalize=true)
  {
    if($normalize) $this->normalize($oldfilename);
    if($normalize) $this->normalize($newfilename);
    return @rename($oldfilename,$newfilename);
  }

  function deleteFile($filename, $normalize=true)
  {
    if($normalize) $this->normalize($filename);
    return @unlink($filename);
  }

  function isFileExists($filename, $normalize=true)
  {
    if($normalize) $this->normalize($filename);
    return file_exists($filename);
  }

  function getImageSize($filename, $normalize=true)
  {
    if($normalize) $this->normalize($filename);
    $size=@getimagesize($filename);
    if(!is_array($size) || !isset($size[0]) || !isset($size[1])) return false;
    return array("width"=>$size[0],"height"=>$size[1]);
  }

  function getFileExtension($filename)
  {
    $index=strrpos($filename,".");
    $break=strrpos($filename,"/");
    if($index===false || $break>$index) return "";
    return substr($filename,$index);
  }

  function createFolder($folder, $attrs=FolderCreateAttributes, $normalize=true)
  {
    if($normalize) $this->normalize($folder);
    $folder=rtrim($folder,"/");
    $umask=@umask(0);
    $result=@mkdir($folder,$attrs);
    @umask($umask);
    return $result;
  }

  function deleteFolder($folder, $normalize=true)
  {
    if($normalize) $this->normalize($folder);
    $folder=rtrim($folder,"/");
    return @rmdir($folder);
  }

  function getFolder($folder, $extensions=false, $normalize=true)
  {
    if($normalize) $this->normalize($folder);
    $folder=rtrim($folder,"/")."/";
    if($extensions!==false && !is_array($extensions))
      $extensions=array($extensions);
    $result=array();
    if(!$resource=@opendir($folder)) return $result;
    while($filename=@readdir($resource)) {
      if(!is_file($folder.$filename)) continue;
      $extension=phpcstrtolower($this->getFileExtension($filename));
      if($extensions===false || in_array($extension,$extensions))
        $result[]=$filename;
    }
    @closedir($resource);
    sort($result);
    return $result;
  }

  function getSubfolders($folder, $normalize=true)
  {
    if($normalize) $this->normalize($folder);
    $folder=rtrim($folder,"/")."/";
    $result=array();
    if(!$resource=@opendir($folder)) return $result;
    while($filename=@readdir($resource)) {
      if(!is_dir($folder.$filename) || char($filename,0)==".") continue;
      $result[]=$filename;
    }
    @closedir($resource);
    sort($result);
    return $result;
  }

  function isUploadAttempt($name)
  {
    if(!isset($_FILES[$name])) return false;
    return $_FILES[$name]["error"]!=UPLOAD_ERR_NO_FILE;
  }

  function getUploadedFile($name)
  {
    if(!isset($_FILES[$name]) || $_FILES[$name]["error"]) return false;
    if(!is_uploaded_file($_FILES[$name]["tmp_name"])) return false;
    $filename=trim(stripSlashesSmart($_FILES[$name]["name"]));
    $content=$this->openFile($_FILES[$name]["tmp_name"],false);
    $size=strlen($content);
    return $size?compact("filename","size","content"):false;
  }

  function processUploadedFile($name, $folder, $format=false, $extensions=UploadSafeExtensions, $convert=array(), $maxsize=false, $normalize=true)
  {
    $folder=rtrim($folder,"/")."/";
    if(!isset($_FILES[$name]) || $_FILES[$name]["error"]) return false;
    if(!is_uploaded_file($_FILES[$name]["tmp_name"])) return false;
    $size=$_FILES[$name]["size"];
    if(!$size || ($maxsize!==false && $size>$maxsize)) return false;
    $localname=trim(stripSlashesSmart($_FILES[$name]["name"]));
    $extension=phpcstrtolower($this->getFileExtension($localname));
    if($extensions!==false) {
      if(is_string($extensions)) $extensions=explodeSmart(",",$extensions);
      if(!in_array($extension,$extensions)) return false;
    }
    if(isset($convert[$extension])) $extension=$convert[$extension];
    if($format) {
      $index=$format["start"];
      do {
        $filename=str_pad($index++,$format["digits"],"0",STR_PAD_LEFT);
        $filename=$format["prepend"].$filename.$format["append"].$extension;
      } while($this->isFileExists($folder.$filename,$normalize));
    }
    else {
      $filename=$this->validateFilename($localname);
      while($this->isFileExists($folder.$filename,$normalize))
        $filename=$this->incrementFilename($filename);
    }
    $target=$folder.$filename;
    if($normalize) $this->normalize($target);
    if(!@move_uploaded_file($_FILES[$name]["tmp_name"],$target)) return false;
    return $folder.$filename;
  }
}

?>
