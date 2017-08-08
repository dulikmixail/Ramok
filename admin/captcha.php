<?php

// PHPC Control Panel Plugin (10.0) - CAPTCHA Support v1.1 by Dagdamor

// PHP Compiler by Serge Igitov (Dagdamor), Version 2.4.5, Copyright 2008
// Released under the LGPL License (www.gnu.org/copyleft/lesser.html)

/*

// Main Menu Construction Code
if(!$database->isTablePresent("captcha")) {
  makeMenuGroup("header","captcha_menu");
  makeMenuItem("admin_install","captcha.php?action=install");
  makeMenuGroup("footer");
}

*/

require "global.php";

adminLog();
$action=acceptStringParameter("action");

makeAdminPage("header");

/********************************* Functions **********************************/

function createDefaultOptions()
{
  return array(
    array("title"=>"captcha_defoption1","description"=>"captcha_defoption1desc","name"=>"captchaImageWidth","value"=>200),
    array("title"=>"captcha_defoption2","description"=>"captcha_defoption2desc","name"=>"captchaImageHeight","value"=>50),
    array("title"=>"captcha_defoption3","description"=>"captcha_defoption3desc","name"=>"captchaImageColor","value"=>"FFFFFF"),
    array("title"=>"captcha_defoption4","description"=>"captcha_defoption4desc","name"=>"captchaCodeLength","value"=>5));
}

/**************************** Plugin Installation *****************************/

if($action=="install") {
  if($database->isTablePresent("captcha")) {
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
        $database->customQuery("CREATE TABLE captcha (".
          "hash TINYTEXT NOT NULL,".
          "code TINYTEXT NOT NULL,".
          "ipaddress TINYTEXT NOT NULL,".
          "dateline INT NOT NULL,".
          "counter INT NOT NULL,".
          "PRIMARY KEY (hash(50)))");
        makeNotification("admin_installtable","captcha");
        $success=true;
        break;
    }
    if($success) {
      installOptions("captcha_defgroup",createDefaultOptions());
      makeNotification("admin_installsuccess");
      makeBreak();
      makeRefreshMenuLink();
    }
    else makeWrongDBError();
  }
}

/******************************************************************************/

makeAdminPage("footer");

?>
