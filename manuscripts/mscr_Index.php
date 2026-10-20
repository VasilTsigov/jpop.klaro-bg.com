<?php require_once('../Connections/localhost.php'); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php require_once("../webassist/database_management/wa_appbuilder_php.php"); ?>
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

$currentPage = $_SERVER["PHP_SELF"];
?>
<?php
$maxRows_rsMnscrpt = 5;
$pageNum_rsMnscrpt = 0;
if (isset($_GET['pageNum_rsMnscrpt'])) {
  $pageNum_rsMnscrpt = $_GET['pageNum_rsMnscrpt'];
}
$startRow_rsMnscrpt = $pageNum_rsMnscrpt * $maxRows_rsMnscrpt;

$colname_rsMnscrpt = "-1";
if (isset($_SESSION['UserID'])) {
  $colname_rsMnscrpt = $_SESSION['UserID'];
}
mysql_select_db($database_localhost, $localhost);
$query_rsMnscrpt = sprintf("SELECT users.UserID, users.UserEmail, CONCAT(users.UserFirstName,' ', users.UserLastName) AS FullName, mnscrpts.mscrID, mnscrpts.mscrFullTitle, mnscrpts.mscrCodeU, mnscrpts.mscrUpldDate, mnscrpts.mscrReviewerFullName_1, mnscrpts.mscrReviewerFullName_2, mnscrpts.mscrReviewerFullName_3, mnscrpts.mscrDateToReviewer, mnscrpts.mscrReviewer_1, mnscrpts.mscrReviewer_2, mnscrpts.mscrReviewer_3, mnscrpts.mscrFileReviewer_1, mnscrpts.mscrDateFileReviewer_1,  mnscrpts.mscrFileReviewer_2, mnscrpts.mscrDateFileReviewer_2, mnscrpts.mscrFileReviewer_3, mnscrpts.mscrDateFileReviewer_3,  mnscrpts.mnscStatus_1, mnscrpts.mnscStatus_2, mnscrpts.mnscStatus_3, mnscrpts.mnscStatus_4, mnscrpts.mscrFileCorrected, mnscrpts.mscrDateFileCorrected, mnscrpts.mnscConfirmReviewer_1, mnscrpts.mnscConfirmReviewer_2, mnscrpts.mnscConfirmReviewer_3, mnscrpts.mnscrDateConfirm_1, mnscrpts.mnscrDateConfirm_2, mnscrpts.mnscrDateConfirm_3, mnscrpts.mnscChecklistFileReviewer_1, mnscrpts.mnscChecklistFileReviewer_2, mnscrpts.mnscChecklistFileReviewer_3, mnscrpts.mscrChecklist_1, mnscrpts.mscrChecklist_2, mnscrpts.mscrChecklist_3, mnscrpts.mscrDateToReviewer_1, mnscrpts.mscrDateToReviewer_2, mnscrpts.mscrDateToReviewer_3, mnscrpts.mscrRecommendation_1, mnscrpts.mscrRecommendation_2, mnscrpts.mscrRecommendation_3, mnscrpts.mscrCorrected, mnscrpts.mscrMS_1, mnscrpts.mscrMS_2, mnscrpts.mscrMS_3, mnscrpts.mscrEditorsLetter, mnscrpts.mscrDateEditorsLetter, mnscrpts.mscrUploadProofFile, mnscrpts.mscrUploadProofDate, mnscrpts.mscrUploadProofEditorsFile, mnscrpts.mscrUploadProofEditorsFileDate FROM mnscrpts, users WHERE users.UserID= mnscrpts.mscrAutorID AND mnscrpts.mscrAutorID=%s ORDER BY mnscrpts.mscrID DESC", GetSQLValueString($colname_rsMnscrpt, "int"));
$query_limit_rsMnscrpt = sprintf("%s LIMIT %d, %d", $query_rsMnscrpt, $startRow_rsMnscrpt, $maxRows_rsMnscrpt);
$rsMnscrpt = mysql_query($query_limit_rsMnscrpt, $localhost) or die(mysql_error());
$row_rsMnscrpt = mysql_fetch_assoc($rsMnscrpt);

