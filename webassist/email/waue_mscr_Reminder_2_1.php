<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "Journal POP|WA|ivilievltu@klaro-bg.com";
$MailSubject     = "Journal POP reminder late review ".$row_rsReminder_2['mscrCodeU']  ."";
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
$MailBody = $MailBody . "<html>\r\n";
$MailBody = $MailBody . "<head></head>\r\n";
$MailBody = $MailBody . "<body>\r\n";
$MailBody = $MailBody . "   ";
$MailBody = $MailBody .  ((isset($_POST["txtReminder"]))?$_POST["txtReminder"]:"");
$MailBody = $MailBody . "\r\n";
$MailBody = $MailBody . "</body>\r\n";
$MailBody = $MailBody . "</html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_mscr_Reminder_2_1");

if (isset($GLOBALS["waue_mscr_Reminder_2_1_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_mscr_Reminder_2_1";
  $MailLogBindings->Success->MailRef = "waue_mscr_Reminder_2_1";
  $MailLogBindings->Failure->MailRef = "waue_mscr_Reminder_2_1";
  $MailLogBindings->processLog(($GLOBALS["waue_mscr_Reminder_2_1_Status"] == "Failure"));
}
$WA_MailObject = null;
?>