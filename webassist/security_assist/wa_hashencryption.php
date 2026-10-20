<?php
$hash_Include_Start_Dir = getcwd();
chdir(dirname(__FILE__));
require_once("wa_securitykey.php");
chdir($hash_Include_Start_Dir);
?>
<?php
class WA_Hash_Encryption {
  function WA_Hash_Encryption($iKey)  {
    $this->key = $iKey;
  }
  function encrypt($string) {
    $result = '';
    for($i=0; $i<strlen($string); $i++) {
      $char = substr($string, $i, 1);
      $keychar = substr($this->key, ($i % strlen($this->key))-1, 1);
      $char = chr(ord($char)+ord($keychar));
      $result.=$char;
    }
    return base64_encode($result);
  }
  function decrypt($string) {
    $result = '';
    $string = base64_decode($string);
    for($i=0; $i<strlen($string); $i++) {
      $char = substr($string, $i, 1);
      $keychar = substr($this->key, ($i % strlen($this->key))-1, 1);
      $char = chr(ord($char)-ord($keychar));
      $result.=$char;
    }
    return $result;
  }
}
$WA_HashEncryption_Obj = new WA_Hash_Encryption($WA_SECURITY_KEY);
function WA_HashEncryption($msg) {
	global $WA_HashEncryption_Obj;
	return $WA_HashEncryption_Obj->encrypt($msg);
}
function WA_HashDecryption($msg) {
	global $WA_HashEncryption_Obj;
	return $WA_HashEncryption_Obj->decrypt($msg);
}
?>