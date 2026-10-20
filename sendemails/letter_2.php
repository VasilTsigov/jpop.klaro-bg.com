<?php require_once('../Connections/localhost.php'); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); ?>
<?php
if (!WA_Auth_RulePasses("Editors")){
	WA_Auth_RestrictAccess("../users/users_LogIn.php");
}
?>
<?php require_once( "../webassist/file_manipulation/helperphp.php" ); ?>
<?php require_once( "../webassist/security_assist/helper_php.php" ); 
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
$query_rsEmails = "SELECT * FROM emailstest";
$rsEmails = mysql_query($query_rsEmails, $localhost) or die(mysql_error());
$row_rsEmails = mysql_fetch_assoc($rsEmails);
$totalRows_rsEmails = mysql_num_rows($rsEmails);
?>
<?php require_once("../webassist/email/mail_php.php"); ?>
<?php require_once("../webassist/email/mailformatting_php.php"); ?>
<?php
if (($_SERVER["REQUEST_METHOD"] == "POST"))     {
  //WA Universal Email object="mail"
  @session_write_close();
  set_time_limit(0);
  $EmailRef = "waue_letter_2_1";
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
  $RecipArray[$CurIndex][] = "Name";
  $TotalEmails += mysql_num_rows($rsEmails);
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
      include("../webassist/email/waue_letter_2_1.php");
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
      <div id="form1_ProgressWrapper">
        <form id="form1" name="form1" method="post" action="">
          <span class="title">Send Mail(s) - Letter 2 </span>
          <hr />
          <div id="letter1">
            <table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="104"><p><img src="letter_2_clip_image002.jpg" alt="jpoplogo" width="97" height="97" /></p></td>
                <td width="528"><p align="center"><strong>PROPAGATION OF ORNAMENTAL    PLANTS</strong><strong> </strong><br />
                  Editorial    Office, University     of Forestry, 10 Kliment    Ohridski blvd.,<br />
                  Sofia 1797,    Bulgaria, Fax: (++ 359 2) 862 28 30, e-mail: <a href="mailto:ivilievltu@yahoo.com">ivilievltu@yahoo.com</a>,<br />
                  <a href="http://www.journal-pop.org/">www.journal-pop.org</a></p></td>
              </tr>
            </table>
            <p>&nbsp;</p>
            <p>Dear Dr. ....................,</p>
            <p>The International journal <em>Propagation of Ornamental Plants</em> (ISSN 1311-9109) is medium in all  aspects of ornamental plants propagation. It publishes original papers aimed at  all aspects of in vitro and in vivo propagation (genetics, physiological,  biochemical, anatomical, technological etc.) of the ornamental species – trees,  shrubs and flowers. <br />
              As Editor-in-Chief, I strive to uphold and improve the  high standard of <em>Propagation of  Ornamental Plants </em>and to advance its function as a communication tool for  scientists. Thus, in addition to research articles <em>Propagation of Ornamental Plants</em> also publishes book reviews,  review papers, short notes, protocols and technologies, announcements of  conferences and meetings.</p>
            <p>The journal is  covered by Current Contents/Agriculture, Biology and Environmental Sciences and  SCIE of Thomson Scientific and by SCOPUS database of Elsevier.<br />
              <strong>It is peer reviewed journal  with Impact Factor.</strong></p>
            <p><strong><u>1. Subscription rates for 2015 are the following:</u></strong><u> </u></p>
            <p><em><u>Institutional</u></em><br />
              - printed version of vol. 15, No 1,2,3,  and 4: 155 USD&nbsp;+ 25 USD postal expenses (<strong>Europe</strong>)  i.e. <strong>180 USD</strong><br />
              - printed version of vol. 15, No 1,2,3,  and 4: 155 USD&nbsp;+ 30 USD postal expenses (<strong>overseas</strong>) i.e. <strong>185 USD</strong><br />
              - electronic version for 1 user (one IP  address) of&nbsp;vol. 15, No 1,2,3, and 4: <strong>155  USD</strong><br />
              &nbsp;&nbsp;<br />
              <em><u>Personal</u></em><br />
              - printed version of vol. 15, No 1,2,3,  and 4: 125 USD&nbsp;+ 20 USD postal expenses (<strong>Europe</strong>)  i.e. <strong>145 USD</strong><br />
              - printed version of vol. 15, No 1,2,3,  and 4: 125 USD&nbsp;+ 25 USD postal expenses (<strong>overseas</strong>) i.e. <strong>150 USD</strong><br />
              - electronic version for 1 user  of&nbsp;vol. 15, No 1,2,3, and 4: <strong>125  USD</strong></p>
            <p><u>2. </u><strong><u>Please find the discounts&nbsp;for printed  version, 2015</u></strong><strong><u>:</u></strong><u> </u><br />
              - for 1-3 subscriptions there  is not discount. <br />
              - for 4-10 subscriptions the discount is 15% <br />
              - for 11-20 subscriptions the discount is 25% <br />
              - for 21-30 subscriptions the discount is 35% <br />
              - for 31-50 subscriptions is 45% <br />
              - for 51-100 subscriptions is 55% <br />
              - for more than 100 subscriptions is 65% </p>
            <p><strong><u>3. The subscription rates for back, printed  issues&nbsp;are the following:</u></strong><br />
              <strong>2014, vol. 14, No 1,2,3,  and 4:</strong> 125 USD&nbsp;+ 20 (25) USD postal  expenses (Europe)  i.e. <strong>145 USD and 1</strong><strong>50 (overseas) USD</strong><br />
              <strong>2013, </strong>vol. 13, No 1,2,3, and 4: 125 USD&nbsp;+ 20 (25) USD postal expenses  (Europe) i.e. <strong>145  USD and 1</strong><strong>50 (overseas) USD<u></u></strong><br />
              <strong>2012, </strong>vol. 12, No 1,2,3, and 4: 100  USD&nbsp;+ 20 (25) USD postal expenses (Europe) i.e. <strong>120 USD and 125  (overseas) USD</strong><br />
              <strong>2011, </strong>vol. 11, No 1,2,3, and 4: 100  USD&nbsp;+ 20 (25) USD postal expenses (Europe) i.e. <strong>120 USD and 125  (overseas) USD</strong><br />
              <strong>2010, vol. </strong><strong>10</strong><strong>, No  1, 2, 3, and 4</strong><strong>= </strong>100 USD + 20 (25) USD postal expenses  i.e<strong>. <strong>120 (Europe)  and 125 (overseas) USD</strong></strong><br />
              2009, vol.&nbsp;9, No 1, 2, 3 and 4 = 100 USD + 20 (25) USD postal  expenses i.e. <strong>12</strong><strong>0 (Europe) and 12</strong><strong>5 (overseas) USD</strong><br />
              2008, vol. 8, No 1, 2, 3 and 4 = 50 USD + 20  (25) USD postal expenses i.e. <strong>70 (Europe)  and 75 (overseas) USD</strong><strong> </strong><br />
              2007, vol. 7, No 1, 2, 3 and 4 = 50 USD + 20 (25) USD postal  expenses i.e. <strong>70 (Europe) and 75 (overseas)  USD</strong><strong> </strong><br />
              2006, vol. 6, No 1, 2, 3 and 4 = 50 USD + 20 (25) USD postal  expenses i.e. <strong>70 (Europe) and 7</strong><strong>5 (overseas) USD</strong><strong> </strong><br />
              2005, vol. 5, No 1, 2, 3 and 4 = 50 USD + 20  (25) USD postal expenses i.e. <strong>70 (Europe)  and 75 (overseas) USD</strong> <br />
              2004, vol. 4, No 1 and 2 = 30 USD + 20  (25) USD postal expenses i.e. <strong>50 (Europe)  and 55 (overseas) USD</strong><strong> </strong><br />
              2003, vol. 3, No 1 and 2 = 30 USD + 20  (25) USD postal expenses i.e. <strong>50 (Europe)  and 55 (overseas) USD</strong><strong> </strong><br />
              2002, vol. 2, No 1 and 2 = 30 USD + 20  (25) USD postal expenses i.e. <strong>50 (Europe)  and 55 (overseas) USD</strong><strong> </strong><br />
              2001, vol. 1, No 1 = 30 USD + 20 (25) USD  postal expenses i.e. <strong>50 (Europe) and 55  (overseas) USD </strong></p>
            <p>The price of electronic offprint (pdf  file) of each publication is 40 USD.</p>
            <p><strong><u>4. Address of the journal:</u></strong><br />
              University of Forestry <br />
              10 Kliment Ohridski blvd.<br />
              1756 Sofia<br />
              Bulgaria <br />
              E-mail: <a href="mailto:ivilievltu@yahoo.com" target="_blank">ivilievltu@yahoo.com</a>&nbsp;&nbsp;<br />
              Fax: + 359 2 862 28 30<br />
              Phone + 359 887 74 05 70</p>
            <p><strong><u>5. The journal bank draft is: </u></strong></p>
            <p><strong>Beneficiary Customer: </strong>SALVIA  PRESS Ltd., <br />
              Address: z.k.  Drujba-2, bl. 523, vhod G, ap. 5, Sofia 1582, Bulgaria, <br />
              DSK BANK - BRANCH  DARVENITZA, <br />
              BIC: STSABGSF, <br />
              IBAN: BG10STSA93000019135100&nbsp;</p>
            <p><strong>Also, you could pay by PayPal</strong><strong> through: </strong>sejani.ltd@gmail.com </p>
            <p>Please do not hesitate to ask me if you  need additional information.</p>
            <p>With best regards</p>
            <p>Prof. Ivan Iliev</p>
            <p>Editor-in-Chief</p>
          </div>
          <p>
            <input name="btnSendMail" type="submit" value="Send Mail(s)" />
            <a href="index.php">Cancel</a></p>
        </form>
      </div>
    </div>
    <div id="form1_ProgressMessageWrapper" class="blockUIOverlay" style="display:none;">
      <script type="text/javascript">
WADFP_SetProgressToForm('form1', 'form1_ProgressMessageWrapper', WADFP_Theme_Options['BigSpin:Moab']);
    </script>
      <div id="form1_ProgressMessage" >
        <p style="margin:10px; padding:5px;" ><img src="../webassist/progress_bar/images/moab-largespin.gif" alt="" title="" style="vertical-align:middle;" />&nbsp;&nbsp;Please wait</p>
      </div>
    </div>
    <!-- InstanceEndEditable --></div>
  </div>
<div id="footer">Journal-POP &copy; 2011 - <?php echo date('Y'); ?></div>
</div>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsEmails);
?>
