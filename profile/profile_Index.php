<?php require_once('../Connections/localhost.php'); ?>
<?php require_once('../Connections/localhost_i.php'); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php require_once('../webassist/mysqli/rsobj.php'); ?>
<?php require_once('../webassist/mysqli/queryobj.php'); ?>
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

?>
<?php
$rsProfile = new WA_MySQLi_RS("rsProfile",$localhost_i,1);
$rsProfile->setQuery("SELECT * FROM users WHERE users.UserID=?");
$rsProfile->bindParam("i", "".($_SESSION['UserID'])  ."", "-1"); //colname
$rsProfile->execute();
?>
<?php
if (!session_id()) session_start();
if ("" == "")     {
  $_SESSION["randomConfirm"] = "".$_SESSION['randomConfirm']  ."";
}?>
<?php
if ("" === "") {
  $InsertQuery = new WA_MySQLi_Query($localhost_i);
  $InsertQuery->Action = "insert";
  $InsertQuery->Table = "log";
  $InsertQuery->bindColumn("logUser", "s", "".$_SESSION['UserFirstName']  ." ".$_SESSION['UserLastName']  .", ".$_SESSION['UserEmail']  ."", "WA_BLANK");
  $InsertQuery->bindColumn("logIP", "s", "".((isset($_SERVER["REMOTE_ADDR"]))?$_SERVER["REMOTE_ADDR"]:"")  ."", "WA_BLANK");
  $InsertQuery->bindColumn("logContent", "s", "Your Profile", "WA_BLANK");
  $InsertQuery->saveInSession("log_logID");
  $InsertQuery->execute();
  $InsertGoTo = "";
  if (function_exists("rel2abs")) $InsertGoTo = $InsertGoTo?rel2abs($InsertGoTo,dirname(__FILE__)):"";
  $InsertQuery->redirect($InsertGoTo);
}
?>
<?php
if (($_SERVER["REQUEST_METHOD"] === "POST") && (isset($_SERVER["HTTP_REFERER"]) && strpos(urldecode($_SERVER["HTTP_REFERER"]), urldecode($_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"])) > 0) && isset($_POST)) {
  $UpdateQuery = new WA_MySQLi_Query($localhost_i);
  $UpdateQuery->Action = "update";
  $UpdateQuery->Table = "users";
  $UpdateQuery->bindColumn("UserFirstName", "s", "".((isset($_POST["txtFirstName"]))?$_POST["txtFirstName"]:"")  ."", "WA_BLANK");
  $UpdateQuery->bindColumn("UserLastName", "s", "".((isset($_POST["txtLastName"]))?$_POST["txtLastName"]:"")  ."", "WA_BLANK");
  $UpdateQuery->bindColumn("UserCity", "s", "".((isset($_POST["txtUserCity"]))?$_POST["txtUserCity"]:"")  ."", "WA_BLANK");
  $UpdateQuery->bindColumn("UserCountry", "s", "".((isset($_POST["txtUserCountry"]))?$_POST["txtUserCountry"]:"")  ."", "WA_BLANK");
  $UpdateQuery->bindColumn("UserAddress", "s", "".((isset($_POST["txtUserAddress"]))?$_POST["txtUserAddress"]:"")  ."", "WA_BLANK");
  $UpdateQuery->addFilter("UserID", "=", "d", "".$rsProfile->getColumnVal('UserID')  ."");
  $UpdateQuery->execute();
  $UpdateGoTo = "../manuscripts/mscr_Index.php";
  if (function_exists("rel2abs")) $UpdateGoTo = $UpdateGoTo?rel2abs($UpdateGoTo,dirname(__FILE__)):"";
  $UpdateQuery->redirect($UpdateGoTo);
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
        <a href="profile_Index.php"><?php echo $_SESSION['UserEmail']; ?></a> 
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
           <span class="title">Your Profile</span>
           <hr /><br />
<table width="100%" border="0" cellspacing="4" cellpadding="0">
  <tr>
    <td width="10%">E-Mail:</td>
    <td width="90%"><?php echo $rsProfile->getColumnVal('UserEmail'); ?></td>
  </tr>
  <tr>
    <td>First Name:</td>
    <td><label for="txtFirstName"></label>
      <input name="txtFirstName" type="text" id="txtFirstName" value="<?php echo $rsProfile->getColumnVal('UserFirstName'); ?>" /></td>
  </tr>
  <tr>
    <td>Last Name:</td>
    <td><label for="txtLastName"></label>
      <input name="txtLastName" type="text" id="txtLastName" value="<?php echo $rsProfile->getColumnVal('UserLastName'); ?>" />
      <label for="txtUserCity"></label></td>
  </tr>
  <tr>
    <td>City:</td>
    <td><input name="txtUserCity" type="text" id="txtUserCity" value="<?php echo $rsProfile->getColumnVal('UserCity'); ?>" /></td>
  </tr>
  <tr>
    <td>Country:</td>
    <td><label for="txtUserCountry"></label>
      <input name="txtUserCountry" type="text" id="txtUserCountry" value="<?php echo $rsProfile->getColumnVal('UserCountry'); ?>" /></td>
  </tr>
  <tr>
    <td valign="top">Address:</td>
    <td><label for="txtUserAddress"></label>
      <textarea name="txtUserAddress" id="txtUserAddress"><?php echo $rsProfile->getColumnVal('UserAddress'); ?></textarea></td>
  </tr>
  <tr>
    <td>Registered on: </td>
    <td><?php echo $rsProfile->getColumnVal('UserRegistrationDate'); ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><a href="profile_ChangePassword.php">Change Password</a></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="btnOK" id="btnOK" value="   OK   " /></td>
  </tr>
</table>

        </form>
      </div><br />
[ <a href="javascript:history.back()">Go Back</a> ] <!-- InstanceEndEditable --></div>
  </div>
<div id="footer">Journal-POP &copy; 2011 - <?php echo date('Y'); ?></div>
</div>
</body>
<!-- InstanceEnd --></html>