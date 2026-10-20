<?php require_once('../Connections/localhost.php'); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>; 
<?php
if (!WA_Auth_RulePasses("Editors")){
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

$currentPage = $_SERVER["PHP_SELF"];
?>
<?php
$maxRows_rslogos = 60;
$pageNum_rslogos = 0;
if (isset($_GET['pageNum_rslogos'])) {
  $pageNum_rslogos = $_GET['pageNum_rslogos'];
}
$startRow_rslogos = $pageNum_rslogos * $maxRows_rslogos;

mysql_select_db($database_localhost, $localhost);
$query_rslogos = "SELECT log.logID, log.logUser, log.logIP, DATE_FORMAT(log.logDate,'%d.%m.%Y') AS logDate, log.logContent, DATE_FORMAT(log.logDate,'%H:%i:%s') AS logTime FROM log ORDER BY log.logID DESC";
$query_limit_rslogos = sprintf("%s LIMIT %d, %d", $query_rslogos, $startRow_rslogos, $maxRows_rslogos);
$rslogos = mysql_query($query_limit_rslogos, $localhost) or die(mysql_error());
$row_rslogos = mysql_fetch_assoc($rslogos);

if (isset($_GET['totalRows_rslogos'])) {
  $totalRows_rslogos = $_GET['totalRows_rslogos'];
} else {
  $all_rslogos = mysql_query($query_rslogos);
  $totalRows_rslogos = mysql_num_rows($all_rslogos);
}
$totalPages_rslogos = ceil($totalRows_rslogos/$maxRows_rslogos)-1;

$queryString_rslogos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rslogos") == false && 
        stristr($param, "totalRows_rslogos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rslogos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rslogos = sprintf("&totalRows_rslogos=%d%s", $totalRows_rslogos, $queryString_rslogos);
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
        <form id="form1" name="form1" method="post" action="logos_EmptyTable.php">
           <span class="title">Logos</span>
     
           <?php if ($totalRows_rslogos == 0) { // Show if recordset empty ?>
             No logs
  <?php } // Show if recordset empty ?>
           <?php if ($totalRows_rslogos > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td colspan="7" align="right"><a href="logos_EmptyTable.php" >Empty logos</a></td>
    </tr>
    <tr>
      <td colspan="7"><strong>Pages:</strong>
        <?php
for ($i=0; $i <= $totalPages_rslogos; $i++) {
  $TFM_counter = ($i) * $maxRows_rslogos + 1;
  $TFM_endCounter = min($totalRows_rslogos,$TFM_counter + $maxRows_rslogos - 1);
  if($i != $pageNum_rslogos) {
    printf('<a href="'."%s?pageNum_rslogos=%d%s", $currentPage, $i, $queryString_rslogos.'">');
    echo "$TFM_counter-$TFM_endCounter</a>";
  }else{
    echo "<strong>$TFM_counter-$TFM_endCounter</strong>";
  }
  if($i != $totalPages_rslogos) echo " | ";
}
?></td>
      </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
      <td width="30%">&nbsp;</td>
      <td width="11%">&nbsp;</td>
      <td width="10%">&nbsp;</td>
      <td colspan="2">&nbsp;</td>
    </tr>
    <?php do { ?>
      <tr>
        <td width="5%" align="right" valign="top"><?php echo $row_rslogos['logID']; ?>.</td>
        <td width="1%" valign="top">&nbsp;</td>
        <td valign="top"><?php echo $row_rslogos['logUser']; ?></td>
        <td valign="top"><?php echo $row_rslogos['logDate']; ?></td>
        <td valign="top"><?php echo $row_rslogos['logTime']; ?></td>
        <td width="11%" valign="top"><?php echo $row_rslogos['logIP']; ?></td>
        <td width="32%" valign="top"><?php echo $row_rslogos['logContent']; ?></td>
      </tr>
      <?php } while ($row_rslogos = mysql_fetch_assoc($rslogos)); ?>
    <tr>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="7"><strong>Pages:</strong>
        <?php
for ($i=0; $i <= $totalPages_rslogos; $i++) {
  $TFM_counter = ($i) * $maxRows_rslogos + 1;
  $TFM_endCounter = min($totalRows_rslogos,$TFM_counter + $maxRows_rslogos - 1);
  if($i != $pageNum_rslogos) {
    printf('<a href="'."%s?pageNum_rslogos=%d%s", $currentPage, $i, $queryString_rslogos.'">');
    echo "$TFM_counter-$TFM_endCounter</a>";
  }else{
    echo "<strong>$TFM_counter-$TFM_endCounter</strong>";
  }
  if($i != $totalPages_rslogos) echo " | ";
}
?></td>
      </tr>
  </table>
  <?php } // Show if recordset not empty ?>
        </form>
      </div>
    <!-- InstanceEndEditable --></div>
  </div>
<div id="footer">Journal-POP &copy; 2011 - <?php echo date('Y'); ?></div>
</div>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rslogos);
?>
