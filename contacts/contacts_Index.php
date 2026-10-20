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
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"><!-- InstanceBegin template="/Templates/main.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Journal-pop - Contacts</title>
<script src="http://code.jquery.com/jquery-1.6.1.js"></script>
<script  src="../scripts/contacts.js"  type="text/javascript"></script>
<!-- InstanceEndEditable -->
<link href="../css/main.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">
/*wagmp*/
<?php include("google_javascript/wagmp_map_1.php"); ?>
</script>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false&key=AIzaSyDsFfQeU-t6zqTj4zCqqEWiIWiSeSDR6MQ">/*wagmp*/</script>
<script type="text/javascript">
/*wagmp*/
<?php include("../webassist/google_javascript/wagmp_map_1.php"); ?>
</script>
<!-- InstanceEndEditable -->
<!-- InstanceParam name="home" type="text" value="" -->
<!-- InstanceParam name="contact" type="text" value="current" -->
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
       <li><a href="contacts_Index.php" class="current">Contact</a></li>
        
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
    <div id="content"><!-- InstanceBeginEditable name="mainContent" --> <span class="title">Contacts</span>
        <div id="wagmp_map_1" style="width: 650px; height: 450px;"></div>
        <div id="contact_form">
          <form id="form1" name="form1" method="post" action="">
            <h2>Contact Form </h2>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>Email:</td>
              </tr>
              <tr>
                <td><label for="txtEmail"></label>
                  <input name="txtEmail" type="text" id="txtEmail" value="<?php echo(ValidatedField("contact","txtEmail")) ?>" size="40" /></td>
              </tr>
              <tr>
                <td><?php
if (ValidatedField('contactsIndex_875','contactsIndex_875'))  {
  if ((strpos((",".ValidatedField("contactsIndex_875","contactsIndex_875").","), "," . "" . ",") !== false || "" == ""))  {
    if (!(false))  {
?>
                  <?php
if (ValidatedField('contactsIndex_875','contactsIndex_875'))  {
  if ((strpos((",".ValidatedField("contactsIndex_875","contactsIndex_875").","), "," . "1" . ",") !== false || "1" == ""))  {
    if (!(false))  {
?>
                  <span class="validation_text">Please enter email.</span>
                  <?php //WAFV_Conditional contacts_Index.php contactsIndex_875(1:)
    }
  }
}?>
                  <?php
if (ValidatedField('contactsIndex_875','contactsIndex_875'))  {
  if ((strpos((",".ValidatedField("contactsIndex_875","contactsIndex_875").","), "," . "2" . ",") !== false || "2" == ""))  {
    if (!(false))  {
?>
                  <span class="validation_text">Please enter valid email.</span>
                  <?php //WAFV_Conditional contacts_Index.php contactsIndex_875(2:)
    }
  }
}?>
                  <?php //WAFV_Conditional contacts_Index.php contactsIndex_875( :)
    }
  }
}?></td>
              </tr>
              <tr>
                <td>Name:</td>
              </tr>
              <tr>
                <td><label for="txtName"></label>
                  <input name="txtName" type="text" id="txtName" value="<?php echo(ValidatedField("contact","txtName")) ?>" size="40" /></td>
              </tr>
              <tr>
                <td><?php
if (ValidatedField('contactsIndex_875','contactsIndex_875'))  {
  if ((strpos((",".ValidatedField("contactsIndex_875","contactsIndex_875").","), "," . "3" . ",") !== false || "3" == ""))  {
    if (!(false))  {
?>
                  <span class="validation_text">Please enter your name.</span>
                  <?php //WAFV_Conditional contacts_Index.php contactsIndex_875(3:)
    }
  }
}?></td>
              </tr>
              <tr>
                <td>Message:</td>
              </tr>
              <tr>
                <td><label for="txtMessage"></label>
                  <textarea name="txtMessage" id="txtMessage" cols="60" rows="10"></textarea></td>
              </tr>
              <tr>
                <td><?php
if (ValidatedField('contactsIndex_875','contactsIndex_875'))  {
  if ((strpos((",".ValidatedField("contactsIndex_875","contactsIndex_875").","), "," . "4" . ",") !== false || "4" == ""))  {
    if (!(false))  {
?>
                  <span class="validation_text">Please enter your message.</span>
                  <?php //WAFV_Conditional contacts_Index.php contactsIndex_875(4:)
    }
  }
}?></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><p><img id="capt1" src="../webassist/captcha/wavt_captchasecurityimages.php?width=150&amp;height=25&amp;field=Security_Code_1&amp;bgcolor=FFFFFF&amp;transparent=0&amp;bgimage=&amp;gridfreq=5&amp;gridcolor=000000&amp;gridorder=behind&amp;noisefreq=5&amp;noisecolor=000000&amp;noiseorder=behind&amp;characters=5&amp;charheight=&amp;font=fonts/MOM_T___.TTF&amp;textcolor=000000" alt="security code" width="150" height="25" /><img src="../webassist/captcha/images/refresh.png" height="18" onclick="document.getElementById('capt1').src+='&amp;ref=1'" /></p>
                  <input id="Security_Code_1" name="Security_Code_1" type="text" value="" />
                  <p>Please rewrite the above code here</p></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="validation_text">
                  <?php
if (ValidatedField('contactsIndex_875','contactsIndex_875'))  {
  if ((strpos((",".ValidatedField("contactsIndex_875","contactsIndex_875").","), "," . "5" . ",") !== false || "5" == ""))  {
    if (!(false))  {
?>
                  Your cecurity code did not mach the image.
                  <?php //WAFV_Conditional contacts_Index.php contactsIndex_875(5:)
    }
  }
}
?>
                </span></td>
              </tr>
              <tr>
                <td><input type="submit" name="txtSend" id="txtSend" value="   Send   " /></td>
              </tr>
            </table>
          </form>
        </div>
        <script type="text/javascript" src="google_javascript/wagmp_maps.js">/*wagmp*/</script>
        <script type="text/javascript" src="../webassist/google_javascript/wagmp_maps.js">/*wagmp*/</script>
    <!-- InstanceEndEditable --></div>
  </div>
<div id="footer">Journal-POP &copy; 2011 - <?php echo date('Y')?></div>
</div>
</body>
<!-- InstanceEnd --></html>