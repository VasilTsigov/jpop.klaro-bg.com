<?php require_once('../Connections/localhost.php'); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php require_once("../webassist/database_management/wa_appbuilder_php.php"); ?>
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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsForReviewer1 = 5;
$pageNum_rsForReviewer1 = 0;
if (isset($_GET['pageNum_rsForReviewer1'])) {
  $pageNum_rsForReviewer1 = $_GET['pageNum_rsForReviewer1'];
}
$startRow_rsForReviewer1 = $pageNum_rsForReviewer1 * $maxRows_rsForReviewer1;

$colname_rsForReviewer1 = "-1";
if (isset($_SESSION['UserID'])) {
  $colname_rsForReviewer1 = (get_magic_quotes_gpc()) ? $_SESSION['UserID'] : addslashes($_SESSION['UserID']);
}
mysql_select_db($database_localhost, $localhost);
$query_rsForReviewer1 = sprintf("SELECT mnscrpts.mscrID, mnscrpts.mscrAutorID, mnscrpts.mscrFullTitle, mnscrpts.mscrCodeU, mnscrpts.mscrUpldDate, mnscrpts.mscrReviewer_1, mnscrpts.mscrFileReviewer_1, mnscrpts.mscrDateFileReviewer_1, mnscrpts.mnscConfirmReviewer_1, mnscrpts.mscrChecklist_1, mnscrDateConfirm_1 FROM mnscrpts WHERE mnscrpts.mscrReviewer_1=%s ORDER BY mnscrpts.mscrID DESC", GetSQLValueString($colname_rsForReviewer1, "int"));
$query_limit_rsForReviewer1 = sprintf("%s LIMIT %d, %d", $query_rsForReviewer1, $startRow_rsForReviewer1, $maxRows_rsForReviewer1);
$rsForReviewer1 = mysql_query($query_limit_rsForReviewer1, $localhost) or die(mysql_error());
$row_rsForReviewer1 = mysql_fetch_assoc($rsForReviewer1);

if (isset($_GET['totalRows_rsForReviewer1'])) {
  $totalRows_rsForReviewer1 = $_GET['totalRows_rsForReviewer1'];
} else {
  $all_rsForReviewer1 = mysql_query($query_rsForReviewer1);
  $totalRows_rsForReviewer1 = mysql_num_rows($all_rsForReviewer1);
}
$totalPages_rsForReviewer1 = ceil($totalRows_rsForReviewer1/$maxRows_rsForReviewer1)-1;

$maxRows_rsForReviewer2 = 5;
$pageNum_rsForReviewer2 = 0;
if (isset($_GET['pageNum_rsForReviewer2'])) {
  $pageNum_rsForReviewer2 = $_GET['pageNum_rsForReviewer2'];
}
$startRow_rsForReviewer2 = $pageNum_rsForReviewer2 * $maxRows_rsForReviewer2;

$colname_rsForReviewer2 = "-1";
if (isset($_SESSION['UserID'])) {
  $colname_rsForReviewer2 = (get_magic_quotes_gpc()) ? $_SESSION['UserID'] : addslashes($_SESSION['UserID']);
}
mysql_select_db($database_localhost, $localhost);
$query_rsForReviewer2 = sprintf("SELECT mnscrpts.mscrID, mnscrpts.mscrAutorID, mnscrpts.mscrFullTitle, mnscrpts.mscrCodeU, mnscrpts.mscrUpldDate, mnscrpts.mscrReviewer_2, mnscrpts.mscrFileReviewer_2, mnscrpts.mscrDateFileReviewer_2, mnscrpts.mnscConfirmReviewer_2, mnscrpts.mscrChecklist_2, mnscrDateConfirm_2 FROM mnscrpts WHERE mnscrpts.mscrReviewer_2=%s ORDER BY mnscrpts.mscrID DESC", GetSQLValueString($colname_rsForReviewer2, "int"));
$query_limit_rsForReviewer2 = sprintf("%s LIMIT %d, %d", $query_rsForReviewer2, $startRow_rsForReviewer2, $maxRows_rsForReviewer2);
$rsForReviewer2 = mysql_query($query_limit_rsForReviewer2, $localhost) or die(mysql_error());
$row_rsForReviewer2 = mysql_fetch_assoc($rsForReviewer2);

