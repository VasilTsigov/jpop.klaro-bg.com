<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "Journal POP|WA|ivilievltu@klaro-bg.com";
$MailSubject     = "Journal POP - your reviewed manuscript  ";
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
  $WA_MailObject = WAUE_AddAttachment($WA_MailObject,"../files/checklist/".$row_rsUpldFileReviewer3['mnscChecklistFileReviewer_3']  ."");

//BCC Entries

//CC Entries

//Body Format
  $WA_MailObject = WAUE_BodyFormat($WA_MailObject,0);

//Set Importance
  $WA_MailObject = WAUE_SetImportance($WA_MailObject,"3");

//Start Mail Body
$MailBody = $MailBody . "<html><head></head><body>\r\n";
$MailBody = $MailBody . "<p>Dear Dr.  ";
$MailBody = $MailBody .  $row_rsUpldFileReviewer3['AutorEmail'];
$MailBody = $MailBody . ",<br />\r\n";
$MailBody = $MailBody . "We   would like to inform you that your manuscript<strong> ";
$MailBody = $MailBody .  $row_rsUpldFileReviewer3['mscrCodeU'];
$MailBody = $MailBody . "</strong> has been reviewed   from <strong>Reviewer No 3</strong>. The checklist and manuscript (with corrections   made by Track Changes) was attached to you and uploaded to the system.<br />\r\n";
$MailBody = $MailBody . "Please wait for the final decision i.e. &quot;Editor's Decision Comment&quot;, which will appear on the website of the journal. <br />\r\n";
$MailBody = $MailBody . "<br/>\r\n";
$MailBody = $MailBody . "journal POP<br/>\r\n";
$MailBody = $MailBody . "Editorial Office</p>\r\n";
$MailBody = $MailBody . "</body></html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_mscr_UpldFileReviewer_3_1");

if (isset($GLOBALS["waue_mscr_UpldFileReviewer_3_1_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_mscr_UpldFileReviewer_3_1";
  $MailLogBindings->Success->MailRef = "waue_mscr_UpldFileReviewer_3_1";
  $MailLogBindings->Failure->MailRef = "waue_mscr_UpldFileReviewer_3_1";
  $MailLogBindings->processLog(($GLOBALS["waue_mscr_UpldFileReviewer_3_1_Status"] == "Failure"));
}
$WA_MailObject = null;
?>