<?php

// PHP Compiler by Serge Igitov (Dagdamor), Version 2.4.5, Copyright 2008
// Released under the LGPL License (www.gnu.org/copyleft/lesser.html)

error_reporting(7);

require "config.php";
require "constant.php";
require "function.php";
require "database.php";

require "../plugins/captcha.php";

$database=new Database;

$captchaSupport->processImage();

?>
