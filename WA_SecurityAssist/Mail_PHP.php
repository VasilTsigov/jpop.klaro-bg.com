<?php

function WA_SecA_getEmailArray($emailStr)     {
  $retArray = array();
  $emailArr = explode(";",$emailStr);
  foreach ($emailArr AS $emailString)     {
    if (strpos($emailString,"@") > 0)     {
      $emailArr2 = explode("|WA|", $emailString);
      if (sizeof($emailArr2) == 1)     {
        $tempArray    = array(2);
        $tempArray[0] = "";
        $tempArray[1] = trim($emailString);
        $retArray[]   = $tempArray;
      }
      else     {
        $tempArr = array("", "");
		$eArr0 = $emailArr2[0];
		$eArr1 = $emailArr2[1];
        if ((strpos($eArr1, "@") > 0 || strpos($eArr1, "@") === 0) && (strpos($eArr1, " ") === false))      {
          $tempArr[0] = trim($emailArr2[0]);
          $tempArr[1] = trim($emailArr2[1]);
        }
        else     {
          $tempArr[0] = trim($emailArr2[1]);
          $tempArr[1] = trim($emailArr2[0]);
        }
        $retArray[] = $tempArr;
      }
    }
  }
  return $retArray;
}

function WA_SecurityAssist_Definition($serverName,$serverPort,$retPath,$organization,$xMailer,$charSet)     {
  $mailObj = new WA_SECURITYASSIST_MAILOBJ($serverName,$serverPort,$retPath,$organization,$xMailer,$charSet);
  return $mailObj;
}

class WA_SECURITYASSIST_MAILOBJ {
  var $SMTP;
  var $Port;
  var $ReturnPath;
  var $Organization;
  var $XMailer;
  var $CharSet;
  var $Importance;
  var $BodyFormat;
  var $attachments;
  var $recipients;
  var $ccrecip;
  var $bccrecip;
  function WA_SECURITYASSIST_MAILOBJ($serverName,$serverPort,$retPath,$organization,$xMailer,$charSet) {
    $this->SMTP         = $serverName;
    $this->Port         = $serverPort;
    $this->ReturnPath   = $retPath;
    $this->Organization = $organization;
    $this->XMailer      = $xMailer;
    $this->CharSet      = $charSet;
    if ($serverName != "") ini_set("SMTP", $serverName);
    if ($serverPort != "") ini_set("smtp_port", $serverPort); 
    $this->Importance = "";
    $this->BodyFormat = "";
    $this->attachments = array();
    $this->recipients  = array();
    $this->ccrecip     = array();
    $this->bccrecip    = array();
  }
}

