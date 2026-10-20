<?php require_once('../Connections/localhost.php'); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php require_once("../webassist/database_management/wa_appbuilder_php.php"); ?>
<?php include("../scripts/zeit.php"); ?>
<?php
if (!WA_Auth_RulePasses("Autors_Reviewer_and_Editors")){
	WA_Auth_RestrictAccess("../users/users_LogIn.php?checkout=1");
}

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

$colname_rsCorrectedFigures = "-1";
if (isset($_GET['mnscrpt_id'])) {
  $colname_rsCorrectedFigures = (get_magic_quotes_gpc()) ? $_GET['mnscrpt_id'] : addslashes($_GET['mnscrpt_id']);
}
mysql_select_db($database_localhost, $localhost);
$query_rsCorrectedFigures = sprintf("SELECT mnscrpts.mscrCodeU, mnscrpts.mscrFotos_1, mnscrpts.mscrFotos_2, mnscrpts.mscrFotos_3, mnscrpts.mscrFotos_4, mnscrpts.mscrFotos_5, mnscrpts.mscrFotos_6, mnscrpts.mscrFotos_7, mnscrpts.mscrFotos_8, mnscrpts.mscrFotos_9, mnscrpts.mscrFotos_10, mnscrpts.mscrGraph_1, mnscrpts.mscrGraph_2, mnscrpts.mscrGraph_3, mnscrpts.mscrGraph_5, mnscrpts.mscrGraph_4, mnscrpts.mscrID, mnscrpts.mscrFotoCorr_1, mnscrpts.mscrFotoCorr_2, mnscrpts.mscrFotoCorr_3, mnscrpts.mscrFotoCorr_4, mnscrpts.mscrFotoCorr_5, mnscrpts.mscrFotoCorr_6, mnscrpts.mscrFotoCorr_7, mnscrpts.mscrFotoCorr_8, mnscrpts.mscrFotoCorr_9, mnscrpts.mscrFotoCorr_10, mnscrpts.mscrGraphCorr_1, mnscrpts.mscrGraphCorr_2, mnscrpts.mscrGraphCorr_3, mnscrpts.mscrGraphCorr_4, mnscrpts.mscrGraphCorr_5 FROM mnscrpts WHERE mnscrpts.mscrID=%s", GetSQLValueString($colname_rsCorrectedFigures, "int"));
$rsCorrectedFigures = mysql_query($query_rsCorrectedFigures, $localhost) or die(mysql_error());
$row_rsCorrectedFigures = mysql_fetch_assoc($rsCorrectedFigures);
$totalRows_rsCorrectedFigures = mysql_num_rows($rsCorrectedFigures);
?>

