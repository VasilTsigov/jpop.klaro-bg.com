<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "Journal POP|WA|ivilievltu@klaro-bg.com";
$MailSubject     = "Message from Reviewer";
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
$MailBody = $MailBody . "<table width=\"500\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n";
$MailBody = $MailBody . "  <tr>\r\n";
$MailBody = $MailBody . "    <td width=\"46\">&nbsp;</td>\r\n";
$MailBody = $MailBody . "    <td width=\"454\">&nbsp;</td>\r\n";
$MailBody = $MailBody . "  </tr>\r\n";
$MailBody = $MailBody . "  <tr>\r\n";
$MailBody = $MailBody . "    <td>&nbsp;</td>\r\n";
$MailBody = $MailBody . "    <td>";
$MailBody = $MailBody .  ((isset($_POST["txtInfo"]))?$_POST["txtInfo"]:"");
$MailBody = $MailBody . "</td>\r\n";
$MailBody = $MailBody . "  </tr>\r\n";
$MailBody = $MailBody . "</table>\r\n";
$MailBody = $MailBody . "</body></html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_mscr_Reviewer2ConfirmNo_1");

if (isset($GLOBALS["waue_mscr_Reviewer2ConfirmNo_1_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_mscr_Reviewer2ConfirmNo_1";
  $MailLogBindings->Success->MailRef = "waue_mscr_Reviewer2ConfirmNo_1";
  $MailLogBindings->Failure->MailRef = "waue_mscr_Reviewer2ConfirmNo_1";
  $MailLogBindings->processLog(($GLOBALS["waue_mscr_Reviewer2ConfirmNo_1_Status"] == "Failure"));
}
$WA_MailObject = null;
?>