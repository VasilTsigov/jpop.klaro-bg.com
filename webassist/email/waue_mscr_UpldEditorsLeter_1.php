<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "Journal POP |WA|ivilievltu@klaro-bg.com";
$MailSubject     = "Journal POP - Letter from the editor";
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
  $WA_MailObject = WAUE_AddAttachment($WA_MailObject,"../files/editletter/".$row_rsEditorsLeter['mscrEditorsLetter']  ."");

//BCC Entries

//CC Entries

//Body Format
  $WA_MailObject = WAUE_BodyFormat($WA_MailObject,0);

//Set Importance
  $WA_MailObject = WAUE_SetImportance($WA_MailObject,"3");

//Start Mail Body
$MailBody = $MailBody . "<html><head></head><body>\r\n";
$MailBody = $MailBody . "\r\n";
$MailBody = $MailBody . "<div>\r\n";
$MailBody = $MailBody . "  <div>\r\n";
$MailBody = $MailBody . "    <div>\r\n";
$MailBody = $MailBody . "      <div>Dear Dr. ";
$MailBody = $MailBody .  $row_rsEditorsLeter['FullAutorName'];
$MailBody = $MailBody . ",<br />\r\n";
$MailBody = $MailBody . "        <br />\r\n";
$MailBody = $MailBody . "        We would   like to inform you that you have received &quot;Editor's Decision and   Comments&quot; for your manuscript. Please find attached this letter to the   journal website (<a href=\"http://www.journal-pop.org/\" target=\"_blank\">www.journal-pop.org</a>). Please open it and follow the suggestions and recommendations there.<br />\r\n";
$MailBody = $MailBody . "        Please correct your manuscript according the reviewer's and Editor's recommendations and suggestions within 15 days. </div>\r\n";
$MailBody = $MailBody . "      <div>Please attach the corrected manuscript to the website of the journal.</div>\r\n";
$MailBody = $MailBody . "      <div> </div>\r\n";
$MailBody = $MailBody . "      <div>Please do not send this manuscript again if it is rejected.</div>\r\n";
$MailBody = $MailBody . "      <div> </div>\r\n";
$MailBody = $MailBody . "      <div>With best regards\r\n";
$MailBody = $MailBody . "        <br />\r\n";
$MailBody = $MailBody . "        <br />\r\n";
$MailBody = $MailBody . "        Editorial Office<br />\r\n";
$MailBody = $MailBody . "        <br />\r\n";
$MailBody = $MailBody . "      Journal &quot;Propagation of Ornamental Plants&quot;</div>\r\n";
$MailBody = $MailBody . "      <p>&nbsp;</p>\r\n";
$MailBody = $MailBody . "    </div>\r\n";
$MailBody = $MailBody . "  </div>\r\n";
$MailBody = $MailBody . "</div>\r\n";
$MailBody = $MailBody . "<p>&nbsp;</p>\r\n";
$MailBody = $MailBody . "</body></html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_mscr_UpldEditorsLeter_1");

if (isset($GLOBALS["waue_mscr_UpldEditorsLeter_1_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_mscr_UpldEditorsLeter_1";
  $MailLogBindings->Success->MailRef = "waue_mscr_UpldEditorsLeter_1";
  $MailLogBindings->Failure->MailRef = "waue_mscr_UpldEditorsLeter_1";
  $MailLogBindings->processLog(($GLOBALS["waue_mscr_UpldEditorsLeter_1_Status"] == "Failure"));
}
$WA_MailObject = null;
?>