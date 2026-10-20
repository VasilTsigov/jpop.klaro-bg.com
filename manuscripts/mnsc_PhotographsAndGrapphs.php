<?php require_once('../Connections/localhost.php'); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
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
<?php
$colname_rsPhotosAndGraphs = "-1";
if (isset($_GET['mnscrpt_id'])) {
  $colname_rsPhotosAndGraphs = (get_magic_quotes_gpc()) ? $_GET['mnscrpt_id'] : addslashes($_GET['mnscrpt_id']);
}
mysql_select_db($database_localhost, $localhost);
$query_rsPhotosAndGraphs = sprintf("SELECT mnscrpts.mscrFullTitle, mnscrpts.mscrCodeU, mnscrpts.mscrUpldDate, mnscrpts.mscrFotos_1, mnscrpts.mscrFotos_2, mnscrpts.mscrFotos_3, mnscrpts.mscrFotos_4, mnscrpts.mscrFotos_5, mnscrpts.mscrFotos_6, mnscrpts.mscrFotos_7, mnscrpts.mscrFotos_8, mnscrpts.mscrFotos_9, mnscrpts.mscrFotos_10, mnscrpts.mscrGraph_1, mnscrpts.mscrGraph_2, mnscrpts.mscrGraph_3, mnscrpts.mscrGraph_4, mnscrpts.mscrGraph_5, CONCAT(users.UserFirstName,' ', users.UserLastName) AS AutorFullName, mnscrpts.mscrID, mnscrpts.mscrFotoCorr_1, mnscrpts.mscrFotoCorr_2, mnscrpts.mscrFotoCorr_3, mnscrpts.mscrFotoCorr_4, mnscrpts.mscrFotoCorr_5, mnscrpts.mscrFotoCorr_6, mnscrpts.mscrFotoCorr_7, mnscrpts.mscrFotoCorr_8, mnscrpts.mscrFotoCorr_9, mnscrpts.mscrFotoCorr_10, mnscrpts.mscrGraphCorr_1, mnscrpts.mscrGraphCorr_2, mnscrpts.mscrGraphCorr_3, mnscrpts.mscrGraphCorr_4, mnscrpts.mscrGraphCorr_5 FROM mnscrpts, users WHERE mnscrpts.mscrID=%s AND users.UserID=mnscrpts.mscrAutorID", GetSQLValueString($colname_rsPhotosAndGraphs, "int"));
$rsPhotosAndGraphs = mysql_query($query_rsPhotosAndGraphs, $localhost) or die(mysql_error());
$row_rsPhotosAndGraphs = mysql_fetch_assoc($rsPhotosAndGraphs);
$totalRows_rsPhotosAndGraphs = mysql_num_rows($rsPhotosAndGraphs);

if (!session_id()) session_start();
if ("" == "")     {
  $_SESSION["randomConfirm"] = "".$_SESSION['randomConfirm']  ."";
}?>
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
        <form id="form1" name="form1" method="post" action="">
           <span class="title">Photographs And Graphs</span>
          <hr /><br />
<table width="100%" border="0" cellspacing="2" cellpadding="0">
  <tr>
    <td width="11%" align="center" bgcolor="#CACACA"><?php echo $row_rsPhotosAndGraphs['mscrCodeU']; ?></td>
    <td colspan="3" bgcolor="#D6D6D6"><?php echo $row_rsPhotosAndGraphs['mscrFullTitle']; ?></td>
    <td width="13%" align="center" bgcolor="#CACACA"><?php echo $row_rsPhotosAndGraphs['mscrUpldDate']; ?></td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
    <td colspan="3">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>Autor:</td>
    <td colspan="3"><?php echo $row_rsPhotosAndGraphs['AutorFullName']; ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="3">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Photographs:</td>
    <td width="26%">1. <a href="../download/photo1.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotos_1']; ?></a></td>
    <td width="2%" align="right">&nbsp;</td>
    <td width="48%"><a href="../download/photo1corr.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotoCorr_1']; ?></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>2. <a href="../download/photo2.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotos_2']; ?></a></td>
    <td align="right">&nbsp;</td>
    <td><a href="../download/photo2corr.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotoCorr_2']; ?></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>3. <a href="../download/photo3.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotos_3']; ?></a></td>
    <td align="right">&nbsp;</td>
    <td><a href="../download/photo3corr.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotoCorr_3']; ?></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>4. <a href="../download/photo4.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotos_4']; ?></a></td>
    <td align="right">&nbsp;</td>
    <td><a href="../download/photo4curr.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotoCorr_4']; ?></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>5. <a href="../download/photo5.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotos_5']; ?></a></td>
    <td align="right">&nbsp;</td>
    <td><a href="../download/photo5corr.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotoCorr_5']; ?></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>6. <a href="../download/photo6.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotos_6']; ?></a></td>
    <td align="right">&nbsp;</td>
    <td><a href="../download/photo6corr.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotoCorr_6']; ?></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>7. <a href="../download/photo7.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotos_7']; ?></a></td>
    <td align="right">&nbsp;</td>
    <td><a href="../download/photo7corr.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotoCorr_7']; ?></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>8. <a href="../download/photo8.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotos_8']; ?></a></td>
    <td align="right">&nbsp;</td>
    <td><a href="../download/photo8corr.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotoCorr_8']; ?></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>9. <a href="../download/photo9.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotos_9']; ?></a></td>
    <td align="right">&nbsp;</td>
    <td><a href="../download/photo9corr.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotoCorr_9']; ?></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>10. <a href="../download/photo10.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotos_10']; ?></a></td>
    <td align="right">&nbsp;</td>
    <td><a href="../download/photo10corr.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrFotoCorr_10']; ?></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Graphs:</td>
    <td>1. <a href="../download/graph1.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrGraph_1']; ?></a></td>
    <td align="right">&nbsp;</td>
    <td><a href="../download/graph1corr.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrGraphCorr_1']; ?></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>2. <a href="../download/graph2.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrGraph_2']; ?></a></td>
    <td align="right">&nbsp;</td>
    <td><a href="../download/graph2corr.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrGraphCorr_2']; ?></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>3. <a href="../download/graph3.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrGraph_3']; ?></a></td>
    <td align="right">&nbsp;</td>
    <td><a href="../download/graph3corr.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrGraphCorr_3']; ?></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>4. <a href="../download/graph4.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrGraph_4']; ?></a></td>
    <td align="right">&nbsp;</td>
    <td><a href="../download/graph4corr.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrGraphCorr_4']; ?></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>5. <a href="../download/graph5.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrGraph_5']; ?></a></td>
    <td align="right">&nbsp;</td>
    <td><a href="../download/graph5corr.php?mnscrpt_id=<?php echo $row_rsPhotosAndGraphs['mscrID']; ?>"><?php echo $row_rsPhotosAndGraphs['mscrGraphCorr_5']; ?></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>[ <a href="javascript:history.back()">Go Back</a> ]</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>

        </form>
      </div>
    <!-- InstanceEndEditable --></div>
  </div>
<div id="footer">Journal-POP &copy; 2011 - <?php echo date('Y'); ?></div>
</div>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsPhotosAndGraphs);
?>
