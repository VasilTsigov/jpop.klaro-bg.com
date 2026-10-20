<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "Journal POP|WA|ivilievltu@klaro-bg.com";
$MailSubject     = "Message from contact form";
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
  $WA_MailObject = WAUE_AddAttachment($WA_MailObject,"../images/EmailLogo/logo.png");

//BCC Entries

//CC Entries

//Body Format
  $WA_MailObject = WAUE_BodyFormat($WA_MailObject,0);

//Set Importance
  $WA_MailObject = WAUE_SetImportance($WA_MailObject,"3");

//Start Mail Body
$MailBody = $MailBody . "<html><head></head><body>\r\n";
$MailBody = $MailBody . "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n";
$MailBody = $MailBody . "  <tr>\r\n";
$MailBody = $MailBody . "    <td><img src=\"../images/EmailLogo/logo.png\" alt=\"\" width=\"97\" height=\"97\" /></td>\r\n";
$MailBody = $MailBody . "    <td>&nbsp;</td>\r\n";
$MailBody = $MailBody . "  </tr>\r\n";
$MailBody = $MailBody . "  <tr>\r\n";
$MailBody = $MailBody . "    <td>&nbsp;</td>\r\n";
$MailBody = $MailBody . "    <td>&nbsp;</td>\r\n";
$MailBody = $MailBody . "  </tr>\r\n";
$MailBody = $MailBody . "  <tr>\r\n";
$MailBody = $MailBody . "    <td width=\"12%\"><strong>Email:</strong></td>\r\n";
$MailBody = $MailBody . "    <td width=\"88%\">";
$MailBody = $MailBody .  ((isset($_POST["txtEmail"]))?$_POST["txtEmail"]:"");
$MailBody = $MailBody . "</td>\r\n";
$MailBody = $MailBody . "  </tr>\r\n";
$MailBody = $MailBody . "  <tr>\r\n";
$MailBody = $MailBody . "    <td><strong>Name:</strong></td>\r\n";
$MailBody = $MailBody . "    <td>";
$MailBody = $MailBody .  ((isset($_POST["txtName"]))?$_POST["txtName"]:"");
$MailBody = $MailBody . "</td>\r\n";
$MailBody = $MailBody . "  </tr>\r\n";
$MailBody = $MailBody . "  <tr>\r\n";
$MailBody = $MailBody . "    <td><strong>Message:</strong></td>\r\n";
$MailBody = $MailBody . "    <td>";
$MailBody = $MailBody .  ((isset($_POST["txtMessage"]))?$_POST["txtMessage"]:"");
$MailBody = $MailBody . "</td>\r\n";
$MailBody = $MailBody . "  </tr>\r\n";
$MailBody = $MailBody . "  <tr>\r\n";
$MailBody = $MailBody . "    <td>&nbsp;</td>\r\n";
$MailBody = $MailBody . "    <td>&nbsp;</td>\r\n";
$MailBody = $MailBody . "  </tr>\r\n";
$MailBody = $MailBody . "</table>\r\n";
$MailBody = $MailBody . "</body></html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_contacts_Index_1");

if (isset($GLOBALS["waue_contacts_Index_1_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_contacts_Index_1";
  $MailLogBindings->Success->MailRef = "waue_contacts_Index_1";
  $MailLogBindings->Failure->MailRef = "waue_contacts_Index_1";
  $MailLogBindings->processLog(($GLOBALS["waue_contacts_Index_1_Status"] == "Failure"));
}
$WA_MailObject = null;
?>