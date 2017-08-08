<?php


// PHP Compiler by Antylevsky Aleksei (Next)

error_reporting(7);

if(!file_exists("config.php")) { header("Location: /install/"); exit; }

include 'count.php';

require "config.php";
require "constant.php";
require "language.php";
require "function.php";
require "filesyst.php";
require "mailsyst.php";
require "database.php";
require "format.php";
require "optimize.php";
require "compiler.php";
require "backcomp.php";

$fileSystem=new FileSystem;
$mailSystem=new MailSystem;
$database=new Database;
$formatter=new Formatter;
$optimizer=new Optimizer;
$compiler=new Compiler;






$plugins=$compiler->getPreloadPlugins();
foreach($plugins as $plugin) { require_once $plugin; }

$compiler->prepare();
foreach($compiler->plugins as $plugin) { require_once $plugin; }
$scope=$compiler->processBundles();
$compiler->processTemplate($compiler->template,$scope);

halt();



?>
