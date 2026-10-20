<?php if (!session_id()) session_start(); ?>
<?php require_once('../Connections/localhost.php'); ?>
<?php require_once("../webassist/form_validations/wavt_scripts_php.php"); ?>
<?php require_once("../webassist/form_validations/wavt_validatedform_php.php"); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php require_once("../webassist/database_management/wa_appbuilder_php.php"); ?>
<?php include("../scripts/zeit.php"); ?>
<?php
if (!WA_Auth_RulePasses("Autors_Reviewer_and_Editors")){
	WA_Auth_RestrictAccess("../users/users_LogIn.php?checkout=1");
}
?>
<?php require_once("../webassist/file_manipulation/helperphp.php"); ?>
<?php 
if ($_SERVER["REQUEST_METHOD"] == "POST")  {
  $WAFV_Redirect = "";
  $_SESSION['WAVT_mscrSubmit_Errors'] = "";
  if ($WAFV_Redirect == "")  {
    $WAFV_Redirect = $_SERVER["PHP_SELF"];
  }
  $WAFV_Errors = "";
  $WAFV_Errors .= WAValidateRQ(((isset($_POST["txtFullTitle"]))?$_POST["txtFullTitle"]:"") . "",false,2);
  $WAFV_Errors .= WAValidateRQ(((isset($_POST["txtAbstract"]))?$_POST["txtAbstract"]:"") . "",false,3);
  $WAFV_Errors .= WAValidateRQ(((isset($_POST["txtKeywords"]))?$_POST["txtKeywords"]:"") . "",false,4);
  $WAFV_Errors .= WAValidateRQ(((isset($_FILES["txtMscrptFile"]))?$_FILES["txtMscrptFile"]["name"]:"") . "",false,6);
  $WAFV_Errors .= WAValidateRQ(((isset($_POST["txtCoverLeter"]))?$_POST["txtCoverLeter"]:"") . "",false,7);
  $WAFV_Errors .= WAValidateRQ(((isset($_FILES["txtMscrptFile"]))?$_FILES["txtMscrptFile"]["name"]:"") . "",false,1);

  if ($WAFV_Errors != "")  {
    PostResult($WAFV_Redirect,$WAFV_Errors,"mscrSubmit");
  }
}
?>
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
?>
<?php
mysql_select_db($database_localhost, $localhost);
$query_rsCode = "SELECT MAX(mnscrpts.mscrCode)+1 AS code FROM mnscrpts";
$rsCode = mysql_query($query_rsCode, $localhost) or die(mysql_error());
$row_rsCode = mysql_fetch_assoc($rsCode);
$totalRows_rsCode = mysql_num_rows($rsCode);

