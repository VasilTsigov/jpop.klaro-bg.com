<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "Journal POP|WA|ivilievltu@klaro-bg.com";
$MailSubject     = "Journal POP - Proof File";
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
  $WA_MailObject = WAUE_AddAttachment($WA_MailObject,"../files/proof/".$WA_DFP_UploadStatus["WA_UploadResult1"]["serverFileName"]  ."");
  $WA_MailObject = WAUE_AddAttachment($WA_MailObject,"../files/editletter/".$WA_DFP_UploadStatus["WA_UploadResult2"]["serverFileName"]  ."");

//BCC Entries

//CC Entries

//Body Format
  $WA_MailObject = WAUE_BodyFormat($WA_MailObject,0);

//Set Importance
  $WA_MailObject = WAUE_SetImportance($WA_MailObject,"3");

//Start Mail Body
$MailBody = $MailBody . "<html><head></head><body>\r\n";
$MailBody = $MailBody . "<div>Dear Dr.";
$MailBody = $MailBody .  $row_Recordset1['FullAutorName'];
$MailBody = $MailBody . ",</div>\r\n";
$MailBody = $MailBody . "<div>We would like to inform you that your proof ";
$MailBody = $MailBody .  $WA_DFP_UploadStatus["WA_UploadResult1"]["serverFileName"];
$MailBody = $MailBody . " and proof editor's letter ";
$MailBody = $MailBody .  $WA_DFP_UploadStatus["WA_UploadResult2"]["serverFileName"];
$MailBody = $MailBody . " are attached to the website of the journal. Please send us your suggestions and recommendations for the improvement of the proof within one week. </div>\r\n";
$MailBody = $MailBody . "</body></html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_mscr_UploadProofFile_1");

if (isset($GLOBALS["waue_mscr_UploadProofFile_1_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_mscr_UploadProofFile_1";
  $MailLogBindings->Success->MailRef = "waue_mscr_UploadProofFile_1";
  $MailLogBindings->Failure->MailRef = "waue_mscr_UploadProofFile_1";
  $MailLogBindings->processLog(($GLOBALS["waue_mscr_UploadProofFile_1_Status"] == "Failure"));
}
$WA_MailObject = null;
?>