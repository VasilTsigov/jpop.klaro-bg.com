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

$colname_rsGraph1corr = "-1";
if (isset($_GET['mnscrpt_id'])) {
  $colname_rsGraph1corr = (get_magic_quotes_gpc()) ? $_GET['mnscrpt_id'] : addslashes($_GET['mnscrpt_id']);
}
mysql_select_db($database_localhost, $localhost);
$query_rsGraph1corr = sprintf("SELECT mnscrpts.mscrID,  mnscrpts.mscrGraphCorr_1 FROM mnscrpts WHERE mnscrpts.mscrID=%s ", GetSQLValueString($colname_rsGraph1corr, "int"));
$rsGraph1corr = mysql_query($query_rsGraph1corr, $localhost) or die(mysql_error());
$row_rsGraph1corr = mysql_fetch_assoc($rsGraph1corr);
$totalRows_rsGraph1corr = mysql_num_rows($rsGraph1corr);?>
<?php
WA_DFP_SetupDownloadStatusStruct("WA_DownloadResult1");
if(!($totalRows_rsGraph1corr == 0)){
	WA_DFP_DownloadFile("WA_DownloadResult1", "../files/graphs/", "".$row_rsGraph1corr['mscrGraphCorr_1']  ."", "[FileName]", false, false, false, "", "", "", "");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
</body>
</html>
<?php
mysql_free_result($rsGraph1corr);
?>
