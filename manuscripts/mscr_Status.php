<?php session_start(); ?>
<?php date_default_timezone_set('Europe/Sofia'); ?>
<?php require_once('../Connections/localhost.php'); ?>
<?php require_once("../webassist/form_validations/wavt_scripts_php.php"); ?>
<?php require_once("../webassist/form_validations/wavt_validatedform_php.php"); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php require_once("../webassist/database_management/wa_appbuilder_php.php"); ?>
<?php require_once('mscr_Statusfunc.php'); ?>
<?php 
if (isset($_POST["btnChangeStatus"]) || isset($_POST["btnChangeStatus_x"]))  {
  $WAFV_Redirect = "";
  $_SESSION['WAVT_mscrStatus_437_Errors'] = "";
  if ($WAFV_Redirect == "")  {
    $WAFV_Redirect = $_SERVER["PHP_SELF"];
  }
  $WAFV_Errors = "";
  $WAFV_Errors .= WAValidateRQ(((isset($_POST["cbStatus"]))?$_POST["cbStatus"]:"") . "",false,1);

  if ($WAFV_Errors != "")  {
    PostResult($WAFV_Redirect,$WAFV_Errors,"mscrStatus_437");
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
$colname_rsManuscripts = "-1";
if (isset($_GET['manuscript'])) {
  $colname_rsManuscripts = $_GET['manuscript'];
}
mysql_select_db($database_localhost, $localhost);
$query_rsManuscripts = sprintf("SELECT mnscrpts.mscrID, mnscrpts.mscrAutorID, mnscrpts.mscrFullTitle, CONCAT(authors.UserFirstName,' ', authors.UserLastName) AS FullAutorName, CONCAT(reviewer1.UserFirstName,' ', reviewer1.UserLastName) AS Reviewer1FullName,CONCAT(reviewer2.UserFirstName,' ', reviewer2.UserLastName) AS Reviewer2FullName,CONCAT(reviewer3.UserFirstName,' ', reviewer3.UserLastName) AS Reviewer3FullName, mnscrpts.mscrCode, mnscrpts.mscrCodeU, mnscrpts.mscrStatus, authors.UserEmail AS AuthorsEmail, reviewer1.UserEmail AS Reviewer1Email, reviewer2.UserEmail AS Reviewer2Email, reviewer3.UserEmail AS Reviewer3Email, mnscrpts.mscrRecommendation_1, mnscrpts.mscrRecommendation_2, mnscrpts.mscrRecommendation_3, mnscrpts.mscrEditorsLetter FROM mnscrpts  INNER JOIN users AS authors ON authors.UserID=mnscrpts.mscrAutorID LEFT JOIN users AS reviewer1 ON reviewer1.UserID=mnscrpts.mscrReviewer_1 LEFT JOIN users AS reviewer2 ON reviewer2.UserID=mnscrpts.mscrReviewer_2 LEFT JOIN users AS reviewer3 ON reviewer3.UserID=mnscrpts.mscrReviewer_3 WHERE mnscrpts.mscrID=%s", GetSQLValueString($colname_rsManuscripts, "int"));
$rsManuscripts = mysql_query($query_rsManuscripts, $localhost) or die(mysql_error());
$row_rsManuscripts = mysql_fetch_assoc($rsManuscripts);
$totalRows_rsManuscripts = mysql_num_rows($rsManuscripts);

$colname_rsReviewer1 = "-1";
if (isset($_GET['manuscript'])) {
  $colname_rsReviewer1 = $_GET['manuscript'];
}
mysql_select_db($database_localhost, $localhost);
$query_rsReviewer1 = sprintf("SELECT mnscrpts.mscrID, mnscrpts.mscrAutorID, mnscrpts.mscrFullTitle, CONCAT(authors.UserFirstName,' ', authors.UserLastName) AS FullAutorName, CONCAT(reviewer1.UserFirstName,' ', reviewer1.UserLastName) AS Reviewer1FullName,CONCAT(reviewer2.UserFirstName,' ', reviewer2.UserLastName) AS Reviewer2FullName,CONCAT(reviewer3.UserFirstName,' ', reviewer3.UserLastName) AS Reviewer3FullName, mnscrpts.mscrCode, mnscrpts.mscrCodeU, mnscrpts.mscrStatus, authors.UserEmail AS AuthorsEmail, reviewer1.UserEmail AS Reviewer1Email, reviewer2.UserEmail AS Reviewer2Email, reviewer3.UserEmail AS Reviewer3Email, mnscrpts.mscrRecommendation_1, mnscrpts.mscrRecommendation_2, mnscrpts.mscrRecommendation_3, mnscrpts.mscrEditorsLetter FROM mnscrpts  INNER JOIN users AS authors ON authors.UserID=mnscrpts.mscrAutorID LEFT JOIN users AS reviewer1 ON reviewer1.UserID=mnscrpts.mscrReviewer_1 LEFT JOIN users AS reviewer2 ON reviewer2.UserID=mnscrpts.mscrReviewer_2 LEFT JOIN users AS reviewer3 ON reviewer3.UserID=mnscrpts.mscrReviewer_3 WHERE mnscrpts.mscrID=%s", GetSQLValueString($colname_rsReviewer1, "int"));
$rsReviewer1 = mysql_query($query_rsReviewer1, $localhost) or die(mysql_error());
$row_rsReviewer1 = mysql_fetch_assoc($rsReviewer1);
$totalRows_rsReviewer1 = mysql_num_rows($rsReviewer1);

$colname_rsReviewer2 = "-1";
if (isset($_GET['manuscript'])) {
  $colname_rsReviewer2 = $_GET['manuscript'];
}
mysql_select_db($database_localhost, $localhost);
$query_rsReviewer2 = sprintf("SELECT mnscrpts.mscrID, mnscrpts.mscrAutorID, mnscrpts.mscrFullTitle, CONCAT(authors.UserFirstName,' ', authors.UserLastName) AS FullAutorName, CONCAT(reviewer1.UserFirstName,' ', reviewer1.UserLastName) AS Reviewer1FullName,CONCAT(reviewer2.UserFirstName,' ', reviewer2.UserLastName) AS Reviewer2FullName,CONCAT(reviewer3.UserFirstName,' ', reviewer3.UserLastName) AS Reviewer3FullName, mnscrpts.mscrCode, mnscrpts.mscrCodeU, mnscrpts.mscrStatus, authors.UserEmail AS AuthorsEmail, reviewer1.UserEmail AS Reviewer1Email, reviewer2.UserEmail AS Reviewer2Email, reviewer3.UserEmail AS Reviewer3Email, mnscrpts.mscrRecommendation_1, mnscrpts.mscrRecommendation_2, mnscrpts.mscrRecommendation_3, mnscrpts.mscrEditorsLetter FROM mnscrpts  INNER JOIN users AS authors ON authors.UserID=mnscrpts.mscrAutorID LEFT JOIN users AS reviewer1 ON reviewer1.UserID=mnscrpts.mscrReviewer_1 LEFT JOIN users AS reviewer2 ON reviewer2.UserID=mnscrpts.mscrReviewer_2 LEFT JOIN users AS reviewer3 ON reviewer3.UserID=mnscrpts.mscrReviewer_3 WHERE mnscrpts.mscrID=%s", GetSQLValueString($colname_rsReviewer2, "int"));
$rsReviewer2 = mysql_query($query_rsReviewer2, $localhost) or die(mysql_error());
$row_rsReviewer2 = mysql_fetch_assoc($rsReviewer2);
$totalRows_rsReviewer2 = mysql_num_rows($rsReviewer2);

$colname_rsReviewer3 = "-1";
if (isset($_GET['manuscript'])) {
  $colname_rsReviewer3 = $_GET['manuscript'];
}
mysql_select_db($database_localhost, $localhost);
$query_rsReviewer3 = sprintf("SELECT mnscrpts.mscrID, mnscrpts.mscrAutorID, mnscrpts.mscrFullTitle, CONCAT(authors.UserFirstName,' ', authors.UserLastName) AS FullAutorName, CONCAT(reviewer1.UserFirstName,' ', reviewer1.UserLastName) AS Reviewer1FullName,CONCAT(reviewer2.UserFirstName,' ', reviewer2.UserLastName) AS Reviewer2FullName,CONCAT(reviewer3.UserFirstName,' ', reviewer3.UserLastName) AS Reviewer3FullName, mnscrpts.mscrCode, mnscrpts.mscrCodeU, mnscrpts.mscrStatus, authors.UserEmail AS AuthorsEmail, reviewer1.UserEmail AS Reviewer1Email, reviewer2.UserEmail AS Reviewer2Email, reviewer3.UserEmail AS Reviewer3Email, mnscrpts.mscrRecommendation_1, mnscrpts.mscrRecommendation_2, mnscrpts.mscrRecommendation_3, mnscrpts.mscrEditorsLetter FROM mnscrpts  INNER JOIN users AS authors ON authors.UserID=mnscrpts.mscrAutorID LEFT JOIN users AS reviewer1 ON reviewer1.UserID=mnscrpts.mscrReviewer_1 LEFT JOIN users AS reviewer2 ON reviewer2.UserID=mnscrpts.mscrReviewer_2 LEFT JOIN users AS reviewer3 ON reviewer3.UserID=mnscrpts.mscrReviewer_3 WHERE mnscrpts.mscrID=%s", GetSQLValueString($colname_rsReviewer3, "int"));
$rsReviewer3 = mysql_query($query_rsReviewer3, $localhost) or die(mysql_error());
$row_rsReviewer3 = mysql_fetch_assoc($rsReviewer3);
$totalRows_rsReviewer3 = mysql_num_rows($rsReviewer3);
?>
<?php 
// WA DataAssist Insert
if ("" == "") // Trigger
{
  $WA_connection = $localhost;
  $WA_table = "log";
  $WA_sessionName = "publised";
  $WA_redirectURL = "";
  if (function_exists("rel2abs")) $WA_redirectURL = $WA_redirectURL?rel2abs($WA_redirectURL,dirname(__FILE__)):"";
  $WA_keepQueryString = false;
  $WA_fieldNamesStr = "logUser|logIP|logContent";
  $WA_fieldValuesStr = "".$_SESSION['UserFirstName']  ." ".$_SESSION['UserLastName']  .", ".$_SESSION['UserEmail']  ." " . $WA_AB_Split . "".((isset($_SERVER["REMOTE_ADDR"]))?$_SERVER["REMOTE_ADDR"]:"")  ."" . $WA_AB_Split . "Enter site Published Manuscript";
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
// WA DataAssist Insert
if (($_SERVER["REQUEST_METHOD"] == "POST") && (isset($_SERVER["HTTP_REFERER"]) && strpos(urldecode($_SERVER["HTTP_REFERER"]), urldecode($_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"])) > 0) && isset($_POST)) // Trigger
{
  $WA_connection = $localhost;
  $WA_table = "log";
  $WA_sessionName = "pub_submit";
  $WA_redirectURL = "";
  if (function_exists("rel2abs")) $WA_redirectURL = $WA_redirectURL?rel2abs($WA_redirectURL,dirname(__FILE__)):"";
  $WA_keepQueryString = false;
  $WA_fieldNamesStr = "logUser|logIP|logContent";
  $WA_fieldValuesStr = "".$_SESSION['UserFirstName']  ." ".$_SESSION['UserLastName']  .", ".$_SESSION['UserEmail']  ."" . $WA_AB_Split . "".((isset($_SERVER["REMOTE_ADDR"]))?$_SERVER["REMOTE_ADDR"]:"")  ."" . $WA_AB_Split . "Submit form to mark the manuscript as \"rejected\" or \"bublished\"";
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
<?php require_once("../webassist/email/mail_php.php"); ?>
<?php require_once("../webassist/email/mailformatting_php.php"); ?>
<?php 
// WA DataAssist Update
if (isset($_POST["btnChangeStatus"]) || isset($_POST["btnChangeStatus_x"])) // Trigger
{
  $WA_connection = $localhost;
  $WA_table = "mnscrpts";
  $WA_redirectURL = "mscr_All.php";
  if (function_exists("rel2abs")) $WA_redirectURL = $WA_redirectURL?rel2abs($WA_redirectURL,dirname(__FILE__)):"";
  $WA_keepQueryString = false;
  $WA_indexField = "mscrID";
  $WA_fieldNamesStr = "mscrStatus";
  $WA_fieldValuesStr = "".((isset($_POST["cbStatus"]))?$_POST["cbStatus"]:"")  ."";
  $WA_columnTypesStr = "none,none,NULL";
  $WA_comparisonStr = "=";
  $WA_fieldNames = explode("|", $WA_fieldNamesStr);
  $WA_fieldValues = explode($WA_AB_Split, $WA_fieldValuesStr);
  $WA_columns = explode("|", $WA_columnTypesStr);
  
  $WA_where_fieldValuesStr = "".$row_rsManuscripts['mscrID']  ."";
  $WA_where_columnTypesStr = "none,none,NULL";
  $WA_where_comparisonStr = "=";
  $WA_where_fieldNames = explode("|", $WA_indexField);
  $WA_where_fieldValues = explode($WA_AB_Split, $WA_where_fieldValuesStr);
  $WA_where_columns = explode("|", $WA_where_columnTypesStr);
  $WA_where_comparisons = explode("|", $WA_where_comparisonStr);
  
  $WA_connectionDB = $database_localhost;
  mysql_select_db($WA_connectionDB, $WA_connection);
  @session_start();
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
<?php
if(strlen($row_rsReviewer2['mscrRecommendation_1'])!== 0) {
if ((isset($_POST["btnSendLetters"]) || isset($_POST["btnSendLetters_x"])))     {
  //WA Universal Email object="mail"
  @session_write_close();
  set_time_limit(0);
  $EmailRef = "waue_mscr_Status_3";
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
  $RecipArray[$CurIndex ][] = "".$row_rsReviewer1['Reviewer1Email']  ."";
  $RecipArray[$CurIndex ][] = "ivilievltu@yahoo.com";
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
      include("../webassist/email/waue_mscr_Status_3.php");
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
}
?>

<?php if(strlen($row_rsReviewer2['mscrRecommendation_2'])!== 0) { 

if ((isset($_POST["btnSendLetters"]) || isset($_POST["btnSendLetters_x"])))     {
  //WA Universal Email object="mail"
  @session_write_close();
  set_time_limit(0);
  $EmailRef = "waue_mscr_Status_4";
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
  $RecipArray[$CurIndex ][] = "".$row_rsReviewer2['Reviewer2Email']  ."";
  $RecipArray[$CurIndex ][] = "ivilievltu@yahoo.com";
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
      include("../webassist/email/waue_mscr_Status_4.php");
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
} 
?>

<?php
if(strlen($row_rsReviewer3['mscrRecommendation_3'])!== 0) {
if ((isset($_POST["btnSendLetters"]) || isset($_POST["btnSendLetters_x"])))     {
  //WA Universal Email object="mail"
  @session_write_close();
  set_time_limit(0);
  $EmailRef = "waue_mscr_Status_5";
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
  $RecipArray[$CurIndex ][] = "".$row_rsReviewer3['Reviewer3Email']  ."";
  $RecipArray[$CurIndex ][] = "v.tsigov@gmail.com";
  $RecipArray[$CurIndex ][] = "ivilievltu@yahoo.com";
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
      include("../webassist/email/waue_mscr_Status_5.php");
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
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/main.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Journal-pop</title>
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
<!-- InstanceParam name="submit_manuscript" type="text" value="" -->
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
    <li><a href="mscr_Submit.php" class="">Submit Manuscripts</a></li>        <li><a href="mscr_Index.php" class="">Manuscripts</a></li>
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
    <div id="mainContent">
      
        <div id="frmInserStatus_ProgressWrapper">
          <form id="frmInserStatus" name="frmInserStatus" method="post" action="">
            <span class="title">Change Mamuscript Status (All, Published or Rejected)</span>
            <hr />
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="16%" height="31" align="right">&nbsp;</td>
                <td width="84%">&nbsp;</td>
              </tr>
              <tr>
                <td align="right" valign="top">Manuscript:</td>
                <td><?php echo $row_rsManuscripts['mscrCodeU']; ?></td>
              </tr>
              <tr>
                <td align="right" valign="top">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td align="right" valign="top">Title:</td>
                <td><?php echo $row_rsManuscripts['mscrFullTitle']; ?></td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td align="right">Author:</td>
                <td><?php echo $row_rsManuscripts['FullAutorName']; ?></td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td align="right">Change Status:</td>
                <td><label for="cbStatus"></label>
                  <select name="cbStatus" id="cbStatus">
                    <option value="" <?php if (!(strcmp("", $row_rsManuscripts['mscrStatus']))) {echo "selected=\"selected\"";} ?>>------- Select Status ------</option>
                    <option value="1" <?php if (!(strcmp(1, $row_rsManuscripts['mscrStatus']))) {echo "selected=\"selected\"";} ?>>Rejected</option>
                    <option value="2" <?php if (!(strcmp(2, $row_rsManuscripts['mscrStatus']))) {echo "selected=\"selected\"";} ?>>Published</option>
                    <option value="0" <?php if (!(strcmp(0, $row_rsManuscripts['mscrStatus']))) {echo "selected=\"selected\"";} ?>>All</option>
                  </select>
                  manuscripts
                  <?php
if (ValidatedField('mscrStatus_437','mscrStatus_437'))  {
  if ((strpos((",".ValidatedField("mscrStatus_437","mscrStatus_437").","), "," . "1" . ",") !== false || "1" == ""))  {
    if (!(false))  {
?>
                  <span class="validation_text">Select Status</span>
                  <?php //WAFV_Conditional mscr_Status.php mscrStatus_437(1:)
    }
  }
}?></td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td><input type="submit" name="btnChangeStatus" id="btnChangeStatus" value="Change Status " /></td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td>[ <a href="javascript:history.back()">Back</a> ]</td>
              </tr>
            </table>
          </form>
        </div>
      
      
        <div id="frmSendLetters_ProgressWrapper">
          <form id="frmSendLetters" name="frmSendLetters" method="post" action="">
            <span class="title">Send Letters To Reviewers</span>
            <hr />
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="16%" height="31" align="right">&nbsp;</td>
                <td width="84%">&nbsp;</td>
              </tr>
              <tr>
                <td align="right" valign="top">Manuscript:</td>
                <td><?php echo $row_rsManuscripts['mscrCodeU']; ?></td>
              </tr>
              <tr>
                <td align="right" valign="top">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td align="right" valign="top">Title:</td>
                <td><?php echo $row_rsManuscripts['mscrFullTitle']; ?></td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td align="right">Author:</td>
                <td><?php echo $row_rsManuscripts['FullAutorName']; ?></td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td align="right" valign="top">Letter to Reviewer No. 1:
                  <label for="txtLetterToReviewer1"></label></td>
                <td><textarea name="txtLetterToReviewer1" id="txtLetterToReviewer1" cols="115" rows="15"><?php echo LeterToReviwer1($row_rsReviewer1['Reviewer1FullName'],$row_rsReviewer1['FullAutorName'],$row_rsReviewer1['mscrCodeU'],$row_rsReviewer1['mscrRecommendation_1'],$row_rsReviewer1['mscrRecommendation_2'],$row_rsReviewer1['mscrRecommendation_3'],$row_rsReviewer1['mscrFullTitle']); ?>
			  </textarea></td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td align="right" valign="top">Letter to Reviewer No. 2:</td>
                <td><label for="txtLetterToReviewer2"></label>
                  <textarea name="txtLetterToReviewer2" id="txtLetterToReviewer2" cols="115" rows="15"><?php echo LeterToReviwer2($row_rsReviewer1['Reviewer2FullName'],$row_rsReviewer2['FullAutorName'],$row_rsReviewer2['mscrCodeU'],$row_rsReviewer2['mscrRecommendation_1'],$row_rsReviewer2['mscrRecommendation_2'],$row_rsReviewer2['mscrRecommendation_3'],$row_rsReviewer2['mscrFullTitle']); ?></textarea></td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td align="right" valign="top">Letter to Reviewer No. 3:</td>
                <td><label for="txtLetterToReviewer3"></label>
                  <textarea name="txtLetterToReviewer3" id="txtLetterToReviewer3" cols="115" rows="15">
<?php echo LeterToReviwer3($row_rsReviewer3['Reviewer3FullName'],$row_rsReviewer3['FullAutorName'], $row_rsReviewer3['mscrCodeU'],$row_rsReviewer3['mscrRecommendation_1'],$row_rsReviewer3['mscrRecommendation_2'],$row_rsReviewer3['mscrRecommendation_3'], $row_rsReviewer3['mscrFullTitle']); ?>
			</textarea></td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td><input type="submit" name="btnSendLetters" id="btnSendLetters" value="Send Letters to Reviewers   " /></td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td>[ <a href="javascript:history.back()">Back</a> ]</td>
              </tr>
            </table>
          </form>
        </div>
      
    </div>
    <div id="frmInserStatus_ProgressMessageWrapper" class="blockUIOverlay" style="display:none;">
      <script type="text/javascript">
WADFP_SetProgressToForm('frmInserStatus', 'frmInserStatus_ProgressMessageWrapper', WADFP_Theme_Options['BigSpin:Moab']);
    </script>
      <div id="frmInserStatus_ProgressMessage" >
        <p style="margin:10px; padding:5px;" ><img src="../webassist/progress_bar/images/moab-largespin.gif" alt="" title="" style="vertical-align:middle;" />&nbsp;&nbsp;Please wait</p>
      </div>
    </div>
    <div id="frmSendLetters_ProgressMessageWrapper" class="blockUIOverlay" style="display:none;">
      <script type="text/javascript">
WADFP_SetProgressToForm('frmSendLetters', 'frmSendLetters_ProgressMessageWrapper', WADFP_Theme_Options['BigSpin:Moab']);
    </script>
      <div id="frmSendLetters_ProgressMessage" >
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
mysql_free_result($rsManuscripts);

mysql_free_result($rsReviewer1);

mysql_free_result($rsReviewer2);

mysql_free_result($rsReviewer3);
?>
