<?php require_once("../WA_SecurityAssist/WA_RandomPassword.php"); ?>
<?php require_once('../Connections/localhost.php'); ?>
<?php require_once("../webassist/form_validations/wavt_scripts_php.php"); ?>
<?php require_once("../webassist/form_validations/wavt_validatedform_php.php"); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php require_once("../webassist/database_management/wa_appbuilder_php.php"); ?>
<?php require_once("../webassist/email/mail_php.php"); ?>
<?php require_once("../webassist/email/mailformatting_php.php"); ?>
<?php 
if (!WA_Auth_RulePasses("Autors_Reviewer_and_Editors")){
	WA_Auth_RestrictAccess("../users/users_LogIn.php?checkout=1");
}
?>
<?php 
if ($_SERVER["REQUEST_METHOD"] == "POST")  {
  $WAFV_Redirect = "";
  $_SESSION['WAVT_mscrSendToReviewerNo1_218_Errors'] = "";
  if ($WAFV_Redirect == "")  {
    $WAFV_Redirect = $_SERVER["PHP_SELF"];
  }
  $WAFV_Errors = "";
  $WAFV_Errors .= WAValidateRQ(((isset($_POST["txtReviewer_2"]))?$_POST["txtReviewer_2"]:"") . "",false,2);

  if ($WAFV_Errors != "")  {
    PostResult($WAFV_Redirect,$WAFV_Errors,"mscrSendToReviewerNo1_218");
  }
}
?>
<?php include("../scripts/zeit.php") ; ?>
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
$colname_rsMnscrpt = "-1";
if (isset($_GET['mnscrpt_id'])) {
  $colname_rsMnscrpt = (get_magic_quotes_gpc()) ? $_GET['mnscrpt_id'] : addslashes($_GET['mnscrpt_id']);
}
mysql_select_db($database_localhost, $localhost);
$query_rsMnscrpt = sprintf("SELECT mnscrpts.mscrID,  mnscrpts.mscrFullTitle, mnscrpts.mscrCodeU, mnscrpts.mscrUpldDate, CONCAT(users.UserFirstName,' ', users.UserLastName) AS  AutorFullName,  mnscrpts.mscrReviewerFullName_1, mnscrpts.mscrReviewerFullName_2, mnscrpts.mscrReviewerFullName_3, mnscrpts.mscrAutorID, users.UserEmail, mnscrpts.mscrReviewer_1, mnscrpts.mscrReviewer_2, mnscrpts.mscrReviewer_3, mnscrpts.mscrFileName FROM mnscrpts, users WHERE mnscrpts.mscrID=%s AND users.UserID=mnscrpts.mscrAutorID", GetSQLValueString($colname_rsMnscrpt, "int"));
$rsMnscrpt = mysql_query($query_rsMnscrpt, $localhost) or die(mysql_error());
$row_rsMnscrpt = mysql_fetch_assoc($rsMnscrpt);
$totalRows_rsMnscrpt = mysql_num_rows($rsMnscrpt);

$colname_rsReviewer_1 = "-1";
if (isset($_POST['txtReviewer_1'])) {
  $colname_rsReviewer_1 = (get_magic_quotes_gpc()) ? $_POST['txtReviewer_1'] : addslashes($_POST['txtReviewer_1']);
}
mysql_select_db($database_localhost, $localhost);
$query_rsReviewer_1 = sprintf("SELECT users.UserID,  CONCAT(users.UserFirstName,' ', users.UserLastName,'   ','( ',users.UserEmail ,' ) ') AS FullName, users.UserLavel, status.statusID, users.UserEmail, CONCAT(users.UserFirstName,' ', users.UserLastName) AS ReviewerFullName FROM users, status WHERE status.statusID= users.UserLavel AND status.statusID>=2  OR status.statusID<=3 AND users.UserID=%s", GetSQLValueString($colname_rsReviewer_1, "int"));
$rsReviewer_1 = mysql_query($query_rsReviewer_1, $localhost) or die(mysql_error());
$row_rsReviewer_1 = mysql_fetch_assoc($rsReviewer_1);
$totalRows_rsReviewer_1 = mysql_num_rows($rsReviewer_1);

