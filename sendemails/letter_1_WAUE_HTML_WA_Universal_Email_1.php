<?php require_once("../webassist/email/mail_php.php"); ?>
<?php require_once('../Connections/localhost.php'); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php
if (!WA_Auth_RulePasses("Editors")){
	WA_Auth_RestrictAccess("../users/users_LogIn.php");
}
?>
<?php require_once( "../webassist/file_manipulation/helperphp.php" ); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
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
mysql_select_db($database_localhost, $localhost);
$query_rsEmails = "SELECT * FROM emailstest";
$rsEmails = mysql_query($query_rsEmails, $localhost) or die(mysql_error());
$row_rsEmails = mysql_fetch_assoc($rsEmails);
$totalRows_rsEmails = mysql_num_rows($rsEmails);
?>
?>
<?php require_once("../webassist/email/mailformatting_php.php"); ?>
<?php
if (($_SERVER["REQUEST_METHOD"] == "POST"))     {
  //WA Universal Email object="mail"
  @session_write_close();
  set_time_limit(0);
  $EmailRef = "waue_letter_1_1";
  $BurstSize = 200;
  $BurstTime = 1;
  $WaitTime = 1;
  $GoToPage = "send_mail_ok.php";
  $RecipArray = array();
  $StartBurst = time();
  $LoopCount = 0;
  $TotalEmails = 0;
  $RecipIndex = 0;
  // build up recipients array
  $CurIndex = sizeof($RecipArray);
  $RecipArray[$CurIndex] = array();
  $RecipArray[$CurIndex][] = $row_rsEmails;
  $RecipArray[$CurIndex][] = $rsEmails;
  $RecipArray[$CurIndex][] = "Email";
  $TotalEmails += mysql_num_rows($rsEmails);
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
      include("../webassist/email/waue_letter_1_1.php");
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
<html><!-- InstanceBegin template="/Templates/waue html body" codeOutsideHTMLIsLocked="false" --><head><!-- InstanceBeginEditable name="emailhead" -->
<style type="text/css">
<!--
table.MsoNormalTable {
font-size:10.0pt;
font-family:"Times New Roman","serif";
}
p.- {
margin:0cm;
margin-bottom:.0001pt;
font-size:12.0pt;
font-family:"Times New Roman","serif";
}
p.heading1 {
margin:0cm;
margin-bottom:.0001pt;
text-align:justify;
font-size:12.0pt;
font-family:"Times New Roman","serif";
}
p.MsoNormal {
margin:0cm;
margin-bottom:.0001pt;
font-size:10.0pt;
font-family:"Times New Roman","serif";
}
p.Hyperlink1 {
margin:0cm;
margin-bottom:.0001pt;
line-height:120%;
text-autospace:ideograph-numeric;
font-size:10.0pt;
font-family:"Adobe Garamond","serif";
color:blue;
text-decoration:underline;
text-underline:thick;
}
p.MsoBodyText {
margin:0cm;
margin-bottom:.0001pt;
text-align:justify;
font-size:10.0pt;
font-family:"Times New Roman","serif";
font-weight:bold;
}
-->
</style>
<!-- InstanceEndEditable --></head><body><!-- InstanceBeginEditable name="emailbody" -->

<table class="MsoNormalTable" border="0" cellspacing="0" cellpadding="0" style="margin-left:2.75pt;border-collapse:collapse;">
  <tr style="height:60.0pt;">
    <td width="104" style="width:78.25pt;border-top:solid black 1.0pt;border-left:none;border-bottom:solid black 1.0pt;border-right:none;padding:2.75pt 2.75pt 2.75pt 2.75pt;height:60.0pt;"><p class="-" style="layout-grid-mode:char;"><a name="OLE_LINK5" id="OLE_LINK5"></a><a name="OLE_LINK4" id="OLE_LINK4"><img src="letter_1_WAUE_HTML_WA_Universal_Email_1_clip_image002.jpg" alt="jpoplogo" width="97" height="97" /></a></p></td>
    <td width="492" style="width:369.0pt;border-top:solid black 1.0pt;border-left:none;border-bottom:solid black 1.0pt;border-right:none;padding:2.75pt 2.75pt 2.75pt 2.75pt;height:60.0pt;"><p class="heading1" align="center" style="margin-top:6.2pt;text-align:center;line-height:120%;layout-grid-mode:char;text-autospace:ideograph-numeric;vertical-align:middle;"><strong><span style="line-height:120%; font-size:15.0pt; color:black; ">PROPAGATION OF ORNAMENTAL    PLANTS</span></strong><strong><span style="line-height:120%; font-size:8.5pt; color:black; "> </span></strong></p>
      <p class="MsoNormal" align="center" style="text-align:center;line-height:120%;text-autospace:ideograph-numeric;vertical-align:middle;"><span style="line-height:120%; font-size:11.0pt; color:black; ">Editorial Office, University     of Forestry, 10 Kliment    Ohridski blvd.,</span></p>
      <p class="Hyperlink1" align="center" style="text-align:center;"><span style="line-height:120%; text-underline:none; font-family:'Times New Roman','serif'; font-size:11.0pt; color:black; text-decoration:none; ">Sofia</span><span style="font-family:'Times New Roman','serif'; font-size:11.0pt; color:black; text-decoration:none; "> 1797, Bulgaria,    Fax: (++ 359 2) 862 28 30, e-mail: </span> <a href="mailto:ivilievltu@yahoo.com"><span style="line-height:120%; text-underline:none; font-family:'Times New Roman','serif'; font-size:11.0pt; color:windowtext; text-decoration:none; ">ivilievltu@yahoo.com</span></a><span style="font-family:'Times New Roman','serif'; font-size:11.0pt; color:windowtext; text-decoration:none; ">,</span></p>
      <p class="Hyperlink1" align="center" style="text-align:center;"> <a href="http://www.journal-pop.org/"><span style="font-family:'Times New Roman','serif'; font-size:11.0pt; color:windowtext; text-decoration:none; ">www.journal-pop.org</span></a><span style="line-height:120%; text-underline:none; font-size:12.0pt; color:black; text-decoration:none; "> </span></p></td>
  </tr>
</table>
<p class="MsoNormal">&nbsp;</p>
<p class="MsoNormal">&nbsp;</p>
<?php echo $row_rsEmails['Name']?>
<p>Dear Dr. <?php echo ($RecipArray[$RecipIndex][0]["[Name]"])?>,</p>
<p class="MsoNormal" style="text-align:justify;"><span style="font-size:12.0pt; ">&nbsp;</span></p>
<p class="MsoNormal" style="text-align:justify;"><span style="font-size:12.0pt; ">The International journal <em>Propagation of Ornamental Plants</em> (ISSN 1311-9109) is medium in all  aspects of ornamental plants propagation. It publishes original papers aimed at  all aspects of in vitro and in vivo propagation (genetics, physiological,  biochemical, anatomical, technological etc.) of the ornamental species – trees,  shrubs and flowers. </span></p>
<p class="MsoNormal" style="text-align:justify;"><span style="font-size:12.0pt; ">As Editor-in-Chief, I strive to uphold and improve  the high standard of <em>Propagation of  Ornamental Plants </em>and to advance its function as a communication tool for  scientists. Thus, in addition to research articles <em>Propagation of Ornamental Plants</em> also publishes book reviews,  review papers, </span><span style="font-size:12.0pt; ">research</span><span style="font-size:12.0pt; "> notes, protocols  and technologies, announcements of conferences and meetings.</span></p>
<p class="MsoNormal" style="text-align:justify;"><span style="font-size:12.0pt; ">It is peer reviewed journal with Impact Factor. </span></p>
<p class="MsoNormal" style="text-align:justify;"><span style="font-size:12.0pt; ">&nbsp;</span></p>
<p class="MsoBodyText"><span style="font-size:12.0pt; font-weight:normal; ">The journal is covered by Current  Contents/Agriculture, Biology and Environmental Sciences and SCIE of Thomson Reuters  and by SCOPUS database of Elsevier.</span></p>
<p class="MsoNormal" style="text-align:justify;"><span style="font-size:12.0pt; ">&nbsp;</span></p>
<p class="MsoNormal" style="text-align:justify;"><strong><em><span style="font-size:12.0pt; ">By this letter I would kindly invite you to submit  your manuscript(s) for possible publishing.</span></em></strong><strong><span style="font-size:12.0pt; "> </span></strong><span style="font-size:12.0pt; "> </span></p>
<p class="MsoNormal" style="text-align:justify;"><span style="font-size:12.0pt; ">Please don&rsquo;t hesitate to contact with me if you have  any additional questions. </span></p>
<p class="MsoNormal" style="text-align:justify;"><span style="font-size:12.0pt; ">&nbsp;</span></p>
<p class="MsoNormal" style="text-align:justify;"><span style="font-size:12.0pt; ">&nbsp;</span></p>
<p class="MsoNormal" style="text-align:justify;"><span style="font-size:12.0pt; ">Best regards</span></p>
<p class="MsoNormal" style="text-align:justify;"><span style="font-size:12.0pt; ">&nbsp;</span></p>
<p class="MsoNormal" style="text-align:justify;"><span style="font-size:12.0pt; ">&nbsp;</span></p>
<p class="MsoNormal" style="text-align:justify;"><span style="font-size:12.0pt; ">Prof. Ivan Iliev</span></p>
<p>Editor-in-Chief</p>
<p class="MsoNormal"><span style="font-size:11.0pt; ">E-mail: </span><a href="mailto:ivilievltu@yahoo.com"><span style="font-size:11.0pt; ">ivilievltu@yahoo.com</span></a><span style="font-size:12.0pt; "> </span></p>
<p class="MsoNormal"><span style="font-size:12.0pt; ">Website: </span> <a href="http://www.journal-pop.org/"><span style="font-size:11.0pt; ">www.journal-pop.org</span></a><span style="font-size:11.0pt; "> </span></p>
<!-- InstanceEndEditable --></body><!-- InstanceEnd --></html>
<?php
mysql_free_result($rsEmails);
?>
