<?php
function WA_RandomPassword($passwordLen, $upperCase, $lowerCase, $numbers, $specialChars)
{
	$retval = "";
	$rpgCharSet = "";
	
	$rpgCharSet .= ($upperCase)?'ABCDEFGHIJKLMNOPQRSTUVWXYZ':'';	
	$rpgCharSet .= ($lowerCase)?'abcdefghijklmnopqrstuvwxyz':'';	
	$rpgCharSet .= ($numbers)?'0123456789':'';
	$rpgCharSet .= $specialChars;
    
	for ($i=0; $i<$passwordLen; $i++)
		$retval .= substr($rpgCharSet, mt_rand(0, strlen($rpgCharSet)-1), 1);

	return $retval;
}
?>