$colname_rsAutorFullName = "-1";
if (isset($_SESSION['UserID'])) {
  $colname_rsAutorFullName = (get_magic_quotes_gpc()) ? $_GET['UserID'] : addslashes($_GET['UserID']);
}
mysql_select_db($database_localhost, $localhost);
$query_rsAutorFullName = sprintf("SELECT CONCAT(users.UserFirstName,' ', users.UserLastName) AS AutorFullName FROM users WHERE users.UserID=%s", GetSQLValueString($colname_rsAutorFullName, "int"));
$rsAutorFullName = mysql_query($query_rsAutorFullName, $localhost) or die(mysql_error());
$row_rsAutorFullName = mysql_fetch_assoc($rsAutorFullName);
$totalRows_rsAutorFullName = mysql_num_rows($rsAutorFullName);
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
// WA_UploadResult1 Params Start
$WA_UploadResult1_Params = array();
// WA_UploadResult1_1 Start
$WA_UploadResult1_Params["WA_UploadResult1_1"] = array(
	'UploadFolder' => "../files/manuscripts/",
	'FileName' => "JPOP".$row_rsCode['code']  ."",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult1_1 End
// WA_UploadResult1 Params End?>
<?php
// WA_UploadResult2 Params Start
$WA_UploadResult2_Params = array();
// WA_UploadResult2_1 Start
$WA_UploadResult2_Params["WA_UploadResult2_1"] = array(
	'UploadFolder' => "../files/photographs/",
	'FileName' => "JPOP".$row_rsCode['code']  ."_photo1",
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
	'FileName' => "JPOP".$row_rsCode['code']  ."_photo2",
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
	'FileName' => "JPOP".$row_rsCode['code']  ."_photo3",
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
	'FileName' => "JPOP".$row_rsCode['code']  ."_photo4",
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
	'FileName' => "JPOP".$row_rsCode['code']  ."_photo5",
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
	'FileName' => "JPOP".$row_rsCode['code']  ."_photo6",
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
	'FileName' => "JPOP".$row_rsCode['code']  ."_photo7",
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
	'FileName' => "JPOP".$row_rsCode['code']  ."_photo8",
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
	'FileName' => "JPOP".$row_rsCode['code']  ."_photo9",
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
	'FileName' => "JPOP".$row_rsCode['code']  ."_photo10",
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
	'FileName' => "JPOP".$row_rsCode['code']  ."_graph1",
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
	'FileName' => "JPOP".$row_rsCode['code']  ."_graph2",
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
	'FileName' => "JPOP".$row_rsCode['code']  ."_graph3",
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
	'FileName' => "JPOP".$row_rsCode['code']  ."_graph4",
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
	'FileName' => "JPOP".$row_rsCode['code']  ."_graph5",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult16_1 End
// WA_UploadResult16 Params End
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult1");
if($_SERVER["REQUEST_METHOD"] == "POST"){
	WA_DFP_UploadFiles("WA_UploadResult1", "txtMscrptFile", "0", "", "false", $WA_UploadResult1_Params);
}
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
<?php require_once("../webassist/email/mail_php.php"); ?>
<?php require_once("../webassist/email/mailformatting_php.php"); ?>
<?php 
// WA DataAssist Insert
if ($_SERVER["REQUEST_METHOD"] == "POST") // Trigger
{
  $WA_connection = $localhost;
  $WA_table = "mnscrpts";
  $WA_sessionName = "mnscrpts_mscrID";
  $WA_redirectURL = "";
  if (function_exists("rel2abs")) $WA_redirectURL = $WA_redirectURL?rel2abs($WA_redirectURL,dirname(__FILE__)):"";
  $WA_keepQueryString = false;
  $WA_fieldNamesStr = "mscrAutorID|mscrCoverLeter|mscrFullTitle|mscrAbstract|mscrKeywords|mscrComment|mscrCode|mscrCodeU|mscrUpldDate|mscrFileName|mscrReviewerFullName_1|mscrReviewerFullName_2|mscrReviewerFullName_3|mscrFotos_1|mscrFotos_2|mscrFotos_3|mscrFotos_4|mscrFotos_5|mscrFotos_6|mscrFotos_7|mscrFotos_8|mscrFotos_9|mscrFotos_10|mscrGraph_1|mscrGraph_2|mscrGraph_3|mscrGraph_4|mscrGraph_5";
  $WA_fieldValuesStr = "".$_SESSION['UserID']  ."" . $WA_AB_Split . "".((isset($_POST["txtCoverLeter"]))?$_POST["txtCoverLeter"]:"")  ."" . $WA_AB_Split . "".((isset($_POST["txtFullTitle"]))?$_POST["txtFullTitle"]:"")  ."" . $WA_AB_Split . "".((isset($_POST["txtAbstract"]))?$_POST["txtAbstract"]:"")  ."" . $WA_AB_Split . "".((isset($_POST["txtKeywords"]))?$_POST["txtKeywords"]:"")  ."" . $WA_AB_Split . "".((isset($_POST["txtComment"]))?$_POST["txtComment"]:"")  ."" . $WA_AB_Split . "".$row_rsCode['code']  ."" . $WA_AB_Split . "JPOP".$row_rsCode['code']  ."" . $WA_AB_Split . "".((isset($_POST["dateNow"]))?$_POST["dateNow"]:"")  ."" . $WA_AB_Split . "".$WA_DFP_UploadStatus["WA_UploadResult1"]["serverFileName"]  ."" . $WA_AB_Split . "Reviewer No 1" . $WA_AB_Split . "Reviewer No 2" . $WA_AB_Split . "Reviewer No 3" . $WA_AB_Split . "".$WA_DFP_UploadStatus["WA_UploadResult2"]["serverFileName"]  ."" . $WA_AB_Split . "".$WA_DFP_UploadStatus["WA_UploadResult3"]["serverFileName"]  ."" . $WA_AB_Split . "".$WA_DFP_UploadStatus["WA_UploadResult4"]["serverFileName"]  ."" . $WA_AB_Split . "".$WA_DFP_UploadStatus["WA_UploadResult5"]["serverFileName"]  ."" . $WA_AB_Split . "".$WA_DFP_UploadStatus["WA_UploadResult6"]["serverFileName"]  ."" . $WA_AB_Split . "".$WA_DFP_UploadStatus["WA_UploadResult7"]["serverFileName"]  ."" . $WA_AB_Split . "".$WA_DFP_UploadStatus["WA_UploadResult8"]["serverFileName"]  ."" . $WA_AB_Split . "".$WA_DFP_UploadStatus["WA_UploadResult9"]["serverFileName"]  ."" . $WA_AB_Split . "".$WA_DFP_UploadStatus["WA_UploadResult10"]["serverFileName"]  ."" . $WA_AB_Split . "".$WA_DFP_UploadStatus["WA_UploadResult11"]["serverFileName"]  ."" . $WA_AB_Split . "".$WA_DFP_UploadStatus["WA_UploadResult12"]["serverFileName"]  ."" . $WA_AB_Split . "".$WA_DFP_UploadStatus["WA_UploadResult13"]["serverFileName"]  ."" . $WA_AB_Split . "".$WA_DFP_UploadStatus["WA_UploadResult14"]["serverFileName"]  ."" . $WA_AB_Split . "".$WA_DFP_UploadStatus["WA_UploadResult15"]["serverFileName"]  ."" . $WA_AB_Split . "".$WA_DFP_UploadStatus["WA_UploadResult16"]["serverFileName"]  ."";
  $WA_columnTypesStr = "none,none,NULL|',none,''|',none,''|',none,''|',none,''|',none,''|none,none,NULL|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''";
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
// WA Application Builder Insert
if ("" == "") // Trigger
{
  $WA_connection = $localhost;
  $WA_table = "log";
  $WA_sessionName = "log_logID";
  $WA_redirectURL = "";
  if (function_exists("rel2abs")) $WA_redirectURL = $WA_redirectURL?rel2abs($WA_redirectURL,dirname(__FILE__)):"";
  $WA_keepQueryString = false;
  $WA_indexField = "logID";
  $WA_fieldNamesStr = "logUser|logIP|logContent";
  $WA_fieldValuesStr = "".$_SESSION['UserFirstName']  ." ".$_SESSION['UserLastName']  .", ".$_SESSION['UserEmail']  ."" . "|" . "".((isset($_SERVER["REMOTE_ADDR"]))?$_SERVER["REMOTE_ADDR"]:"")  ."" . "|" . "Submit manuscript";
  $WA_columnTypesStr = "',none,''|',none,''|',none,''";
  $WA_fieldNames = explode("|", $WA_fieldNamesStr);
  $WA_fieldValues = explode("|", $WA_fieldValuesStr);
  $WA_columns = explode("|", $WA_columnTypesStr);
  $WA_connectionDB = $database_localhost;
  mysql_select_db($WA_connectionDB, $WA_connection);
  if (!session_id()) session_start();
  $insertParamsObj = WA_AB_generateInsertParams($WA_fieldNames, $WA_columns, $WA_fieldValues, -1);
  $WA_Sql = "INSERT INTO `" . $WA_table . "` (" . $insertParamsObj->WA_tableValues . ") VALUES (" . $insertParamsObj->WA_dbValues . ")";
  $MM_editCmd = mysql_query($WA_Sql, $WA_connection) or die(mysql_error());
  $_SESSION[$WA_sessionName] = mysql_insert_id();
  if ($WA_redirectURL != "")  {
    if ($WA_keepQueryString && $WA_redirectURL != "" && isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] !== "" && sizeof($_POST) > 0) {
      $WA_redirectURL .= ((strpos($WA_redirectURL, '?') === false)?"?":"&").$_SERVER["QUERY_STRING"];
    }
    header("Location: ".$WA_redirectURL);
  }
}
?>
<?php
if ((($_SERVER["REQUEST_METHOD"] == "POST") && (isset($_SERVER["HTTP_REFERER"]) && strpos(urldecode($_SERVER["HTTP_REFERER"]), urldecode($_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"])) > 0) && isset($_POST)))     {
  //WA Universal Email object="mail"
  @session_write_close();
  set_time_limit(0);
  $EmailRef = "waue_mscr_Submit_1";
  $BurstSize = 200;
  $BurstTime = 1;
  $WaitTime = 1;
  $GoToPage = "mscr_ThankYou.php";
  $RecipArray = array();
  $StartBurst = time();
  $LoopCount = 0;
  $TotalEmails = 0;
  $RecipIndex = 0;
  // build up recipients array
  $CurIndex = sizeof($RecipArray);
  $RecipArray[$CurIndex] = array();
  $RecipArray[$CurIndex ][] = "".$_SESSION['UserEmail']  ."";
  $TotalEmails += sizeof($RecipArray[$CurIndex]);
  $RealWait = ($WaitTime<0.25)?0.25:($WaitTime+0.1);
  $TimeTracker = Array();
  $TotalBursts = floor($TotalEmails/$BurstSize);
  $AfterBursts = $TotalEmails % $BurstSize;
  $TimeRemaining = ($TotalBursts * $BurstTime) + ($AfterBursts*$RealWait);
  if ($TimeRemaining < ($TotalEmails*$RealWait) )  {
    $TimeRemaining = $TotalEmails*$RealWait;
  }
  writeUEProgress($EmailRef,0,$TotalEmails,$TimeRemaining);
  while ($RecipIndex < sizeof($RecipArray))  {
    $EnteredValue = is_string($RecipArray[$RecipIndex][0]);
    $CurIndex = 0;
    while (($EnteredValue && $CurIndex < sizeof($RecipArray[$RecipIndex])) || (!$EnteredValue && $RecipArray[$RecipIndex][0])) {
      $starttime = microtime_float();
      if ($EnteredValue)  {
        $RecipientEmail = $RecipArray[$RecipIndex][$CurIndex];
      }  else  {
        $RecipientEmail = $RecipArray[$RecipIndex][0][$RecipArray[$RecipIndex][2]];
      }
      $EmailsRemaining = ($TotalEmails- $LoopCount);
      $BurstsRemaining = ceil(($EmailsRemaining-$AfterBursts)/$BurstSize);
      $IntoBurst = ($EmailsRemaining-$AfterBursts) % $BurstSize;
      if ($AfterBursts<$EmailsRemaining) $IntoBurst = 0;
      $TimeRemaining = ($BurstsRemaining * $BurstTime * 60) + ((($AfterBursts<$EmailsRemaining)?$AfterBursts:$EmailsRemaining)*$RealWait) - (($AfterBursts>$EmailsRemaining)?0:($IntoBurst*$RealWait));
      if ($TimeRemaining < ($EmailsRemaining*$RealWait) )  {
        $TimeRemaining = $EmailsRemaining*$RealWait;
      }
      $CurIndex ++;
      $LoopCount ++;
      writeUEProgress($EmailRef,$LoopCount,$TotalEmails,round($TimeRemaining));
      wa_sleep($WaitTime);
      include("../webassist/email/waue_mscr_Submit_1.php");
      $endtime = microtime_float();
      $TimeTracker[] =$endtime - $starttime;
      $RealWait = array_sum($TimeTracker)/sizeof($TimeTracker);
      if ($LoopCount % $BurstSize == 0 && $CurIndex < sizeof($RecipArray[$RecipIndex]))  {
        $TimePassed = (time() - $StartBurst);
        if ($TimePassed < ($BurstTime*60))  {
          $WaitBurst = ($BurstTime*60) -$TimePassed;
          wa_sleep($WaitBurst);
        }
        else  {
          $TimeRemaining = ($TotalEmails- $LoopCount)*$RealWait;
        }
        $StartBurst = time();
      }
      if (!$EnteredValue)  {
        $RecipArray[$RecipIndex][0] =  mysql_fetch_assoc($RecipArray[$RecipIndex][1]);
      }
    }
    $RecipIndex ++;
  }
  @session_start();
  $_SESSION[$EmailRef."_Status"] = $GLOBALS[$EmailRef."_Status"];
  $_SESSION[$EmailRef."_Index"] = $GLOBALS[$EmailRef."_Index"];
  $_SESSION[$EmailRef."_From"] = $GLOBALS[$EmailRef."_From"];
  $_SESSION[$EmailRef."_To"] = $GLOBALS[$EmailRef."_To"];
  $_SESSION[$EmailRef."_Subject"] = $GLOBALS[$EmailRef."_Subject"];
  $_SESSION[$EmailRef."_Body"] = $GLOBALS[$EmailRef."_Body"];
  $_SESSION[$EmailRef."_Header"] = $GLOBALS[$EmailRef."_Header"];
  $_SESSION[$EmailRef."_Log"] = $GLOBALS[$EmailRef."_Log"];
  if (function_exists("rel2abs")) $GoToPage = $GoToPage?rel2abs($GoToPage,dirname(__FILE__)):"";
  if ($GoToPage!="")     {
    header("Location: ".$GoToPage);
  }
}
?>
<?php
if (($_SERVER["REQUEST_METHOD"] == "POST"))     {
  //WA Universal Email object="mail"
  @session_write_close();
  set_time_limit(0);
  $EmailRef = "waue_mscr_Submit_2";
  $BurstSize = 200;
  $BurstTime = 1;
  $WaitTime = 1;
  $GoToPage = "";
  $RecipArray = array();
  $StartBurst = time();
  $LoopCount = 0;
  $TotalEmails = 0;
  $RecipIndex = 0;
  // build up recipients array
  $CurIndex = sizeof($RecipArray);
  $RecipArray[$CurIndex] = array();
  $RecipArray[$CurIndex ][] = "v.tsigov@gmail.com";
  $TotalEmails += sizeof($RecipArray[$CurIndex]);
  $RealWait = ($WaitTime<0.25)?0.25:($WaitTime+0.1);
  $TimeTracker = Array();
  $TotalBursts = floor($TotalEmails/$BurstSize);
  $AfterBursts = $TotalEmails % $BurstSize;
  $TimeRemaining = ($TotalBursts * $BurstTime) + ($AfterBursts*$RealWait);
  if ($TimeRemaining < ($TotalEmails*$RealWait) )  {
    $TimeRemaining = $TotalEmails*$RealWait;
  }
  writeUEProgress($EmailRef,0,$TotalEmails,$TimeRemaining);
  while ($RecipIndex < sizeof($RecipArray))  {
    $EnteredValue = is_string($RecipArray[$RecipIndex][0]);
    $CurIndex = 0;
    while (($EnteredValue && $CurIndex < sizeof($RecipArray[$RecipIndex])) || (!$EnteredValue && $RecipArray[$RecipIndex][0])) {
      $starttime = microtime_float();
      if ($EnteredValue)  {
        $RecipientEmail = $RecipArray[$RecipIndex][$CurIndex];
      }  else  {
        $RecipientEmail = $RecipArray[$RecipIndex][0][$RecipArray[$RecipIndex][2]];
      }
      $EmailsRemaining = ($TotalEmails- $LoopCount);
      $BurstsRemaining = ceil(($EmailsRemaining-$AfterBursts)/$BurstSize);
      $IntoBurst = ($EmailsRemaining-$AfterBursts) % $BurstSize;
      if ($AfterBursts<$EmailsRemaining) $IntoBurst = 0;
      $TimeRemaining = ($BurstsRemaining * $BurstTime * 60) + ((($AfterBursts<$EmailsRemaining)?$AfterBursts:$EmailsRemaining)*$RealWait) - (($AfterBursts>$EmailsRemaining)?0:($IntoBurst*$RealWait));
      if ($TimeRemaining < ($EmailsRemaining*$RealWait) )  {
        $TimeRemaining = $EmailsRemaining*$RealWait;
      }
      $CurIndex ++;
      $LoopCount ++;
      writeUEProgress($EmailRef,$LoopCount,$TotalEmails,round($TimeRemaining));
      wa_sleep($WaitTime);
      include("../webassist/email/waue_mscr_Submit_2.php");
      $endtime = microtime_float();
      $TimeTracker[] =$endtime - $starttime;
      $RealWait = array_sum($TimeTracker)/sizeof($TimeTracker);
      if ($LoopCount % $BurstSize == 0 && $CurIndex < sizeof($RecipArray[$RecipIndex]))  {
        $TimePassed = (time() - $StartBurst);
        if ($TimePassed < ($BurstTime*60))  {
          $WaitBurst = ($BurstTime*60) -$TimePassed;
          wa_sleep($WaitBurst);
        }
        else  {
          $TimeRemaining = ($TotalEmails- $LoopCount)*$RealWait;
        }
        $StartBurst = time();
      }
      if (!$EnteredValue)  {
        $RecipArray[$RecipIndex][0] =  mysql_fetch_assoc($RecipArray[$RecipIndex][1]);
      }
    }
    $RecipIndex ++;
  }
  @session_start();
  $_SESSION[$EmailRef."_Status"] = $GLOBALS[$EmailRef."_Status"];
  $_SESSION[$EmailRef."_Index"] = $GLOBALS[$EmailRef."_Index"];
  $_SESSION[$EmailRef."_From"] = $GLOBALS[$EmailRef."_From"];
  $_SESSION[$EmailRef."_To"] = $GLOBALS[$EmailRef."_To"];
  $_SESSION[$EmailRef."_Subject"] = $GLOBALS[$EmailRef."_Subject"];
  $_SESSION[$EmailRef."_Body"] = $GLOBALS[$EmailRef."_Body"];
  $_SESSION[$EmailRef."_Header"] = $GLOBALS[$EmailRef."_Header"];
  $_SESSION[$EmailRef."_Log"] = $GLOBALS[$EmailRef."_Log"];
  if (function_exists("rel2abs")) $GoToPage = $GoToPage?rel2abs($GoToPage,dirname(__FILE__)):"";
  if ($GoToPage!="")     {
    header("Location: ".$GoToPage);
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><!-- InstanceBegin template="/Templates/waue html body" codeOutsideHTMLIsLocked="false" --><head><!-- InstanceBeginEditable name="emailhead" --><!-- InstanceEndEditable --></head><body><!-- InstanceBeginEditable name="emailbody" -->
 Dear Dr. <?php echo $_SESSION['UserFirstName']?> <?php echo $_SESSION['UserLastName']?>,<br /><br />

  Thank you for the submited manusctript - JPOP <?php echo $row_rsCode['code']?>. <span lang="EN-US" xml:lang="EN-US">It will be sending to reviewers and will be considered for publication in Journal Propagation of Ornamental Plants</span>.<br />
Please find attached the <strong>Consent to publish and transfer of copiright</strong>. You are kindly requested to follow the next steps:<br />
<br />
1. Please fill in the <strong>title of your contribution</strong> and the <strong>author(s)</strong> on the indicated positions of the consent form <br />
2. Print and sign the consent (position in the end: <strong>Signature</strong>) <br />
3. Please send the consent <strong>BY AIRMAIL</strong> to: <br />
 
<p>Dr. Ivan Iliev<br />
Editor-in-chief<br />
Journal Propagation of Ornamental Plants<br />
University of Forestry<br />
10 Kliment Ohridski blvd.<br />
1756 Sofia <br />
Bulgaria</p>
<p><span lang="EN-US" xml:lang="EN-US">We will contact you again as soon as a final decision has been reached by the Editorial Board.</span><br />
<span lang="EN-US" xml:lang="EN-US">Please remember to quote the <strong>manuscript number (JPOP<?php echo $row_rsCode['code']?>) </strong>in any future correspondence</span></p>
<p> </p>
<p>With best regards<br />
  <a href="http://journal-pop.org/" target="_blank"><span id="lw_1305987834_0">journal</span></a> POP<br />
  Editorial office </p>
<!-- InstanceEndEditable --></body>
<?php
mysql_free_result($rsCode);

mysql_free_result($rsAutorFullName);
?>
<!-- InstanceEnd --></html>