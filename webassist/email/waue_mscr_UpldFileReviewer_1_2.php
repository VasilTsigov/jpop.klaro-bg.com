<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "journal POP|WA|";
$MailSubject     = "Reviewer ".$row_rsUpldFileReviewer1['mscrReviewerFullName_1']  ." uploads file";
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
$MailBody = $MailBody . "Hi Ivan,<br/>\r\n";
$MailBody = $MailBody .  $row_rsUpldFileReviewer1['mscrReviewerFullName_1'];
$MailBody = $MailBody . "\r\n";
$MailBody = $MailBody . "has reviewed and uploaded a manuscript <br/>\"";
$MailBody = $MailBody .  $row_rsUpldFileReviewer1['mscrFullTitle'];
$MailBody = $MailBody . "\"\r\n";
$MailBody = $MailBody . "\" from ";
$MailBody = $MailBody .  $row_rsUpldFileReviewer1['AutorFullName'];
$MailBody = $MailBody . " <br/>\r\n";
$MailBody = $MailBody . "journal POP Team";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_mscr_UpldFileReviewer_1_2");

if (isset($_SESSION["waue_mscr_UpldFileReviewer_1_2_Status"])) {
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
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_mscr_UpldFileReviewer_1_2";
  $MailLogBindings->Success->MailRef = "waue_mscr_UpldFileReviewer_1_2";
  $MailLogBindings->Failure->MailRef = "waue_mscr_UpldFileReviewer_1_2";
  $MailLogBindings->processLog(($_SESSION["waue_mscr_UpldFileReviewer_1_2_Status"] == "Failure"));
}
$WA_MailObject = null;
?>