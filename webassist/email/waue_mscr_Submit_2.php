<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "ivilievltu@klaro-bg.com";
$MailSubject     = "Manuscript upload and send to system";
$_SERVER["QUERY_STRING"] = "";

//Global Variables

  $WA_MailObject = WAUE_Definition("","","","","","UTF-8");

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
$MailBody = $MailBody . "<html><head></head><body>Hi Editor,<br/>\r\n";
$MailBody = $MailBody .  $_SESSION['UserFirstName'];
$MailBody = $MailBody . " ";
$MailBody = $MailBody .  $_SESSION['UserLastName'];
$MailBody = $MailBody . " <br/>\r\n";
$MailBody = $MailBody . "has send and upload manuscript with title \"";
$MailBody = $MailBody .  ((isset($_POST["txtFullTitle"]))?$_POST["txtFullTitle"]:"");
$MailBody = $MailBody . "\" to the system</body></html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_mscr_Submit_2");

if (isset($GLOBALS["waue_mscr_Submit_2_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_mscr_Submit_2";
  $MailLogBindings->Success->MailRef = "waue_mscr_Submit_2";
  $MailLogBindings->Failure->MailRef = "waue_mscr_Submit_2";
  $MailLogBindings->processLog(($GLOBALS["waue_mscr_Submit_2_Status"] == "Failure"));
}
$WA_MailObject = null;
?>