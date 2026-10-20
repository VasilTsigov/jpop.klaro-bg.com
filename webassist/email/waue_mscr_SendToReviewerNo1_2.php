<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "ivilievltu@klaro-bg.com";
$MailSubject     = "Reminder to Reviewer";
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
$MailBody = $MailBody . "Dear Dr ";
$MailBody = $MailBody .  $row_rsReviewer_1['ReviewerFullName'];
$MailBody = $MailBody . ",<br />\r\n";
$MailBody = $MailBody . "You agreed to review manuscript ";
$MailBody = $MailBody .  $row_rsMnscrpt['mscrCodeU'];
$MailBody = $MailBody . " and your completed review was due by .....2014.<br />\r\n";
$MailBody = $MailBody . "Therefore we would be grateful if you would submit your review as soon as possible to the website of journal POP (<a href=\"http://www.journal-pop.org/\" target=\"_blank\">www.journal-pop.org</a>).<br />\r\n";
$MailBody = $MailBody . "<br />\r\n";
$MailBody = $MailBody . "With best regards<br />\r\n";
$MailBody = $MailBody . "<br />\r\n";
$MailBody = $MailBody . "Prof. Ivan Iliev<br />\r\n";
$MailBody = $MailBody . "Editor-in-Chief</body></html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_mscr_SendToReviewerNo1_2");

if (isset($GLOBALS["waue_mscr_SendToReviewerNo1_2_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_mscr_SendToReviewerNo1_2";
  $MailLogBindings->Success->MailRef = "waue_mscr_SendToReviewerNo1_2";
  $MailLogBindings->Failure->MailRef = "waue_mscr_SendToReviewerNo1_2";
  $MailLogBindings->processLog(($GLOBALS["waue_mscr_SendToReviewerNo1_2_Status"] == "Failure"));
}
$WA_MailObject = null;
?>