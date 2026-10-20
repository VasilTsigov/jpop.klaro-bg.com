<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "ivilievltu@klaro-bg.com";
$MailSubject     = "International journal Propagation of Ornamental Plants (ISSN 1311-9109)";
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
  $WA_MailObject = WAUE_AddAttachment($WA_MailObject,"yourfile.gif");

//BCC Entries

//CC Entries

//Body Format
  $WA_MailObject = WAUE_BodyFormat($WA_MailObject,0);

//Set Importance
  $WA_MailObject = WAUE_SetImportance($WA_MailObject,"3");

//Start Mail Body
$MailBody = $MailBody . "<html><head>\r\n";
$MailBody = $MailBody . "<style type=\"text/css\">\r\n";
$MailBody = $MailBody . "<!--\r\n";
$MailBody = $MailBody . "table.MsoNormalTable {\r\n";
$MailBody = $MailBody . "font-size:10.0pt;\r\n";
$MailBody = $MailBody . "font-family:\"Times New Roman\",\"serif\";\r\n";
$MailBody = $MailBody . "}\r\n";
$MailBody = $MailBody . "p.- {\r\n";
$MailBody = $MailBody . "margin:0cm;\r\n";
$MailBody = $MailBody . "margin-bottom:.0001pt;\r\n";
$MailBody = $MailBody . "font-size:12.0pt;\r\n";
$MailBody = $MailBody . "font-family:\"Times New Roman\",\"serif\";\r\n";
$MailBody = $MailBody . "}\r\n";
$MailBody = $MailBody . "p.heading1 {\r\n";
$MailBody = $MailBody . "margin:0cm;\r\n";
$MailBody = $MailBody . "margin-bottom:.0001pt;\r\n";
$MailBody = $MailBody . "text-align:justify;\r\n";
$MailBody = $MailBody . "font-size:12.0pt;\r\n";
$MailBody = $MailBody . "font-family:\"Times New Roman\",\"serif\";\r\n";
$MailBody = $MailBody . "}\r\n";
$MailBody = $MailBody . "p.MsoNormal {\r\n";
$MailBody = $MailBody . "margin:0cm;\r\n";
$MailBody = $MailBody . "margin-bottom:.0001pt;\r\n";
$MailBody = $MailBody . "font-size:10.0pt;\r\n";
$MailBody = $MailBody . "font-family:\"Times New Roman\",\"serif\";\r\n";
$MailBody = $MailBody . "}\r\n";
$MailBody = $MailBody . "p.Hyperlink1 {\r\n";
$MailBody = $MailBody . "margin:0cm;\r\n";
$MailBody = $MailBody . "margin-bottom:.0001pt;\r\n";
$MailBody = $MailBody . "line-height:120%;\r\n";
$MailBody = $MailBody . "text-autospace:ideograph-numeric;\r\n";
$MailBody = $MailBody . "font-size:10.0pt;\r\n";
$MailBody = $MailBody . "font-family:\"Adobe Garamond\",\"serif\";\r\n";
$MailBody = $MailBody . "color:blue;\r\n";
$MailBody = $MailBody . "text-decoration:underline;\r\n";
$MailBody = $MailBody . "text-underline:thick;\r\n";
$MailBody = $MailBody . "}\r\n";
$MailBody = $MailBody . "p.MsoBodyText {\r\n";
$MailBody = $MailBody . "margin:0cm;\r\n";
$MailBody = $MailBody . "margin-bottom:.0001pt;\r\n";
$MailBody = $MailBody . "text-align:justify;\r\n";
$MailBody = $MailBody . "font-size:10.0pt;\r\n";
$MailBody = $MailBody . "font-family:\"Times New Roman\",\"serif\";\r\n";
$MailBody = $MailBody . "font-weight:bold;\r\n";
$MailBody = $MailBody . "}\r\n";
$MailBody = $MailBody . "-->\r\n";
$MailBody = $MailBody . "</style>\r\n";
$MailBody = $MailBody . "</head><body>\r\n";
$MailBody = $MailBody . "<table class=\"MsoNormalTable\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"margin-left:2.75pt;border-collapse:collapse;\">\r\n";
$MailBody = $MailBody . "  <tr style=\"height:60.0pt;\">\r\n";
$MailBody = $MailBody . "    <td width=\"104\" style=\"width:78.25pt;border-top:solid black 1.0pt;border-left:none;border-bottom:solid black 1.0pt;border-right:none;padding:2.75pt 2.75pt 2.75pt 2.75pt;height:60.0pt;\"><p class=\"-\" style=\"layout-grid-mode:char;\"><a name=\"OLE_LINK2\" id=\"OLE_LINK2\"></a><a name=\"OLE_LINK1\" id=\"OLE_LINK1\"><span class=\"-\" style=\"layout-grid-mode:char;\"><a name=\"OLE_LINK1\" id=\"OLE_LINK3\"><img src=\"http://jpop.klaro-bg.com/sendmails/index_WAUE_HTML_WA_Universal_Email_1_clip_image002.jpg\" alt=\"\" width=\"97\" height=\"97\" /></a></span></a></p></td>\r\n";
$MailBody = $MailBody . "    <td width=\"492\" style=\"width:369.0pt;border-top:solid black 1.0pt;border-left:none;border-bottom:solid black 1.0pt;border-right:none;padding:2.75pt 2.75pt 2.75pt 2.75pt;height:60.0pt;\"><p class=\"heading1\" align=\"center\" style=\"margin-top:6.2pt;text-align:center;line-height:120%;layout-grid-mode:char;text-autospace:ideograph-numeric;vertical-align:middle;\"><strong><span style=\"line-height:120%; font-size:15.0pt; color:black; \">PROPAGATION OF ORNAMENTAL    PLANTS</span></strong><strong><span style=\"line-height:120%; font-size:8.5pt; color:black; \"> </span></strong></p>\r\n";
$MailBody = $MailBody . "      <p class=\"MsoNormal\" align=\"center\" style=\"text-align:center;line-height:120%;text-autospace:ideograph-numeric;vertical-align:middle;\"><span style=\"line-height:120%; font-size:11.0pt; color:black; \">Editorial Office, University     of Forestry, 10 Kliment    Ohridski blvd.,</span></p>\r\n";
$MailBody = $MailBody . "      <p class=\"Hyperlink1\" align=\"center\" style=\"text-align:center;\"><span style=\"line-height:120%; text-underline:none; font-family:'Times New Roman','serif'; font-size:11.0pt; color:black; text-decoration:none; \">Sofia</span><span style=\"font-family:'Times New Roman','serif'; font-size:11.0pt; color:black; text-decoration:none; \"> 1797, Bulgaria,    Fax: (++ 359 2) 862 28 30, e-mail: </span> <a href=\"mailto:ivilievltu@yahoo.com\"><span style=\"line-height:120%; text-underline:none; font-family:'Times New Roman','serif'; font-size:11.0pt; color:windowtext; text-decoration:none; \">ivilievltu@yahoo.com</span></a><span style=\"font-family:'Times New Roman','serif'; font-size:11.0pt; color:windowtext; text-decoration:none; \">,</span></p>\r\n";
$MailBody = $MailBody . "      <p class=\"Hyperlink1\" align=\"center\" style=\"text-align:center;\"> <a href=\"http://www.journal-pop.org/\"><span style=\"font-family:'Times New Roman','serif'; font-size:11.0pt; color:windowtext; text-decoration:none; \">www.journal-pop.org</span></a><span style=\"line-height:120%; text-underline:none; font-size:12.0pt; color:black; text-decoration:none; \"> </span></p></td>\r\n";
$MailBody = $MailBody . "  </tr>\r\n";
$MailBody = $MailBody . "</table>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\">&nbsp;</p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\">&nbsp;</p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\">&nbsp;</p>\r\n";
$MailBody = $MailBody . "<h1>Dear Dr.<span style=\"color: black\"> ";
$MailBody = $MailBody .  $row_rsEmails['Name'];
$MailBody = $MailBody . "</span>,</h1>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">&nbsp;</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">The International journal <em>Propagation of Ornamental Plants</em> (ISSN 1311-9109) is medium in all  aspects of ornamental plants propagation. It publishes original papers aimed at  all aspects of in vitro and in vivo propagation (genetics, physiological,  biochemical, anatomical, technological etc.) of the ornamental species – trees,  shrubs and flowers. </span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">As Editor-in-Chief, I strive to uphold and improve  the high standard of <em>Propagation of  Ornamental Plants </em>and to advance its function as a communication tool for  scientists. Thus, in addition to research articles <em>Propagation of Ornamental Plants</em> also publishes book reviews,  review papers, </span><span style=\"font-size:12.0pt; \">research</span><span style=\"font-size:12.0pt; \"> notes, protocols  and technologies, announcements of conferences and meetings.</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">It is peer reviewed journal with Impact Factor. </span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">&nbsp;</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoBodyText\"><span style=\"font-size:12.0pt; font-weight:normal; \">The journal is covered by Current  Contents/Agriculture, Biology and Environmental Sciences and SCIE of Thomson Reuters  and by SCOPUS database of Elsevier.</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">&nbsp;</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><strong><em><span style=\"font-size:12.0pt; \">By this letter I would kindly invite you to submit  your manuscript(s) for possible publishing.</span></em></strong><strong><span style=\"font-size:12.0pt; \"> </span></strong><span style=\"font-size:12.0pt; \"> </span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">Please don&rsquo;t hesitate to contact with me if you have  any additional questions. </span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">&nbsp;</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">&nbsp;</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">Best regards</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">&nbsp;</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">&nbsp;</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">Prof. Ivan Iliev</span></p>\r\n";
$MailBody = $MailBody . "<h1>Editor-in-Chief</h1>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\"><span style=\"font-size:11.0pt; \">E-mail: </span><a href=\"mailto:ivilievltu@yahoo.com\"><span style=\"font-size:11.0pt; \">ivilievltu@yahoo.com</span></a><span style=\"font-size:12.0pt; \"> </span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\"><span style=\"font-size:12.0pt; \">Website: </span> <a href=\"http://www.journal-pop.org/\"><span style=\"font-size:11.0pt; \">www.journal-pop.org</span></a><span style=\"font-size:11.0pt; \"> </span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\"><span style=\"font-size:12.0pt; \">&nbsp;</span></p>\r\n";
$MailBody = $MailBody . "</body></html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_index_1");

if (isset($GLOBALS["waue_index_1_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_index_1";
  $MailLogBindings->Success->MailRef = "waue_index_1";
  $MailLogBindings->Failure->MailRef = "waue_index_1";
  $MailLogBindings->processLog(($GLOBALS["waue_index_1_Status"] == "Failure"));
}
$WA_MailObject = null;
?>