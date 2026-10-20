<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "Journal POP|WA|ivilievltu@klaro-bg.com";
$MailSubject     = "Autor ".$row_rsUpldCorrFile['FullAutorName']  ." has uploaded his corrected manuscript";
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
  $WA_MailObject = WAUE_AddAttachment($WA_MailObject,"../files/corrected/".$row_rsUpldCorrFile['mscrFileCorrected']  ."");

//BCC Entries

//CC Entries

//Body Format
  $WA_MailObject = WAUE_BodyFormat($WA_MailObject,0);

//Set Importance
  $WA_MailObject = WAUE_SetImportance($WA_MailObject,"3");

//Start Mail Body
$MailBody = $MailBody . "<html><head></head><body>Hi Ivan, <br/> \r\n";
$MailBody = $MailBody . "Author <strong>";
$MailBody = $MailBody .  $row_rsUpldCorrFile['FullAutorName'];
$MailBody = $MailBody . "</strong>\r\n";
$MailBody = $MailBody . "has uploaded his corrected manuscript <strong>";
$MailBody = $MailBody .  $row_rsUpldCorrFile['mscrCodeU'];
$MailBody = $MailBody . "</strong> on ";
$MailBody = $MailBody .  $row_rsUpldCorrFile['mscrDateFileCorrected'];
$MailBody = $MailBody . ": <br/>\r\n";
$MailBody = $MailBody . "Title:  <strong>";
$MailBody = $MailBody .  $row_rsUpldCorrFile['mscrFullTitle'];
$MailBody = $MailBody . "</strong><br/><br/>\r\n";
$MailBody = $MailBody . "Your Journal POP Team</body></html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_mscr_UpldFileCorrected_1");

if (isset($GLOBALS["waue_mscr_UpldFileCorrected_1_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_mscr_UpldFileCorrected_1";
  $MailLogBindings->Success->MailRef = "waue_mscr_UpldFileCorrected_1";
  $MailLogBindings->Failure->MailRef = "waue_mscr_UpldFileCorrected_1";
  $MailLogBindings->processLog(($GLOBALS["waue_mscr_UpldFileCorrected_1_Status"] == "Failure"));
}
$WA_MailObject = null;
?>