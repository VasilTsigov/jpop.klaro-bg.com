<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "info@journal-pop.org";
$MailSubject     = "Reviewered File From ".$row_rsReviewerFile1['paperReviewer1FullName']  ."";
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
  $WA_MailObject = WAUE_AddAttachment($WA_MailObject,".../files/reviewered/".$row_rsReviewerFile1['paperFileReviewered_1']  ."");

//BCC Entries

//CC Entries

//Body Format
  $WA_MailObject = WAUE_BodyFormat($WA_MailObject,0);

//Set Importance
  $WA_MailObject = WAUE_SetImportance($WA_MailObject,"3");

//Start Mail Body
$MailBody = $MailBody . "<html><head></head><body>\r\n";
$MailBody = $MailBody . "\r\n";
$MailBody = $MailBody . "<p>Hi ";
$MailBody = $MailBody .  $row_rsReviewerFile1['AutorFullName'];
$MailBody = $MailBody . ",<br />\r\n";
$MailBody = $MailBody . "  ";
$MailBody = $MailBody .  $row_rsReviewerFile1['paperReviewer1FullName'];
$MailBody = $MailBody . " has reviewed your manuscript &quot;";
$MailBody = $MailBody .  $row_rsReviewerFile1['paperFullTitle'];
$MailBody = $MailBody . "&quot;.<br />\r\n";
$MailBody = $MailBody . "  Please do<br />\r\n";
$MailBody = $MailBody . "  1. Make correction on your file.<br />\r\n";
$MailBody = $MailBody . "  2. Go to http:// .... and login<br />\r\n";
$MailBody = $MailBody . "  3. Go to ......<br />\r\n";
$MailBody = $MailBody . "4. Upload Your corrected file.<br />\r\n";
$MailBody = $MailBody . "<br />\r\n";
$MailBody = $MailBody . "Thank you<br />\r\n";
$MailBody = $MailBody . "journal-pop.org\r\n";
$MailBody = $MailBody . "</p>\r\n";
$MailBody = $MailBody . "<p>&nbsp;</p>\r\n";
$MailBody = $MailBody . "</body></html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_submitPaper_UploadReviewerFile2_2");

if (isset($_SESSION["waue_submitPaper_UploadReviewerFile2_2_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //Success Or Failure
  $MailLogBindings->SuccessOrFailure->ToDo = "none";
  $MailLogBindings->SuccessOrFailure->Connection = "";
  $MailLogBindings->SuccessOrFailure->TableName = "";
  $MailLogBindings->SuccessOrFailure->EmailColumn = "";
  $MailLogBindings->SuccessOrFailure->ColumnList = array();
  $MailLogBindings->SuccessOrFailure->TypeList = array();
  $MailLogBindings->SuccessOrFailure->ValueList = array();
  //Success Only
  $MailLogBindings->Success->ToDo = "none";
  $MailLogBindings->Success->Connection = "";
  $MailLogBindings->Success->TableName = "";
  $MailLogBindings->Success->EmailColumn = "";
  $MailLogBindings->Success->ColumnList = array();
  $MailLogBindings->Success->TypeList = array();
  $MailLogBindings->Success->ValueList = array();
  //Failure Only
  $MailLogBindings->Failure->ToDo = "none";
  $MailLogBindings->Failure->Connection = "";
  $MailLogBindings->Failure->TableName = "";
  $MailLogBindings->Failure->EmailColumn = "";
  $MailLogBindings->Failure->ColumnList = array();
  $MailLogBindings->Failure->TypeList = array();
  $MailLogBindings->Failure->ValueList = array();
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_submitPaper_UploadReviewerFile2_2";
  $MailLogBindings->Success->MailRef = "waue_submitPaper_UploadReviewerFile2_2";
  $MailLogBindings->Failure->MailRef = "waue_submitPaper_UploadReviewerFile2_2";
  $MailLogBindings->processLog(($_SESSION["waue_submitPaper_UploadReviewerFile2_2_Status"] == "Failure"));
}
$WA_MailObject = null;
?>