<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "Journal POP|WA|ivilievltu@klaro-bg.com";
$MailSubject     = "Rejected Manuscript";
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
$MailBody = $MailBody . "<p>Dear colleague(s),<br />\r\n";
$MailBody = $MailBody . "<span lang=\"EN-AU\" xml:lang=\"EN-AU\">I regret that the Editorial board considers that your manuscript  ";
$MailBody = $MailBody .  $row_rsDirectReject['mscrCodeU'];
$MailBody = $MailBody . "</span><span lang=\"EN-AU\" xml:lang=\"EN-AU\"> is not acceptable for publishing in journal &ldquo;<em>Propagation of Ornamental Plants&rdquo;.</em> An explanation for this decision is given in the enclosed review of the referees. </span><span lang=\"EN-AU\" xml:lang=\"EN-AU\">Thank   you for considering our journal as a possible publication medium. You   are welcome to submit other manuscript(s) related with propagation of   ornamental plants.</span>  </p>\r\n";
$MailBody = $MailBody . "<p>With best regards</p>\r\n";
$MailBody = $MailBody . "<p>Dr. Ivan Iliev<br />\r\n";
$MailBody = $MailBody . "  Editor-in-chief  <br />\r\n";
$MailBody = $MailBody . "</p>\r\n";
$MailBody = $MailBody . "<p>&nbsp;</p>\r\n";
$MailBody = $MailBody . "</body></html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_mscr_DirectReject_1");

if (isset($GLOBALS["waue_mscr_DirectReject_1_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_mscr_DirectReject_1";
  $MailLogBindings->Success->MailRef = "waue_mscr_DirectReject_1";
  $MailLogBindings->Failure->MailRef = "waue_mscr_DirectReject_1";
  $MailLogBindings->processLog(($GLOBALS["waue_mscr_DirectReject_1_Status"] == "Failure"));
}
$WA_MailObject = null;
?>