<?php if(!session_id()) session_start(); ?>
<?php require_once("../webassist/security_assist/wa_hashencryption.php"); ?>
<?php require_once("../webassist/security_assist/wa_randompassword.php"); ?>
<?php require_once("../webassist/form_validations/wavt_scripts_php.php"); ?>
<?php require_once("../webassist/form_validations/wavt_validatedform_php.php"); ?>
<?php require_once('../Connections/localhost.php');?>
<?php require_once("../webassist/security_assist/wa_sha1encryption.php"); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php require_once("../webassist/database_management/wa_appbuilder_php.php"); ?>
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
$colname_rsLostPassword = "-1";
if (isset($_POST['txtEmail'])) {
  $colname_rsLostPassword = $_POST['txtEmail'];
}
mysql_select_db($database_localhost, $localhost);
$query_rsLostPassword = sprintf("SELECT * FROM users WHERE users.UserEmail=%s", GetSQLValueString($colname_rsLostPassword, "text"));
$rsLostPassword = mysql_query($query_rsLostPassword, $localhost) or die(mysql_error());
$row_rsLostPassword = mysql_fetch_assoc($rsLostPassword);
$totalRows_rsLostPassword = mysql_num_rows($rsLostPassword);?>
<?php
@session_start();
if ("" == "")     {
  $_SESSION["randomConfirm"] = "".WA_RandomPassword(6, false, true, true, "")  ."";
}?>
<?php
@session_start();
if ("" == "")     {
  $_SESSION["newPassword"] = "".WA_RandomPassword(6, false, true, true, "")  ."";
}
?>
<?php 
if ($_SERVER["REQUEST_METHOD"] == "POST")  {
  $WAFV_Redirect = "";
  $_SESSION['WAVT_profileSendPassword_328_Errors'] = "";
  if ($WAFV_Redirect == "")  {
    $WAFV_Redirect = $_SERVER["PHP_SELF"];
  }
  $WAFV_Errors = "";
  $WAFV_Errors .= WAValidateEM(((isset($_POST["txtEmail"]))?$_POST["txtEmail"]:"") . "",true,2);
  $WAFV_Errors .= WAValidateNM($totalRows_rsLostPassword . "",1,100,"",",.",true,1);

  if ($WAFV_Errors != "")  {
    PostResult($WAFV_Redirect,$WAFV_Errors,"profileSendPassword_328");
  }
}
?>

