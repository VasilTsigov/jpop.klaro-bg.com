<?php require_once("../webassist/form_validations/wavt_scripts_php.php"); ?>
<?php require_once("../webassist/form_validations/wavt_validatedform_php.php"); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php 
if ($_SERVER["REQUEST_METHOD"] == "POST")  {
  $WAFV_Redirect = "";
  $_SESSION['WAVT_contactsIndex_875_Errors'] = "";
  if ($WAFV_Redirect == "")  {
    $WAFV_Redirect = $_SERVER["PHP_SELF"];
  }
  $WAFV_Errors = "";
  $WAFV_Errors .= WAValidateRQ(((isset($_POST["txtEmail"]))?$_POST["txtEmail"]:"") . "",false,1);
  $WAFV_Errors .= WAValidateEM(((isset($_POST["txtEmail"]))?$_POST["txtEmail"]:"") . "",true,2);
  $WAFV_Errors .= WAValidateRQ(((isset($_POST["txtName"]))?$_POST["txtName"]:"") . "",false,3);
  $WAFV_Errors .= WAValidateRQ(((isset($_POST["txtMessage"]))?$_POST["txtMessage"]:"") . "",false,4);
  $WAFV_Errors .= WAValidateLE(strtolower(((isset($_POST["Security_Code_1"]))?$_POST["Security_Code_1"]:"")) . "",strtolower($_SESSION['captcha_Security_Code_1']) . "",true,5);

  if ($WAFV_Errors != "")  {
    PostResult($WAFV_Redirect,$WAFV_Errors,"contactsIndex_875");
  }
}
?>
<?php require_once("../webassist/email/mail_php.php"); ?>
<?php require_once("../webassist/email/mailformatting_php.php"); ?>
<?php
if ((isset($_POST["txtSend"]) || isset($_POST["txtSend_x"])))     {
  //WA Universal Email object="mail"
  @session_write_close();
  set_time_limit(0);
  $EmailRef = "waue_contacts_Index_1";
  $BurstSize = 200;
  $BurstTime = 1;
  $WaitTime = 1;
  $GoToPage = "contacts_ThankYou.php";
  $RecipArray = array();
  $StartBurst = time();
  $LoopCount = 0;
  $TotalEmails = 0;
  $RecipIndex = 0;
  // build up recipients array
  $CurIndex = sizeof($RecipArray);
  $RecipArray[$CurIndex] = array();
  $RecipArray[$CurIndex ][] = "ivilievltu@yahoo.com";
  $RecipArray[$CurIndex ][] = "v.tsigov@gmail.com";
  $TotalEmails += sizeof($RecipArray[$CurIndex]);
  $RealWait = ($WaitTime<0.25)?0.25:($WaitTime+0.1);
  $TimeTracker = Array();
  $TotalBursts = floor($TotalEmails/$BurstSize);
  $AfterBursts = $TotalEmails % $BurstSize;
  $TimeRemaining = ($TotalBursts * $BurstTime) + ($AfterBursts*$RealWait);
  if ($TimeRemaining < ($TotalEmails*$RealWait) )  {
    $TimeRemaining = $TotalEmails*$RealWait;
  }
  writeUEProgress($EmailRef,0,$TotalEmails,$TimeRemaining);
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
      writeUEProgress($EmailRef,$LoopCount,$TotalEmails,round($TimeRemaining));
      wa_sleep($WaitTime);
      include("../webassist/email/waue_contacts_Index_1.php");
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
  @session_start();
  $_SESSION[$EmailRef."_Status"] = $GLOBALS[$EmailRef."_Status"];
  $_SESSION[$EmailRef."_Index"] = $GLOBALS[$EmailRef."_Index"];
  $_SESSION[$EmailRef."_From"] = $GLOBALS[$EmailRef."_From"];
  $_SESSION[$EmailRef."_To"] = $GLOBALS[$EmailRef."_To"];
  $_SESSION[$EmailRef."_Subject"] = $GLOBALS[$EmailRef."_Subject"];
  $_SESSION[$EmailRef."_Body"] = $GLOBALS[$EmailRef."_Body"];
  $_SESSION[$EmailRef."_Header"] = $GLOBALS[$EmailRef."_Header"];
  $_SESSION[$EmailRef."_Log"] = $GLOBALS[$EmailRef."_Log"];
  if (function_exists("rel2abs")) $GoToPage = $GoToPage?rel2abs($GoToPage,dirname(__FILE__)):"";
  if ($GoToPage!="")     {
    header("Location: ".$GoToPage);
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head><!-- InstanceBegin template="/Templates/waue html body" codeOutsideHTMLIsLocked="false" --><!-- InstanceBeginEditable name="emailhead" --><!-- InstanceEndEditable --></head><body><!-- InstanceBeginEditable name="emailbody" -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><img src="../images/EmailLogo/logo.png" alt="" width="97" height="97" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="12%"><strong>Email:</strong></td>
    <td width="88%"><?php echo ((isset($_POST["txtEmail"]))?$_POST["txtEmail"]:"")?></td>
  </tr>
  <tr>
    <td><strong>Name:</strong></td>
    <td><?php echo ((isset($_POST["txtName"]))?$_POST["txtName"]:"")?></td>
  </tr>
  <tr>
    <td><strong>Message:</strong></td>
    <td><?php echo ((isset($_POST["txtMessage"]))?$_POST["txtMessage"]:"")?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<!-- InstanceEndEditable --></body><!-- InstanceEnd --></html>