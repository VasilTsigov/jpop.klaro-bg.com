<?php require_once( "HelperGroupsRulesPHP.php" ); ?>
<?php require_once( "Mail_PHP.php" ); ?>
<?php
if (!isset($_SESSION)) {
	session_start();
}

$WA_Auth_Separator = "|§|";

function WA_AuthenticateUser($WA_Auth_Parameter){

	$UserAuthenticated = false;
	
	mysql_select_db($WA_Auth_Parameter["database"], $WA_Auth_Parameter["connection"]);
	$WA_Auth_loginSQL = "SELECT `".implode('`,`', $WA_Auth_Parameter["sessionColumns"])."` FROM `".$WA_Auth_Parameter["tableName"]."` WHERE ";

	for($idx=0;$idx<count($WA_Auth_Parameter["columns"]);$idx++){
		$WA_Auth_loginSQL = sprintf($WA_Auth_loginSQL.(($idx!=0)?" AND ":" ")."`%s`=%s ", $WA_Auth_Parameter["columns"][$idx], WA_GetSQLValueString($WA_Auth_Parameter["columnValues"][$idx], $WA_Auth_Parameter["columnTypes"][$idx]));
	}
	$WA_Auth_RS = mysql_query($WA_Auth_loginSQL, $WA_Auth_Parameter["connection"]) or die(mysql_error());
	$WA_Auth_Rows = mysql_num_rows($WA_Auth_RS);
	
	if($WA_Auth_Rows){
		$UserAuthenticated = true;

		$idx = 0;
		foreach ($WA_Auth_Parameter["sessionNames"] as $sessionName){
			$_SESSION[$sessionName] = mysql_result($WA_Auth_RS,0,$WA_Auth_Parameter["sessionColumns"][$idx]);
			$idx++;
		}

		if (isset($_GET['accesscheck'])) {
			$WA_Auth_Parameter["successRedirect"] = urldecode($_GET['accesscheck']);
		}

		if($WA_Auth_Parameter["successRedirect"]!=""){
			$WA_Auth_Parameter["successRedirect"] = WA_Auth_BuildRedirectURL($WA_Auth_Parameter["successRedirect"], $WA_Auth_Parameter["keepQueryString"], FALSE);
			header("Location: ".$WA_Auth_Parameter["successRedirect"]);
			exit();
		}
	}
		
	if($WA_Auth_Parameter["failRedirect"]!=""){
		$WA_Auth_Parameter["failRedirect"] = WA_Auth_BuildRedirectURL($WA_Auth_Parameter["failRedirect"], $WA_Auth_Parameter["keepQueryString"], FALSE);
		header("Location: ".$WA_Auth_Parameter["failRedirect"]);
		exit();
	}
}


function WA_Auth_ClearSession($clearAll, $clearThese){

	if($clearAll){
		foreach ($_SESSION as $key => $value){
			unset($_SESSION[$key]);
		}
	}
	else{
		foreach($clearThese as $value){
			unset($_SESSION[$value]);
		}
	}
}

function WA_Auth_RestrictAccess($redirectURL){
	$redirectURL = WA_Auth_BuildRedirectURL($redirectURL, FALSE, TRUE);
	header("Location: ".$redirectURL);
	exit();
}

function WA_Auth_ForgotPassword($WA_Auth_Parameter){
	$selectColumns = array();
	for($idx=0;$idx<count($WA_Auth_Parameter["selectColumns"]);$idx++){
		if($WA_Auth_Parameter["selectColumns"][$idx]!=''){
			$selectColumns[] = $WA_Auth_Parameter["selectColumns"][$idx];
		}
	}
	$selectColumns[] = $WA_Auth_Parameter["usernameColumn"];
	$selectColumns[] = $WA_Auth_Parameter["passwordColumn"];
	$selectColumns[] = $WA_Auth_Parameter["toAddressColumn"];


	mysql_select_db($WA_Auth_Parameter["database"], $WA_Auth_Parameter["connection"]);
	$WA_Auth_ForgotSQL = "SELECT `".implode('`,`', $selectColumns)."` FROM `".$WA_Auth_Parameter["tableName"]."` WHERE `".$WA_Auth_Parameter["filterColumn"]."` =";
	$WA_Auth_ForgotSQL = sprintf($WA_Auth_ForgotSQL." %s ", WA_GetSQLValueString($WA_Auth_Parameter["columnValue"], $WA_Auth_Parameter["columnType"]));

	$WA_Auth_RS = mysql_query($WA_Auth_ForgotSQL, $WA_Auth_Parameter["connection"]) or die(mysql_error());
	$WA_Auth_Rows = mysql_num_rows($WA_Auth_RS);
	
	if($WA_Auth_Rows){
		$row_WA_Auth_RS = mysql_fetch_assoc($WA_Auth_RS);
		$WA_Auth_Parameter["mailBody"] = preg_replace("/\\n/", "\r\n", $WA_Auth_Parameter["mailBody"]);

		for($idx=0;$idx<count($selectColumns);$idx++){
			$WA_Auth_Parameter["mailBody"] = preg_replace("/\[".$selectColumns[$idx]."\]/", $row_WA_Auth_RS[$selectColumns[$idx]], $WA_Auth_Parameter["mailBody"]);
		}
		for($idx=0;$idx<count($WA_Auth_Parameter["sessionVariables"]);$idx++){
			$WA_Auth_Parameter["mailBody"] = preg_replace("/\[Session\.".$WA_Auth_Parameter["sessionVariables"][$idx]."\]/", isset($_SESSION[$WA_Auth_Parameter["sessionVariables"][$idx]])?$_SESSION[$WA_Auth_Parameter["sessionVariables"][$idx]]:"", $WA_Auth_Parameter["mailBody"]);
		}
		if($WA_Auth_Parameter["fromAddressDisplay"]!=''){
			$WA_Auth_Parameter["fromAddress"] = $WA_Auth_Parameter["fromAddress"].'|WA|'.$WA_Auth_Parameter["fromAddressDisplay"];
		}
		$WA_Auth_Parameter["toAddress"] = $row_WA_Auth_RS[$WA_Auth_Parameter["toAddressColumn"]];
		call_user_func($WA_Auth_Parameter["emailFunction"], $WA_Auth_Parameter);
		if($WA_Auth_Parameter["successRedirect"]!=""){
			$WA_Auth_Parameter["successRedirect"] = WA_Auth_BuildRedirectURL($WA_Auth_Parameter["successRedirect"], $WA_Auth_Parameter["keepQueryString"], FALSE);
			header("Location: ".$WA_Auth_Parameter["successRedirect"]);
			exit();
		}

	}
	else{
		if($WA_Auth_Parameter["failRedirect"]!=""){
			$WA_Auth_Parameter["failRedirect"] = WA_Auth_BuildRedirectURL($WA_Auth_Parameter["failRedirect"], $WA_Auth_Parameter["keepQueryString"], FALSE);
			header("Location: ".$WA_Auth_Parameter["failRedirect"]);
			exit();
		}
	}
}


