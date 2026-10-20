<?php require_once('../Connections/localhost.php'); ?>
<?php require_once("../webassist/file_manipulation/helperphp.php"); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$colname_rsProofFile = "-1";
if (isset($_GET['download'])) {
  $colname_rsProofFile = $_GET['download'];
}
mysql_select_db($database_localhost, $localhost);
$query_rsProofFile = sprintf("SELECT mnscrpts.mscrID, mnscrpts.mscrUploadProofFile FROM mnscrpts WHERE mnscrpts.mscrID=%s", GetSQLValueString($colname_rsProofFile, "int"));
$rsProofFile = mysql_query($query_rsProofFile, $localhost) or die(mysql_error());
$row_rsProofFile = mysql_fetch_assoc($rsProofFile);
$totalRows_rsProofFile = mysql_num_rows($rsProofFile);$colname_rsProofFile = "-1";
if (isset($_GET['download'])) {
  $colname_rsProofFile = $_GET['download'];
}
mysql_select_db($database_localhost, $localhost);
$query_rsProofFile = sprintf("SELECT mnscrpts.mscrID, mnscrpts.mscrUploadProofFile FROM mnscrpts WHERE mnscrpts.mscrID=%s", GetSQLValueString($colname_rsProofFile, "int"));
$rsProofFile = mysql_query($query_rsProofFile, $localhost) or die(mysql_error());
$row_rsProofFile = mysql_fetch_assoc($rsProofFile);
$totalRows_rsProofFile = mysql_num_rows($rsProofFile);?>
<?php
WA_DFP_SetupDownloadStatusStruct("WA_DownloadResult1");
if(!($totalRows_rsProofFile == 0)){
	WA_DFP_DownloadFile("WA_DownloadResult1", "../files/proof/", "".$row_rsProofFile['mscrUploadProofFile']  ."", "[FileName]", false, false, false, "", "", "", "");
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
</body>
</html>
<?php
mysql_free_result($rsProofFile);
?>
