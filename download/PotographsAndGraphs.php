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

$colname_rsPhotosAndGraphs = "-1";
if (isset($_GET['download'])) {
  $colname_rsPhotosAndGraphs = (get_magic_quotes_gpc()) ? $_GET['download'] : addslashes($_GET['download']);
}
mysql_select_db($database_localhost, $localhost);
$query_rsPhotosAndGraphs = sprintf("SELECT mnscrpts.mscrID, mnscrpts.mscrFotos_1, mnscrpts.mscrFotos_2, mnscrpts.mscrFotos_3, mnscrpts.mscrFotos_4, mnscrpts.mscrFotos_5, mnscrpts.mscrFotos_6, mnscrpts.mscrFotos_7, mnscrpts.mscrFotos_8, mnscrpts.mscrFotos_9, mnscrpts.mscrFotos_10, mnscrpts.mscrGraph_1, mnscrpts.mscrGraph_2, mnscrpts.mscrGraph_3, mnscrpts.mscrGraph_4, mnscrpts.mscrGraph_5 FROM mnscrpts WHERE mnscrpts.mscrID=%s", GetSQLValueString($colname_rsPhotosAndGraphs, "int"));
$rsPhotosAndGraphs = mysql_query($query_rsPhotosAndGraphs, $localhost) or die(mysql_error());
$row_rsPhotosAndGraphs = mysql_fetch_assoc($rsPhotosAndGraphs);
$totalRows_rsPhotosAndGraphs = mysql_num_rows($rsPhotosAndGraphs);?>
<?php
WA_DFP_SetupDownloadStatusStruct("WA_DownloadResult1");
if(!($totalRows_rsPhotosAndGraphs == 0)){
	WA_DFP_DownloadFile("WA_DownloadResult1", "../files/photographs/", "".$row_rsPhotosAndGraphs['mscrFotos_1']  ."", "[FileName]", false, false, false, "", "", "", "");
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
mysql_free_result($rsPhotosAndGraphs);
?>
