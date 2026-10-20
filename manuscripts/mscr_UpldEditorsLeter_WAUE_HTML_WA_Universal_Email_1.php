<?php require_once('../Connections/localhost.php'); ?>
<?php require_once("../webassist/form_validations/wavt_scripts_php.php"); ?>
<?php require_once("../webassist/form_validations/wavt_validatedform_php.php"); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php  
if (!WA_Auth_RulePasses("Editors")){
	WA_Auth_RestrictAccess("../users/users_LogIn.php");
}
?>
<?php require_once("../webassist/file_manipulation/helperphp.php"); ?>
<?php require_once("../webassist/database_management/wa_appbuilder_php.php"); ?>
<?php 
if ($_SERVER["REQUEST_METHOD"] == "POST")  {
  $WAFV_Redirect = "";
  $_SESSION['WAVT_mscrUpldEditorsLeter_734_Errors'] = "";
  if ($WAFV_Redirect == "")  {
    $WAFV_Redirect = $_SERVER["PHP_SELF"];
  }
  $WAFV_Errors = "";
  $WAFV_Errors .= WAValidateRQ(((isset($_FILES["txtEditorsLetter"]))?$_FILES["txtEditorsLetter"]["name"]:"") . "",false,1);

  if ($WAFV_Errors != "")  {
    PostResult($WAFV_Redirect,$WAFV_Errors,"mscrUpldEditorsLeter_734");
  }
}
?>
<?php include("../scripts/zeit.php"); ?>
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
$colname_rsEditorsLeter = "-1";
if (isset($_GET['mnscrpt_id'])) {
  $colname_rsEditorsLeter = (get_magic_quotes_gpc()) ? $_GET['mnscrpt_id'] : addslashes($_GET['mnscrpt_id']);
}
mysql_select_db($database_localhost, $localhost);
$query_rsEditorsLeter = sprintf("SELECT mnscrpts.mscrID, mnscrpts.mscrAutorID, mnscrpts.mscrFullTitle, mnscrpts.mscrCodeU, mnscrpts.mscrEditorsLetter, users.UserEmail, CONCAT(users.UserFirstName,' ', users.UserLastName) AS FullAutorName, mnscrpts.mscrUpldDate FROM mnscrpts, users WHERE mnscrpts.mscrAutorID=users.UserID AND mnscrpts.mscrID=%s", GetSQLValueString($colname_rsEditorsLeter, "int"));
$rsEditorsLeter = mysql_query($query_rsEditorsLeter, $localhost) or die(mysql_error());
$row_rsEditorsLeter = mysql_fetch_assoc($rsEditorsLeter);
$totalRows_rsEditorsLeter = mysql_num_rows($rsEditorsLeter);?>
<?php
// WA_UploadResult1 Params Start
$WA_UploadResult1_Params = array();
// WA_UploadResult1_1 Start
$WA_UploadResult1_Params["WA_UploadResult1_1"] = array(
	'UploadFolder' => "../files/editletter/",
	'FileName' => "[FileName](FromEditor)",
	'DefaultFileName' => "",
	'ResizeType' => "0",
	'ResizeWidth' => "0",
	'ResizeHeight' => "0",
	'ResizeFillColor' => "" );
