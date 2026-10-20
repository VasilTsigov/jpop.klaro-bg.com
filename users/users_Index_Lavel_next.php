<?php require_once('../Connections/localhost.php'); ?>
<?php require_once( "../webassist/file_manipulation/helperphp.php" ); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
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
$colname_rsReviewer_1 = "-1";
if (isset($_GET['userid'])) {
  $colname_rsReviewer_1 = $_GET['userid'];
}
mysql_select_db($database_localhost, $localhost);
$query_rsReviewer_1 = sprintf("SELECT * FROM mnscrpts, users WHERE users.UserID=mnscrpts.mscrReviewer_1 AND users.UserID=%s", GetSQLValueString($colname_rsReviewer_1, "int"));
$rsReviewer_1 = mysql_query($query_rsReviewer_1, $localhost) or die(mysql_error());
$row_rsReviewer_1 = mysql_fetch_assoc($rsReviewer_1);
$totalRows_rsReviewer_1 = mysql_num_rows($rsReviewer_1);
?>
?>
<?php
$colname_rsReviewer_2 = "-1";
if (isset($_GET['userid'])) {
  $colname_rsReviewer_2 = $_GET['userid'];
}
mysql_select_db($database_localhost, $localhost);
$query_rsReviewer_2 = sprintf("SELECT * FROM mnscrpts, users WHERE users.UserID=mnscrpts.mscrReviewer_2 AND users.UserID=%s", GetSQLValueString($colname_rsReviewer_2, "int"));
$rsReviewer_2 = mysql_query($query_rsReviewer_2, $localhost) or die(mysql_error());
$row_rsReviewer_2 = mysql_fetch_assoc($rsReviewer_2);
$totalRows_rsReviewer_2 = mysql_num_rows($rsReviewer_2);

$colname_rsReviewer_3 = "-1";
if (isset($_GET['userid'])) {
  $colname_rsReviewer_3 = $_GET['userid'];
}
mysql_select_db($database_localhost, $localhost);
$query_rsReviewer_3 = sprintf("SELECT * FROM mnscrpts, users WHERE users.UserID=mnscrpts.mscrReviewer_3 AND users.UserID=%s", GetSQLValueString($colname_rsReviewer_3, "int"));
$rsReviewer_3 = mysql_query($query_rsReviewer_3, $localhost) or die(mysql_error());
$row_rsReviewer_3 = mysql_fetch_assoc($rsReviewer_3);
$totalRows_rsReviewer_3 = mysql_num_rows($rsReviewer_3);
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
        <li><a href="users_LogIn.php" class="">Login</a></li>
    <li><a href="../manuscripts/mscr_Submit.php" class="">Submit Manuscripts</a></li>        <li><a href="../manuscripts/mscr_Index.php" class="">Manuscripts</a></li>
      <li><a href="users_Register.php" class="" >Register</a></li>
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
        <form id="form1" name="form1" method="post" action="">
           <span class="title">Reviewer - <?php echo $row_rsReviewer_1['UserFirstName']; ?>  <?php echo $row_rsReviewer_1['UserLastName']; ?> (<?php echo $row_rsReviewer_1['UserEmail']; ?>)</span>
           <hr />
        </form>
        <div id="divReviewer1">  
<table width="100%" border="0" cellpadding="0" cellspacing="3">
  <tr>
    <td width="8%">&nbsp;</td>
    <td width="76%">&nbsp;</td>
    <td width="16%">&nbsp;</td>
  </tr>
  <?php do { ?>
    <tr>
      <td align="center" valign="top"><?php echo $row_rsReviewer_1['mscrCodeU']; ?></td>
      <td><?php echo $row_rsReviewer_1['mscrFullTitle']; ?></td>
      <td>&nbsp;</td>
    </tr>
    <?php } while ($row_rsReviewer_1 = mysql_fetch_assoc($rsReviewer_1)); ?>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
</tr>
</table>
        </div>
        <div id="divReviewer2">  <table width="100%" border="0" cellpadding="0" cellspacing="3">
  <tr>
    <td width="8%">&nbsp;</td>
    <td width="76%">&nbsp;</td>
    <td width="16%">&nbsp;</td>
  </tr>
  <?php do { ?>
    <tr>
      <td align="center" valign="top"><?php echo $row_rsReviewer_2['mscrCodeU']; ?></td>
      <td><?php echo $row_rsReviewer_2['mscrFullTitle']; ?></td>
      <td>&nbsp;</td>
    </tr>
    <?php } while ($row_rsReviewer_2 = mysql_fetch_assoc($rsReviewer_2)); ?>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
</tr>
</table>
</div>
        <div id="divReviewer3"> 
          <table width="100%" border="0" cellspacing="3" cellpadding="0">
            <tr>
              <td width="8%">&nbsp;</td>
              <td width="76%">&nbsp;</td>
              <td width="16%">&nbsp;</td>
            </tr>
            <?php do { ?>
              <tr>
                <td align="center" valign="top"><?php echo $row_rsReviewer_3['mscrCodeU']; ?></td>
                <td><?php echo $row_rsReviewer_3['mscrFullTitle']; ?></td>
                <td>&nbsp;</td>
              </tr>
              <?php } while ($row_rsReviewer_3 = mysql_fetch_assoc($rsReviewer_3)); ?>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
          </table>
        </div>
      </div>
    <!-- InstanceEndEditable --></div>
  </div>
<div id="footer">Journal-POP &copy; 2011 - <?php echo date('Y'); ?></div>
</div>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsReviewer_1);

mysql_free_result($rsReviewer_2);

mysql_free_result($rsReviewer_3);
?>