function WA_SecurityAssist_SendMail($mailObj,$mailAttachments,$mailBCC,$mailCC,$mailTo,$mailImportance,$mailFrom,$mailSubject,$mailBody)     {
  if (strpos($mailTo,"@") < 0)  {
    return;
  }
  $fromArray = WA_SecA_getEmailArray($mailFrom);
  $mailTo2 = "";
  $mailContent = "";
  $mailHeader = "";
  $mailFrom = $fromArray[0][1];
  if ($fromArray[0][0] != "")     {
    $mailFrom = $fromArray[0][0]." <".$fromArray[0][1].">";
  }
  $mailHeader .= "MIME-Version: 1.0\r\n";
  if (sizeof($mailObj->attachments) > 0 && is_array($mailObj->attachments[0]))     {
    $mailHeader .= "Content-Type: multipart/mixed";
    $mailHeader .= "; boundary = WAMULTIBREAKWA\r\n";
  }
  else if ($mailObj->BodyFormat == 2) {
    $mailHeader .= "Content-Type: multipart/alternative; boundary = WAMULTIBREAKWA\r\n";
    $headers  = "";
  }
  else {
    if ($mailObj->BodyFormat == 1) $mailHeader .= "Content-Type: text/plain";
    elseif ($mailObj->BodyFormat == 0) $mailHeader .= "Content-Type: text/html";
	if ($mailObj->CharSet != "") $mailHeader .= "; charset=\"".$mailObj->CharSet."\"\r\n";
	else $mailHeader .= "\r\n";
  }

  foreach ($mailObj->recipients AS $emailArr) {
  	if ($mailTo != "") $mailTo .= ", ";
  	if ($mailTo2 != "") $mailTo2 .= ", ";
	if ($emailArr[0] != "") $mailTo .= $emailArr[0];
	if ($emailArr[1] != "") $mailTo2 .= $emailArr[1]." <".$emailArr[0].">";
	else $mailTo2 .= $emailArr[0];
  }
  if (strpos($mailTo2, "@")) {
    $mailHeader .= "To: ".$mailTo2."\r\n";
  }
  $mailHeader .= "From: ".$mailFrom."\r\n";

  foreach ($mailObj->ccrecip AS $emailArr) {
  	if ($mailCC != "") $mailCC .= ", ";
	if ($emailArr[1] != "") $mailCC .= $emailArr[1]." <".$emailArr[0].">";
	else $mailCC .= $emailArr[0];
  }
  if (strpos($mailCC, "@")) {
    $mailHeader .= "Cc: ".$mailCC."\r\n";
  }

  foreach ($mailObj->bccrecip AS $emailArr) {
  	if ($mailBCC != "") $mailBCC .= ", ";
	if ($emailArr[1] != "") $mailBCC .= $emailArr[1]." <".$emailArr[0].">";
	else $mailBCC .= $emailArr[0];
  }
  if (strpos($mailBCC, "@")) {
    $mailHeader .= "Bcc: ".$mailBCC."\r\n";
  }

  $mailHeader .= "Reply-To: ".$fromArray[0][1].";\r\n";
  $mailHeader .= "Subject: ".$mailSubject."\r\n";
  $mailHeader .= "X-Sender: ".$mailFrom."\r\n";
  $mailHeader .= "X-Priority: ".$mailObj->Importance."\r\n";
  $mailHeader .= "Date: ". date('r (T)'). "\r\n";
  if ($mailObj->ReturnPath != "") {
    $retArray = WA_SecA_getEmailArray($mailObj->ReturnPath);
    $mailObj->ReturnPath = "<".$retArray[0][1].">";
    if ($retArray[0][0] != "")     {
      $mailObj->ReturnPath = $retArray[0][0]." <".$retArray[0][1].">";
    }
    $mailHeader .= "Return-Path: ".$mailObj->ReturnPath."\r\n";
	$theMSGID = $retArray[0][1];
	$theMSGID = explode("@", $theMSGID);
	$theMSGID = "<".md5($theMSGID[0]).">@".$theMSGID[1];
	$mailHeader .= "Message-ID: ".$theMSGID."\r\n";
  }
  if ($mailObj->Organization != "") {
    $mailHeader .= "Organization: ".$mailObj->Organization."\r\n";
  }
  if ($mailObj->XMailer != "") {
    $mailHeader .= "X-Mailer: ".$mailObj->XMailer."\r\n";
  }
  if ($mailObj->BodyFormat == 2 || sizeof($mailObj->attachments) > 0)     {
    $mailContent = "This is a multi-part message in MIME format.\n--WAMULTIBREAKWA\n";
	switch ($mailObj->BodyFormat)   {
	  case 2:
        if (sizeof($mailObj->attachments) > 0)  {
          $mailContent .= "Content-Type: multipart/alternative; boundary = WAMULTIBREAKWA\r\n";
        }
        $mailContent .= "Content-Type: text/plain";
        if ($mailObj->CharSet != "") $mailContent .= "; charset=\"".$mailObj->CharSet."\"\r\n";
		else $mailContent .= "\r\n";
		$theReplace  = "\n--WAMULTIBREAKWA\n";
        $theReplace .= "Content-Type: text/html";
        if ($mailObj->CharSet != "") $theReplace .= "; charset=\"".$mailObj->CharSet."\"\r\n";
		else $theReplace .= "\r\n";
        $mailBody    = str_replace("<multipartbreak />", $theReplace, $mailBody);
        $mailContent .= $mailBody;
	    break;
      case 1:
        $mailContent .= "Content-Type: text/plain";
        if ($mailObj->CharSet != "") $mailContent .= "; charset=\"".$mailObj->CharSet."\"\r\n";
		else $mailContent .= "\r\n";
        $mailContent .= "Content-Transfer-Encoding: 8bit;\r\n";
		$mailContent .= $mailBody;
        break;
      case 0: 
        $mailContent .= "Content-Type: text/html";
        if ($mailObj->CharSet != "") $mailContent .= "; charset=\"".$mailObj->CharSet."\"\r\n";
		else $mailHeader .= "\r\n";
        $mailContent .= "Content-Transfer-Encoding: 8bit\r\n";
		$mailContent .= $mailBody;
        break;
    }
  }
  else {
    $mailContent .= $mailBody;
  }
  if(sizeof($mailObj->attachments) > 0)    {
    foreach ($mailObj->attachments as $fileArr)    {
      if (is_readable($fileArr[3]))    {
        if (strtolower($fileArr[1]) == "base64")     {
          $data = chunk_split(base64_encode(implode("", file($fileArr[3]))));
        }
        else     {
          $data = implode("", file($fileArr[3]));
        }
        $mailAttachments .= "\n--WAMULTIBREAKWA";
        $mailAttachments .= "\nContent-Type: ".$fileArr[0];
        $mailAttachments .= "; name=\"".basename($fileArr[3])."\"\r\n";
        $mailAttachments .= "Content-Transfer-Encoding: ".$fileArr[1]."\r\n";
        $mailAttachments .= "Content-Disposition: inline;";
        $mailAttachments .= " filename=\"".basename($fileArr[3])."\"\r\n\r\n";
        $mailAttachments .= $data;
      }
    }
  }
  $mailContent = str_replace("<multipartbreak />", "--WAMULTIBREAKWA\n", $mailContent);
  $mailHeader  = str_replace("<multipartbreak />", "--WAMULTIBREAKWA\n", $mailHeader);
  $mailContent .= $mailAttachments;
  $mailObj = mail($mailTo,$mailSubject,$mailContent,$mailHeader);

  return $mailObj;
}


?>