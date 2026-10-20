<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "Journal POP|WA|ivilievltu@klaro-bg.com";
$MailSubject     = "Manuscript ".$row_rsMnscrpt['mscrCodeU']  ." to review";
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
  $WA_MailObject = WAUE_SetImportance($WA_MailObject,"1");

//Start Mail Body
$MailBody = $MailBody . "<html>\r\n";
$MailBody = $MailBody . "<head></head>\r\n";
$MailBody = $MailBody . "<body>\r\n";
$MailBody = $MailBody . "<p>Dear Dr. ";
$MailBody = $MailBody .  $row_rsReviewer_1['ReviewerFullName'];
$MailBody = $MailBody . ",<br />\r\n";
$MailBody = $MailBody . "  <br />\r\n";
$MailBody = $MailBody . "In view of your expertise I would be very   grateful if you could review the following manuscript which has been   submitted to Online Manuscript Submission,            Review and Tracking System            for the journal            Propagation of Ornamental Plants.</p>\r\n";
$MailBody = $MailBody . "<p><br />\r\n";
$MailBody = $MailBody . "  Manuscript Number:  <strong>";
$MailBody = $MailBody .  $row_rsMnscrpt['mscrCodeU'];
$MailBody = $MailBody . "</strong><br />\r\n";
$MailBody = $MailBody . "  <br />\r\n";
$MailBody = $MailBody . "  Title:  <strong>";
$MailBody = $MailBody .  $row_rsMnscrpt['mscrFullTitle'];
$MailBody = $MailBody . "</strong><br />\r\n";
$MailBody = $MailBody . "  <br />\r\n";
$MailBody = $MailBody . "  In case you are interested in reviewing this submission please click on this   link: <br />\r\n";
$MailBody = $MailBody . "  <br />\r\n";
$MailBody = $MailBody . "  <a href=\"http://jpop.klaro-bg.com/manuscripts/mscr_Reviewer1ConfirmYes.php?mnscrpt_id=";
$MailBody = $MailBody .  $row_rsMnscrpt['mscrID'];
$MailBody = $MailBody . "&code=";
$MailBody = $MailBody .  $_SESSION['confirmYes'];
$MailBody = $MailBody . "\">I agree to review this submission.</a>\r\n";
$MailBody = $MailBody . "  <br />\r\n";
$MailBody = $MailBody . "  <br />\r\n";
$MailBody = $MailBody . "  If you do not have time to do this, or do not feel qualified, please click on this link: <br />\r\n";
$MailBody = $MailBody . "  <br />\r\n";
$MailBody = $MailBody . "  <a href=\"http://jpop.klaro-bg.com/manuscripts/mscr_Reviewer1ConfirmNo.php?mnscrpt_id=";
$MailBody = $MailBody .  $row_rsMnscrpt['mscrID'];
$MailBody = $MailBody . "&code=";
$MailBody = $MailBody .  $_SESSION['confirmNo'];
$MailBody = $MailBody . "\">I decline to review this submission.</a>\r\n";
$MailBody = $MailBody . "  \r\n";
$MailBody = $MailBody . "  </p>\r\n";
$MailBody = $MailBody . "<p><br />\r\n";
$MailBody = $MailBody . "  We   hope you are willing to review the manuscript. If so, would you be so   kind as to return your review to us within 30 days of agreeing to   review? Thank you.<br />\r\n";
$MailBody = $MailBody . "  <br />\r\n";
$MailBody = $MailBody . "  You are requested to submit your review online by using<br />\r\n";
$MailBody = $MailBody . "  the Editorial Manager system which can be found at:<br />\r\n";
$MailBody = $MailBody . "  http://jpop.klaro-bg.com<br />\r\n";
$MailBody = $MailBody . "  <br />\r\n";
$MailBody = $MailBody . "  <br />\r\n";
$MailBody = $MailBody . "  IN ORDER TO KEEP DELAYS TO A MINIMUM, PLEASE ACCEPT OR DECLINE THIS ASSIGNMENT ONLINE AS SOON AS POSSIBLE!<br />\r\n";
$MailBody = $MailBody . "  <br />\r\n";
$MailBody = $MailBody . "  In   case you wish to annotate the manuscript, you may upload the attachment   when you send your comments. Please click 'Upload Reviewer   Attachments'.<br />\r\n";
$MailBody = $MailBody . "  <br />\r\n";
$MailBody = $MailBody . "  I hope you are willing to review the manuscript.  Thank you for your   assistance.<br />\r\n";
$MailBody = $MailBody . "  <br />\r\n";
$MailBody = $MailBody . "  Journal POP<br />\r\n";
$MailBody = $MailBody . "  Editorial Office </p>\r\n";
$MailBody = $MailBody . "\r\n";
$MailBody = $MailBody . "\r\n";
$MailBody = $MailBody . "</body>\r\n";
$MailBody = $MailBody . "</html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_mscr_SendToReviewer_1");

if (isset($GLOBALS["waue_mscr_SendToReviewer_1_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_mscr_SendToReviewer_1";
  $MailLogBindings->Success->MailRef = "waue_mscr_SendToReviewer_1";
  $MailLogBindings->Failure->MailRef = "waue_mscr_SendToReviewer_1";
  $MailLogBindings->processLog(($GLOBALS["waue_mscr_SendToReviewer_1_Status"] == "Failure"));
}
$WA_MailObject = null;
?>