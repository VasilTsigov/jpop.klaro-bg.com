<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "journal POP|WA|ivilievltu@klaro-bg.com";
$MailSubject     = "Instructions to  author";
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
  $WA_MailObject = WAUE_AddAttachment($WA_MailObject,"../files/attach/CONSENT_for_AUTHORS.doc");

//BCC Entries

//CC Entries

//Body Format
  $WA_MailObject = WAUE_BodyFormat($WA_MailObject,0);

//Set Importance
  $WA_MailObject = WAUE_SetImportance($WA_MailObject,"3");

//Start Mail Body
$MailBody = $MailBody . "<html><head></head><body>\r\n";
$MailBody = $MailBody . " Dear Dr. ";
$MailBody = $MailBody .  $_SESSION['UserFirstName'];
$MailBody = $MailBody . " ";
$MailBody = $MailBody .  $_SESSION['UserLastName'];
$MailBody = $MailBody . ",<br /><br />\r\n";
$MailBody = $MailBody . "\r\n";
$MailBody = $MailBody . "  Thank you for the submited manusctript - JPOP ";
$MailBody = $MailBody .  $row_rsCode['code'];
$MailBody = $MailBody . ". <span lang=\"EN-US\" xml:lang=\"EN-US\">It will be sending to reviewers and will be considered for publication in Journal Propagation of Ornamental Plants</span>.<br />\r\n";
$MailBody = $MailBody . "Please find attached the <strong>Consent to publish and transfer of copiright</strong>. You are kindly requested to follow the next steps:<br />\r\n";
$MailBody = $MailBody . "<br />\r\n";
$MailBody = $MailBody . "1. Please fill in the <strong>title of your contribution</strong> and the <strong>author(s)</strong> on the indicated positions of the consent form <br />\r\n";
$MailBody = $MailBody . "2. Print and sign the consent (position in the end: <strong>Signature</strong>) <br />\r\n";
$MailBody = $MailBody . "3. Please send the consent <strong>BY AIRMAIL</strong> to: <br />\r\n";
$MailBody = $MailBody . " \r\n";
$MailBody = $MailBody . "<p>Dr. Ivan Iliev<br />\r\n";
$MailBody = $MailBody . "Editor-in-chief<br />\r\n";
$MailBody = $MailBody . "Journal Propagation of Ornamental Plants<br />\r\n";
$MailBody = $MailBody . "University of Forestry<br />\r\n";
$MailBody = $MailBody . "10 Kliment Ohridski blvd.<br />\r\n";
$MailBody = $MailBody . "1756 Sofia <br />\r\n";
$MailBody = $MailBody . "Bulgaria</p>\r\n";
$MailBody = $MailBody . "<p><span lang=\"EN-US\" xml:lang=\"EN-US\">We will contact you again as soon as a final decision has been reached by the Editorial Board.</span><br />\r\n";
$MailBody = $MailBody . "<span lang=\"EN-US\" xml:lang=\"EN-US\">Please remember to quote the <strong>manuscript number (JPOP";
$MailBody = $MailBody .  $row_rsCode['code'];
$MailBody = $MailBody . ") </strong>in any future correspondence</span></p>\r\n";
$MailBody = $MailBody . "<p> </p>\r\n";
$MailBody = $MailBody . "<p>With best regards<br />\r\n";
$MailBody = $MailBody . "  <a href=\"http://journal-pop.org/\" target=\"_blank\"><span id=\"lw_1305987834_0\">journal</span></a> POP<br />\r\n";
$MailBody = $MailBody . "  Editorial office </p>\r\n";
$MailBody = $MailBody . "</body>\r\n";

mysql_free_result($rsCode);

mysql_free_result($rsAutorFullName);

$MailBody = $MailBody . "\r\n";
$MailBody = $MailBody . "</html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_mscr_Submit_1");

if (isset($GLOBALS["waue_mscr_Submit_1_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_mscr_Submit_1";
  $MailLogBindings->Success->MailRef = "waue_mscr_Submit_1";
  $MailLogBindings->Failure->MailRef = "waue_mscr_Submit_1";
  $MailLogBindings->processLog(($GLOBALS["waue_mscr_Submit_1_Status"] == "Failure"));
}
$WA_MailObject = null;
?>