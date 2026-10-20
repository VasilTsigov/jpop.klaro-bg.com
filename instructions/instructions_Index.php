<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php  
if (!WA_Auth_RulePasses("Autors_Reviewer_and_Editors")){
	WA_Auth_RestrictAccess("../users/users_LogIn.php?checkout=1");
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
<!-- InstanceParam name="instructions" type="text" value="current" -->
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
     <li><a href="instructions_Index.php" class="current">Instructions</a></li>
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
<div id="left_sidebar">
  <p>&nbsp;</p>

</div>
<!-- InstanceEndEditable --></div>
    <div id="content"><!-- InstanceBeginEditable name="mainContent" -->
    <span class="title">Instructions to the authors and reviewers</span>
  
    <div id="mainContent">
    <form id="form1" name="form1" method="post" action="">
      <p><span class="bold">To the authors:</span><br />
        <a href="../download/ContentForAutors.php">Consent for authors<br />
        <br />
        </a><strong>How to submit  manuscript:</strong><br />
        <br />
1. Go to the journal website: <a href="http://www.journal-pop.org" target="_blank">www.journal-pop.org</a><br />
2. Click &quot;Submit or review manuscript&quot;<br />
3. Click &quot;Submit manuscript&quot;<br />
4. Fill in <strong>all</strong> boxes indicated by asterisk (*).<br />
- Box &quot;file&quot;: Click on  the box &quot;brose&quot;, select your manuscript (+ abstract and tables) and click &quot;open&quot;. The window will be closed. (The manuscript must be submitted as <strong><u>doc file)</u></strong>. <br />
- The photographs and graphs (if you have) must be  attached to the indicated separate boxes (The <strong><u>photographs</u></strong> must be <strong>TIF</strong> (or JPEG) files and <strong><u>the scanned image</u></strong> must be <strong><u>minimum 300 dpi</u></strong>. The <strong><u>graphs</u></strong> must be <strong><u>Excel</u></strong> files).<br />
6. Click on &quot;Submit manuscript&quot;.<br />
7. You will receive automatic e-mail for successful  submission. Please open it and follow the next instruction i.e. fill in, sign,  print, and send the consent for authors (the link above) by <strong><u>airmail</u></strong>.<br />
        <br />
        <span class="bold">To the reviewer:</span><br />
        <a href="../download/Instructions.php">Instructions for reviewer</a><br />
        <a href="../download/Checlist.php">Checklist to the reviewer</a></p>
      <p><strong>How to submit your  review:</strong><br />
        <br />
        1. Use the website of the journal: <a href="http://www.journal-pop.org/">www.journal-pop.org</a><br />
        2. Click on &ldquo;Submit and review  manuscript&rdquo; <br />
        3. Open your menu &quot;Manuscript for review&quot;<br />
        4. Click on &quot;upload file&quot;. A new window will  be opened.<br />
        5. Click on the first &quot;browse&quot; buton (in  right) and select the file (manuscript) with the text corrected&nbsp;by you. If  you have not any corrections on the text, please attach the  manuscript of the author (original manuscript). Then click &quot;open&quot;. The  window will be closed.<br />
        6. Click on the second &quot;browse&quot; buton (it is  &quot;checklist completed&quot;),&nbsp;select your completed checklist (the link above), and click &quot;open&quot;. The window will be  closed.<br />
        7. Click on the box &quot;select recommendation&quot;  and select one of the options.<br />
        8. Click on &quot;upload file&quot;.</p>
</form>
</div>
  <!-- InstanceEndEditable --></div>
  </div>
<div id="footer">Journal-POP &copy; 2011 - <?php echo date('Y'); ?></div>
</div>
</body>
<!-- InstanceEnd --></html>