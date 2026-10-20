<?php require_once("../WA_SecurityAssist/WA_RandomPassword.php"); ?>
<?php require_once('../Connections/localhost.php'); ?>
<?php require_once("../webassist/form_validations/wavt_scripts_php.php"); ?>
<?php require_once("../webassist/form_validations/wavt_validatedform_php.php"); ?>
<?php require_once('../Connections/localhost.php'); ?>
<?php require_once('../Connections/localhost.php'); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php require_once("../webassist/database_management/wa_appbuilder_php.php"); ?>
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
  $WAFV_Errors .= WAValidateRQ(((isset($_POST["txtReviewer_3"]))?$_POST["txtReviewer_3"]:"") . "",false,1);

  if ($WAFV_Errors != "")  {
    PostResult($WAFV_Redirect,$WAFV_Errors,"mscrSendToReviewerNo1_218");
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
$query_rsReviewer_3 = sprintf("SELECT users.UserID,  CONCAT(users.UserFirstName,' ', users.UserLastName,'   ','( ',users.UserEmail ,' ) ') AS FullName, users.UserLavel, status.statusID, users.UserEmail, CONCAT(users.UserFirstName,' ', users.UserLastName) AS ReviewerFullName FROM users, status WHERE status.statusID= users.UserLavel AND status.statusID>=2 AND status.statusID<=3 AND users.UserID=%s", GetSQLValueString($colname_rsReviewer_3, "int"));
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
<?php require_once("../webassist/email/mail_php.php"); ?>
<?php require_once("../webassist/email/mailformatting_php.php"); ?>
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
  $WA_fieldValuesStr = "".$_SESSION['UserFirstName']  ." ".$_SESSION['UserLastName']  .", ".$_SESSION['UserEmail']  ."" . $WA_AB_Split . "".((isset($_SERVER["REMOTE_ADDR"]))?$_SERVER["REMOTE_ADDR"]:"")  ."" . $WA_AB_Split . "Send To Reviewer No 3";
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
  $WA_fieldNamesStr = "mscrDateToReviewer_3|mnscConfirmReviewer_3|mnscrCodeConfirmYes_3|mnscrCodeConfirmNo_3|mnscrDateConfirm_3|mscrReviewer_3|mscrReviewerFullName_3|mscrDateToReviewer";
  $WA_fieldValuesStr = "to reviewer: ".$datumZ  ." " . "|" . "".$null  ."" . "|" . "".$_SESSION['confirmYes']  ."" . "|" . "".$_SESSION['confirmNo']  ."" . "|" . "".$null  ."" . "|" . "".((isset($_POST["txtReviewer_3"]))?$_POST["txtReviewer_3"]:"")  ."" . "|" . "".$row_rsReviewer_3['ReviewerFullName']  ."" . "|" . "".((isset($_POST["dateNow"]))?$_POST["dateNow"]:"")  ."";
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
if ((isset($_POST["button"]) || isset($_POST["button_x"])))     {
  //WA Universal Email object="mail"
  @session_write_close();
  set_time_limit(0);
  $EmailRef = "waue_mscr_SendToReviewerNo3_1";
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
  $RecipArray[$CurIndex ][] = "".$row_rsReviewer_3['UserEmail']  ."";
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
      include("../webassist/email/waue_mscr_SendToReviewerNo3_1.php");
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
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/main.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->

<title>Journal-POP - Select Reviewer No 3</title>
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
      <div id="form1_ProgressWrapper">
        <form id="form1" name="form1" method="post" action="">
          <span class="title">Select Reviewer No 3</span>
          <hr />
          <table width="100%" border="0" cellpadding="0" cellspacing="2">
            <tr>
              <td width="13%" valign="top"><strong>Title:</strong></td>
              <td colspan="7" valign="top"><?php echo $row_rsMnscrpt['mscrFullTitle']; ?></td>
              </tr>
            <tr>
              <td><strong>Manuscript No:</strong></td>
              <td colspan="5"><?php echo $row_rsMnscrpt['mscrCodeU']; ?></td>
              <td width="33%" colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td><strong>Autor:</strong></td>
              <td colspan="5"><?php echo $row_rsMnscrpt['AutorFullName']; ?> ( <?php echo $row_rsMnscrpt['UserEmail']; ?> )</td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td><strong>Upload Date:</strong></td>
              <td colspan="5"><?php echo $row_rsMnscrpt['mscrUpldDate']; ?></td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td colspan="5">&nbsp;</td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td><strong>Reviewer No 1:</strong></td>
              <td colspan="3"><?php echo $row_rsMnscrpt['mscrReviewerFullName_1']; ?></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td><strong>
                <input name="dateNow" type="hidden" id="dateNow" value="<?php echo $datumZ; ?>" />
                Reviewer No 2:</strong></td>
              <td colspan="3"><?php echo $row_rsMnscrpt['mscrReviewerFullName_2']; ?></td>
              <td width="1%">&nbsp;</td>
              <td width="1%">&nbsp;</td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td><strong>Reviewer No 3:</strong></td>
              <td colspan="3"><select name="txtReviewer_3" id="txtReviewer_3">
                <option value="" <?php if (!(strcmp("", $row_rsMnscrpt['mscrReviewer_3']))) {echo "selected=\"selected\"";} ?>>Select reviewer ...</option>
                <?php
do {  
?>
                <option value="<?php echo $row_rsReviewer['UserID']?>"<?php if (!(strcmp($row_rsReviewer['UserID'], $row_rsMnscrpt['mscrReviewer_3']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rsReviewer['FullName']?></option>
                <?php
} while ($row_rsReviewer = mysql_fetch_assoc($rsReviewer));
  $rows = mysql_num_rows($rsReviewer);
  if($rows > 0) {
      mysql_data_seek($rsReviewer, 0);
	  $row_rsReviewer = mysql_fetch_assoc($rsReviewer);
  }
?>
              </select>
                <span class="validation_text">
                <?php
if (ValidatedField('mscrSendToReviewerNo1_218','mscrSendToReviewerNo1_218'))  {
  if ((strpos((",".ValidatedField("mscrSendToReviewerNo1_218","mscrSendToReviewerNo1_218").","), "," . "1" . ",") !== false || "1" == ""))  {
    if (!(false))  {
?>
                  Please  select Reviewer
                  <?php //WAFV_Conditional mscr_SendToReviewerNo3.php mscrSendToReviewerNo1_218(1:)
    }
  }
}?>
                </span></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td colspan="2"><a href="mscr_Reminder_3.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>">Reminder</a></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td width="8%">&nbsp;</td>
              <td width="13%">&nbsp;</td>
              <td width="31%">&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td><input type="submit" name="button" id="button" value="   Select   " /></td>
              <td><a href="mscr_All.php">Cancel</a></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td>[ <a href="javascript:history.back()">Go Back</a> ]</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td colspan="2">&nbsp;</td>
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


mysql_free_result($rsMnscrpt);

mysql_free_result($rsReviewer_1);

mysql_free_result($rsReviewer_2);

mysql_free_result($rsReviewer_3);

mysql_free_result($rsReviewer);
?>
