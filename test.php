<?php ?>
<?php require_once('Connections/localhost.php'); ?>
<?php require_once("webassist/form_validations/wavt_scripts_php.php"); ?>
<?php require_once("webassist/form_validations/wavt_validatedform_php.php"); ?>
<?php 
if ($_SERVER["REQUEST_METHOD"] == "POST")  {
  $WAFV_Redirect = "";
  $_SESSION['WAVT_test_562_Errors'] = "";
  if ($WAFV_Redirect == "")  {
    $WAFV_Redirect = $_SERVER["PHP_SELF"];
  }
  $WAFV_Errors = "";
  $WAFV_Errors .= WAValidateRQ(((isset($_POST["textfield"]))?$_POST["textfield"]:"") . "",false,2);

  if ($WAFV_Errors != "")  {
    PostResult($WAFV_Redirect,$WAFV_Errors,"test_562");
  }
}
?>
<?php require_once("webassist/database_management/wa_appbuilder_php.php"); ?>
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

mysql_select_db($database_localhost, $localhost);
$query_rsDate = "SELECT DATE_FORMAT(test.testTimeStamp,'%d.%m.%Y') AS Date, DATE_FORMAT(test.testTimeStamp,'%H:%i:%s') AS Time, DATE_FORMAT(test.testTimeStamp,'%Y') AS Year, test.testTimeStamp FROM test";
$rsDate = mysql_query($query_rsDate, $localhost) or die(mysql_error());
$row_rsDate = mysql_fetch_assoc($rsDate);
$totalRows_rsDate = mysql_num_rows($rsDate);
$query_rsDate = "SELECT DATE_FORMAT(test.testTimeStamp,'%d.%m.%Y') AS Date, DATE_FORMAT(test.testTimeStamp, '%H:%i:%s') AS Time, DATE_FORMAT(test.testTimeStamp,'%Y') AS Year FROM test";
$rsDate = mysql_query($query_rsDate, $localhost) or die(mysql_error());
$row_rsDate = mysql_fetch_assoc($rsDate);
$totalRows_rsDate = mysql_num_rows($rsDate);?>
<?php 
// WA DataAssist Insert
if ($_SERVER["REQUEST_METHOD"] == "POST") // Trigger
{
  $WA_connection = $localhost;
  $WA_table = "test";
  $WA_sessionName = "test_testID";
  $WA_redirectURL = "test.php";
  if (function_exists("rel2abs")) $WA_redirectURL = $WA_redirectURL?rel2abs($WA_redirectURL,dirname(__FILE__)):"";
  $WA_keepQueryString = false;
  $WA_fieldNamesStr = "testTimeStamp";
  $WA_fieldValuesStr = "".date('Y-m-d H:i:s')  ."";
  $WA_columnTypesStr = "',none,NULL";
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
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script src="http://code.jquery.com/jquery-1.6.1.js"></script>
<script src="scripts/test.js"  type="text/javascript"></script>
<style type="text/css">
#test {
	background: #CCC;
	height: 100px;
	width: 100px;
}
</style>
<script src="webassist/validation_tooltips/validation_tooltips.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="webassist/validation_tooltips/validation_tooltips.css"/>
<script type="text/javascript">
var WA_SpryValidationTooltip_ErrorTips  = new WA_SpryValidationTooltip(10,20,15,"slant-left","top-right",2,10,"#9d8d7a","#2d5575","#a2c3dc","#a2c3dc",100);
</script>
</head>

<body>
<form id="form1" name="form1" method="post" action="">
  <input type="submit" name="button" id="button" value="Submit" />
  <br />
  <table id="table" width="100%" border="0" cellspacing="0" cellpadding="0">
    <?php do { ?>
      <tr>
        <td><?php echo $row_rsDate['testTimeStamp']; ?></td>
        <td><?php echo $row_rsDate['Date']; ?></td>
        <td><?php echo $row_rsDate['Time']; ?></td>
        <td><?php echo $row_rsDate['Year']; ?></td>
        <td>&nbsp;</td>
      </tr>
      <?php } while ($row_rsDate = mysql_fetch_assoc($rsDate)); ?>
  </table><br />
  <a href="#" id="vasil">vasil</a>
  <br />
  <br />
  <div id="test"></div>
  <p>&nbsp;</p>
  <p>
    <label for="textfield"></label>
    <label for="textfield2"></label>
    <input type="text" name="textfield" id="textfield2" />
    <?php
if (ValidatedField('test_562','test_562'))  {
  if ((strpos((",".ValidatedField("test_562","test_562").","), "," . "2" . ",") !== false || "2" == ""))  {
    if (!(false))  {
?>
      sddsedsdsds
  <?php //WAFV_Conditional test.php test_562(2:)
    }
  }
}?>
  </p>
</form>
</body>
</html>
<?php
mysql_free_result($rsDate);
?>
