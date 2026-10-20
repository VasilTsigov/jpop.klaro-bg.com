<?php
$MailAttachments = "";
$MailBCC         = "";
$MailCC          = "";
$MailTo          = "";
$MailBodyFormat  = "";
$MailBody        = "";
$MailImportance  = "";
$MailFrom        = "Journal POP|WA|ivilievltu@klaro-bg.com";
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

//BCC Entries

//CC Entries

//Body Format
  $WA_MailObject = WAUE_BodyFormat($WA_MailObject,0);

//Set Importance
  $WA_MailObject = WAUE_SetImportance($WA_MailObject,"1");

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
$MailBody = $MailBody . "p.NormalWeb1 {\r\n";
$MailBody = $MailBody . "margin:0cm;\r\n";
$MailBody = $MailBody . "margin-bottom:.0001pt;\r\n";
$MailBody = $MailBody . "font-size:12.0pt;\r\n";
$MailBody = $MailBody . "font-family:\"Times New Roman\",\"serif\";\r\n";
$MailBody = $MailBody . "}\r\n";
$MailBody = $MailBody . "p.Normal1 {\r\n";
$MailBody = $MailBody . "margin:0cm;\r\n";
$MailBody = $MailBody . "margin-bottom:.0001pt;\r\n";
$MailBody = $MailBody . "font-size:12.0pt;\r\n";
$MailBody = $MailBody . "font-family:\"Times New Roman\",\"serif\";\r\n";
$MailBody = $MailBody . "}\r\n";
$MailBody = $MailBody . "-->\r\n";
$MailBody = $MailBody . "</style>\r\n";
$MailBody = $MailBody . "</head><body>\r\n";
$MailBody = $MailBody . "<table class=\"MsoNormalTable\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"margin-left:2.75pt;border-collapse:collapse;\">\r\n";
$MailBody = $MailBody . "  <tr style=\"height:60.0pt;\">\r\n";
$MailBody = $MailBody . "    <td width=\"104\" style=\"width:78.25pt;border-top:solid black 1.0pt;border-left:none;border-bottom:solid black 1.0pt;border-right:none;padding:2.75pt 2.75pt 2.75pt 2.75pt;height:60.0pt;\"><p class=\"-\" style=\"layout-grid-mode:char;\"><a name=\"OLE_LINK2\" id=\"OLE_LINK2\"></a><a name=\"OLE_LINK1\" id=\"OLE_LINK1\"><img src=\"letter_1_clip_image002.jpg\" alt=\"jpoplogo\" width=\"97\" height=\"97\" /></a></p></td>\r\n";
$MailBody = $MailBody . "    <td width=\"528\" style=\"width:396.0pt;border-top:solid black 1.0pt;border-left:none;border-bottom:solid black 1.0pt;border-right:none;padding:2.75pt 2.75pt 2.75pt 2.75pt;height:60.0pt;\"><p class=\"heading1\" align=\"center\" style=\"margin-top:6.2pt;text-align:center;line-height:120%;layout-grid-mode:char;text-autospace:ideograph-numeric;vertical-align:middle;\"><strong><span style=\"line-height:120%; font-size:15.0pt; color:black; \">PROPAGATION OF ORNAMENTAL    PLANTS</span></strong><strong><span style=\"line-height:120%; font-size:8.5pt; color:black; \"> </span></strong></p>\r\n";
$MailBody = $MailBody . "      <p class=\"MsoNormal\" align=\"center\" style=\"text-align:center;line-height:120%;text-autospace:ideograph-numeric;vertical-align:middle;\"><span style=\"line-height:120%; font-size:11.0pt; color:black; \">Editorial Office, University     of Forestry, 10 Kliment    Ohridski blvd.,</span></p>\r\n";
$MailBody = $MailBody . "      <p class=\"Hyperlink1\" align=\"center\" style=\"text-align:center;\"><span style=\"line-height:120%; text-underline:none; font-family:'Times New Roman','serif'; font-size:11.0pt; color:black; text-decoration:none; \">Sofia 1797,    Bulgaria, Fax: (++ 359 2) 862 28 30, e-mail: </span> <a href=\"mailto:ivilievltu@yahoo.com\"><span style=\"line-height:120%; text-underline:none; font-family:'Times New Roman','serif'; font-size:11.0pt; color:windowtext; text-decoration:none; \">ivilievltu@yahoo.com</span></a><span style=\"font-family:'Times New Roman','serif'; font-size:11.0pt; color:windowtext; text-decoration:none; \">,</span></p>\r\n";
$MailBody = $MailBody . "      <p class=\"Hyperlink1\" align=\"center\" style=\"text-align:center;\"> <a href=\"http://www.journal-pop.org/\"><span style=\"font-family:'Times New Roman','serif'; font-size:11.0pt; color:windowtext; text-decoration:none; \">www.journal-pop.org</span></a><span style=\"line-height:120%; text-underline:none; font-size:12.0pt; color:black; text-decoration:none; \"> </span></p></td>\r\n";
$MailBody = $MailBody . "  </tr>\r\n";
$MailBody = $MailBody . "</table>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\"><span style=\"font-size:12.0pt; \">&nbsp;</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">&nbsp;</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">&nbsp;</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\"><span style=\"font-size:12.0pt; \">&nbsp;</span></p>\r\n";
$MailBody = $MailBody . "<p>Dear Dr. ";
$MailBody = $MailBody .  $row_rsEmails['Name'];
$MailBody = $MailBody . ",</p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">&nbsp;</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">The  International journal <em>Propagation of  Ornamental Plants</em> (ISSN 1311-9109) is medium in all aspects of ornamental  plants propagation. It publishes original papers aimed at all aspects of in  vitro and in vivo propagation (genetics, physiological, biochemical,  anatomical, technological etc.) of the ornamental species – trees, shrubs and  flowers. </span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">As  Editor-in-Chief, I strive to uphold and improve the high standard of <em>Propagation of Ornamental Plants </em>and to  advance its function as a communication tool for scientists. Thus, in addition  to research articles <em>Propagation of  Ornamental Plants</em> also publishes book reviews, review papers, short notes,  protocols and technologies, announcements of conferences and meetings.</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">&nbsp;</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoBodyText\"><span style=\"font-size:12.0pt; \">The  journal is covered by Current Contents/Agriculture, Biology and Environmental  Sciences and SCIE of Thomson Scientific and by SCOPUS database of Elsevier.</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><strong><span style=\"font-size:12.0pt; \">It is peer reviewed journal with Impact  Factor.</span></strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">&nbsp;</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\"><strong><u>1.  Subscription rates for 2015 are the following:</u></strong><u> </u></p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">&nbsp;</p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\"><em><u>Institutional</u></em></p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">- printed version of vol. 15,  No 1,2,3, and 4: 155 USD&nbsp;+ 25 USD postal expenses (<strong>Europe</strong>)  i.e. <strong>180 USD</strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">- printed version of vol. 15,  No 1,2,3, and 4: 155 USD&nbsp;+ 30 USD postal expenses (<strong>overseas</strong>) i.e. <strong>185 USD</strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">- electronic version for 1 user  (one IP address) of&nbsp;vol. 15, No 1,2,3, and 4: <strong>155 USD</strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">&nbsp;&nbsp;</p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\"><em><u>Personal</u></em></p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">- printed version of vol. 15,  No 1,2,3, and 4: 125 USD&nbsp;+ 20 USD postal expenses (<strong>Europe</strong>)  i.e. <strong>145 USD</strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">- printed version of vol. 15,  No 1,2,3, and 4: 125 USD&nbsp;+ 25 USD postal expenses (<strong>overseas</strong>) i.e. <strong>150 USD</strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">- electronic version for 1 user  of&nbsp;vol. 15, No 1,2,3, and 4: <strong>125  USD</strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">&nbsp;</p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\" style=\"text-align:justify;\"><u>2. </u><strong><u>Please find the discounts&nbsp;for printed version, 2015</u></strong><strong><u>:</u></strong><u> </u></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"background:white;\"><span style=\"font-size:12.0pt; color:black; \">- for  1-3 subscriptions </span><span style=\"font-size:12.0pt; \">there  is not discount.</span><span style=\"font-family:'Courier New'; font-size:12.0pt; color:black; \"> </span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"background:white;\"><span style=\"font-size:12.0pt; color:black; \">- for  4-10 subscriptions the discount is 15% </span><span style=\"font-family:'Courier New'; font-size:12.0pt; color:black; \"> </span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"background:white;\"><span style=\"font-size:12.0pt; color:black; \">- for  11-20 subscriptions the discount is 25%</span><span style=\"font-family:'Courier New'; font-size:12.0pt; color:black; \"> </span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"background:white;\"><span style=\"font-size:12.0pt; color:black; \">- for  21-30 subscriptions the discount is 35% </span><span style=\"font-family:'Courier New'; font-size:12.0pt; color:black; \"> </span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"background:white;\"><span style=\"font-size:12.0pt; color:black; \">- for  31-50 subscriptions is 45% </span><span style=\"font-family:'Courier New'; font-size:12.0pt; color:black; \"> </span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"background:white;\"><span style=\"font-size:12.0pt; color:black; \">- for  51-100 subscriptions is 55% </span><span style=\"font-family:'Courier New'; font-size:12.0pt; color:black; \"> </span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"background:white;\"><span style=\"font-size:12.0pt; color:black; \">- for  more than 100 subscriptions is 65% </span><span style=\"font-family:'Courier New'; font-size:12.0pt; color:black; \"> </span></p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\" style=\"text-align:justify;\">&nbsp;</p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\" style=\"text-align:justify;\"><strong><u>3. The subscription rates for back, printed issues&nbsp;are the following:</u></strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\" style=\"text-align:justify;\"><strong><span style=\"font-weight:normal; \">2014, vol. 14, No 1,2,3, and 4:</span></strong> 125 USD&nbsp;+ 20 (25) USD postal expenses (Europe) i.e. <strong>145 USD and 1</strong><strong>50 (overseas) USD</strong><strong><span style=\"font-weight:normal; \"> </span></strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\" style=\"text-align:justify;\"><strong><span style=\"font-weight:normal; \">2013, </span></strong>vol. 13,  No 1,2,3, and 4: 125 USD&nbsp;+ 20 (25) USD postal expenses (Europe) i.e. <strong>145 USD and 1</strong><strong>50 (overseas) USD<u></u></strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\"><strong><span style=\"font-weight:normal; \">2012, </span></strong>vol. 12, No 1,2,3, and 4: 100  USD&nbsp;+ 20 (25) USD postal expenses (Europe) i.e. <strong>120 USD and 125  (overseas) USD</strong><strong><span style=\"font-weight:normal; \"> </span></strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\"><strong><span style=\"font-weight:normal; \">2011, </span></strong>vol. 11, No 1,2,3, and 4: 100  USD&nbsp;+ 20 (25) USD postal expenses (Europe) i.e. <strong>120 USD and 125  (overseas) USD</strong><strong><span style=\"font-weight:normal; \"> </span></strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\"><strong><span style=\"font-weight:normal; \">2010, vol. </span></strong><strong><span style=\"font-weight:normal; \">10</span></strong><strong><span style=\"font-weight:normal; \">, No  1, 2, 3, and 4</span></strong><strong>= </strong>100 USD + 20 (25) USD postal expenses  i.e<strong>. <strong>120 (Europe)  and 125 (overseas) USD</strong></strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\">2009, vol.&nbsp;9, No 1, 2, 3 and 4 = 100 USD + 20 (25) USD postal  expenses i.e. <strong>12</strong><strong>0 (Europe) and 12</strong><strong>5 (overseas) USD</strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\">2008, vol. 8, No 1, 2, 3 and 4 = 50 USD + 20 (25)  USD postal expenses i.e. <strong>70 (Europe) and 75  (overseas) USD</strong><strong> </strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\">2007, vol. 7, No 1, 2, 3 and 4 = 50 USD + 20 (25) USD postal  expenses i.e. <strong>70 (Europe) and 75 (overseas)  USD</strong><strong> </strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\">2006, vol. 6, No 1, 2, 3 and 4 = 50 USD + 20 (25) USD postal  expenses i.e. <strong>70 (Europe) and 7</strong><strong>5 (overseas) USD</strong><strong> </strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\">2005, vol. 5, No 1, 2, 3 and 4 = 50 USD + 20 (25)  USD postal expenses i.e. <strong>70 (Europe) and 75  (overseas) USD</strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">2004, vol. 4, No 1 and 2 = 30  USD + 20 (25) USD postal expenses i.e. <strong>50 (Europe)  and 55 (overseas) USD</strong><strong> </strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">2003, vol. 3, No 1 and 2 = 30  USD + 20 (25) USD postal expenses i.e. <strong>50 (Europe)  and 55 (overseas) USD</strong><strong> </strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">2002, vol. 2, No 1 and 2 = 30  USD + 20 (25) USD postal expenses i.e. <strong>50 (Europe)  and 55 (overseas) USD</strong><strong> </strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">2001, vol. 1, No 1 = 30 USD +  20 (25) USD postal expenses i.e. <strong>50 (Europe)  and 55 (overseas) USD </strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">&nbsp; </p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">The price of electronic  offprint (pdf file) of each publication is 40 USD.</p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">&nbsp;</p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\"><strong><u>4. Address of the journal:</u></strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">University of Forestry </p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">10 Kliment Ohridski blvd.</p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">1756  Sofia</p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">Bulgaria </p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">E-mail: <a href=\"mailto:ivilievltu@yahoo.com\" target=\"_blank\"><span style=\"font-size:10.0pt; \">ivilievltu@yahoo.com</span></a>&nbsp;&nbsp;</p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">Fax: + 359 2 862 28 30</p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">Phone + 359 887 74 05 70</p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">&nbsp;</p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\"><strong><u>5. The journal bank draft is: </u></strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\" style=\"text-align:justify;\">&nbsp;</p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\" style=\"text-align:justify;\"><strong>Beneficiary Customer: </strong>SALVIA PRESS Ltd., </p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\" style=\"text-align:justify;\">Address: z.k. Drujba-2, bl.  523, vhod G, ap. 5, Sofia 1582, Bulgaria, </p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\" style=\"text-align:justify;\">DSK BANK - BRANCH DARVENITZA, </p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\" style=\"text-align:justify;\">BIC: STSABGSF, </p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\" style=\"text-align:justify;\">IBAN: BG10STSA93000019135100&nbsp;</p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\" style=\"text-align:justify;\"><strong>&nbsp;</strong></p>\r\n";
$MailBody = $MailBody . "<p class=\"Normal1\" style=\"text-align:justify;\"><strong>Also, you could pay by PayPal</strong><strong> through: </strong>sejani.ltd@gmail.com </p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">&nbsp;</p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">Please do not hesitate to ask  me if you need additional information.</p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">&nbsp;</p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">With best regards</p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">&nbsp;</p>\r\n";
$MailBody = $MailBody . "<p class=\"NormalWeb1\">Prof. Ivan Iliev</p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\">&nbsp;</p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">&nbsp;</span></p>\r\n";
$MailBody = $MailBody . "<p class=\"MsoNormal\" style=\"text-align:justify;\"><span style=\"font-size:12.0pt; \">Editor-in-Chief</span></p>\r\n";
$MailBody = $MailBody . "</body></html>";
//End Mail Body

$WA_MailObject = WAUE_SendMail($WA_MailObject,$MailAttachments,$MailBCC,$MailCC,$MailTo,$MailImportance,$MailFrom,$MailSubject,$MailBody,"waue_letter_2_1");

if (isset($GLOBALS["waue_letter_2_1_Status"])) {
  $MailLogBindings = new WAUE_Log_Bindings();
  //Start Log Bindings
  //End Log Bindings
  $MailLogBindings->SuccessOrFailure->MailRef = "waue_letter_2_1";
  $MailLogBindings->Success->MailRef = "waue_letter_2_1";
  $MailLogBindings->Failure->MailRef = "waue_letter_2_1";
  $MailLogBindings->processLog(($GLOBALS["waue_letter_2_1_Status"] == "Failure"));
}
$WA_MailObject = null;
?>