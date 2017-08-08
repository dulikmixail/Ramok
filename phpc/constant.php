<?php

// PHP Compiler  by Antylevsky Aleksei (Next)

define("URL_base","");
define("PhpcLocalesList","en,ru");
define("PhpcPreloadPlugins","");

define("PhpcPasswordParam","phpcpassword");
define("PhpcPasswordCookie","phpcpassword");
define("PhpcLocaleCookie","phpclocale");
define("PhpcStyleCookie","phpcstyle");
define("PhpcSessionCookie","phpcsession");

define("PhpcSessionEnabled",true);
define("PhpcSessionUseCookies",true);
define("PhpcSessionUseURLs",false);
define("PhpcSessionCatchEnabled",true);
define("PhpcSessionCatchRestrictions","");
define("PhpcSessionValidator","");
define("PhpcSessionTimeout",600);
define("PhpcSessionGCProbability",1);
define("PhpcSessionGCDivisor",50);
define("PhpcSessionParamsLimit",200);

define("GzipCompressionEnabled",false);
define("GzipCompressionLevel",1);

define("FileCacheEnabled",true);
define("FileCacheFilename","/cache/%s.dat");

define("DatabaseQueryLogEnabled",false);
define("DatabaseStartupQueries","");
define("DatabaseRestrictedTables","adminlog,memberlog");

define("CompilerNoCacheHeaders",true);
define("CompilerErrorPage","404");
define("CompilerIndexPage","index");
define("CompilerComplexInherit","bundles,params");
define("CompilerPluginFilename","../plugins/%s.php");
define("CompilerEchoLimit",10);

define("CompilerCacheEnabled",false);
define("CompilerCacheCompressionEnabled",false);
define("CompilerCacheCompressionLevel",1);

define("MailHeadersNewline","\n");
define("MailMessageNewline","\n");
define("MailDebuggerEnabled",false);
define("MailDebuggerFilename","/mail/%s.txt");

/******************************************************************************/

define("OneMinute",60);
define("OneHour",3600);
define("OneDay",86400);
define("OneWeek",604800);
define("OneMonth",2592000);
define("OneYear",31536000);

define("FolderCreateAttributes",0777);
define("EmailAddressPattern","{^\w[\w\-.]*@\w[\w\-]*\.[\w\-.]*\w\$}");
define("UploadSafeExtensions",".gif,.jpg,.jpeg,.png");

define("PredefinedChop"," .,:;-!?([{/\t\r\n");
define("PredefinedDots","...");
define("PredefinedNewline","<br>");
define("PredefinedParagraphOpen","<p>");
define("PredefinedParagraphClose","</p>");
define("PredefinedLinkDefault","<a href=\"%s\">%s</a>");
define("PredefinedLinkTarget","<a href=\"%s\" target=\"%s\">%s</a>");
define("PredefinedOptionDefault","<option value=\"%s\">%s</option>");
define("PredefinedOptionSelected","<option value=\"%s\" selected=\"selected\">%s</option>");

define("FatalReport","<b>{header}</b> {text}<br>\r\n{error}{query}");
define("FatalReportError","<b>{header}</b> {text}<br>\r\n");
define("FatalReportQuery","<b>{header}</b> {text}<br>\r\n");

/******************************************************************************/

$timeOffsetServer=0;
$timeOffsetClient=0;

$uploadDefaultFormat=array("start"=>1,"digits"=>8,"prepend"=>"","append"=>"");

?>