// WA_UploadResult1_1 End
// WA_UploadResult1 Params End
?>
<?php
WA_DFP_SetupUploadStatusStruct("WA_UploadResult1");
if(isset($_POST["button"])){
	WA_DFP_UploadFiles("WA_UploadResult1", "txtEditorsLetter", "0", "", "false", $WA_UploadResult1_Params);
}
?>
<?php require_once("../webassist/email/mail_php.php"); ?>
<?php require_once("../webassist/email/mailformatting_php.php"); ?>
<?php 
// WA DataAssist Update
if ($_SERVER["REQUEST_METHOD"] == "POST") // Trigger
{
  $WA_connection = $localhost;
  $WA_table = "mnscrpts";
  $WA_redirectURL = "";
  if (function_exists("rel2abs")) $WA_redirectURL = $WA_redirectURL?rel2abs($WA_redirectURL,dirname(__FILE__)):"";
  $WA_keepQueryString = false;
  $WA_indexField = "mscrID";
  $WA_fieldNamesStr = "mscrEditorsLetter|mscrDateEditorsLetter";
  $WA_fieldValuesStr = "".$WA_DFP_UploadStatus["WA_UploadResult1"]["serverFileName"]  ."" . $WA_AB_Split . "".((isset($_POST["dateNow"]))?$_POST["dateNow"]:"")  ."";
  $WA_columnTypesStr = "',none,''|',none,''";
  $WA_comparisonStr = "=|=";
  $WA_fieldNames = explode("|", $WA_fieldNamesStr);
  $WA_fieldValues = explode($WA_AB_Split, $WA_fieldValuesStr);
  $WA_columns = explode("|", $WA_columnTypesStr);
  
  $WA_where_fieldValuesStr = "".$row_rsEditorsLeter['mscrID']  ."";
  $WA_where_columnTypesStr = "none,none,NULL";
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
if (!isset($_SESSION))session_start();
if (($WA_DFP_UploadStatus["WA_UploadResult1"]["statusCode"] == 1))     {
  //WA Universal Email object="mail"
  set_time_limit(0);
  $EmailRef = "waue_mscr_UpldEditorsLeter_1";
  $BurstSize = 200;
  $BurstTime = 1;
  $WaitTime = 1;
  $GoToPage = "mscr_All.php";
  $RecipArray = array();
  $StartBurst = time();
  $LoopCount = 0;
  $TotalEmails = 0;
  $RecipIndex = 0;
  // build up recipients array
  $CurIndex = sizeof($RecipArray);
  $RecipArray[$CurIndex] = array();
  $RecipArray[$CurIndex ][] = "".$row_rsEditorsLeter['UserEmail']  ."";
  $TotalEmails += sizeof($RecipArray[$CurIndex]);
  $RealWait = ($WaitTime<0.25)?0.25:($WaitTime+0.1);
  $TimeTracker = Array();
  $TotalBursts = floor($TotalEmails/$BurstSize);
  $AfterBursts = $TotalEmails % $BurstSize;
  $TimeRemaining = ($TotalBursts * $BurstTime) + ($AfterBursts*$RealWait);
  if ($TimeRemaining < ($TotalEmails*$RealWait) )  {
    $TimeRemaining = $TotalEmails*$RealWait;
  }
  $_SESSION[$EmailRef."_Total"] = $TotalEmails;
  $_SESSION[$EmailRef."_Index"] = 0;
  $_SESSION[$EmailRef."_Remaining"] = $TimeRemaining;
  while ($RecipIndex < sizeof($RecipArray))  {
    $EnteredValue = is_string($RecipArray[$RecipIndex][0]);
    $CurIndex = 0;
    while (($EnteredValue && $CurIndex < sizeof($RecipArray[$RecipIndex])) || (!$EnteredValue && $RecipArray[$RecipIndex][0])) {
      $starttime = microtime_float();
      if ($EnteredValue)  {
        $RecipientEmail = $RecipArray[$RecipIndex][$CurIndex];
      }  else  {
        $RecipientEmail = $RecipArray[$RecipIndex][0][$RecipArray[$RecipIndex][2]];
      }
      $EmailsRemaining = ($TotalEmails- $LoopCount);
      $BurstsRemaining = ceil(($EmailsRemaining-$AfterBursts)/$BurstSize);
      $IntoBurst = ($EmailsRemaining-$AfterBursts) % $BurstSize;
      if ($AfterBursts<$EmailsRemaining) $IntoBurst = 0;
      $TimeRemaining = ($BurstsRemaining * $BurstTime * 60) + ((($AfterBursts<$EmailsRemaining)?$AfterBursts:$EmailsRemaining)*$RealWait) - (($AfterBursts>$EmailsRemaining)?0:($IntoBurst*$RealWait));
      if ($TimeRemaining < ($EmailsRemaining*$RealWait) )  {
        $TimeRemaining = $EmailsRemaining*$RealWait;
      }
      $CurIndex ++;
      $LoopCount ++;
      session_commit();
      session_start();
      $_SESSION[$EmailRef."_Index"] = $LoopCount;
      $_SESSION[$EmailRef."_Remaining"] = round($TimeRemaining);
      session_commit();
      wa_sleep($WaitTime);
      include("../webassist/email/waue_mscr_UpldEditorsLeter_1.php");
      $endtime = microtime_float();
      $TimeTracker[] =$endtime - $starttime;
      $RealWait = array_sum($TimeTracker)/sizeof($TimeTracker);
      if ($LoopCount % $BurstSize == 0 && $CurIndex < sizeof($RecipArray[$RecipIndex]))  {
        $TimePassed = (time() - $StartBurst);
        if ($TimePassed < ($BurstTime*60))  {
          $WaitBurst = ($BurstTime*60) -$TimePassed;
          wa_sleep($WaitBurst);
        }
        else  {
          $TimeRemaining = ($TotalEmails- $LoopCount)*$RealWait;
        }
        $StartBurst = time();
      }
      if (!$EnteredValue)  {
        $RecipArray[$RecipIndex][0] =  mysql_fetch_assoc($RecipArray[$RecipIndex][1]);
      }
    }
    $RecipIndex ++;
  }
  $_SESSION[$EmailRef."_Total"] = 0;
  $_SESSION[$EmailRef."_Index"] = 0;
  $_SESSION[$EmailRef."_Remaining"] = 0;
  session_commit();
  session_start();
  if ($GoToPage!="")     {
    header("Location: ".$GoToPage);
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- InstanceBegin template="/Templates/waue html body" codeOutsideHTMLIsLocked="false" --><head><!-- InstanceBeginEditable name="emailhead" --><!-- InstanceEndEditable --></head><body><!-- InstanceBeginEditable name="emailbody" -->

<div>
  <div>
    <div>Dear Dr. <?php echo $row_rsEditorsLeter['FullAutorName']?>,</div>
    <div>
      <div></div>
    We would like to inform you that you have received &quot;Editor's   Decision and Comments&quot; for your manuscript. Please find attached this   letter to the journal website (<a href="http://www.journal-pop.org" target="_blank">www.journal-pop.org</a>). Please open it and follow the suggestions and recommendations there.</div>
    <div> </div>
    <div>With best regards</div>
    <div> </div>
    <div>Editorial Office</div>
    <div>Journal &quot;Propagation of Ornamental Plants&quot;</div>
    <div>-------------------------------------------<br />
      Dr. Ivan Iliev<br />
      University of Forestry<br />
      10 Kliment Ohridski blvd.<br />
      1756 Sofia<br />
      Bulgaria<br />
      Fax: + 359 2 862 28 30<br />
      E-mail: ivilievltu@yahoo.com</div>
  </div>
</div>
<p>&nbsp;</p>
<!-- InstanceEndEditable --></body><!-- InstanceEnd -->