<?php 
// WA DataAssist Update
if (isset($_POST["button"]) || isset($_POST["button_x"])) // Trigger
{
  $WA_connection = $localhost;
  $WA_table = "users";
  $WA_redirectURL = "";
  if (function_exists("rel2abs")) $WA_redirectURL = $WA_redirectURL?rel2abs($WA_redirectURL,dirname(__FILE__)):"";
  $WA_keepQueryString = false;
  $WA_indexField = "UserEmail";
  $WA_fieldNamesStr = "UserPassword";
  $WA_fieldValuesStr = "".WA_SHA1Encryption($_SESSION['newPassword'])  ."";
  $WA_columnTypesStr = "',none,''";
  $WA_comparisonStr = "=";
  $WA_fieldNames = explode("|", $WA_fieldNamesStr);
  $WA_fieldValues = explode($WA_AB_Split, $WA_fieldValuesStr);
  $WA_columns = explode("|", $WA_columnTypesStr);
  
  $WA_where_fieldValuesStr = "".((isset($_POST["txtEmail"]))?$_POST["txtEmail"]:"")  ."";
  $WA_where_columnTypesStr = "',none,''";
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

function WA_SecurityAssist_Email_1_SendMail($WA_Auth_Parameter){
  $WA_MailObject = WA_SecurityAssist_Definition("mail.klaro-bg.com","25","","","","UTF-8");
  $WA_MailObject = WA_SecurityAssist_SendMail($WA_MailObject,"","","",$WA_Auth_Parameter["toAddress"],"",$WA_Auth_Parameter["fromAddress"],$WA_Auth_Parameter["subject"],$WA_Auth_Parameter["mailBody"]);
  $WA_MailObject = null;
}// WA_SecurityAssist_Email_1_SendMail

if(isset($_POST["button"]) || isset($_POST["button_x"])){
	//WA SecurityAssist Email object="mail"
  $WA_Auth_Parameter = array(
	"connection" => $localhost,
  	"database" => $database_localhost,
	"tableName" => "users",
	"filterColumn" => "UserEmail",
	"filterEncryption" => "",
	"columnValue" => "".((isset($_POST["txtEmail"]))?$_POST["txtEmail"]:"")  ."",
	"columnType" => "text",
	"usernameColumn" => "UserEmail",
	"usernameEncryption" => "",
	"passwordColumn" => "UserPassword",
	"passwordEncryption" => "",
	"selectColumns" => array("UserEmail","UserPassword"),
	"sessionVariables" => array("newPassword"),
	"successRedirect" => "profile_SendSucces.php",
	"failRedirect" => "profile_SendPassword.php",
	"keepQueryString" => TRUE,
	"toAddressColumn" => "UserEmail",
	"toAddressEncryption" => "",
	"fromAddress" => "ivilievltu@klaro-bg.com",
	"fromAddressDisplay" => "Journal POP",
	"subject" => "New Password",
	"mailBody" => "User:[UserEmail]\r\nPassword: [Session.newPassword]
",
	"emailFunction" => "WA_SecurityAssist_Email_1_SendMail"
	);

	WA_Auth_ForgotPassword($WA_Auth_Parameter);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!-- InstanceBegin template="/Templates/main.dwt.php" codeOutsideHTMLIsLocked="false" -->
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
<?php echo $_SESSION['UserEmail']; ?>

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
  <div id="header"></div>
  <div id="navigation">
    <ul>
      <li><a href="../index.php" class="">Home</a></li>
      <li><a href="../users/users_LogIn.php" class="">Login</a></li>
      <li><a href="../manuscripts/mscr_Submit.php" class="">Submit Manuscripts</a></li>
      <li><a href="../manuscripts/mscr_Index.php" class="">Manuscripts</a></li>
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
      <!-- InstanceBeginEditable name="LeftSidebar" --> <!-- InstanceEndEditable --></div>
    <div id="content"><!-- InstanceBeginEditable name="mainContent" -->
      <div id="mainContent">
        <form id="form1" name="form1" method="post" action="">
          <span class="title">Send New Password</span>
          <hr />
          <br />
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="7%">E-Mail:</td>
              <td width="32%"><label for="txtEmail"></label>
                <input name="txtEmail" type="text" id="txtEmail" size="40" /></td>
              <td width="61%"><span class="validation_text">
                <?php
if (ValidatedField('profileSendPassword_328','profileSendPassword_328'))  {
  if ((strpos((",".ValidatedField("profileSendPassword_328","profileSendPassword_328").","), "," . "" . ",") !== false || "" == ""))  {
    if (!(false))  {
?>
                  <?php
if (ValidatedField('profileSendPassword_328','profileSendPassword_328'))  {
  if ((strpos((",".ValidatedField("profileSendPassword_328","profileSendPassword_328").","), "," . "1" . ",") !== false || "1" == ""))  {
    if (!(false))  {
?>
                    That email address is not registered.
                    <?php //WAFV_Conditional profile_SendPassword.php profileSendPassword_328(1:)
    }
  }
}
?>
                  <?php
if (ValidatedField('profileSendPassword_328','profileSendPassword_328'))  {
  if ((strpos((",".ValidatedField("profileSendPassword_328","profileSendPassword_328").","), "," . "2" . ",") !== false || "2" == ""))  {
    if (!(false))  {
?>
                    Please emter valid email address.
                    <?php //WAFV_Conditional profile_SendPassword.php profileSendPassword_328(2:)
    }
  }
}
?>
                  <?php //WAFV_Conditional profile_SendPassword.php profileSendPassword_328( :)
    }
  }
}?>
              </span></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td colspan="2"><?php if ($totalRows_rsLostPassword > 0) { // Show if recordset not empty ?>
                  Your password has been send.<br />
                  Check your email and <a href="../users/users_LogIn.php">log in</a> using the supplied password.
                  <?php } // Show if recordset not empty ?>
                </span></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td colspan="2"><input type="submit" name="button" id="button" value="   Send    " /></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td colspan="2"><p>[ <a href="javascript:history.back()">Go Back</a> ]</p></td>
            </tr>
          </table>
        </form>
      </div>
    <!-- InstanceEndEditable --></div>
  </div>
  <div id="footer">Journal-POP &copy; 2011- 2013</div>
</div>
</body>
<!-- InstanceEnd -->
</html>
<?php
mysql_free_result($rsLostPassword);
?>