$colname_rsReviewer_2 = "-1";
if (isset($_POST['txtReviewer_2'])) {
  $colname_rsReviewer_2 = (get_magic_quotes_gpc()) ? $_POST['txtReviewer_2'] : addslashes($_POST['txtReviewer_2']);
}
mysql_select_db($database_localhost, $localhost);
$query_rsReviewer_2 = sprintf("SELECT users.UserID,  CONCAT(users.UserFirstName,' ', users.UserLastName,'   ','( ',users.UserEmail ,' ) ') AS FullName, users.UserLavel, status.statusID, users.UserEmail, CONCAT(users.UserFirstName,' ', users.UserLastName) AS ReviewerFullName FROM users, status WHERE status.statusID= users.UserLavel AND status.statusID>=2 AND status.statusID<=3 AND users.UserID=%s", GetSQLValueString($colname_rsReviewer_2, "int"));
$rsReviewer_2 = mysql_query($query_rsReviewer_2, $localhost) or die(mysql_error());
$row_rsReviewer_2 = mysql_fetch_assoc($rsReviewer_2);
$totalRows_rsReviewer_2 = mysql_num_rows($rsReviewer_2);

$colname_rsReviewer_3 = "-1";
if (isset($_POST['txtReviewer_3'])) {
  $colname_rsReviewer_3 = (get_magic_quotes_gpc()) ? $_POST['txtReviewer_3'] : addslashes($_POST['txtReviewer_3']);
}
mysql_select_db($database_localhost, $localhost);
$query_rsReviewer_3 = sprintf("SELECT users.UserID,  CONCAT(users.UserFirstName,' ', users.UserLastName,'   ','( ',users.UserEmail ,' ) ') AS FullName, users.UserLavel, status.statusID, users.UserEmail, CONCAT(users.UserFirstName,' ', users.UserLastName) AS ReviewerFullName FROM users, status WHERE status.statusID= users.UserLavel AND status.statusID=2 AND users.UserID=%s", GetSQLValueString($colname_rsReviewer_3, "int"));
$rsReviewer_3 = mysql_query($query_rsReviewer_3, $localhost) or die(mysql_error());
$row_rsReviewer_3 = mysql_fetch_assoc($rsReviewer_3);
$totalRows_rsReviewer_3 = mysql_num_rows($rsReviewer_3);

mysql_select_db($database_localhost, $localhost);
$query_rsReviewer = "SELECT users.UserID,  CONCAT(users.UserFirstName,' ', users.UserLastName,'   ','( ',users.UserEmail ,' ) ') AS FullName, users.UserLavel, status.statusID FROM users, status WHERE status.statusID= users.UserLavel AND status.statusID>=2  AND status.statusID<=3 ";
$rsReviewer = mysql_query($query_rsReviewer, $localhost) or die(mysql_error());
$row_rsReviewer = mysql_fetch_assoc($rsReviewer);
$totalRows_rsReviewer = mysql_num_rows($rsReviewer);

