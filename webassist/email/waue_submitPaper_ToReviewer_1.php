<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "info@journal-pop.org";
$MailSubject     = "Manuscript for reviewe";
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
  $WA_MailObject = WAUE_AddAttachment($WA_MailObject,"../files/paper/".$row_rsPapers['paperFile']  ."");

//BCC Entries

//CC Entries

//Body Format
  $WA_MailObject = WAUE_BodyFormat($WA_MailObject,0);

//Set Importance
  $WA_MailObject = WAUE_SetImportance($WA_MailObject,"3");

//Start Mail Body
$MailBody = $MailBody . "<html><head></head><body>\r\n";
$MailBody = $MailBody . "\r\n";
$MailBody = $MailBody . "<p>You receievd a manuscrip for review. Please 1. ... 2. ...</p>\r\n";
$MailBody = $MailBody . "<p>&nbsp;</p>\r\n";
$MailBody = $MailBody . "</body></html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_submitPaper_ToReviewer_1");

if (isset($_SESSION["waue_submitPaper_ToReviewer_1_Status"])) {
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
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_submitPaper_ToReviewer_1";
  $MailLogBindings->Success->MailRef = "waue_submitPaper_ToReviewer_1";
  $MailLogBindings->Failure->MailRef = "waue_submitPaper_ToReviewer_1";
  $MailLogBindings->processLog(($_SESSION["waue_submitPaper_ToReviewer_1_Status"] == "Failure"));
}
$WA_MailObject = null;
?>