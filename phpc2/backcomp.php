<?php

// PHP Compiler  by Antylevsky Aleksei (Next)

if(defined("PhpcAdminPanel")) {
  define("APPROVAL_ACCEPT",ApprovalValueAccept);
  define("APPROVAL_REJECT",ApprovalValueReject);
  define("APPROVAL_DELETE",ApprovalValueDelete);
}

function administrator($script="") { return isAdministrator($script); }
function clientAddress() { return getClientAddress(); }
function equalArrays($array1, $array2) { return areArraysEqual($array1,$array2); }
function incrementalValue() { return getIncrementalValue(); }
function isBanned($blackList) { return isClientBanned($blackList); }
function trueInteger($value) { return isTrueInteger($value); }
function trueFloat($value) { return isTrueFloat($value); }

?>