if (!session_id()) session_start();
if ("" == "")     {
  $_SESSION["randomConfirm"] = "".$_SESSION['randomConfirm']  ."";
}?>
<?php
if (!session_id()) session_start();
if (isset($_POST["button"]))     {
  $_SESSION["confirmYes"] = "".WA_RandomPassword(9, true, true, true, "")  ."";
}?>
<?php
if (!session_id()) session_start();
if (isset($_POST["button"]))     {
  $_SESSION["confirmNo"] = "".WA_RandomPassword(9, true, true, true, "")  ."";
}?>
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
  $WA_fieldValuesStr = "".$_SESSION['UserFirstName']  ." ".$_SESSION['UserLastName']  .", ".$_SESSION['UserEmail']  ."" . $WA_AB_Split . "".((isset($_SERVER["REMOTE_ADDR"]))?$_SERVER["REMOTE_ADDR"]:"")  ."" . $WA_AB_Split . "Send To Reviewer No 2";
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
  $WA_redirectURL = "";
  if (function_exists("rel2abs")) $WA_redirectURL = $WA_redirectURL?rel2abs($WA_redirectURL,dirname(__FILE__)):"";
  $WA_keepQueryString = false;
  $WA_indexField = "mscrID";
  $WA_fieldNamesStr = "mscrDateToReviewer_2|mnscConfirmReviewer_2|mnscrCodeConfirmYes_2|mnscrCodeConfirmNo_2|mnscrDateConfirm_2|mscrReviewer_2|mscrReviewerFullName_2|mscrDateToReviewer";
  $WA_fieldValuesStr = "to reviewer: ".$datumZ  ." " . "|" . "".$null  ."" . "|" . "".$_SESSION['confirmYes']  ."" . "|" . "".$_SESSION['confirmNo']  ."" . "|" . "".$null  ."" . "|" . "".((isset($_POST["txtReviewer_2"]))?$_POST["txtReviewer_2"]:"")  ."" . "|" . "".$row_rsReviewer_2['ReviewerFullName']  ."" . "|" . "".((isset($_POST["dateNow"]))?$_POST["dateNow"]:"")  ."";
  $WA_columnTypesStr = "',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''";
  $WA_comparisonStr = "=|=|=|=|=|=|=|=";
  $WA_fieldNames = explode("|", $WA_fieldNamesStr);
  $WA_fieldValues = explode("|", $WA_fieldValuesStr);
  $WA_columns = explode("|", $WA_columnTypesStr);
  
  $WA_where_fieldValuesStr = "".$row_rsMnscrpt['mscrID']  ."";
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
<?php
if (($_SERVER["REQUEST_METHOD"] == "POST"))     {
  //WA Universal Email object="mail"
  @session_write_close();
  set_time_limit(0);
  $EmailRef = "waue_mscr_SendToReviewerNo2_1";
  $BurstSize = 200;
  $BurstTime = 1;
  $WaitTime = 1;
  $GoToPage = "mscr_All.php";
  $RecipArray = array();
  $StartBurst = time();
  $LoopCount = 0;
  $TotalEmails = 0;
  $RecipIndex = 0;
  // build up recipients array
  $CurIndex = sizeof($RecipArray);
  $RecipArray[$CurIndex] = array();
  $RecipArray[$CurIndex ][] = "".$row_rsReviewer_2['UserEmail']  ."";
  $RecipArray[$CurIndex ][] = "ivilievltu@yahoo.com";
  $RecipArray[$CurIndex ][] = "v.tsigov@yahoo.com";
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
      include("../webassist/email/waue_mscr_SendToReviewerNo2_1.php");
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
<p>Dear Dr. <?php echo $row_rsReviewer_2['ReviewerFullName']?>,<br />
  <br />
In view of your expertise I would be very   grateful if you could review the following manuscript which has been   submitted to Online Manuscript Submission,            Review and Tracking System            for the journal            Propagation of Ornamental Plants.</p>
<p><br />
  Manuscript Number:  <strong><?php echo $row_rsMnscrpt['mscrCodeU']?></strong><br />
  <br />
  Title:  <strong><?php echo $row_rsMnscrpt['mscrFullTitle']?></strong><br />
  <br />
  In case you are interested in reviewing this submission please click on this   link: <br />
  <br />
  <a href="http://jpop.klaro-bg.com/manuscripts/mscr_Reviewer2ConfirmYes.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']?>&code=<?php echo $_SESSION['confirmYes']?>">I agree to review this submission.</a>
  <br />
  <br />
  If you do not have time to do this, or do not feel qualified, please click on this link: <br />
  <br />
  <a href="http://jpop.klaro-bg.com/manuscripts/mscr_Reviewer2ConfirmNo.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']?>&code=<?php echo $_SESSION['confirmNo']?>">I decline to review this submission.</a>
  
  </p>
<p><br />
  We   hope you are willing to review the manuscript. If so, would you be so   kind as to return your review to us within 30 days of agreeing to   review? Thank you.<br />
  <br />
  You are requested to submit your review online by using<br />
  the Editorial Manager system which can be found at:<br />
  http://jpop.klaro-bg.com<br />
  <br />
  <br />
  IN ORDER TO KEEP DELAYS TO A MINIMUM, PLEASE ACCEPT OR DECLINE THIS ASSIGNMENT ONLINE AS SOON AS POSSIBLE!<br />
  <br />
  In   case you wish to annotate the manuscript, you may upload the attachment   when you send your comments. Please click 'Upload Reviewer   Attachments'.<br />
  <br />
  I hope you are willing to review the manuscript.  Thank you for your   assistance.<br />
  <br />
  Journal POP<br />
  Editorial Office </p>
<!-- InstanceEndEditable --></body><!-- InstanceEnd --></html>