if (isset($_GET['totalRows_rsForReviewer2'])) {
  $totalRows_rsForReviewer2 = $_GET['totalRows_rsForReviewer2'];
} else {
  $all_rsForReviewer2 = mysql_query($query_rsForReviewer2);
  $totalRows_rsForReviewer2 = mysql_num_rows($all_rsForReviewer2);
}
$totalPages_rsForReviewer2 = ceil($totalRows_rsForReviewer2/$maxRows_rsForReviewer2)-1;

$maxRows_rsForReviewer3 = 5;
$pageNum_rsForReviewer3 = 0;
if (isset($_GET['pageNum_rsForReviewer3'])) {
  $pageNum_rsForReviewer3 = $_GET['pageNum_rsForReviewer3'];
}
$startRow_rsForReviewer3 = $pageNum_rsForReviewer3 * $maxRows_rsForReviewer3;

$colname_rsForReviewer3 = "-1";
if (isset($_SESSION['UserID'])) {
  $colname_rsForReviewer3 = (get_magic_quotes_gpc()) ? $_SESSION['UserID'] : addslashes($_SESSION['UserID']);
}
mysql_select_db($database_localhost, $localhost);
$query_rsForReviewer3 = sprintf("SELECT mnscrpts.mscrID, mnscrpts.mscrAutorID, mnscrpts.mscrFullTitle, mnscrpts.mscrCodeU, mnscrpts.mscrUpldDate, mnscrpts.mscrReviewer_3, mnscrpts.mscrFileReviewer_3, mnscrpts.mscrDateFileReviewer_3, mnscrpts.mnscConfirmReviewer_3, mnscrpts.mscrChecklist_3, mnscrDateConfirm_3 FROM mnscrpts WHERE mnscrpts.mscrReviewer_3=%s ORDER BY mnscrpts.mscrID DESC", GetSQLValueString($colname_rsForReviewer3, "int"));
$query_limit_rsForReviewer3 = sprintf("%s LIMIT %d, %d", $query_rsForReviewer3, $startRow_rsForReviewer3, $maxRows_rsForReviewer3);
$rsForReviewer3 = mysql_query($query_limit_rsForReviewer3, $localhost) or die(mysql_error());
$row_rsForReviewer3 = mysql_fetch_assoc($rsForReviewer3);

if (isset($_GET['totalRows_rsForReviewer3'])) {
  $totalRows_rsForReviewer3 = $_GET['totalRows_rsForReviewer3'];
} else {
  $all_rsForReviewer3 = mysql_query($query_rsForReviewer3);
  $totalRows_rsForReviewer3 = mysql_num_rows($all_rsForReviewer3);
}
$totalPages_rsForReviewer3 = ceil($totalRows_rsForReviewer3/$maxRows_rsForReviewer3)-1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/main.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<?php
if (!session_id()) session_start();
if ("" == "")     {
  $_SESSION["randomConfirm"] = "".$_SESSION['randomConfirm']  ."";
}

$queryString_rsForReviewer1 = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsForReviewer1") == false && 
        stristr($param, "totalRows_rsForReviewer1") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsForReviewer1 = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsForReviewer1 = sprintf("&totalRows_rsForReviewer1=%d%s", $totalRows_rsForReviewer1, $queryString_rsForReviewer1);

$queryString_rsForReviewer2 = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsForReviewer2") == false && 
        stristr($param, "totalRows_rsForReviewer2") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsForReviewer2 = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsForReviewer2 = sprintf("&totalRows_rsForReviewer2=%d%s", $totalRows_rsForReviewer2, $queryString_rsForReviewer2);