if (isset($_GET['totalRows_rsMnscrpt'])) {
  $totalRows_rsMnscrpt = $_GET['totalRows_rsMnscrpt'];
} else {
  $all_rsMnscrpt = mysql_query($query_rsMnscrpt, $localhost);
  $totalRows_rsMnscrpt = mysql_num_rows($all_rsMnscrpt);
}
$totalPages_rsMnscrpt = ceil($totalRows_rsMnscrpt/$maxRows_rsMnscrpt)-1;
?>
<?php
if (!session_id()) session_start();
if ("" == "")     {
  $_SESSION["randomConfirm"] = "".$_SESSION['randomConfirm']  ."";
}

$queryString_rsMnscrpt = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsMnscrpt") == false && 
        stristr($param, "totalRows_rsMnscrpt") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsMnscrpt = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsMnscrpt = sprintf("&totalRows_rsMnscrpt=%d%s", $totalRows_rsMnscrpt, $queryString_rsMnscrpt);?>
<?php 
// WA DataAssist Insert
if ("" == "") // Trigger
{
  $WA_connection = $localhost;
  $WA_table = "log";
  $WA_sessionName = "log_logID";
  $WA_redirectURL = "";
  if (function_exists("rel2abs")) $WA_redirectURL = $WA_redirectURL?rel2abs($WA_redirectURL,dirname(__FILE__)):"";
  $WA_keepQueryString = false;
  $WA_fieldNamesStr = "logUser|logIP|logContent";
  $WA_fieldValuesStr = "".$_SESSION['UserFirstName']  ." ".$_SESSION['UserLastName']  .", ".$_SESSION['UserEmail']  ."" . $WA_AB_Split . "".((isset($_SERVER["REMOTE_ADDR"]))?$_SERVER["REMOTE_ADDR"]:"")  ."" . $WA_AB_Split . "My manuscripts";
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
<title>Journal-pop - My Manuscripts</title>
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
<style type="text/css">
a:link {
	text-decoration: none;
	color: #666;
}
a:visited {
	text-decoration: none;
	color: #666;
}
a:hover {
	text-decoration: underline;
	color: #333;
}
a:active {
	text-decoration: none;
	color: #333;
}
</style><!-- InstanceEndEditable -->
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
<!-- InstanceParam name="manuscript" type="text" value="current" -->
</head>

<body>

<div id="container">
  <div id="header">
    
  </div>
  <div id="navigation">
    
<ul>
        <li><a href="../index.php" class="">Home</a></li> 
        <li><a href="../users/users_LogIn.php" class="">Login</a></li>
    <li><a href="mscr_Submit.php" class="">Submit Manuscripts</a></li>        <li><a href="mscr_Index.php" class="current">Manuscripts</a></li>
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
    <?php if ($totalRows_rsMnscrpt > 0) { // Show if recordset not empty ?>
  <div id="mainContent"> <span class="title">My Manuscripts</span>
    <hr />
    <label for="textfield"></label>
    <br />
    Manuscripts:
    <?php
for ($i=0; $i <= $totalPages_rsMnscrpt; $i++) {
  $TFM_counter = ($i) * $maxRows_rsMnscrpt + 1;
  $TFM_endCounter = min($totalRows_rsMnscrpt,$TFM_counter + $maxRows_rsMnscrpt - 1);
  if($i != $pageNum_rsMnscrpt) {
    printf('<a href="'."%s?pageNum_rsMnscrpt=%d%s", $currentPage, $i, $queryString_rsMnscrpt.'">');
    echo "$TFM_counter-$TFM_endCounter</a>";
  }else{
    echo "<strong>$TFM_counter-$TFM_endCounter</strong>";
  }
  if($i != $totalPages_rsMnscrpt) echo " | ";
}
?>
    <br />
    <br />
    <?php do { ?>
      <div id="all_manuscripts">
        <form id="form1" name="form1" method="post" action="">
          <table width="100%" border="0" cellpadding="0" cellspacing="1" id="tbl_all_manuscript">
          <tr>
            <td width="87" align="center" bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="0" cellspacing="1" id="tbl_all_manuscript2">
              <tr>
                <td width="117" height="20" rowspan="2" align="center" valign="middle" bgcolor="#FEED77"><?php echo $row_rsMnscrpt['mscrCodeU']; ?></td>
                <td colspan="4" rowspan="2" align="left" valign="middle" bgcolor="#DFDFDF"><?php echo $row_rsMnscrpt['mscrFullTitle']; ?></td>
                <td colspan="2" align="center" bgcolor="#DFDFDF"><?php echo $row_rsMnscrpt['FullName']; ?></td>
                
              </tr>
              <tr>
                <td bgcolor="#DFDFDF"><?php echo $row_rsMnscrpt['mscrUpldDate']; ?></td>
                <td align="right" bgcolor="#DFDFDF">[ <a href="../download/mainManuscriptFile.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>">Manuscript</a> ]</td>
              </tr>
              <tr>
                <td align="left" valign="middle" bgcolor="#FFFFFF">1. <a href="mscr_SendToReviewerNo1.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>"></a>Reviewer No 1 </td>
                <td colspan="2" align="left" valign="middle" bgcolor="#FFFFFF"><?php echo $row_rsMnscrpt['mscrDateToReviewer_1']; ?></td>
                <td width="124" align="left" bgcolor="#FFFFFF">( <?php echo $row_rsMnscrpt['mnscConfirmReviewer_1']; ?> - <?php echo $row_rsMnscrpt['mnscrDateConfirm_1']; ?> )</td>
                <td width="190" align="left" bgcolor="#FFFFFF">[ <a href="../download/mscrFileChecklist_1.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>"><?php echo $row_rsMnscrpt['mscrChecklist_1']; ?></a> ] [ <a href="../download/mnscrFileReviewer_1.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>"><?php echo $row_rsMnscrpt['mscrMS_1']; ?></a> ] ( <?php echo $row_rsMnscrpt['mscrDateFileReviewer_1']; ?> ) </td>
                <td width="79" align="left" bgcolor="#FFFFFF"><?php echo $row_rsMnscrpt['mscrRecommendation_1']; ?></td>
                <td width="102" align="right" bgcolor="#FFFFFF">[ <a href="../coverLeter/coverLeter_Index.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>">Cover Letter</a> ]</td>
              </tr>
              <tr>
                <td align="left" valign="middle" bgcolor="#FFFFFF">2. Reviewer No 2 </td>
                <td colspan="2" align="left" valign="middle" bgcolor="#FFFFFF"><?php echo $row_rsMnscrpt['mscrDateToReviewer_2']; ?></td>
                <td align="left" bgcolor="#FFFFFF">( <?php echo $row_rsMnscrpt['mnscConfirmReviewer_2']; ?> - <?php echo $row_rsMnscrpt['mnscrDateConfirm_2']; ?> )</td>
                <td align="left" bgcolor="#FFFFFF">[ <a href="../download/mscrFileChecklist_2.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>"><?php echo $row_rsMnscrpt['mscrChecklist_2']; ?></a> ] [ <a href="../download/mnscrFileReviewer_2.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>"><?php echo $row_rsMnscrpt['mscrMS_2']; ?></a> ] ( <?php echo $row_rsMnscrpt['mscrDateFileReviewer_2']; ?> ) </td>
                <td width="79" align="left" bgcolor="#FFFFFF"><?php echo $row_rsMnscrpt['mscrRecommendation_2']; ?></td>
                <td width="102" align="right" bgcolor="#FFFFFF">[ <a href="../abstract/abstract_Index.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>">Abstract</a> ]</td>
              </tr>
              <tr>
                <td align="left" valign="middle" bgcolor="#FFFFFF">3. Reviewer No 3 </td>
                <td colspan="2" align="left" valign="middle" bgcolor="#FFFFFF"><?php echo $row_rsMnscrpt['mscrDateToReviewer_3']; ?></td>
                <td align="left" bgcolor="#FFFFFF">( <?php echo $row_rsMnscrpt['mnscConfirmReviewer_3']; ?> - <?php echo $row_rsMnscrpt['mnscrDateConfirm_3']; ?> )</td>
                <td align="left" bgcolor="#FFFFFF">[ <a href="../download/mscrFileChecklist_3.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>"><?php echo $row_rsMnscrpt['mscrChecklist_3']; ?></a> ] [ <a href="../download/mnscrFileReviewer_3.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>"><?php echo $row_rsMnscrpt['mscrMS_3']; ?></a> ] ( <?php echo $row_rsMnscrpt['mscrDateFileReviewer_3']; ?> ) </td>
                <td width="79" align="left" bgcolor="#FFFFFF"><?php echo $row_rsMnscrpt['mscrRecommendation_3']; ?></td>
                <td width="102" align="right" bgcolor="#FFFFFF">[ <a href="mnsc_PhotographsAndGrapphs.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>">Figures</a> ]</td>
              </tr>
              <tr>
                <td height="14" colspan="2" align="center" valign="middle" bgcolor="#FFFFFF"><a href="../download/mnscrCorrectedFile.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>">[</a> <a href="mscr_UpldFileCorrected.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>">Upload corrected manuscript</a> <a href="../download/mnscrCorrectedFile.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>">]</a></td>
                <td height="14" colspan="3" align="left" valign="middle" bgcolor="#FFFFFF"><a href="../download/mnscrCorrectedFile.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>"><?php echo $row_rsMnscrpt['mscrCorrected']; ?></a> ( <?php echo $row_rsMnscrpt['mscrDateFileCorrected']; ?> )</td>
                <td align="right" valign="middle" bgcolor="#FFFFFF">&nbsp;</td>
                <td align="right" valign="middle" bgcolor="#FFFFFF">&nbsp;</td>
              </tr>
              <tr>
                <td height="14" colspan="2" align="center" valign="middle" bgcolor="#FFFFFF">[ <a href="mscr_SubmitFigures.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>">Upload corrected Figures</a> ]</td>
                <td height="14" colspan="5" align="left" valign="middle" bgcolor="#FFFFFF">Editor's Decision Comments: <a href="../download/mscrEditorsLetter.php?download=<?php echo $row_rsMnscrpt['mscrID']; ?>"><?php echo $row_rsMnscrpt['mscrEditorsLetter']; ?></a> ( <?php echo $row_rsMnscrpt['mscrDateEditorsLetter']; ?> )</td>
                </tr>
              <tr>
                <td height="14" colspan="2" align="center" valign="middle" bgcolor="#FFFFFF">Proof File:</td>
                <td height="14" colspan="5" align="left" valign="middle" bgcolor="#FFFFFF"><a href="../download/mscrProofFile.php?download=<?php echo $row_rsMnscrpt['mscrID']; ?>"><?php echo $row_rsMnscrpt['mscrUploadProofFile']; ?></a> ( <?php echo $row_rsMnscrpt['mscrUploadProofDate']; ?> )</td>
              </tr>
              <tr>
                <td height="14" colspan="2" align="center" valign="middle" bgcolor="#FFFFFF">Proof Editor's Letter:</td>
                <td height="14" colspan="5" align="left" valign="middle" bgcolor="#FFFFFF"><a href="../download/mscrProofEditorsLetter.php?download=<?php echo $row_rsMnscrpt['mscrID']; ?>"><?php echo $row_rsMnscrpt['mscrUploadProofEditorsFile']; ?></a> ( <?php echo $row_rsMnscrpt['mscrUploadProofEditorsFileDate']; ?> )</td>
              </tr>
            </table>              <a href="mnsc_PhotographsAndGrapphs.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']; ?>"></a></td>
          </tr>
          </table>
        </form>
      </div>
      <?php } while ($row_rsMnscrpt = mysql_fetch_assoc($rsMnscrpt)); ?>
  </div>
      Manuscripts:
      <?php
for ($i=0; $i <= $totalPages_rsMnscrpt; $i++) {
  $TFM_counter = ($i) * $maxRows_rsMnscrpt + 1;
  $TFM_endCounter = min($totalRows_rsMnscrpt,$TFM_counter + $maxRows_rsMnscrpt - 1);
  if($i != $pageNum_rsMnscrpt) {
    printf('<a href="'."%s?pageNum_rsMnscrpt=%d%s", $currentPage, $i, $queryString_rsMnscrpt.'">');
    echo "$TFM_counter-$TFM_endCounter</a>";
  }else{
    echo "<strong>$TFM_counter-$TFM_endCounter</strong>";
  }
  if($i != $totalPages_rsMnscrpt) echo " | ";
}
?>
      <?php } // Show if recordset not empty ?>
<?php if ($totalRows_rsMnscrpt == 0) { // Show if recordset empty ?>
        No manuscripts
  <?php } // Show if recordset empty ?>
    <!-- InstanceEndEditable --></div>
  </div>
<div id="footer">Journal-POP &copy; 2011 - <?php echo date('Y'); ?></div>
</div>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsMnscrpt);
?>
