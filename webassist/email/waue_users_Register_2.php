<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "Journal POP|WA|ivilievltu@klaro-bg.com";
$MailSubject     = "Please confirm registration";
$_SERVER["QUERY_STRING"] = "";

//Global Variables

  $WA_MailObject = WAUE_Definition("","","","","","");

if ($RecipientEmail)     {
  $WA_MailObject = WAUE_AddRecipient($WA_MailObject,$RecipientEmail);
}
else      {
  //To Entries
}

//Additional Headers

//Attachment Entries

//BCC Entries

//CC Entries

//Body Format
  $WA_MailObject = WAUE_BodyFormat($WA_MailObject,0);

//Set Importance
  $WA_MailObject = WAUE_SetImportance($WA_MailObject,"3");

//Start Mail Body
$MailBody = $MailBody . "<html><head></head><body>\r\n";
$MailBody = $MailBody . "<p>Please klick on the link below to confirm your registration:</p>\r\n";
$MailBody = $MailBody . "<p><a href=\"http://jpop.klaro-bg.com/users/users_Confirm.php?UID=";
$MailBody = $MailBody .  (mysql_insert_id());
$MailBody = $MailBody . "&amp;code=";
$MailBody = $MailBody .  ((isset($_SESSION["randomConfirm"]))?$_SESSION["randomConfirm"]:"");
$MailBody = $MailBody . "\">I confirm my registration.</a></p>\r\n";
$MailBody = $MailBody . "<p>(If you can't click over the link please select it, copy and paste into the browser address fild and press &quot;Ehter&quot;.)</p>\r\n";
$MailBody = $MailBody . "<p>Thenk you.</p>\r\n";
$MailBody = $MailBody . "<p>Journal POP</p>\r\n";
$MailBody = $MailBody . "</body>\r\n";

mysql_free_result($CheckRepeat);

$MailBody = $MailBody . "\r\n";
$MailBody = $MailBody . "</html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_users_Register_2");

if (isset($GLOBALS["waue_users_Register_2_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_users_Register_2";
  $MailLogBindings->Success->MailRef = "waue_users_Register_2";
  $MailLogBindings->Failure->MailRef = "waue_users_Register_2";
  $MailLogBindings->processLog(($GLOBALS["waue_users_Register_2_Status"] == "Failure"));
}
$WA_MailObject = null;
?>