$queryString_rsForReviewer3 = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsForReviewer3") == false && 
        stristr($param, "totalRows_rsForReviewer3") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsForReviewer3 = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsForReviewer3 = sprintf("&totalRows_rsForReviewer3=%d%s", $totalRows_rsForReviewer3, $queryString_rsForReviewer3);?>
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
  $WA_fieldValuesStr = "".$_SESSION['UserFirstName']  ." ".$_SESSION['UserLastName']  .", ".$_SESSION['UserEmail']  ."" . $WA_AB_Split . "".((isset($_SERVER["REMOTE_ADDR"]))?$_SERVER["REMOTE_ADDR"]:"")  ."" . $WA_AB_Split . "Reviewer";
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
<title>Journal-pop - Manuscripts For Reviewer</title>
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
<!-- InstanceParam name="manuscript" type="text" value="current" -->
</head>

<body>

<div id="container">
  <div id="header">
    
  </div>
  <div id="navigation">
    
<ul>
        <li><a href="../index.php" class="">Home</a></li> 
        <li><a href="../users/users_LogIn.php" class="">Login</a></li>
    <li><a href="mscr_Submit.php" class="">Submit Manuscripts</a></li>        <li><a href="mscr_Index.php" class="current">Manuscripts</a></li>
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
      <span class="title">Manuscripts For Review</span>
      
      <div id="mainContent">
      <br />
      <?php if ($totalRows_rsForReviewer1 > 0) { // Show if recordset not empty ?>
          <strong>As Reviewer No 1:</strong><br /><br />
  <form id="formReviewer1" name="formReviewer1" method="post" action="">
  Manuscripts:
    <?php
for ($i=0; $i <= $totalPages_rsForReviewer1; $i++) {
  $TFM_counter = ($i) * $maxRows_rsForReviewer1 + 1;
  $TFM_endCounter = min($totalRows_rsForReviewer1,$TFM_counter + $maxRows_rsForReviewer1 - 1);
  if($i != $pageNum_rsForReviewer1) {
    printf('<a href="'."%s?pageNum_rsForReviewer1=%d%s", $currentPage, $i, $queryString_rsForReviewer1.'">');
    echo "$TFM_counter-$TFM_endCounter</a>";
  }else{
    echo "<strong>$TFM_counter-$TFM_endCounter</strong>";
  }
  if($i != $totalPages_rsForReviewer1) echo " | ";
}
?>
    <?php do { ?>
      <div id="Reviewer_1">
        
        
        <table width="100%" border="0" cellspacing="5" cellpadding="0">
          <tr>
            <td width="8%" align="center" bgcolor="#FEED77"><?php echo $row_rsForReviewer1['mscrCodeU']; ?></td>
            <td width="71%" height="22" bgcolor="#D8D8D8"><?php echo $row_rsForReviewer1['mscrFullTitle']; ?></td>
            <td width="9%" align="center"><?php echo $row_rsForReviewer1['mscrUpldDate']; ?></td>
            <td width="12%" align="center" bgcolor="#CCCCCC">[ <a href="mscr_UpldFileReviewer_1.php?mnscrpt_id=<?php echo $row_rsForReviewer1['mscrID']; ?>&amp;reviewer1=<?php echo $row_rsForReviewer1['mscrReviewer_1']; ?>">Upload file</a> ]</td>
            </tr>
          <tr>
            <td colspan="4" align="left">Received: [ <a href="../download/mainManuscriptFile.php?mnscrpt_id=<?php echo $row_rsForReviewer1['mscrID']; ?>">Manuscript</a> ] [ <a href="mnsc_PhotographsAndGrapphs.php?mnscrpt_id=<?php echo $row_rsForReviewer1['mscrID']; ?>">Photographs and Graphs</a> ] [ <a href="../download/Checlist.php">Checklist</a> ] [ <a href="../download/Instructions.php">Instructions</a> ] [ <?php echo $row_rsForReviewer1['mscrUpldDate']; ?> ]</td>
          </tr>
          <tr>
            <td colspan="4" align="left">Confirmed: <?php echo $row_rsForReviewer1['mnscrDateConfirm_1']; ?></td>
          </tr>
          <tr>
            <td colspan="4" align="left">Reviewed: [ <a href="../download/mscrFileChecklist_1.php?mnscrpt_id=<?php echo $row_rsForReviewer1['mscrID']; ?>"><?php echo $row_rsForReviewer1['mscrChecklist_1']; ?></a> ] [ <a href="../download/mnscrFileReviewer_1.php?mnscrpt_id=<?php echo $row_rsForReviewer1['mscrID']; ?>"><?php echo $row_rsForReviewer1['mscrFileReviewer_1']; ?></a> ] ( <?php echo $row_rsForReviewer1['mscrDateFileReviewer_1']; ?> )</td>
            </tr>
          </table>
        
        
        
      </div>
      <?php } while ($row_rsForReviewer1 = mysql_fetch_assoc($rsForReviewer1)); ?>
      Manuscripts:
    <?php
for ($i=0; $i <= $totalPages_rsForReviewer1; $i++) {
  $TFM_counter = ($i) * $maxRows_rsForReviewer1 + 1;
  $TFM_endCounter = min($totalRows_rsForReviewer1,$TFM_counter + $maxRows_rsForReviewer1 - 1);
  if($i != $pageNum_rsForReviewer1) {
    printf('<a href="'."%s?pageNum_rsForReviewer1=%d%s", $currentPage, $i, $queryString_rsForReviewer1.'">');
    echo "$TFM_counter-$TFM_endCounter</a>";
  }else{
    echo "<strong>$TFM_counter-$TFM_endCounter</strong>";
  }
  if($i != $totalPages_rsForReviewer1) echo " | ";
}
?>
  </form>
  <?php } // Show if recordset not empty ?>
  <?php if ($totalRows_rsForReviewer1 == 0) { // Show if recordset empty ?>
    No manuscripts as reviewer No 1
  <?php } // Show if recordset empty ?>
  <br />
       <?php if ($totalRows_rsForReviewer2 > 0) { // Show if recordset not empty ?>
          <strong>As Reviewer No 2:</strong><br /><br />
<form id="formReviewer2" name="formReviewer2" method="post" action="">
  Manuscripts:
  <?php
for ($i=0; $i <= $totalPages_rsForReviewer2; $i++) {
  $TFM_counter = ($i) * $maxRows_rsForReviewer2 + 1;
  $TFM_endCounter = min($totalRows_rsForReviewer2,$TFM_counter + $maxRows_rsForReviewer2 - 1);
  if($i != $pageNum_rsForReviewer2) {
    printf('<a href="'."%s?pageNum_rsForReviewer2=%d%s", $currentPage, $i, $queryString_rsForReviewer2.'">');
    echo "$TFM_counter-$TFM_endCounter</a>";
  }else{
    echo "<strong>$TFM_counter-$TFM_endCounter</strong>";
  }
  if($i != $totalPages_rsForReviewer2) echo " | ";
}
?>
    
    <?php do { ?>
      <div id="Reviewer_2">
        
        
        <table width="100%" border="0" cellspacing="5" cellpadding="0">
          <tr>
            <td width="8%" align="center" bgcolor="#FEED77"><?php echo $row_rsForReviewer2['mscrCodeU']; ?></td>
            <td width="71%" height="22" bgcolor="#D8D8D8"><?php echo $row_rsForReviewer2['mscrFullTitle']; ?></td>
            <td width="9%" align="center"><?php echo $row_rsForReviewer2['mscrUpldDate']; ?></td>
            <td width="12%" align="center" bgcolor="#CCCCCC">[ <a href="mscr_UpldFileReviewer_2.php?mnscrpt_id=<?php echo $row_rsForReviewer2['mscrID']; ?>&amp;reviewer2=<?php echo $row_rsForReviewer2['mscrReviewer_2']; ?>">Upload file</a> ]</td>
            </tr>
          <tr>
            <td colspan="4" align="left">Received: [ <a href="../download/mainManuscriptFile.php?mnscrpt_id=<?php echo $row_rsForReviewer2['mscrID']; ?>">Manuscript</a> ] [ <a href="mnsc_PhotographsAndGrapphs.php?mnscrpt_id=<?php echo $row_rsForReviewer2['mscrID']; ?>">Photographs and Graphs</a> ] [ <a href="../download/Checlist.php">Checklist</a> ] [ <a href="../download/Instructions.php">Instructions</a> ] [ <?php echo $row_rsForReviewer2['mscrUpldDate']; ?> ]</td>
          </tr>
          <tr>
            <td colspan="4" align="left">Confirmed: <?php echo $row_rsForReviewer2['mnscrDateConfirm_2']; ?></td>
          </tr>
          <tr>
            <td colspan="4" align="left">Reviewed: [ <a href="../download/mscrFileChecklist_2.php?mnscrpt_id=<?php echo $row_rsForReviewer2['mscrID']; ?>"><?php echo $row_rsForReviewer2['mscrChecklist_2']; ?></a> ] [ <a href="../download/mnscrFileReviewer_2.php?mnscrpt_id=<?php echo $row_rsForReviewer2['mscrID']; ?>"><?php echo $row_rsForReviewer2['mscrFileReviewer_2']; ?></a> ] ( <?php echo $row_rsForReviewer2['mscrDateFileReviewer_2']; ?> )</td>
            </tr>
          </table>
        
        
        
      </div>
      <?php } while ($row_rsForReviewer2 = mysql_fetch_assoc($rsForReviewer2)); ?>
      Manuscripts:
    <?php
for ($i=0; $i <= $totalPages_rsForReviewer2; $i++) {
  $TFM_counter = ($i) * $maxRows_rsForReviewer2 + 1;
  $TFM_endCounter = min($totalRows_rsForReviewer2,$TFM_counter + $maxRows_rsForReviewer2 - 1);
  if($i != $pageNum_rsForReviewer2) {
    printf('<a href="'."%s?pageNum_rsForReviewer2=%d%s", $currentPage, $i, $queryString_rsForReviewer2.'">');
    echo "$TFM_counter-$TFM_endCounter</a>";
  }else{
    echo "<strong>$TFM_counter-$TFM_endCounter</strong>";
  }
  if($i != $totalPages_rsForReviewer2) echo " | ";
}
?>           
</form>
  <?php } // Show if recordset not empty ?>
  <?php if ($totalRows_rsForReviewer2 == 0) { // Show if recordset empty ?>
    No manuscripts as reviewer No 2
  <?php } // Show if recordset empty ?>
  <br />
        <?php if ($totalRows_rsForReviewer3 > 0) { // Show if recordset not empty ?>
          <strong>As Reviewer No 3:</strong><br /><br />
  <form id="formReviewer3" name="formReviewer3" method="post" action="">
  Manuscripts:
    <?php
for ($i=0; $i <= $totalPages_rsForReviewer3; $i++) {
  $TFM_counter = ($i) * $maxRows_rsForReviewer3 + 1;
  $TFM_endCounter = min($totalRows_rsForReviewer3,$TFM_counter + $maxRows_rsForReviewer3 - 1);
  if($i != $pageNum_rsForReviewer3) {
    printf('<a href="'."%s?pageNum_rsForReviewer3=%d%s", $currentPage, $i, $queryString_rsForReviewer3.'">');
    echo "$TFM_counter-$TFM_endCounter</a>";
  }else{
    echo "<strong>$TFM_counter-$TFM_endCounter</strong>";
  }
  if($i != $totalPages_rsForReviewer3) echo " | ";
}
?>
    <?php do { ?>
      <div id="Reviewer_3">
        <table width="100%" border="0" cellspacing="5" cellpadding="0">
          <tr>
            <td width="8%" align="center" bgcolor="#FEED77"><?php echo $row_rsForReviewer3['mscrCodeU']; ?></td>
            <td width="71%" height="22" bgcolor="#D8D8D8"><?php echo $row_rsForReviewer3['mscrFullTitle']; ?></td>
            <td width="9%" align="center"><?php echo $row_rsForReviewer3['mscrUpldDate']; ?></td>
            <td width="12%" align="center" bgcolor="#CCCCCC">[ <a href="mscr_UpldFileReviewer_3.php?mnscrpt_id=<?php echo $row_rsForReviewer3['mscrID']; ?>&amp;reviewer3=<?php echo $row_rsForReviewer3['mscrReviewer_3']; ?>">Upload file</a> ]</td>
          </tr>
          <tr>
            <td colspan="4" align="left">Received: [ <a href="../download/mainManuscriptFile.php?mnscrpt_id=<?php echo $row_rsForReviewer3['mscrID']; ?>">Manuscript</a> ] [ <a href="mnsc_PhotographsAndGrapphs.php?mnscrpt_id=<?php echo $row_rsForReviewer3['mscrID']; ?>">Photographs and Graphs</a> ] [ <a href="../download/Checlist.php">Checklist</a> ] [ <a href="../download/Instructions.php">Instructions</a> ] [ <?php echo $row_rsForReviewer3['mscrUpldDate']; ?> ]</td>
          </tr>
          <tr>
            <td colspan="4" align="left">Confirmed: <?php echo $row_rsForReviewer3['mnscrDateConfirm_3']; ?></td>
          </tr>
          <tr>
            <td colspan="4" align="left">Reviewed: [ <a href="../download/mscrFileChecklist_3.php?mnscrpt_id=<?php echo $row_rsForReviewer3['mscrID']; ?>"><?php echo $row_rsForReviewer3['mscrChecklist_3']; ?></a> ] [ <a href="../download/mnscrFileReviewer_3.php?mnscrpt_id=<?php echo $row_rsForReviewer3['mscrID']; ?>"><?php echo $row_rsForReviewer3['mscrFileReviewer_3']; ?></a> ] ( <?php echo $row_rsForReviewer3['mscrDateFileReviewer_3']; ?> )</td>
            </tr>
        </table>
      </div>
      <?php } while ($row_rsForReviewer3 = mysql_fetch_assoc($rsForReviewer3)); ?>
      Manuscripts:
    <?php
for ($i=0; $i <= $totalPages_rsForReviewer3; $i++) {
  $TFM_counter = ($i) * $maxRows_rsForReviewer3 + 1;
  $TFM_endCounter = min($totalRows_rsForReviewer3,$TFM_counter + $maxRows_rsForReviewer3 - 1);
  if($i != $pageNum_rsForReviewer3) {
    printf('<a href="'."%s?pageNum_rsForReviewer3=%d%s", $currentPage, $i, $queryString_rsForReviewer3.'">');
    echo "$TFM_counter-$TFM_endCounter</a>";
  }else{
    echo "<strong>$TFM_counter-$TFM_endCounter</strong>";
  }
  if($i != $totalPages_rsForReviewer3) echo " | ";
}
?>
  </form>
  <?php } // Show if recordset not empty ?>
  <?php if ($totalRows_rsForReviewer3 == 0) { // Show if recordset empty ?>
    No manuscripts as reviewer No 3
  <?php } // Show if recordset empty ?>
  <br />
  <br />
[ <a href="javascript:history.back()">Go Back</a> ] </div>
    <!-- InstanceEndEditable --></div>
  </div>
<div id="footer">Journal-POP &copy; 2011 - <?php echo date('Y'); ?></div>
</div>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsForReviewer1);

mysql_free_result($rsForReviewer2);

mysql_free_result($rsForReviewer3);
?>
