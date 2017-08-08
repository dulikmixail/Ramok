<?php

// PHP Compiler by Antylevsky Aleksei (Next)

error_reporting(7);

if(!file_exists("../phpc/config.php")) { header("Location: /install/"); exit; }

require "../phpc/config.php";
require "../phpc/constant.php";
require "../phpc/language.php";
require "../phpc/function.php";
require "../phpc/filesyst.php";
require "../phpc/mailsyst.php";
require "../phpc/database.php";
require "../phpc/format.php";
require "../phpc/optimize.php";
require "../phpc/compiler.php";
require "../phpc/backcomp.php";

require "constant.php";
require "controls.php";
require "function.php";

$fileSystem=new FileSystem;
$mailSystem=new MailSystem;
$database=new Database;
$formatter=new Formatter;
$optimizer=new Optimizer;
$compiler=new Compiler;

$plugins=$compiler->getPreloadPlugins();
foreach($plugins as $plugin) { require_once $plugin; }

processGlobalCache();
gzipCompressionStart();

if(!isAdministrator(basename($_SERVER["PHP_SELF"],".php"))) {
  makeAdminPage("header");
  makeAdminAuthorization();
  makeAdminPage("footer");
}

?>
