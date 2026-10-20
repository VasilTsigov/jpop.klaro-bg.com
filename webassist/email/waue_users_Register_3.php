<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "Journal POP|WA|ivilievltu@klaro-bg.com";
$MailSubject     = "New author submit registration form";
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
$MailBody = $MailBody . "<html><head></head><body>Hi Ivan,<br/>\r\n";
$MailBody = $MailBody . "User ";
$MailBody = $MailBody .  ((isset($_POST["txtFirstName"]))?$_POST["txtFirstName"]:"");
$MailBody = $MailBody . " ";
$MailBody = $MailBody .  ((isset($_POST["txtLastName"]))?$_POST["txtLastName"]:"");
$MailBody = $MailBody . " from ";
$MailBody = $MailBody .  ((isset($_POST["txtCity"]))?$_POST["txtCity"]:"");
$MailBody = $MailBody . ", ";
$MailBody = $MailBody .  ((isset($_POST["txtCountry"]))?$_POST["txtCountry"]:"");
$MailBody = $MailBody . "\r\n";
$MailBody = $MailBody . "has submited the form for registration. <br/>\r\n";
$MailBody = $MailBody . "After registration his status will be an \"Autor\". <br/>\r\n";
$MailBody = $MailBody . "Only you can change his/her ststus. <br />\r\n";
$MailBody = $MailBody . "<br/>\r\n";
$MailBody = $MailBody . "Best regards from your System!</body></html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_users_Register_3");

if (isset($GLOBALS["waue_users_Register_3_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_users_Register_3";
  $MailLogBindings->Success->MailRef = "waue_users_Register_3";
  $MailLogBindings->Failure->MailRef = "waue_users_Register_3";
  $MailLogBindings->processLog(($GLOBALS["waue_users_Register_3_Status"] == "Failure"));
}
$WA_MailObject = null;
?>