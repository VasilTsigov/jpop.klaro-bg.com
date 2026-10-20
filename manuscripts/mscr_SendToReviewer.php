<?php require_once('../Connections/localhost.php'); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php require_once("../webassist/database_management/wa_appbuilder_php.php"); ?>
<?php  
if (!WA_Auth_RulePasses("Autors_Reviewer_and_Editors")){
	WA_Auth_RestrictAccess("../users/users_LogIn.php?checkout=1");
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
if (isset($_GET['mnscrpt'])) {
  $colname_rsMnscrpt = (get_magic_quotes_gpc()) ? $_GET['mnscrpt'] : addslashes($_GET['mnscrpt']);
}
mysql_select_db($database_localhost, $localhost);
$query_rsMnscrpt = sprintf("SELECT mnscrpts.mscrID, mnscrpts.mscrAutorID, mnscrpts.mscrFullTitle, mnscrpts.mscrCodeU, mnscrpts.mscrUpldDate, CONCAT(users.UserFirstName,' ', users.UserLastName) AS  FullName, mnscrpts.mscrReviewer_1, mnscrpts.mscrReviewer_2, mnscrpts.mscrReviewer_3, mnscrpts.mscrFileName, mnscrpts.mscrReviewerFullName_1, mnscrpts.mscrReviewerFullName_2, mnscrpts.mscrReviewerFullName_3 FROM mnscrpts, users WHERE mnscrpts.mscrID=%s AND users.UserID=mnscrpts.mscrAutorID", GetSQLValueString($colname_rsMnscrpt, "int"));
$rsMnscrpt = mysql_query($query_rsMnscrpt, $localhost) or die(mysql_error());
$row_rsMnscrpt = mysql_fetch_assoc($rsMnscrpt);
$totalRows_rsMnscrpt = mysql_num_rows($rsMnscrpt);

$colname_rsReviewer_1 = "-1";
if (isset($_POST['txtReviewer_1'])) {
  $colname_rsReviewer_1 = (get_magic_quotes_gpc()) ? $_POST['txtReviewer_1'] : addslashes($_POST['txtReviewer_1']);
}
mysql_select_db($database_localhost, $localhost);
$query_rsReviewer_1 = sprintf("SELECT users.UserID,  CONCAT(users.UserFirstName,' ', users.UserLastName,'   ','( ',users.UserEmail ,' ) ') AS FullName, users.UserLavel, status.statusID, users.UserEmail, CONCAT(users.UserFirstName,' ', users.UserLastName) AS ReviewerFullName FROM users, status WHERE status.statusID= users.UserLavel AND status.statusID=2 AND users.UserID=%s", GetSQLValueString($colname_rsReviewer_1, "int"));
$rsReviewer_1 = mysql_query($query_rsReviewer_1, $localhost) or die(mysql_error());
$row_rsReviewer_1 = mysql_fetch_assoc($rsReviewer_1);
$totalRows_rsReviewer_1 = mysql_num_rows($rsReviewer_1);

$colname_rsReviewer_2 = "-1";
if (isset($_POST['txtReviewer_2'])) {
  $colname_rsReviewer_2 = (get_magic_quotes_gpc()) ? $_POST['txtReviewer_2'] : addslashes($_POST['txtReviewer_2']);
}
mysql_select_db($database_localhost, $localhost);
$query_rsReviewer_2 = sprintf("SELECT users.UserID,  CONCAT(users.UserFirstName,' ', users.UserLastName,'   ','( ',users.UserEmail ,' ) ') AS FullName, users.UserLavel, status.statusID, users.UserEmail, CONCAT(users.UserFirstName,' ', users.UserLastName) AS ReviewerFullName FROM users, status WHERE status.statusID= users.UserLavel AND status.statusID=2 AND users.UserID=%s", GetSQLValueString($colname_rsReviewer_2, "int"));
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
$query_rsReviewer = "SELECT users.UserID,  CONCAT(users.UserFirstName,' ', users.UserLastName,'   ','( ',users.UserEmail ,' ) ') AS FullName, users.UserLavel, status.statusID FROM users, status  WHERE status.statusID= users.UserLavel AND status.statusID=2 ";
$rsReviewer = mysql_query($query_rsReviewer, $localhost) or die(mysql_error());
$row_rsReviewer = mysql_fetch_assoc($rsReviewer);
$totalRows_rsReviewer = mysql_num_rows($rsReviewer);

if (!session_id()) session_start();
if ("" == "")     {
  $_SESSION["randomConfirm"] = "".$_SESSION['randomConfirm']  ."";
}?>
<?php require_once("../webassist/email/mail_php.php"); ?>
<?php require_once("../webassist/email/mailformatting_php.php"); ?>
<?php 
// WA DataAssist Update
if (($_SERVER["REQUEST_METHOD"] == "POST") && (isset($_SERVER["HTTP_REFERER"]) && strpos(urldecode($_SERVER["HTTP_REFERER"]), urldecode($_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"])) > 0) && isset($_POST)) // Trigger
{
  $WA_connection = $localhost;
  $WA_table = "mnscrpts";
  $WA_redirectURL = "";
  if (function_exists("rel2abs")) $WA_redirectURL = $WA_redirectURL?rel2abs($WA_redirectURL,dirname(__FILE__)):"";
  $WA_keepQueryString = false;
  $WA_indexField = "mscrID";
  $WA_fieldNamesStr = "mscrReviewer_1|mscrReviewer_2|mscrReviewer_3|mscrReviewerFullName_1|mscrReviewerFullName_2|mscrReviewerFullName_3|mscrDateToReviewer|mnscStatus_2";
  $WA_fieldValuesStr = "".((isset($_POST["txtReviewer_1"]))?$_POST["txtReviewer_1"]:"")  ."" . $WA_AB_Split . "".((isset($_POST["txtReviewer_2"]))?$_POST["txtReviewer_2"]:"")  ."" . $WA_AB_Split . "".((isset($_POST["txtReviewer_3"]))?$_POST["txtReviewer_3"]:"")  ."" . $WA_AB_Split . "".$row_rsReviewer_1['ReviewerFullName']  ."" . $WA_AB_Split . "".$row_rsReviewer_2['ReviewerFullName']  ."" . $WA_AB_Split . "".$row_rsReviewer_3['ReviewerFullName']  ."" . $WA_AB_Split . "".((isset($_POST["dateNow"]))?$_POST["dateNow"]:"")  ."" . $WA_AB_Split . "to reviewer";
  $WA_columnTypesStr = "',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''|',none,''";
  $WA_comparisonStr = "=|=|=|=|=|=|=|=";
  $WA_fieldNames = explode("|", $WA_fieldNamesStr);
  $WA_fieldValues = explode($WA_AB_Split, $WA_fieldValuesStr);
  $WA_columns = explode("|", $WA_columnTypesStr);
  
  $WA_where_fieldValuesStr = "".$row_rsMnscrpt['mscrID']  ."";
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
if (($_SERVER["REQUEST_METHOD"] == "POST"))     {
  //WA Universal Email object="mail"
  @session_write_close();
  set_time_limit(0);
  $EmailRef = "waue_mscr_SendToReviewer_1";
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
  $RecipArray[$CurIndex ][] = "".$row_rsReviewer_1['UserEmail']  ."";
  $RecipArray[$CurIndex ][] = "".$row_rsReviewer_2['UserEmail']  ."";
  $RecipArray[$CurIndex ][] = "".$row_rsReviewer_3['UserEmail']  ."";
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
      include("../webassist/email/waue_mscr_SendToReviewer_1.php");
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
          <span class="title">Send To Reviewer</span>
          <hr />
          <table width="100%" border="0" cellpadding="0" cellspacing="2">
            <tr>
              <td width="9%" valign="top"><?php echo $row_rsMnscrpt['mscrCodeU']; ?></td>
              <td colspan="5" valign="top"><?php echo $row_rsMnscrpt['mscrFullTitle']; ?></td>
              <td width="16%" valign="top"><?php echo $row_rsMnscrpt['FullName']; ?></td>
              <td width="17%" valign="top"><?php echo $row_rsMnscrpt['mscrUpldDate']; ?></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td colspan="5">&nbsp;</td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td><input name="dateNow" type="hidden" id="dateNow" value="<?php echo $datumZ; ?>" /></td>
              <td width="12%">Select Reviewer: </td>
              <td width="2%">1.</td>
              <td width="41%"><label for="txtReviewer_1"></label>
                <select name="txtReviewer_1" id="txtReviewer_1">
                  <option value="" <?php if (!(strcmp("", $row_rsMnscrpt['mscrReviewer_1']))) {echo "selected=\"selected\"";} ?>>Not Reviewer</option>
                  <?php
do {  
?>
                  <option value="<?php echo $row_rsReviewer['UserID']?>"<?php if (!(strcmp($row_rsReviewer['UserID'], $row_rsMnscrpt['mscrReviewer_1']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rsReviewer['FullName']?></option>
                  <?php
} while ($row_rsReviewer = mysql_fetch_assoc($rsReviewer));
  $rows = mysql_num_rows($rsReviewer);
  if($rows > 0) {
      mysql_data_seek($rsReviewer, 0);
	  $row_rsReviewer = mysql_fetch_assoc($rsReviewer);
  }
?>
                </select></td>
              <td width="2%">&nbsp;</td>
              <td width="1%">&nbsp;</td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>2.</td>
              <td><label for="txtReviewer_2"></label>
                <select name="txtReviewer_2" id="txtReviewer_2">
                  <option value="" <?php if (!(strcmp("", $row_rsMnscrpt['mscrReviewer_2']))) {echo "selected=\"selected\"";} ?>>Not Reviewer</option>
                  <?php
do {  
?>
                  <option value="<?php echo $row_rsReviewer['UserID']?>"<?php if (!(strcmp($row_rsReviewer['UserID'], $row_rsMnscrpt['mscrReviewer_2']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rsReviewer['FullName']?></option>
                  <?php
} while ($row_rsReviewer = mysql_fetch_assoc($rsReviewer));
  $rows = mysql_num_rows($rsReviewer);
  if($rows > 0) {
      mysql_data_seek($rsReviewer, 0);
	  $row_rsReviewer = mysql_fetch_assoc($rsReviewer);
  }
?>
                </select></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>3.</td>
              <td><label for="txtReviewer_3"></label>
                <select name="txtReviewer_3" id="txtReviewer_3">
                  <option value="" <?php if (!(strcmp("", $row_rsMnscrpt['mscrReviewer_3']))) {echo "selected=\"selected\"";} ?>>Not Reviewer</option>
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
                </select></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td><input type="submit" name="button" id="button" value="Select Reviewer" /></td>
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
