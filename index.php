<?php require_once( "webassist/security_assist/helper_php.php" ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/main.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Journal-POP - Home</title>
<!-- InstanceEndEditable -->
<link href="css/main.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<!-- InstanceParam name="home" type="text" value="current" -->
<!-- InstanceParam name="contact" type="text" value="" -->
<!-- InstanceParam name="instructions" type="text" value="" -->
<!-- InstanceParam name="submit_paper" type="text" value="" -->
<!-- InstanceParam name="register" type="text" value="" -->
<!-- InstanceParam name="login" type="text" value="" -->
<!-- InstanceParam name="your_manuscript" type="text" value="" -->
<script type="text/javascript" src="CSSMenuWriter/cssmw0/menu.js"></script>
<style type="text/css" media="all">
<!--
@import url("CSSMenuWriter/cssmw0/menu.css");
-->
</style>
<!-- InstanceBeginEditable name="menu_ie" -->
<!--[if lte IE 6]>
<style type="text/css" media="all">
@import url("../CSSMenuWriter/cssmw0/menu_ie.css");
</style>

<![endif]-->
<!-- InstanceEndEditable -->
<script type="text/javascript" src="CSSMenuWriter/cssmw1/menu.js"></script>
<style type="text/css" media="all">
<!--
@import url("CSSMenuWriter/cssmw1/menu.css");
-->
</style>
<!--[if lte IE 6]>
<style type="text/css" media="all">
@import url("../CSSMenuWriter/cssmw1/menu_ie.css");
</style>

<![endif]-->
<script type="text/javascript" src="CSSMenuWriter/cssmw2/menu.js"></script>
<style type="text/css" media="all">
<!--
@import url("CSSMenuWriter/cssmw2/menu.css");
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
@import url("CSSMenuWriter/cssmw2/menu.css");
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
        <li><a href="index.php" class="current">Home</a></li> 
        <li><a href="users/users_LogIn.php" class="">Login</a></li>
    <li><a href="manuscripts/mscr_Submit.php" class="">Submit Manuscripts</a></li>        <li><a href="manuscripts/mscr_Index.php" class="">Manuscripts</a></li>
      <li><a href="users/users_Register.php" class="" >Register</a></li>
     <li><a href="instructions/instructions_Index.php" class="">Instructions</a></li>
       <li><a href="contacts/contacts_Index.php" class="">Contact</a></li>
        
    </ul>
    
  </div>
  <div id="mainContainer">
  <div id="sidebarLeft">
    
    <div id="login_left_siedebar">
      <?php if(WA_Auth_RulePasses("Autors_Reviewer_and_Editors")){ // Begin Show Region ?>
        <a href="profile/profile_Index.php"><?php echo $_SESSION['UserEmail']; ?></a> 
        <?php } // End Show Region ?>
    </div> 
    <div id="autors_menu">
    <?php if(WA_Auth_RulePasses("Autors")){ // Begin Show Region ?>
     
        <?php require_once("CSSMenuWriter/cssmw2/menu.php"); ?>
      
     <?php } // End Show Region ?>
      </div>
      
      <div id="reviewers_menu">
        <?php if(WA_Auth_RulePasses("Reviewer")){ // Begin Show Region ?>
          <?php require_once("CSSMenuWriter/cssmw1/menu.php"); ?>
          <?php } // End Show Region ?>
      </div>
    <div id="editors_menu">
      <?php if(WA_Auth_RulePasses("Editors")){ // Begin Show Region ?>
        <?php require_once("CSSMenuWriter/cssmw0/menu.php"); ?>
        <?php } // End Show Region ?>
    </div>
    
<!-- InstanceBeginEditable name="LeftSidebar" --><!-- InstanceEndEditable --></div>
    <div id="content"><!-- InstanceBeginEditable name="mainContent" -->
    <span class="title">Home</span>

  <div id="mainContent">
    <form id="form1" name="form1" method="post" action="">
      <blockquote>
        <p>Welcome  to the Online Manuscript Submission, Review and Tracking System for the Journal Propagation of  Ornamental Plants.<br />
We beleive that you will find this System very user friendly. To make your start  even easier, please find below a few instructions: <br />
<br />
<strong>New </strong><strong>a</strong><strong>uthors:</strong> Please click the 'Register'  button from the menu above and enter the requested information. Upon successful  registration you will be sent an e-mail with instructions to verify your  registration. <br />
<u>Note:</u> When you have received an  e-mail from us with an assigned e-mail as user ID and password, DO NOT REGISTER AGAIN. Just log in to the system and you will be automaticly loged as  'Author'. After  that you could submit your manuscript.<br />
<br />
<strong>Returning </strong><strong>a</strong><strong>uthors:</strong> Please use the provided  username and password and log in to track your manuscript or to submit a NEW  manuscript. (Do not register again as you will then be unable to track your  manuscript). <br />
<u>Note</u>: Please upload your  manuscript only ONCE on to the system. After uploading your manuscript, you will  be sent an e-mail requesting that you approve your submission. Please return to  the main menu and APPROVE your submission accordingly. </p>
        <p><strong>To all  authors:</strong> The following  submission file formats is supported, including: Word, TIFF, JPEG, and Excel. PDF is not an  acceptable file format. <br />
          <br />
          <strong>Reviewers: </strong>Please click the 'Login'  button from the menu above and log in to the system. You may view and/or  download manuscripts assigned to you for review, submit your comments for the  editors and the authors, and track the progress of your manuscripts through the  system. <br />
          <u>Note:</u> Please click the 'Accept' or  'Decline' button as soon as possible after receipt of the e-mail asking you to  review a manuscript. To change your username and password: Log in to the system  and select 'Update My Information' from the menu above. At the top of the  Update My Information screen, click the 'Change Password' button and follow the  directions. <br />
          After you are logged as reviewer you may also submit  your own manuscript through &ldquo;Submit manuscript&rdquo; menu.</p>
        <p><strong>Forgot your password? </strong>If you have  forgot your password, click the 'Login' button and click 'Forgot Your  Password?' at the bottom of the Login screen and follow the instructions. <br />
          Please expect information in your e-mail after each  your action with the system. </p>
      </blockquote>
    </form>
  </div>
  <!-- NACHALO NA TYXO.BG BROYACH -->
<script type="text/javascript">
<!--
d=document;d.write('<a href="http://www.tyxo.bg/?146662" title="Tyxo.bg counter"><img width="1" height="1" border="0" alt="Tyxo.bg counter" src="'+location.protocol+'//cnt.tyxo.bg/146662?rnd='+Math.round(Math.random()*2147483647));
d.write('&sp='+screen.width+'x'+screen.height+'&r='+escape(d.referrer)+'"></a>');
//-->
</script><noscript><a href="http://www.tyxo.bg/?146662" title="Tyxo.bg counter"><img src="http://cnt.tyxo.bg/146662" width="1" height="1" border="0" alt="Tyxo.bg counter" /></a></noscript>
<!-- KRAI NA TYXO.BG BROYACH -->
  <!-- InstanceEndEditable --></div>
  </div>
<div id="footer">Journal-POP &copy; 2011 - <?php echo date('Y'); ?></div>
</div>
</body>
<!-- InstanceEnd --></html>