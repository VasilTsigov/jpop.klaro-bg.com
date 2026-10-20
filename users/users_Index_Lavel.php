<?php require_once('../Connections/localhost.php'); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php
if (!WA_Auth_RulePasses("Autors_Reviewer_and_Editors")){
	WA_Auth_RestrictAccess("users_LogIn.php?checkout=1");
}

if (!WA_Auth_RulePasses("Editors")){
	WA_Auth_RestrictAccess("users_LogIn.php");
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
$colname_rsUsers = "2";
if (isset($_GET['lavel'])) {
  $colname_rsUsers = $_GET['lavel'];
}
mysql_select_db($database_localhost, $localhost);
$query_rsUsers = sprintf("SELECT  users.UserID,users.UserEmail, users.UserFirstName, users.UserLastName, users.UserCity, DATE_FORMAT(users.UserRegistrationDate,'%%d.%%m.%%Y') as DateRegister, users.UserCountry, status.statusName, users.UserID, COUNT(m1.mscrReviewer_1) AS Count1, COUNT(m1.mscrReviewer_2) AS Count2, COUNT(m1.mscrReviewer_3) AS Count3 FROM users   LEFT JOIN status ON status.statusUserLave=users.UserLavel  LEFT JOIN mnscrpts m1 ON m1.mscrReviewer_1 = users.UserID WHERE users.UserLavel = %s GROUP BY users.UserEmail ORDER BY Count1 DESC, Count2 DESC, Count3 DESC", GetSQLValueString($colname_rsUsers, "int"));
$rsUsers = mysql_query($query_rsUsers, $localhost) or die(mysql_error());
$row_rsUsers = mysql_fetch_assoc($rsUsers);
$totalRows_rsUsers = mysql_num_rows($rsUsers);

$maxRows_rsUsersCount = 10;
$pageNum_rsUsersCount = 0;
if (isset($_GET['pageNum_rsUsersCount'])) {
  $pageNum_rsUsersCount = $_GET['pageNum_rsUsersCount'];
}
$startRow_rsUsersCount = $pageNum_rsUsersCount * $maxRows_rsUsersCount;

mysql_select_db($database_localhost, $localhost);
$query_rsUsersCount = "SELECT status.statusName,  COUNT(users.UserLavel) AS UsersCout, status.statusUserLave FROM users, status WHERE status.statusUserLave=users.UserLavel GROUP BY users.UserLavel ORDER BY status.statusUserLave DESC";
$query_limit_rsUsersCount = sprintf("%s LIMIT %d, %d", $query_rsUsersCount, $startRow_rsUsersCount, $maxRows_rsUsersCount);
$rsUsersCount = mysql_query($query_limit_rsUsersCount, $localhost) or die(mysql_error());
$row_rsUsersCount = mysql_fetch_assoc($rsUsersCount);

if (isset($_GET['totalRows_rsUsersCount'])) {
  $totalRows_rsUsersCount = $_GET['totalRows_rsUsersCount'];
} else {
  $all_rsUsersCount = mysql_query($query_rsUsersCount, $localhost);
  $totalRows_rsUsersCount = mysql_num_rows($all_rsUsersCount);
}
$totalPages_rsUsersCount = ceil($totalRows_rsUsersCount/$maxRows_rsUsersCount)-1;

mysql_select_db($database_localhost, $localhost);
$query_rsCountAllUsers = "SELECT COUNT(users.UserLavel) AS CountAllUsers FROM users, status WHERE users.UserLavel=status.statusUserLave";
$rsCountAllUsers = mysql_query($query_rsCountAllUsers, $localhost) or die(mysql_error());
$row_rsCountAllUsers = mysql_fetch_assoc($rsCountAllUsers);
$totalRows_rsCountAllUsers = mysql_num_rows($rsCountAllUsers);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/main.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>journal-pop - Users</title>
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
    <span class="title"><?php echo $_GET['usertype']; ?>s - <?php echo $_GET['userscount']; ?></span>

    <div id="mainContent">
    <form id="form1" name="form1" method="post" action="">
      
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="20" colspan="9" align="right">&nbsp;</td>
          </tr>
          <tr>
            <td height="20" colspan="4" align="left"><p><a href="javascript:history.back()">[ Back ]</a></p>
              
            </td>
            <td height="20" colspan="5" align="right">
			<span class="bold"><a href="users_Index.php">All Users - <?php echo $row_rsCountAllUsers['CountAllUsers']; ?> </a></span><br />
			<?php do { ?>
<span class="bold">  <a href="users_Index_Lavel.php?lavel=<?php echo $row_rsUsersCount['statusUserLave']; ?>&amp;usertype=<?php echo $row_rsUsersCount['statusName']; ?>&amp;userscount=<?php echo $row_rsUsersCount['UsersCout']; ?>"><?php echo $row_rsUsersCount['statusName']; ?>s - <?php echo $row_rsUsersCount['UsersCout']; ?></a> </span><br />
                
                <?php } while ($row_rsUsersCount = mysql_fetch_assoc($rsUsersCount)); ?></td>
            </tr>
          <tr>
            <td height="20" align="right">&nbsp;</td>
            <td>&nbsp;</td>
            <td align="left">&nbsp;</td>
            <td align="left">&nbsp;</td>
            <td colspan="2" align="left">&nbsp;</td>
            <td align="left">&nbsp;</td>
            <td align="left">&nbsp;</td>
            <td align="left">&nbsp;</td>
          </tr>
       <?php do { ?>   
          <tr>
            <td width="3%" height="20" align="right" valign="top"><?php echo $row_rsUsers['UserID']; ?>. </td>
            <td width="1%" valign="top">&nbsp;</td>
            <td width="23%" align="left" valign="top"><a href="../profile/profile_Users.php?profile=<?php echo $row_rsUsers['UserID']; ?>"><?php echo $row_rsUsers['UserEmail']; ?></a></td>
            <td width="21%" align="left" valign="top"><?php echo $row_rsUsers['UserFirstName']; ?> <?php echo $row_rsUsers['UserLastName']; ?></td>
            <td width="26%" align="left" valign="top"><?php echo $row_rsUsers['UserCountry']; ?>, <?php echo $row_rsUsers['UserCity']; ?></td>
            <td width="5%" align="left" valign="top"><?php if(WA_Auth_RulePasses("ShowReviewerBtn")){ // Begin Show Region ?>
                <a href="users_Index_Lavel_next.php?userid=<?php echo $row_rsUsers['UserID']; ?>"><?php echo $row_rsUsers['Count1']; ?>, <?php echo $row_rsUsers['Count2']; ?>, <?php echo $row_rsUsers['Count3']; ?></a>
<?php } // End Show Region ?>
</td>
            <td width="9%" align="left" valign="top"><?php echo $row_rsUsers['DateRegister']; ?></td>
            <td width="7%" align="left" valign="top"><?php echo $row_rsUsers['statusName']; ?></td>
            <td width="5%" align="right" valign="top"><a href="users_ChangeStatus.php?update=<?php echo $row_rsUsers['UserID']; ?>">status</a></td>
          </tr>
         <?php } while ($row_rsUsers = mysql_fetch_assoc($rsUsers)); ?> 
        </table>
        
<p><a href="javascript:history.back()">[ Back ]</a></p>
    </form>
  </div>
  <!-- InstanceEndEditable --></div>
  </div>
<div id="footer">Journal-POP &copy; 2011 - <?php echo date('Y'); ?></div>
</div>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsUsers);

mysql_free_result($rsUsersCount);

mysql_free_result($rsCountAllUsers);
?>