function WA_Auth_BuildRedirectURL($redirectURL, $keepCurrentQueryString, $addDeniedURL){

	if ($keepCurrentQueryString && $redirectURL != "" && isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] !== "") {
		$redirectURL .= ((strpos($redirectURL, '?') === false)?"?":"&").$_SERVER["QUERY_STRING"];
	}
	
	if($addDeniedURL){
		$WA_Auth_Referrer = $_SERVER['PHP_SELF'];
		if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0){
			$WA_Auth_Referrer .= "?".$QUERY_STRING;
		}
		$redirectURL = $redirectURL.((strpos($redirectURL, "?"))?"&":"?")."accesscheck=".urlencode($WA_Auth_Referrer.((isset($_SERVER["QUERY_STRING"]))?"?".$_SERVER["QUERY_STRING"]:""));
	}
    if(strpos($redirectURL, '/') === 0){
		$redirectURL = 'http'.((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]!="off")?"s":"").'://'.$_SERVER['HTTP_HOST'].$redirectURL;
	}
	
	return $redirectURL;
}

// Rules functions

function WA_Auth_RulePasses($ruleName){
	return WA_Auth_RuleObject_EvaluateRules($ruleName);
}

function WA_Auth_RuleObject_EvaluateRules($ruleName){
	$rulePasses = FALSE;
	$comparisons = WA_Auth_GetComparisonsForRule($ruleName);
	$compareLen = count($comparisons);
//echo($compareLen);
	for($idx=0;$idx<$compareLen;$idx++){
		$comparison = $comparisons[$idx];
		$compareSucceeds = !$comparison[0];
		switch($comparison[2]) {
/*
			1-9		Direct value comparisons
			10-19		String Comparisons
			20-29		List Comparisons
*/
			case 1:
				$compareSucceeds = ($comparison[1]==$comparison[3]);
				break;
				
			case 2:
				$compareSucceeds = ($comparison[1]!=$comparison[3]);
				break;
				
			case 3:
				$compareSucceeds = ($comparison[1]<$comparison[3]);
				break;
				
			case 4:
				$compareSucceeds = ($comparison[1]<=$comparison[3]);
				break;
				
			case 5:
				$compareSucceeds = ($comparison[1]>$comparison[3]);
				break;
				
			case 6:
				$compareSucceeds = ($comparison[1]>=$comparison[3]);
				break;
				
			case 20:
				$compareSucceeds = WA_Auth_GroupContainsValue($comparison[3], $comparison[1]);
				break;

		}

		if(!$comparison[0] && $compareSucceeds){
			$rulePasses = FALSE;
			break;
		}
		else if ($comparison[0] && $compareSucceeds){
			$rulePasses = TRUE;
			break;
		}
		else if(!$comparison[0] && !$compareSucceeds){
			$rulePasses = TRUE;
		}
		else if($comparison[0] && !$compareSucceeds){
			$rulePasses = FALSE;
		}
	}

	return $rulePasses;

}


// Groups functions

function WA_Auth_GroupContainsValue($groupName, $value){
	$group = WA_Auth_GetGroup($groupName);
	return in_array($value, $group);
}


// Debug functions
function WA_Auth_SessionDebug(){
	if (!isset($_SESSION)) {
		session_start();
	}
	$str = "Session variables: <br />";
	foreach ($_SESSION as $key => $value){
		$str.=$key." = ".$value."<br />";
	}
	echo($str);
}

function WA_Auth_RuleObject_DebugAllComparisons($comparisons){
	for($idx =0;$idx<count($comparisons);$idx++){
		WA_Auth_RuleObject_DebugComparison($comparisons[$idx]);
	}
}

function WA_Auth_RuleObject_DebugComparison($comparison){
	echo(($comparison[0]?"TRUE":"FALSE")."<br />".$comparison[1]."<br />".$comparison[2]."<br />".$comparison[3]."<br />" );
}

?>
<?php
if (!function_exists("WA_GetSQLValueString")) {
function WA_GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
?>