<?php echo $row_rsCorrectedFigures['mscrCodeU']; ?>
<?php require_once("../webassist/file_manipulation/helperphp.php"); ?>
<?php
$query_rsCode = "SELECT MAX(mnscrpts.mscrCode)+1 AS code FROM mnscrpts";
$rsCode = mysql_query($query_rsCode, $localhost) or die(mysql_error());
$row_rsCode = mysql_fetch_assoc($rsCode);
$totalRows_rsCode = mysql_num_rows($rsCode);
?>
<?php
if (!session_id()) session_start();
if ("" == "")     {
  $_SESSION["randomConfirm"] = "".$_SESSION['randomConfirm']  ."";
}?>
<?php
// WA_UploadResult2 Params Start
$WA_UploadResult2_Params = array();
// WA_UploadResult2_1 Start
$WA_UploadResult2_Params["WA_UploadResult2_1"] = array(
	'UploadFolder' => "../files/photographs/",
	'FileName' => "JPOP".$row_rsCorrectedFigures['mscrCodeU'] ."_photo1(corrected)",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult2_1 End
// WA_UploadResult2 Params End?>
<?php
// WA_UploadResult3 Params Start
$WA_UploadResult3_Params = array();
// WA_UploadResult3_1 Start
$WA_UploadResult3_Params["WA_UploadResult3_1"] = array(
	'UploadFolder' => "../files/photographs/",
	'FileName' => "JPOP".$row_rsCorrectedFigures['mscrCodeU']  ."_photo2(corrected)",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult3_1 End
// WA_UploadResult3 Params End?>
<?php
// WA_UploadResult4 Params Start
$WA_UploadResult4_Params = array();
// WA_UploadResult4_1 Start
$WA_UploadResult4_Params["WA_UploadResult4_1"] = array(
	'UploadFolder' => "../files/photographs/",
	'FileName' => "JPOP".$row_rsCorrectedFigures['mscrCodeU']  ."_photo3(corrected)",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult4_1 End
// WA_UploadResult4 Params End?>
<?php
// WA_UploadResult5 Params Start
$WA_UploadResult5_Params = array();
// WA_UploadResult5_1 Start
$WA_UploadResult5_Params["WA_UploadResult5_1"] = array(
	'UploadFolder' => "../files/photographs/",
	'FileName' => "JPOP".$row_rsCorrectedFigures['mscrCodeU']  ."_photo4(corrected)",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult5_1 End
// WA_UploadResult5 Params End?>
<?php
// WA_UploadResult6 Params Start
$WA_UploadResult6_Params = array();
// WA_UploadResult6_1 Start
$WA_UploadResult6_Params["WA_UploadResult6_1"] = array(
	'UploadFolder' => "../files/photographs/",
	'FileName' => "JPOP".$row_rsCorrectedFigures['mscrCodeU']  ."_photo5(corrected)",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult6_1 End
// WA_UploadResult6 Params End?>
<?php
// WA_UploadResult7 Params Start
$WA_UploadResult7_Params = array();
// WA_UploadResult7_1 Start
$WA_UploadResult7_Params["WA_UploadResult7_1"] = array(
	'UploadFolder' => "../files/photographs/",
	'FileName' => "JPOP".$row_rsCorrectedFigures['mscrCodeU']  ."_photo6(corrected)",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult7_1 End
// WA_UploadResult7 Params End?>
<?php
// WA_UploadResult8 Params Start
$WA_UploadResult8_Params = array();
// WA_UploadResult8_1 Start
$WA_UploadResult8_Params["WA_UploadResult8_1"] = array(
	'UploadFolder' => "../files/photographs/",
	'FileName' => "JPOP".$row_rsCorrectedFigures['mscrCodeU']  ."_photo7(corrected)",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult8_1 End
// WA_UploadResult8 Params End?>
<?php
// WA_UploadResult9 Params Start
$WA_UploadResult9_Params = array();
// WA_UploadResult9_1 Start
$WA_UploadResult9_Params["WA_UploadResult9_1"] = array(
	'UploadFolder' => "../files/photographs/",
	'FileName' => "JPOP".$row_rsCorrectedFigures['mscrCodeU']  ."_photo8(corrected)",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult9_1 End
// WA_UploadResult9 Params End?>
<?php
// WA_UploadResult10 Params Start
$WA_UploadResult10_Params = array();
// WA_UploadResult10_1 Start
$WA_UploadResult10_Params["WA_UploadResult10_1"] = array(
	'UploadFolder' => "../files/photographs/",
	'FileName' => "JPOP".$row_rsCorrectedFigures['mscrCodeU']  ."_photo9(corrected)",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult10_1 End
// WA_UploadResult10 Params End?>
<?php
// WA_UploadResult11 Params Start
$WA_UploadResult11_Params = array();
// WA_UploadResult11_1 Start
$WA_UploadResult11_Params["WA_UploadResult11_1"] = array(
	'UploadFolder' => "../files/photographs/",
	'FileName' => "JPOP".$row_rsCorrectedFigures['mscrCodeU']  ."_photo10(corrected)",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult11_1 End
// WA_UploadResult11 Params End?>
<?php
// WA_UploadResult12 Params Start
$WA_UploadResult12_Params = array();
// WA_UploadResult12_1 Start
$WA_UploadResult12_Params["WA_UploadResult12_1"] = array(
	'UploadFolder' => "../files/graphs/",
	'FileName' => "JPOP".$row_rsCorrectedFigures['mscrCodeU']  ."_graph1(corrected)",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult12_1 End
// WA_UploadResult12 Params End?>
<?php
// WA_UploadResult13 Params Start
$WA_UploadResult13_Params = array();
// WA_UploadResult13_1 Start
$WA_UploadResult13_Params["WA_UploadResult13_1"] = array(
	'UploadFolder' => "../files/graphs/",
	'FileName' => "JPOP".$row_rsCorrectedFigures['mscrCodeU']  ."_graph2(corrected)",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult13_1 End
// WA_UploadResult13 Params End?>
<?php
// WA_UploadResult14 Params Start
$WA_UploadResult14_Params = array();
// WA_UploadResult14_1 Start
$WA_UploadResult14_Params["WA_UploadResult14_1"] = array(
	'UploadFolder' => "../files/graphs/",
	'FileName' => "JPOP".$row_rsCorrectedFigures['mscrCodeU']  ."_graph3(corrected)",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult14_1 End
// WA_UploadResult14 Params End?>
<?php
// WA_UploadResult15 Params Start
$WA_UploadResult15_Params = array();
// WA_UploadResult15_1 Start
$WA_UploadResult15_Params["WA_UploadResult15_1"] = array(
	'UploadFolder' => "../files/graphs/",
	'FileName' => "JPOP".$row_rsCorrectedFigures['mscrCodeU']  ."_graph4",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult15_1 End
// WA_UploadResult15 Params End?>
<?php
// WA_UploadResult16 Params Start
$WA_UploadResult16_Params = array();
// WA_UploadResult16_1 Start
$WA_UploadResult16_Params["WA_UploadResult16_1"] = array(
	'UploadFolder' => "../files/graphs/",
	'FileName' => "JPOP".$row_rsCorrectedFigures['mscrCodeU']  ."_graph5(corrected)",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult16_1 End
// WA_UploadResult16 Params End
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult2");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	WA_DFP_UploadFiles("WA_UploadResult2", "txtPhoto_1", "0", "", "false", $WA_UploadResult2_Params);
}
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult3");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	WA_DFP_UploadFiles("WA_UploadResult3", "txtPhoto_2", "0", "", "false", $WA_UploadResult3_Params);
}
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult4");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	WA_DFP_UploadFiles("WA_UploadResult4", "txtPhoto_3", "0", "", "false", $WA_UploadResult4_Params);
}
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult5");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	WA_DFP_UploadFiles("WA_UploadResult5", "txtPhoto_4", "0", "", "false", $WA_UploadResult5_Params);
}
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult6");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	WA_DFP_UploadFiles("WA_UploadResult6", "txtPhoto_5", "0", "", "false", $WA_UploadResult6_Params);
}
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult7");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	WA_DFP_UploadFiles("WA_UploadResult7", "txtPhoto_6", "0", "", "false", $WA_UploadResult7_Params);
}
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult8");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	WA_DFP_UploadFiles("WA_UploadResult8", "txtPhoto_7", "0", "", "false", $WA_UploadResult8_Params);
}
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult9");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	WA_DFP_UploadFiles("WA_UploadResult9", "txtPhoto_8", "0", "", "false", $WA_UploadResult9_Params);
}
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult10");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	WA_DFP_UploadFiles("WA_UploadResult10", "txtPhoto_9", "0", "", "false", $WA_UploadResult10_Params);
}
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult11");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	WA_DFP_UploadFiles("WA_UploadResult11", "txtPhoto_10", "0", "", "false", $WA_UploadResult11_Params);
}
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult12");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	WA_DFP_UploadFiles("WA_UploadResult12", "txtGraphs_1", "0", "", "false", $WA_UploadResult12_Params);
}
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult13");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	WA_DFP_UploadFiles("WA_UploadResult13", "txtGraphs_2", "0", "", "false", $WA_UploadResult13_Params);
}
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult14");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	WA_DFP_UploadFiles("WA_UploadResult14", "txtGraphs_3", "0", "", "false", $WA_UploadResult14_Params);
}
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult15");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	WA_DFP_UploadFiles("WA_UploadResult15", "txtGraphs_4", "0", "", "false", $WA_UploadResult15_Params);
}
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult16");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	WA_DFP_UploadFiles("WA_UploadResult16", "txtGraphs_5", "0", "", "false", $WA_UploadResult16_Params);
}
?>
<?php 
// WA DataAssist Insert
if ("" == "") // Trigger
{
  $WA_connection = $localhost;
  $WA_table = "log";
  $WA_sessionName = "log_logID";
  $WA_redirectURL = "";
  if (function_exists("rel2abs")) $WA_redirectURL = $WA_redirectURL?rel2abs($WA_redirectURL,dirname(__FILE__)):"";
  $WA_keepQueryString = false;
  $WA_fieldNamesStr = "logUser|logIP|logContent";
  $WA_fieldValuesStr = "".$_SESSION['UserFirstName']  ." ".$_SESSION['UserLastName']  .", ".$_SESSION['UserEmail']  ."" . $WA_AB_Split . "".((isset($_SERVER["REMOTE_ADDR"]))?$_SERVER["REMOTE_ADDR"]:"")  ."" . $WA_AB_Split . "Submit corrected figurs";
  $WA_columnTypesStr = "',none,''|',none,''|',none,''";
  $WA_fieldNames = explode("|", $WA_fieldNamesStr);
  $WA_fieldValues = explode($WA_AB_Split, $WA_fieldValuesStr);
  $WA_columns = explode("|", $WA_columnTypesStr);
  $WA_connectionDB = $database_localhost;
  mysql_select_db($WA_connectionDB, $WA_connection);
  @session_start();
  $insertParamsObj = WA_AB_generateInsertParams($WA_fieldNames, $WA_columns, $WA_fieldValues, -1);
  $WA_Sql = "INSERT INTO `" . $WA_table . "` (" . $insertParamsObj->WA_tableValues . ") VALUES (" . $insertParamsObj->WA_dbValues . ")";
  $MM_editCmd = mysql_query($WA_Sql, $WA_connection) or die(mysql_error());
  $_SESSION[$WA_sessionName] = mysql_insert_id($WA_connection);
  if ($WA_redirectURL != "")  {
    $WA_redirectURL = str_replace("[Insert_ID]",$_SESSION[$WA_sessionName],$WA_redirectURL);
    if ($WA_keepQueryString && $WA_redirectURL != "" && isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] !== "" && sizeof($_POST) > 0) {
      $WA_redirectURL .= ((strpos($WA_redirectURL, '?') === false)?"?":"&").$_SERVER["QUERY_STRING"];
    }
    header("Location: ".$WA_redirectURL);
  }
}
?>
<?php 
// WA Application Builder Update
if (($_SERVER["REQUEST_METHOD"] == "POST") && (isset($_SERVER["HTTP_REFERER"]) && strpos(urldecode($_SERVER["HTTP_REFERER"]), urldecode($_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"])) > 0) && isset($_POST)) // Trigger
{
  $WA_connection = $localhost;
  $WA_table = "mnscrpts";
  $WA_redirectURL = "mscr_Index.php";
  if (function_exists("rel2abs")) $WA_redirectURL = $WA_redirectURL?rel2abs($WA_redirectURL,dirname(__FILE__)):"";
  $WA_keepQueryString = false;
  $WA_indexField = "mscrID";
  $WA_fieldNamesStr = "mscrFotoCorr_1|mscrFotoCorr_2|mscrFotoCorr_3|mscrFotoCorr_4|mscrFotoCorr_5|mscrFotoCorr_6|mscrFotoCorr_7|mscrFotoCorr_8|mscrFotoCorr_9|mscrFotoCorr_10|mscrGraph_1|mscrGraph_2|mscrGraph_3|mscrGraph_4|mscrGraph_5";
  $WA_fieldValuesStr = "".$WA_DFP_UploadStatus["WA_UploadResult2"]["serverFileName"]  ."" . "|" . "".$WA_DFP_UploadStatus["WA_UploadResult3"]["serverFileName"]  ."" . "|" . "".$WA_DFP_UploadStatus["WA_UploadResult4"]["serverFileName"]  ."" . "|" . "".$WA_DFP_UploadStatus["WA_UploadResult5"]["serverFileName"]  ."" . "|" . "".$WA_DFP_UploadStatus["WA_UploadResult6"]["serverFileName"]  ."" . "|" . "".$WA_DFP_UploadStatus["WA_UploadResult7"]["serverFileName"]  ."" . "|" . "".$WA_DFP_UploadStatus["WA_UploadResult8"]["serverFileName"]  ."" . "|" . "".$WA_DFP_UploadStatus["WA_UploadResult9"]["serverFileName"]  ."" . "|" . "".$WA_DFP_UploadStatus["WA_UploadResult10"]["serverFileName"]  ."" . "|" . "".$WA_DFP_UploadStatus["WA_UploadResult11"]["serverFileName"]  ."" . "|" . "".$WA_DFP_UploadStatus["WA_UploadResult12"]["serverFileName"]  ."" . "|" . "".$WA_DFP_UploadStatus["WA_UploadResult13"]["serverFileName"]  ."" . "|" . "".$WA_DFP_UploadStatus["WA_UploadResult14"]["serverFileName"]  ."" . "|" . "".$WA_DFP_UploadStatus["WA_UploadResult15"]["serverFileName"]  ."" . "|" . "".$WA_DFP_UploadStatus["WA_UploadResult16"]["serverFileName"]  ."";
  $WA_columnTypesStr = "',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''";
  $WA_comparisonStr = "=|=|=|=|=|=|=|=|=|=|=|=|=|=|=";
  $WA_fieldNames = explode("|", $WA_fieldNamesStr);
  $WA_fieldValues = explode("|", $WA_fieldValuesStr);
  $WA_columns = explode("|", $WA_columnTypesStr);
  
  $WA_where_fieldValuesStr = "".$row_rsCorrectedFigures['mscrID']  ."";
  $WA_where_columnTypesStr = "none,none,NULL";
  $WA_where_comparisonStr = "=";
  $WA_where_fieldNames = explode("|", $WA_indexField);
  $WA_where_fieldValues = explode("|", $WA_where_fieldValuesStr);
  $WA_where_columns = explode("|", $WA_where_columnTypesStr);
  $WA_where_comparisons = explode("|", $WA_where_comparisonStr);
  
  $WA_connectionDB = $database_localhost;
  mysql_select_db($WA_connectionDB, $WA_connection);
  if (!session_id()) session_start();
  $updateParamsObj = WA_AB_generateInsertParams($WA_fieldNames, $WA_columns, $WA_fieldValues, -1);
  $WhereObj = WA_AB_generateWhereClause($WA_where_fieldNames, $WA_where_columns, $WA_where_fieldValues,  $WA_where_comparisons );
  $WA_Sql = "UPDATE `" . $WA_table . "` SET " . $updateParamsObj->WA_setValues . " WHERE " . $WhereObj->sqlWhereClause . "";
  $MM_editCmd = mysql_query($WA_Sql, $WA_connection) or die(mysql_error());
  if ($WA_redirectURL != "")  {
    if ($WA_keepQueryString && $WA_redirectURL != "" && isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] !== "" && sizeof($_POST) > 0) {
      $WA_redirectURL .= ((strpos($WA_redirectURL, '?') === false)?"?":"&").$_SERVER["QUERY_STRING"];
    }
    header("Location: ".$WA_redirectURL);
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/main.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Journal-pop - Submit Manuscripts</title>
<!-- InstanceEndEditable -->
<link href="../css/main.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<!-- InstanceParam name="home" type="text" value="" -->
<!-- InstanceParam name="contact" type="text" value="" -->
<!-- InstanceParam name="instructions" type="text" value="" -->
<!-- InstanceParam name="submit_paper" type="text" value="" -->
<!-- InstanceParam name="register" type="text" value="" -->
<!-- InstanceParam name="login" type="text" value="" -->
<!-- InstanceParam name="your_manuscript" type="text" value="" -->
<script type="text/javascript" src="../CSSMenuWriter/cssmw0/menu.js"></script>
<style type="text/css" media="all">
<!--
@import url("../CSSMenuWriter/cssmw0/menu.css");
-->
</style>
<!-- InstanceBeginEditable name="menu_ie" -->
<!--[if lte IE 6]>
<style type="text/css" media="all">
@import url("../CSSMenuWriter/cssmw0/menu_ie.css");
</style>

<![endif]-->
<script src="../webassist/progress_bar/jquery-blockui-formprocessing.js" type="text/javascript"></script>
<script src="http://code.jquery.com/jquery-1.6.1.js"></script>

<!-- InstanceEndEditable -->
<script type="text/javascript" src="../CSSMenuWriter/cssmw1/menu.js"></script>
<style type="text/css" media="all">
<!--
@import url("../CSSMenuWriter/cssmw1/menu.css");
-->
</style>
<!--[if lte IE 6]>
<style type="text/css" media="all">
@import url("../CSSMenuWriter/cssmw1/menu_ie.css");
</style>

<![endif]-->
<script type="text/javascript" src="../CSSMenuWriter/cssmw2/menu.js"></script>
<style type="text/css" media="all">
<!--
@import url("../CSSMenuWriter/cssmw2/menu.css");
a:link {
	color: #366;
	text-decoration: underline;
}
a:visited {
	text-decoration: underline;
	color: #366;
}
a:hover {
	text-decoration: none;
	color: #396;
}
a:active {
	text-decoration: underline;
	color: #396;
}
-->
</style>
<!--[if lte IE 6]>
<style type="text/css" media="all">
@import url("../CSSMenuWriter/cssmw2/menu_ie.css");
</style>

<![endif]-->
<style type="text/css" media="all">
<!--
@import url("../CSSMenuWriter/cssmw2/menu.css");
-->
</style>
<!-- InstanceParam name="submit_manuscript" type="text" value="current" -->
<!-- InstanceParam name="manuscript" type="text" value="" -->
</head>

<body>

<div id="container">
  <div id="header">
    
  </div>
  <div id="navigation">
    
<ul>
        <li><a href="../index.php" class="">Home</a></li> 
        <li><a href="../users/users_LogIn.php" class="">Login</a></li>
    <li><a href="mscr_Submit.php" class="current">Submit Manuscripts</a></li>        <li><a href="mscr_Index.php" class="">Manuscripts</a></li>
      <li><a href="../users/users_Register.php" class="" >Register</a></li>
     <li><a href="../instructions/instructions_Index.php" class="">Instructions</a></li>
       <li><a href="../contacts/contacts_Index.php" class="">Contact</a></li>
        
    </ul>
    
  </div>
  <div id="mainContainer">
  <div id="sidebarLeft">
    
    <div id="login_left_siedebar">
      <?php if(WA_Auth_RulePasses("Autors_Reviewer_and_Editors")){ // Begin Show Region ?>
        <a href="../profile/profile_Index.php"><?php echo $_SESSION['UserEmail']; ?></a> 
        <?php } // End Show Region ?>
    </div> 
    <div id="autors_menu">
    <?php if(WA_Auth_RulePasses("Autors")){ // Begin Show Region ?>
     
        <?php require_once("../CSSMenuWriter/cssmw2/menu.php"); ?>
      
     <?php } // End Show Region ?>
      </div>
      
      <div id="reviewers_menu">
        <?php if(WA_Auth_RulePasses("Reviewer")){ // Begin Show Region ?>
          <?php require_once("../CSSMenuWriter/cssmw1/menu.php"); ?>
          <?php } // End Show Region ?>
      </div>
    <div id="editors_menu">
      <?php if(WA_Auth_RulePasses("Editors")){ // Begin Show Region ?>
        <?php require_once("../CSSMenuWriter/cssmw0/menu.php"); ?>
        <?php } // End Show Region ?>
    </div>
    
<!-- InstanceBeginEditable name="LeftSidebar" -->
      
    <!-- InstanceEndEditable --></div>
    <div id="content"><!-- InstanceBeginEditable name="mainContent" -->
    <div id="mainContent"> <span class="title">Submit Corrected Figures
      <input name="dateNow" type="hidden" id="dateNow" value="<?php echo $datumZ; ?>" />
    </span>
      <hr /><br />
      <div id="form1_ProgressWrapper">
        <form action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="7%">&nbsp;</td>
              <td width="24%">&nbsp;</td>
              <td width="27%">&nbsp;</td>
              <td width="42%" valign="middle">&nbsp;</td>
            </tr>
            <tr>
              <td height="26" align="center" bgcolor="#CCCCCC"><strong>Photos:</strong></td>
              <td align="center" bgcolor="#CCCCCC"><strong>Uploaded Photos And Graphs:</strong></td>
              <td align="center" bgcolor="#CCCCCC"><strong>Corrected Photos And Graphs:</strong></td>
              <td align="center" valign="middle" bgcolor="#CCCCCC"><strong>Select And Upload Corrected Photos And Graphs:</strong></td>
            </tr>
            <tr>
              <td valign="middle">&nbsp;</td>
              <td align="center" valign="middle">&nbsp;</td>
              <td align="center" valign="middle">&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td align="right" valign="middle">Photo 1:</td>
              <td align="center" valign="middle"><a href="../download/photo1.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotos_1']; ?></a></td>
              <td align="center" valign="middle"><a href="../download/photo1corr.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotoCorr_1']; ?></a></td>
              <td><input name="txtPhoto_1" type="file" id="txtPhoto_1"  size="30" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle">Photo 2:</td>
              <td align="center" valign="middle"><a href="../download/photo2.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotos_2']; ?></a></td>
              <td align="center" valign="middle"><a href="../download/photo2corr.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotoCorr_2']; ?></a></td>
              <td><input name="txtPhoto_2" type="file" id="txtPhoto_2" size="30" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle">Photo 3:</td>
              <td align="center" valign="middle"><a href="../download/photo3.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotos_3']; ?></a></td>
              <td align="center" valign="middle"><a href="../download/photo3corr.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotoCorr_3']; ?></a></td>
              <td><input name="txtPhoto_3" type="file" id="txtPhoto_3" size="30" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle">Photo 4:</td>
              <td align="center" valign="middle"><a href="../download/photo4.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotos_4']; ?></a></td>
              <td align="center" valign="middle"><a href="../download/photo4curr.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotoCorr_4']; ?></a></td>
              <td><input name="txtPhoto_4" type="file" id="txtPhoto_4" size="30" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle">Photo 5:</td>
              <td align="center" valign="middle"><a href="../download/photo5.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotos_5']; ?></a></td>
              <td align="center" valign="middle"><a href="../download/photo5corr.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotoCorr_5']; ?></a></td>
              <td><input name="txtPhoto_5" type="file" id="txtPhoto_5" size="30" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle">Photo 6:</td>
              <td align="center" valign="middle"><a href="../download/photo6.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotos_6']; ?></a></td>
              <td align="center" valign="middle"><a href="../download/photo6corr.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotoCorr_6']; ?></a></td>
              <td><input name="txtPhoto_6" type="file" id="txtPhoto_6" size="30" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle">Photo 7:</td>
              <td align="center" valign="middle"><a href="../download/photo7.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotos_7']; ?></a></td>
              <td align="center" valign="middle"><a href="../download/photo7corr.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotoCorr_7']; ?></a></td>
              <td><input name="txtPhoto_7" type="file" id="txtPhoto_7" size="30" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle">Photo 8:</td>
              <td align="center" valign="middle"><a href="../download/photo8.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotos_8']; ?></a></td>
              <td align="center" valign="middle"><a href="../download/photo8corr.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotoCorr_8']; ?></a></td>
              <td><input name="txtPhoto_8" type="file" id="txtPhoto_8" size="30" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle">Photo 9:</td>
              <td align="center" valign="middle"><a href="../download/photo9.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotos_9']; ?></a></td>
              <td align="center" valign="middle"><a href="../download/photo9corr.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotoCorr_9']; ?></a></td>
              <td><input name="txtPhoto_9" type="file" id="txtPhoto_9" size="30" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle">Photo 10:</td>
              <td align="center" valign="middle"><a href="../download/photo10.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotos_10']; ?></a></td>
              <td align="center" valign="middle"><a href="../download/photo10corr.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrFotoCorr_10']; ?></a></td>
              <td><input name="txtPhoto_10" type="file" id="txtPhoto_10" size="30" /></td>
            </tr>
            <tr>
              <td align="right">&nbsp;</td>
              <td align="center">&nbsp;</td>
              <td align="center">&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td align="right" valign="middle">Graph 1:</td>
              <td align="center" valign="middle"><a href="../download/graph1.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrGraph_1']; ?></a></td>
              <td align="center" valign="middle"><a href="../download/graph1corr.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrGraphCorr_1']; ?></a></td>
              <td><input name="txtGraphs_1" type="file" id="txtGraphs_1" size="30" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle">Graph 2:</td>
              <td align="center" valign="middle"><a href="../download/graph2.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrGraph_2']; ?></a></td>
              <td align="center" valign="middle"><a href="../download/graph2corr.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrGraphCorr_2']; ?></a></td>
              <td><input name="txtGraphs_2" type="file" id="txtGraphs_2" size="30" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle">Graph 3:</td>
              <td align="center" valign="middle"><a href="../download/graph3.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrGraph_3']; ?></a></td>
              <td align="center" valign="middle"><a href="../download/graph3corr.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrGraphCorr_3']; ?></a></td>
              <td><input name="txtGraphs_3" type="file" id="txtGraphs_3" size="30" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle">Graph 4:</td>
              <td align="center" valign="middle"><a href="../download/graph4.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrGraph_4']; ?></a></td>
              <td align="center" valign="middle"><a href="../download/graph4corr.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrGraphCorr_4']; ?></a></td>
              <td><input name="txtGraphs_4" type="file" id="txtGraphs_4" size="30" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle">Graph 5:</td>
              <td align="center" valign="middle"><a href="../download/graph5.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrGraph_5']; ?></a></td>
              <td align="center" valign="middle"><a href="../download/graph5corr.php?mnscrpt_id=<?php echo $row_rsCorrectedFigures['mscrID']; ?>"><?php echo $row_rsCorrectedFigures['mscrGraphCorr_5']; ?></a></td>
              <td><input name="txtGraphs_5" type="file" id="txtGraphs_5" size="30" /></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2" valign="middle"><input type="submit" name="btnSubmitMscr" id="btnSubmitMscr" value="Submit Corrected Figures" /></td>
              <td valign="middle"><a href="mscr_Index.php">Cancel</a></td>
              <td valign="middle">&nbsp;</td>
              </tr>
          </table>
        </form>
      </div>
    </div>
    <div id="form1_ProgressMessageWrapper" class="blockUIOverlay" style="display:none;">
      <script type="text/javascript">
WADFP_SetProgressToForm('form1', 'form1_ProgressMessageWrapper', WADFP_Theme_Options['BigSpin:Moab']);
    </script>
      <div id="form1_ProgressMessage" >
        <p style="margin:10px; padding:5px;" ><img src="../webassist/progress_bar/images/moab-largespin.gif" alt="" title="" style="vertical-align:middle;" />&nbsp;&nbsp;Please wait</p>
      </div>
    </div>
    <!-- InstanceEndEditable --></div>
  </div>
<div id="footer">Journal-POP &copy; 2011 - <?php echo date('Y'); ?></div>
</div>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsCorrectedFigures);
?>
