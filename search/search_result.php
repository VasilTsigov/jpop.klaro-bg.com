<?php require_once('../Connections/localhost.php'); ?>
<?php require_once( "../webassist/file_manipulation/helperphp.php" ); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php require_once("../webassist/database_management/wa_appbuilder_php.php"); ?>
<?php require("../my_functions/my_php_functions.php"); ?>
<?php
if (!WA_Auth_RulePasses("Editors")){
	WA_Auth_RestrictAccess("../users/users_LogIn.php");
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
?>
<?php
/////////////////////////////////////////////////////////////////
//$_POST = array_map("trim", $_POST);
//print_r($_POST);
/////////////////////////////////////////////////
$colname_rsSearch = "-1";
if (isset($_POST['txtSearch'])) {
  $colname_rsSearch = $_POST['txtSearch'];
}
mysql_select_db($database_localhost, $localhost);
$query_rsSearch = sprintf("SELECT mnscrpts.mscrID, mnscrpts.mscrAutorID,  mnscrpts.mscrFullTitle, mnscrpts.mscrAbstract, mnscrpts.mscrKeywords, users.UserID, users.UserEmail,CONCAT_WS(' ', users.UserFirstName, users.UserLastName) AS FullAuthorName, users.UserLastName, mnscrpts.mscrCode, mnscrpts.mscrUpldDate, mnscrpts.mscrCodeU, mnscrpts.mscrCoverLeter, mnscrpts.mscrRecommendation_1, mnscrpts.mscrRecommendation_2, mnscrpts.mscrRecommendation_3 FROM mnscrpts INNER JOIN users ON mnscrpts.mscrAutorID = users.UserID WHERE CONCAT(UPPER(users.UserFirstName), ' ',UPPER(users.UserLastName)) LIKE UPPER(%s) OR  UPPER(users.UserFirstName) LIKE UPPER(%s) OR  UPPER(users.UserLastName) LIKE UPPER(%s) OR  UPPER(mnscrpts.mscrAbstract) LIKE UPPER(%s) OR   UPPER(mnscrpts.mscrFullTitle) LIKE UPPER(%s) OR   UPPER(mnscrpts.mscrCoverLeter) LIKE UPPER(%s) OR   UPPER(mnscrpts.mscrKeywords) LIKE UPPER(%s) ", GetSQLValueString("%" . $colname_rsSearch . "%", "text"),GetSQLValueString("%" . $colname_rsSearch . "%", "text"),GetSQLValueString("%" . $colname_rsSearch . "%", "text"),GetSQLValueString("%" . $colname_rsSearch . "%", "text"),GetSQLValueString("%" . $colname_rsSearch . "%", "text"),GetSQLValueString("%" . $colname_rsSearch . "%", "text"),GetSQLValueString("%" . $colname_rsSearch . "%", "text"));
$rsSearch = mysql_query($query_rsSearch, $localhost) or die(mysql_error());
$row_rsSearch = mysql_fetch_assoc($rsSearch);
$totalRows_rsSearch = mysql_num_rows($rsSearch);?>
<?php 
// WA DataAssist Insert
if (!($totalRows_rsSearch == 0)) // Trigger
{
  $WA_connection = $localhost;
  $WA_table = "log";
  $WA_sessionName = "searchresult";
  $WA_redirectURL = "";
  if (function_exists("rel2abs")) $WA_redirectURL = $WA_redirectURL?rel2abs($WA_redirectURL,dirname(__FILE__)):"";
  $WA_keepQueryString = false;
  $WA_fieldNamesStr = "logUser|logIP|logContent";
  $WA_fieldValuesStr = "".$_SESSION['UserFirstName']  ." ".$_SESSION['UserEmail']  ."" . $WA_AB_Split . "".((isset($_SERVER["REMOTE_ADDR"]))?$_SERVER["REMOTE_ADDR"]:"")  ."" . $WA_AB_Split . "Open search result site to show result";
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/main.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Journal-pop</title>
<!-- InstanceEndEditable -->
<link href="../css/main.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<meta charset="utf-8" />

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
    <li><a href="../manuscripts/mscr_Submit.php" class="">Submit Manuscripts</a></li>        <li><a href="../manuscripts/mscr_Index.php" class="">Manuscripts</a></li>
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
       
           <span class="title">Search Result</span><hr />
           <p><a href="javascript:history.back()">[ Back ]</a></p>
           <?php do { ?>
            <?php if ($totalRows_rsSearch > 0) { // Show if recordset not empty ?>
              <div id="divSearchResult">
                <table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="11%" rowspan="2" align="center"  bgcolor="<?php echo colorRecommendation($row_rsSearch['mscrRecommendation_1'],$row_rsSearch['mscrRecommendation_2'],$row_rsSearch['mscrRecommendation_3']); ?>"><a href="../manuscripts/mscr_Status.php?manuscript=<?php echo $row_rsSearch['mscrID']; ?>"><?php echo $row_rsSearch['mscrCodeU']; ?></a></td>
    <td width="73%" rowspan="2" bgcolor="#DFDFDF"><?php echo $row_rsSearch['mscrFullTitle']; ?></td>
    <td width="16%" align="center" bgcolor="#DFDFDF"><?php echo $row_rsSearch['FullAuthorName']; ?></td>
    </tr>
  <tr>
    <td align="center" bgcolor="#DFDFDF"><?php echo $row_rsSearch['mscrUpldDate']; ?></td>
  </tr>
  <tr>
    <td align="right" valign="top"><strong>Cover Letter:</strong></td>
    <td colspan="2"><?php echo $row_rsSearch['mscrCoverLeter']; ?></td>
    </tr>
  <tr>
    <td align="right" valign="top"><strong>Abstract:</strong></td>
    <td colspan="2"><?php echo $row_rsSearch['mscrAbstract']; ?></td>
    </tr>
  <tr>
    <td align="right" valign="top"><strong>Keywords:</strong></td>
    <td colspan="2"><?php echo $row_rsSearch['mscrKeywords']; ?></td>
    </tr>
                </table>

              </div>
              <?php } // Show if recordset not empty ?>
<?php } while ($row_rsSearch = mysql_fetch_assoc($rsSearch)); ?>
 <p><a href="javascript:history.back()">[ Back ]</a></p>
      </div>
      <?php if ($totalRows_rsSearch == 0) { // Show if recordset empty ?>
        <div class="validation_text" id="divNoSearchResult ">No search result</div>
        <?php } // Show if recordset empty ?>
    <!-- InstanceEndEditable --></div>
  </div>
<div id="footer">Journal-POP &copy; 2011 - <?php echo date('Y'); ?></div>
</div>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsSearch);
?>
