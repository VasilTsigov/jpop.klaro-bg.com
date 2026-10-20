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

$colname_rsProofEditorsLetter = "-1";
if (isset($_GET['download'])) {
  $colname_rsProofEditorsLetter = $_GET['download'];
}
mysql_select_db($database_localhost, $localhost);
$query_rsProofEditorsLetter = sprintf("SELECT mnscrpts.mscrID, mnscrpts.mscrUploadProofEditorsFile FROM mnscrpts WHERE mnscrpts.mscrID=%s", GetSQLValueString($colname_rsProofEditorsLetter, "int"));
$rsProofEditorsLetter = mysql_query($query_rsProofEditorsLetter, $localhost) or die(mysql_error());
$row_rsProofEditorsLetter = mysql_fetch_assoc($rsProofEditorsLetter);
$totalRows_rsProofEditorsLetter = mysql_num_rows($rsProofEditorsLetter);?>
<?php
WA_DFP_SetupDownloadStatusStruct("WA_DownloadResult1");
if(!($totalRows_rsProofEditorsLetter == 0)){
	WA_DFP_DownloadFile("WA_DownloadResult1", "../files/editletter/", "".$row_rsProofEditorsLetter['mscrUploadProofEditorsFile']  ."", "[FileName]", false, false, false, "", "", "", "");
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
mysql_free_result($rsProofEditorsLetter);
?>
