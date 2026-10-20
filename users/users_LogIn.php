<?php
@session_start();
?>
<?php @session_start(); ?>
<?php require_once("../WA_SecurityAssist/WA_SHA1Encryption.php"); ?>
<?php require_once('../Connections/localhost.php');?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php
if ((((isset($_POST["checkRemember"]))?$_POST["checkRemember"]:"") != ""))     {
  setcookie("userEmail", "".((isset($_POST["txtEmail"]))?$_POST["txtEmail"]:"")  ."", time()+(60*60*24*30), "/", "", 0);
}
?>
<?php
if ((((isset($_POST["checkRemember"]))?$_POST["checkRemember"]:"") != ""))     {
  setcookie("usersPass", "".((isset($_POST["txtPassword"]))?$_POST["txtPassword"]:"")  ."", time()+(60*60*24*30), "/", "", 0);
}?>
<?php
if ((((isset($_POST["checkRemember"]))?$_POST["checkRemember"]:"") != ""))     {
  setcookie("usersRememberMe", "1", time()+(60*60*24*30), "/", "", 0);
}
?>
<?php
if(isset($_POST["btnLogIn"]) || isset($_POST["btnLogIn_x"])){
	$WA_Auth_Parameter = array(
	"connection" => $localhost,
	"database" => $database_localhost,
	"tableName" => "users",
	"columns" => explode($WA_Auth_Separator,"UserPassword".$WA_Auth_Separator."UserEmail"),
	"columnValues" => explode($WA_Auth_Separator,"".WA_SHA1Encryption(((isset($_POST["txtPassword"]))?$_POST["txtPassword"]:""))  ."".$WA_Auth_Separator."".((isset($_POST["txtEmail"]))?$_POST["txtEmail"]:"")  .""),
	"columnTypes" => explode($WA_Auth_Separator,"text".$WA_Auth_Separator."text"),
	"sessionColumns" => explode($WA_Auth_Separator,"UserEmail".$WA_Auth_Separator."UserLavel".$WA_Auth_Separator."UserFirstName".$WA_Auth_Separator."UserLastName".$WA_Auth_Separator."UserID".$WA_Auth_Separator."UserPassword"),
	"sessionNames" => explode($WA_Auth_Separator,"UserEmail".$WA_Auth_Separator."UserLavel".$WA_Auth_Separator."UserFirstName".$WA_Auth_Separator."UserLastName".$WA_Auth_Separator."UserID".$WA_Auth_Separator."UserPassword"),
	"successRedirect" => "users_Welcom.php",
	"failRedirect" => "users_LogIn.php",
	"gotoPreviousURL" => FALSE,
	"keepQueryString" => FALSE
	);
	
	WA_AuthenticateUser($WA_Auth_Parameter);
}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/main.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Yournal POP - Login</title>
<!-- InstanceEndEditable -->
<link href="../css/main.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<!-- InstanceParam name="home" type="text" value="" -->
<!-- InstanceParam name="contact" type="text" value="" -->
<!-- InstanceParam name="instructions" type="text" value="" -->
<!-- InstanceParam name="submit_paper" type="text" value="" -->
<!-- InstanceParam name="register" type="text" value="" -->
<!-- InstanceParam name="login" type="text" value="current" -->
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
        <li><a href="users_LogIn.php" class="current">Login</a></li>
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
    
<!-- InstanceBeginEditable name="LeftSidebar" --><!-- InstanceEndEditable --></div>
    <div id="content"><!-- InstanceBeginEditable name="mainContent" -->
    <span class="title">Login</span>
  
    <div id="mainContent">
      <?php if(!WA_Auth_RulePasses("Autors_Reviewer_and_Editors")){ // Begin Show Region ?>
        <div id="login">
          <blockquote>
            <blockquote>
              <form id="form1" name="form1" method="post" action="">
                <table width="100%" border="0" cellspacing="2" cellpadding="0">
                  <tr>
                    <td colspan="2"><span class="validation_text"><?php if(WA_Auth_RulePasses("checkout rule")){ // Begin Show Region ?>
                      </MM:DECORATION></MM_HIDDENREGION></span>
                      <MM_HIDDENREGION><MM:DECORATION OUTLINE="Show%20Region" OUTLINEID=6>You have attemted to access a page with restricted content.<br />
                        Please login or <a href="users_Register.php">register</a> to continiue.</MM:DECORATION></MM_HIDDENREGION>
                      <span class="validation_text">
  <MM_HIDDENREGION><MM:DECORATION OUTLINE="Show%20Region" OUTLINEID=6>
    <?php } // End Show Region ?>
                      </span></td>
                    </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td width="687">&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="67">Email:</td>
                    <td><label for="txtEmail"></label>
                      <input name="txtEmail" type="text" id="txtUserName" value="<?php echo((isset($_COOKIE["userEmail"]))?$_COOKIE["userEmail"]:"") ?>" size="35" /></td>
                    </tr>
                  <tr>
                    <td>Password:</td>
                    <td><label for="txtPassword"></label>
                      <input name="txtPassword" type="password" id="txtPassword" value="<?php echo((isset($_COOKIE["usersPass"]))?$_COOKIE["usersPass"]:"") ?>" size="35" /></td>
                    </tr>
                  <tr>
                    <td align="right"><label for="checkRemember"></label></td>
                    <td><input <?php if (!(strcmp(((isset($_COOKIE["usersRememberMe"]))?$_COOKIE["usersRememberMe"]:""),1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="checkRemember" id="checkRemember" />
                      <label for="checkRemember"></label>                      
                      Remember me</td>
                    </tr>
                  <tr>
                    <td align="right">&nbsp;</td>
                    <td>(<a href="../profile/profile_SendPassword.php">forgot</a> your password ?)</td>
                    </tr>
                  <tr>
                    <td colspan="2" align="left"><input  type="submit" name="btnLogIn" id="btnLogIn" value="    Login    " /></td>
                    </tr>
                  <tr>
                    <td align="right">&nbsp;</td>
                    <td>&nbsp;</td>
                    </tr>
                  </table>
              </form>
            </blockquote>
          </blockquote>
        </div>
        <?php } // End Show Region ?>
        <?php if(WA_Auth_RulePasses("Autors_Reviewer_and_Editors")){ // Begin Show Region ?>
          <p>You are login</p>
          <?php } // End Show Region ?>
    </div>
  <!-- InstanceEndEditable --></div>
  </div>
<div id="footer">Journal-POP &copy; 2011 - <?php echo date('Y'); ?></div>
</div>
</body>
<!-- InstanceEnd --></html>