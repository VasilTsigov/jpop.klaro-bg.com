<?php 
// version 1.20
$UEPagePath = getcwd();
chdir(dirname(__FILE__));

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/OAuth.php';
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/POP3.php';
require 'phpmailer/src/SMTP.php';

if (file_exists("../mysqli/queryobj.php")) require_once("../mysqli/queryobj.php");
chdir($UEPagePath);
if (!class_exists("Email_Log")) {
	
class Email_Log {
	public function __construct($Action, $Table, $EmailColumn, $Connection) {
    $this->Query = new WA_MySQLi_Query($Connection);
    $this->Query->Action = ($Action=="create"?"insert":$Action);
    $this->Query->Table = $Table;
    $this->EmailColumn = $EmailColumn;
  }
  public function updateLogValues($EmailObj) {
    for ($x=0; $x<sizeof($this->Query->ParamValues); $x++) {
      switch ($this->Query->ParamValues[$x]) {
        case "[To]":
            $this->Query->ParamValues[$x]=$EmailObj->getAllRecipientAddressesAsString();
            break;
        case "[From]":
            $this->Query->ParamValues[$x]=$EmailObj->From;
            break;
        case "[Subject]":
            $this->Query->ParamValues[$x]=$EmailObj->Subject;
            break;
        case "[Body]":
            $this->Query->ParamValues[$x]=$EmailObj->Body;
            break;
        case "[Header]":
            $this->Query->ParamValues[$x]=$EmailObj->Header;
            break;
        case "[Error]":
            $this->Query->ParamValues[$x]=$EmailObj->ErrorMessage;
            break;
        case "[Status]":
            $this->Query->ParamValues[$x]=$EmailObj->Status;
            break;
        case "[Index]":
            $this->Query->ParamValues[$x]=$EmailObj->EmailsSent;
            break;
      } 
    }
  }
}

class WA_Email_Params {
  public $Attachments;
  public $BCC;
  public $Body;
  public $BodyFile;
  public $BodyFormat;
  public $BodyText;
  public $CC;
  public $CharSet;
  public $Debugoutput;
  public $Error;
  public $ErrorMessage;
  public $From;
  public $Host;
  public $Importance;
  public $Interrupt;
  public $Method;
  public $Password;
  public $Port;
  public $Redirect;
  public $ReturnPath;
  public $ReplyTo;
  public $SingleSend;
  public $SMTPDebug;
  public $SMTPOptions;
  public $Status;
  public $Subject;
  public $To;
  public $Username;
  public $XMailer;
}

class WA_Email  {
	public function __construct($Name, $Params = null) {
    if ($Params == null) {
      $Params = new WA_Email_Params();
    }
    $this->ActualTime   = 0;
    $this->Attachments  = $Params->Attachments?$Params->Attachments:array();
    $this->BCC          = $Params->BCC?$Params->BCC:array();
    $this->Body         = isset($Params->Body)?$Params->Body:"";
    $this->BodyFile     = isset($Params->BodyFile)?$Params->BodyFile:"";
    $this->BodyFormat   = isset($Params->BodyFormat)?$Params->BodyFormat:"";
    $this->BodyText     = isset($Params->BodyText)?$Params->BodyText:"";
    $this->BurstSize    = isset($Params->BurstSize)?$Params->BurstSize:0;
    $this->BurstTime    = isset($Params->BurstTime)?$Params->BurstTime:0;
    $this->CC           = isset($Params->CC)?$Params->CC:array();
    $this->CharSet      = isset($Params->CharSet)?$Params->CharSet:"";
    $this->Debugoutput  = isset($Params->Debugoutput)?$Params->Debugoutput:'html';
    $this->EmailsSent   = 0;
    $this->Error        = false;
    $this->ErrorMessage = "";
    $this->From         = isset($Params->From)?$Params->From:"";
    $this->Header       = "";
    $this->LastTime     = 0;
    $this->SMTPDebug    = isset($Params->SMTPDebug)?$Params->SMTPDebug:0;
    $this->SMTPOptions  = isset($Params->SMTPOptions)?$Params->SMTPOptions:array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
    $this->Host         = isset($Params->Host)?$Params->Host:"localhost";
    $this->Importance   = isset($Params->Importance)?$Params->Importance:"";
    $this->Interrupt    = false;
    $this->Method       = isset($Params->Method)?$Params->Method:"mail";
    $this->Mailer       = new PHPMailer(true);
    $this->Name         = $Name;
    $this->onSuccess    = false;
    $this->onFailure    = false;
    $this->onSend       = false;
    $this->onSent       = false;
    $this->Password     = isset($Params->Password)?$Params->Password:false;
		//Set the SMTP port number - likely to be 25, 465 or 587
    $this->Port         = isset($Params->Port)?$Params->Port:25;
    $this->Prepared     = false;
    $this->Redirect     = isset($Params->Redirect)?$Params->Redirect:"";
    $this->ReturnPath   = isset($Params->ReturnPath)?$Params->ReturnPath:"";
    $this->ReplyTo      = isset($Params->ReplyTo)?$Params->ReplyTo:"";
    $this->Sent         = false;
    $this->SingleSend   = isset($Params->SingleSend)?$Params->SingleSend:false;
    $this->SMTPAuth     = isset($Params->SMTPAuth)?$Params->SMTPAuth:true;
    $this->SMTPSecure   = isset($Params->SMTPSecure)?$Params->SMTPSecure:false;
    $this->StartBurst   = 0;
    $this->StartTime    = 0;
    $this->Status       = "init";
    $this->Subject      = isset($Params->Subject)?$Params->Subject:"";
    $this->To           = isset($Params->To)?$Params->To:array();
    $this->Username     = isset($Params->Username)?$Params->Username:false;
    $this->WaitTime     = isset($Params->WaitTime)?$Params->WaitTime:0;
    $this->XMailer      = isset($Params->XMailer)?$Params->XMailer:"";
	}
  
  public function addAttachment($file) {
    if (strpos($file,"/")===0 && !realpath($file)) $file = $_SERVER['DOCUMENT_ROOT'] . substr($file,1);
    if (!file_exists($file)) $file = realpath($file);
    if (is_file($file)) $this->Attachments[] = array($file,"");
  }
  
  public function addAttachmentFromForm($fieldName) {
	 if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]["tmp_name"])  {
	   if (is_file($_FILES[$fieldName]["tmp_name"])) $this->Attachments[] = array($_FILES[$fieldName]["tmp_name"], $_FILES[$fieldName]["name"]);
	 }
  }
  
  public function addAttachmentFromPHP($fileName,$saveAs) {
    ob_start();
    if (!file_exists($fileName)) $fileName = realpath($fileName);
    if (is_file($fileName)) require($fileName);
    $content = ob_get_contents();
    ob_end_clean();
    if ($content) $this->Mailer->addStringAttachment($content,$saveAs);
  }
  
  public function addAttachmentFromRS($rs, $col) {
    if (is_string($rs)) $rs = $GLOBALS[$rs];
		if (is_object($rs) && get_class($rs) == "WA_MySQLi_RS") {
      while (!$rs->atEnd()) {
        $file = $rs->getColumnVal($col,false);
        if (strpos($file,"/")===0 && !realpath($file)) $file = $_SERVER['DOCUMENT_ROOT'] . substr($file,1);
        if (!file_exists($file)) $file = realpath($file);
        if (is_file($file)) $this->Attachments[] = array($file,"");
        $rs->moveNext();
      }
      $rs->moveFirst();
    } else {
      mysql_data_seek($rs, 0);
      $rowCount = 0;
      while ($rsRow = mysql_fetch_assoc($rs)) {
        $file = $rsRow[$col];
        if (strpos($file,"/")===0 && !realpath($file)) $file = $_SERVER['DOCUMENT_ROOT'] . substr($file,1);
        if (!file_exists($file)) $file = realpath($file);
        if (is_file($file)) $this->Attachments[] = array($file,"");
        $rowCount++;
      }
      mysql_data_seek($rs, 0);
    }
  }
  
  public function addBCC($emailStr) {
    if (!is_array($this->BCC)) $this->BCC = $this->getEmailArray($this->BCC);
    $toArr = $this->getEmailArray($emailStr);
    if ($toArr) $this->BCC = array_merge($this->BCC, $toArr);
  }
  
  public function addBCCFromRS($rs, $col) {
    if (is_string($rs)) $rs = $GLOBALS[$rs];
		if (is_object($rs) && get_class($rs) == "WA_MySQLi_RS") {
      while (!$rs->atEnd()) {
        $toArr = $this->getEmailArray($rs->getColumnVal($col,false));
        $this->BCC = array_merge($this->BCC, $toArr);
        $rs->moveNext();
      }
      $rs->moveFirst();
    } else {
      mysql_data_seek($rs, 0);
      $rowCount = 0;
      while ($rsRow = mysql_fetch_assoc($rs)) {
        $toArr = $this->getEmailArray($rsRow[$col]);
        $this->BCC = array_merge($this->BCC, $toArr);
        $rowCount++;
      }
      mysql_data_seek($rs, 0);
    }
  }
  
  public function addCC($emailStr) {
    if (!is_array($this->CC)) $this->CC = $this->getEmailArray($this->To);
    $toArr = $this->getEmailArray($emailStr);
    if ($toArr) $this->CC = array_merge($this->CC, $toArr);
  }
  
  public function addCCFromRS($rs, $col) {
    if (is_string($rs)) $rs = $GLOBALS[$rs];
		if (is_object($rs) && get_class($rs) == "WA_MySQLi_RS") {
      while (!$rs->atEnd()) {
        $toArr = $this->getEmailArray($rs->getColumnVal($col,false));
        $this->CC = array_merge($this->CC, $toArr);
        $rs->moveNext();
      }
      $rs->moveFirst();
    } else {
      mysql_data_seek($rs, 0);
      $rowCount = 0;
      while ($rsRow = mysql_fetch_assoc($rs)) {
        $toArr = $this->getEmailArray($rsRow[$col]);
        $this->CC = array_merge($this->BCC, $toArr);
        $rowCount++;
      }
      mysql_data_seek($rs, 0);
    }
  }
  
  public function addTo($emailStr) {
    if (strpos($emailStr,"|WA|") !== false) $emailStr = str_replace("|WA|"," <",$emailStr) . ">";
    $toArray = $this->getEmailArray($emailStr);
    if ($toArray) $this->To[] = $toArray;
  }
  
  public function addToFromRS($rs, $col) {
    if (is_string($rs)) $rs = $GLOBALS[$rs];
		if (is_object($rs) && get_class($rs) == "WA_MySQLi_RS") {
      $rs->moveFirst();
      while (!$rs->atEnd()) {
        if ($this->Mailer->validateAddress($rs->getColumnVal($col,false))) $this->To[] = array($rs, $rs->getColumnVal($col,false), $rs->Index);
        $rs->moveNext();
      }
      $rs->moveFirst();
    } else {
      mysql_data_seek($rs, 0);
      $rowCount = 0;
      while ($rsRow = mysql_fetch_assoc($rs)) {
        if ($this->Mailer->validateAddress($rsRow[$col])) $this->To[] = array($rs, $rsRow[$col], $rowCount);
        $rowCount++;
      }
      mysql_data_seek($rs, 0);
    }
  }
  
  public function addToFromFile($fileName) { 
    if (!file_exists($fileName)) $fileName = realpath($fileName);
    if (!file_exists($fileName)) return;
    $handle = fopen($fileName, "r");
    $output = fread($handle, filesize($fileName));
    fclose($handle);
    $allEmails = $this->getEmailArray($output);
    for ($x=0; $x<sizeof($allEmails); $x++) {
      $this->To[][0] = $allEmails[$x];
    }
  }
  
  public function close() {
    if ($this->Method == "smtp") $this->Mailer->SmtpClose();
    $this->redirect();
  }
  
  public function getAllRecipientAddressesAsString() {
    $retStr = "";
    $allRecipients = $this->Mailer->getAllRecipientAddresses(); 
    foreach ($allRecipients as $key=>$val) {
      if ($retStr != "") $retStr .= ", ";
      $retStr .= $key;
    }
    return $retStr;
  }
  
  public function getEmailArray($emailStr) {
    $emailStr = preg_replace("/[\r\n\t;]/",",",$emailStr);
    $emailArr = explode(",",$emailStr);
    for ($x=0; $x<sizeof($emailArr); $x++) {
      if (strpos($emailArr[$x],"|WA|") !== false) {
        $emailArr[$x] = str_replace("|WA|"," <",$emailArr[$x]) . ">";
      }
    }
    $emailStr = implode(",",$emailArr);
    return $this->Mailer->parseAddresses($emailStr);
  }

  public function getFromPage($fileName, $updatePaths=true)  {
    ob_start();
    $origWD = getcwd();
    chdir(dirname($fileName));
    if (file_exists(basename($fileName))) {
			require(basename($fileName));
		} else {
			echo("Email body file not found: " . getcwd() . "/" . basename($fileName));
		}
    $content = ob_get_contents();
    ob_end_clean();
	  if ($updatePaths) $content = $this->updatePaths($content);
    chdir($origWD);
    return $content;
  }
  
  public function groupAllRecipients() {
    $singleSend = array(array());
    for ($y=0; $y<sizeof($this->To); $y++) {
      if (sizeof($this->To[$y]) == 3) {
        $this->To[$y] = $this->getEmailArray($this->To[$y][1]);
      }
      for ($z=0; $z<sizeof($this->To[$y]); $z++) {
        $singleSend[0][] = $this->To[$y][$z];
      }
    }
    $this->To = $singleSend;
  }

  public function prepare() {
    $this->Prepared = true;
	  if ($this->Method == "smtp") {
      $this->Mailer->isSMTP();
      $this->Mailer->SMTPOptions = $this->SMTPOptions;
      $this->Mailer->SMTPDebug = $this->SMTPDebug;
      $this->Mailer->Debugoutput = $this->Debugoutput;  
      $this->Mailer->SMTPAuth = $this->SMTPAuth;
      $this->Mailer->Username = $this->Username;
      $this->Mailer->Password = $this->Password;
	  } else if ($this->Method == "smtp") {
      
    }
    
    if ($this->SMTPSecure) {
		$this->Mailer->SMTPSecure = $this->SMTPSecure;
	}
    $this->Bursts = 0;
    if ($this->BurstSize && $this->BurstTime) {
      $this->Bursts = ceil(sizeof($this->To)/$this->BurstSize);
    }
    $this->ActualTime = $this->WaitTime + 1;
    
	  $this->Mailer->Host = $this->Host; 
	  $this->Mailer->Port = $this->Port;
		$this->Mailer->clearAllRecipients();
		$fromArray = $this->getEmailArray($this->From);
      if (sizeof($fromArray) == 0) {
		    $this->Error = true;
		    $this->ErrorMessage = "Invalid From Address.";
		  return;
	    } 
		$this->Mailer->setFrom($fromArray[0]['address'], $fromArray[0]['name']);
		if ($this->ReturnPath)  {
		  $returnArray = $this->getEmailArray($this->ReturnPath);
		  if ($returnArray) $this->Mailer->ReturnPath = $returnArray[0]['address'];
		  if ($returnArray) $this->Mailer->Sender = $returnArray[0]['address'];
		}
		if ($this->ReplyTo)  {
		  $replyArray = $this->getEmailArray($this->ReplyTo);
		  if ($replyArray) $this->Mailer->addReplyTo($replyArray[0]['address'], $replyArray[0]['name']);
		}
		if ($this->CharSet) {
		  $this->Mailer->CharSet  = $this->CharSet;
		}
		for ($x=0; $x<sizeof($this->Attachments); $x++) {
		  $this->Mailer->addAttachment($this->Attachments[$x][0], $this->Attachments[$x][1]);
		}
    if (!is_array($this->CC)) $this->CC = $this->getEmailArray($this->CC);
		for ($x=0; $x<sizeof($this->CC); $x++) {
		  $this->Mailer->addCC($this->CC[$x]['address'], $this->CC[$x]['name']);
		}
    if (!is_array($this->BCC)) $this->BCC = $this->getEmailArray($this->BCC);
		for ($x=0; $x<sizeof($this->BCC); $x++) {
		  $this->Mailer->addBCC($this->BCC[$x]['address'], $this->BCC[$x]['name']);
		}
		if ($this->Importance && $this->Importance != 3) {
		  $this->Mailer->Priority = $this->Importance;
      if ($this->Importance == 1) {
        $this->Mailer->AddCustomHeader("X-MSMail-Priority: High");
        $this->Mailer->AddCustomHeader("Importance: High");
      }
		}
		if ($this->XMailer) {
		  $this->Mailer->XMailer  = $this->XMailer;
		}
    if ($this->Method == "smtp") $this->Mailer->SMTPKeepAlive = true;
  }
	
	public function redirect() {
		if ($this->Redirect) {
			header("location: " . $this->Redirect);
			die();
		}
	}
  
  public function send($group=0) {
    $this->Mailer->clearAddresses();
    if ($this->SingleSend) {
      $this->groupAllRecipients();
    }
	  if (!isset($this->To[$group])) {
		  $this->Error = true;
		  $this->ErrorMessage = "Invalid To Address.";
	    return;
	  }
    if (sizeof($this->To[$group]) == 3 && !is_array($this->To[$group][0])) {
      $addFromRS = array();
      $addRow = $this->getEmailArray($this->To[$group][1]);
      if ($addRow) {
        $rsObj = $this->To[$group][0];
        if (is_object($rsObj) && get_class($rsObj) == "WA_MySQLi_RS") {
          $rsObj->Index = $this->To[$group][2];
        } else {
          mysql_data_seek($rsObj, $this->To[$group][2]);
          $rsName = false;
          foreach ($GLOBALS as $key=>$val) {
            if ($val == $rsObj) {
              $rsName = $key;
              break;
            }
          }
          if ($rsName) $GLOBALS["row_".$rsName] = mysql_fetch_assoc($rsObj);
        }
        $this->To[$group] = $addRow;
      } else {
        return;
      }
    }
    if (!$this->Prepared) $this->prepare();
    for ($y=0; $y<sizeof($this->To[$group]); $y++) {
      $this->Mailer->addAddress($this->To[$group][$y]['address'], $this->To[$group][$y]['name']);
    }
    $WaitBurst = 0;
    if (!$this->StartTime) {
      $this->StartTime = time();
      $this->StartBurst = time();
      $this->LastTime = time();
    }
    set_time_limit(30);
    if ($this->EmailsSent!=0 && $this->WaitTime!=0) {
      set_time_limit($this->WaitTime + 30);
      $this->sleep($this->WaitTime);
    }
    if ($this->EmailsSent!=0 && $this->BurstSize && $this->BurstTime) {
      if ($this->EmailsSent%$this->BurstSize == 0) {
        $TimePassed = (time() - $this->StartBurst);
        if ($TimePassed < ($this->BurstTime*60))  {
          $WaitBurst = ($this->BurstTime*60) -$TimePassed;
          set_time_limit($WaitBurst + 30);
          $this->sleep($WaitBurst);
        }
        else  {
          $TimeRemaining = ($TotalEmails- $LoopCount)*$RealWait;
        }
        $this->StartBurst = time();
      }
    }
		$this->Mailer->Subject = $this->Subject;
    if ($this->BodyFile) {
      $this->Body = $this->getFromPage($this->BodyFile);
    }
    $this->Mailer->msgHTML($this->Body);
    try {
      $this->Sent = $this->Mailer->send();
    } catch (phpmailerException $e) {
      $this->Error = true;
      $this->ErrorMessage = $e->errorMessage();
    } catch (Exception $e) {
      $this->Error = true;
      $this->ErrorMessage = $e->errorMessage();
    }
    $this->Status = $this->Sent?"success":"failure";
    $this->ActualTime = ((($this->EmailsSent) * $this->ActualTime) +(time() - $this->LastTime - $WaitBurst))/($this->EmailsSent+1);  
    $this->EmailsSent++;
    $this->LastTime = time();
    $this->writeProgress();
    $this->Header = $this->Mailer->createHeader();
    if ($this->Sent) {
      if ($this->onSuccess) {
        $this->onSuccess->Query->bindColumn($this->onSuccess->EmailColumn, "s", $this->getAllRecipientAddressesAsString(), "WA_BLANK");
        if ($this->onSuccess->Query->Action == "update") $this->onSuccess->Query->addFilter($this->onSuccess->EmailColumn, "=", "s", $this->getAllRecipientAddressesAsString());
        $this->onSuccess->updateLogValues($this);
        $this->onSuccess->Query->execute();
      }
    } else {
      if ($this->onFailure) {
        $this->onFailure->Query->bindColumn($this->onFailure->EmailColumn, "s", $this->getAllRecipientAddressesAsString(), "WA_BLANK");
        if ($this->onFailure->Query->Action == "update") $this->onFailure->Query->addFilter($this->onSuccess->EmailColumn, "=", "s", $this->getAllRecipientAddressesAsString());
        $this->onFailure->updateLogValues($this);
        $this->onFailure->Query->execute();
      }
    }
    if ($this->onSend) {
      $this->onSend->Query->bindColumn($this->onSend->EmailColumn, "s", $this->getAllRecipientAddressesAsString(), "WA_BLANK");
      if ($this->onSend->Query->Action == "update") $this->onSend->Query->addFilter($this->onSuccess->EmailColumn, "=", "s", $this->getAllRecipientAddressesAsString());
      $this->onSend->updateLogValues($this);
      $this->onSend->Query->execute();
    } 
    if ($this->onSent) {
      eval($this->onSent);
    }
    if ($this->Interrupt) $this->To = array();
    @session_start();
    $_SESSION[$this->Name."_Status"] = $this->Status;
    $_SESSION[$this->Name."_Error"] = $this->Error;
    $_SESSION[$this->Name."_ErrorMessage"] = $this->ErrorMessage;
    $_SESSION[$this->Name."_Index"] = $this->EmailsSent;
    $_SESSION[$this->Name."_From"] = $this->From;
    $_SESSION[$this->Name."_To"] = $this->getAllRecipientAddressesAsString();
    $_SESSION[$this->Name."_Subject"] = $this->Subject;
    $_SESSION[$this->Name."_Body"] = $this->Body;
    $_SESSION[$this->Name."_Header"] = $this->Header;
    $_SESSION[$this->Name."_Log"] = "sending to: ". $this->getAllRecipientAddressesAsString() . "... " . ($this->Status);
    @session_commit(); 
	}
  
  public function sleep($sleepTime)  {
    if (floatval($sleepTime) != intval($sleepTime)):
       usleep($sleepTime*1000000);
    else:
       sleep($sleepTime);
    endif;  
  }
  
  public function timeEstimate() {
    $timeEstimate = 0;
    $emails = sizeof($this->To);
    if ($this->Bursts > 1) {
      $BurstPer = $this->ActualTime * $this->BurstSize;
      if ($BurstPer < ($this->BurstTime * 60)) {
        $timeEstimate = ($this->BurstTime * 60) * ($this->Bursts-1);
        $emails -= ($this->Bursts-1) * ($this->BurstSize);
      }
    }
    $timeEstimate += ($emails * ($this->ActualTime?$this->ActualTime:1));
    return $timeEstimate;
  }
  
  public function timeRemaining() {
    $timeRemaining = 0;
    $emails = sizeof($this->To);
    
    if ($this->BurstSize && $this->BurstTime) {
      
      $burstsCompleted = floor(($this->EmailsSent-1)/$this->BurstSize);
      $emailsInCurrentBurst = $this->BurstSize - (($this->EmailsSent-1) % $this->BurstSize);

      $currentBurst = time() - $this->StartBurst;
      if (($burstsCompleted+1) < $this->Bursts) {
        $remainingBurst = ($this->BurstTime * 60) - $currentBurst;
        if ($remainingBurst < 0) $remainingBurst = 0;
        if (($emailsInCurrentBurst * $this->ActualTime) > $remainingBurst) $remainingBurst = $emailsInCurrentBurst * $this->ActualTime;
      } else {
        $remainingBurst = ($emails - ($this->EmailsSent-1)) * $this->ActualTime;
      }

      $timeRemaining += $remainingBurst;
      $burstsCompleted++;
      if (($this->Bursts - $burstsCompleted) > 1) {
        $BurstPer = $this->ActualTime * $this->BurstSize;
        if ($BurstPer < ($this->BurstTime * 60)) {
          $timeRemaining += ($this->BurstTime * 60) * ($this->Bursts - $burstsCompleted - 1);
          $emails -= ($this->Bursts-1) * ($this->BurstSize);
        } else {
          $emails -= ($this->EmailsSent-1) + $emailsInCurrentBurst;
        }
      } else {
        $emails -= ($this->EmailsSent-1) + $emailsInCurrentBurst;
      }
      if ($emails<0) $emails = 0;

    } else {
      $emails -= ($this->EmailsSent-1);
    }

    $timeRemaining += ($emails * ($this->ActualTime?$this->ActualTime:1));
    return $timeRemaining;
  }
	
  public function updatePath($url,$path) {
	  $retPath = trim($path);
	  if (strpos($retPath,"/") == 0) {
		  $retPath = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $retPath;
	  } else {
		  while (strpos($retPath,"../") === 0) {
			  $url = dirname($url);
			  $retPath = substr($retPath,3);
		  }
		  $retPath = $url . "/" . $retPath;
	  }
	  return $retPath;
  }
  
  public function UpdatePaths($origStr)  {
		$fullURL = dirname((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
		$fullURL = dirname($this->updatePath($fullURL,$this->BodyFile));
		$props = array();
		$props[] = 'href';
		$props[] = 'action';
		$props[] = 'src';
		//  @import url("css/common.css");
		$subStr = $origStr;
		$matchpat = '/(url|Image|MM_preloadImages|MM_swapImage)(\(\s*|\s*=\s*)([\'"])?([^)\'"\r\n]*)\3(\s*\)|\s*[;\,])/i';
		//echo($origStr);
		preg_match($matchpat,$subStr,$subPats);
		$ignore = '/^(https?%3A%2F%2F|https?:\/\/|mailto:|\[\*|javascript:|callto:|wtai:|sms:|market:|geopoint:|ymsgr:|msnim:|gtalk:|skype:|sip:|whatsapp:|geo:|data:|tel:|#|\/)/i';
			while (sizeof($subPats) > 0)  {
			if ($subPats[1] == "MM_swapImage")  {
				preg_match('/(MM_swapImage)((?:\(\s*|\s*=\s*)[^\r\n,]*,[^\r\n,]*,)([\'"])?([^)\r\n]*)\3(\s*\)|\s*[;\,])/i',$subStr,$subPats);
			}
			if ($subPats[1] == "MM_preloadImages")  {
				preg_match('/MM_preloadImages\(((?:[^\),],?){1,})\)/i',$subStr,$subPats2);
				$pieces = explode(",",$subPats2[1]);
				$subPats[0] = $subPats2[0];
				$subPats[1] = substr($subPats2[0],0,strrpos($subPats2[0],$pieces[sizeof($pieces)-1]));
				$subPats[2] = "";
				$subPats[5] = ")";
				for ($x=0; $x<sizeof($pieces); $x++)  {
				  preg_match('/\s*([\'"])?([^)\r\n]*)\1/',$pieces[$x],$linkAtts);
				  $subPats[3] = $linkAtts[1];
				  $subPats[4] = $linkAtts[2];
				  if (preg_match($ignore,$linkAtts[2])==0 && $linkAtts[2] != "" && $x<sizeof($pieces)-1) {
					  $newPath = $this->updatePath($fullURL,$linkAtts[2]);
					  $subPats[1] = str_replace($pieces[$x],$subPats[3] . $newPath. $subPats[3] ,$subPats[1]);
				  }
				}
			}
			if (preg_match($ignore,$subPats[4])==0 && $subPats[4] != "") {
			  $newPath = $this->updatePath($fullURL,$subPats[4]);
			  $origStr = str_replace($subPats[0],$subPats[1]. $subPats[2] . $subPats[3] . $newPath. $subPats[3] . $subPats[5],$origStr);
			}
			$subStr = str_replace($subPats[0],'',$subStr);
			preg_match($matchpat,$subStr,$subPats);
		}
		$ignoreTags["fb:like"] = array();
		$ignoreTags["fb:like"][] = "action";
		for ($x=0; $x<sizeof($props); $x++)  {
		  $beforeMatch = "";
		  $afterMatch = $origStr;
		  $matchptn = $props[$x];
		  preg_match('/'.$matchptn.'=([\'"])([^"\']*)["\']/i',$origStr,$subPats);
		  while (sizeof($subPats) > 0)  {
			$beforeMatch .= substr($afterMatch,0,strpos($afterMatch,$subPats[0]));
			$afterMatch = substr($afterMatch,strpos($afterMatch,$subPats[0]) + strlen($subPats[0]));
			$skipIt = false;
			$tagMatch = substr($beforeMatch,strpos($beforeMatch,"<")+1);
			$tagMatch = substr($tagMatch,0,strpos($tagMatch,">"));
			if (strpos($tagMatch," ") !== false) $tagMatch = substr($tagMatch,0,strpos($tagMatch," "));
			if (isset($ignoreTags[$tagMatch]) && in_array($matchptn,$ignoreTags[$tagMatch])) {
			  $skipIt = true;
			}
			if (preg_match($ignore,$subPats[2])==0 && !$skipIt) {
				$newPath = $this->updatePath($fullURL,$subPats[2]);
				$beforeMatch .= $matchptn.'=' . $subPats[1] . $newPath. $subPats[1];
			} else {
				$beforeMatch .= $subPats[0];
			}
			preg_match('/'.$matchptn.'=([\'"])([^"\']*)["\']/i',$afterMatch,$subPats);
		  }
		  $origStr = $beforeMatch . $afterMatch;
		}
		return $origStr;
	}
	
  
  public function writeProgress() {
    $UESendPath = getcwd();
    chdir(dirname(__FILE__));
    $myFile = $this->Name . "_Progress_xml.php";
    $fh = @fopen($myFile, 'w');
    if ($fh) {
      fwrite($fh, '<progress total="'.sizeof($this->To).'" current="'.$this->EmailsSent.'" remaining="'.floor($this->timeRemaining()).'" />');
      fclose($fh);
    }
    chdir($UESendPath);
  }
	
  public function clearProgress() {
    $UESendPath = getcwd();
    chdir(dirname(__FILE__));
    $myFile = $this->Name . "_Progress_xml.php";
    unlink($myFile);
    chdir($UESendPath);
  }
  
}

function RemoveValue($theValue, $theExact, $theStart, $theEnd, $theInclude)  {
     if (array_search($theValue,$theExact) !== false)  {
		return true; 
	 }
	 for ($x=0; $x<sizeof($theStart); $x++)  {
		 if (strpos($theValue,$theStart[$x]) === 0)  {
			 return true;
		 }
	 }
	 for ($x=0; $x<sizeof($theEnd); $x++)  {
		 if (strrpos($theValue,$theEnd[$x]) === strlen($theValue)-strlen($theEnd[$x]))  {
			 return true;
		 }
	 }
	 for ($x=0; $x<sizeof($theInclude); $x++)  {
		 if (strrpos($theValue,$theInclude[$x]) !== false)  {
			 return true;
		 }
	 }
	  return false;
}
	
}
?>