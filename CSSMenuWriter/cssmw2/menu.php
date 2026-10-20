<?php
$WA_localRoot = "/";
$WA_remoteRoot = "/";
$WA_curURL = strtolower((isset($_SERVER["PHP_SELF"]))?$_SERVER["PHP_SELF"]:"");
$assumedRoot = $WA_remoteRoot;
if (strpos($WA_curURL,strtolower($WA_localRoot)) === 0 && (strlen($WA_localRoot) >= strlen($WA_remoteRoot) || strpos($WA_curURL,strtolower($WA_localRoot)) === 0)) {
  $assumedRoot = $WA_localRoot;
}
$WA_thisFile = $_SERVER['PHP_SELF'];
if (strpos(strtolower($WA_thisFile), strtolower($assumedRoot)) === 0) {
	$WA_thisFile = substr($WA_thisFile, strlen($assumedRoot));
}
else {
	$WA_thisFile = substr($WA_thisFile, 1);
}
$WA_ddsArray = explode("/", $WA_thisFile);
$WA_dotDotSlash = "";
for ($n=0; $n<sizeof($WA_ddsArray)-1; $n++) {
	$WA_dotDotSlash .= "../";
}
?>
<ul class="level-0" id="cssmw2">
<li><a href="<?php echo($assumedRoot); ?>profile/profile_Index.php">Your Profile</a></li>
<li><a href="<?php echo($assumedRoot); ?>manuscripts/mscr_Index.php">Your manuscripts</a></li>
<li><a href="<?php echo($assumedRoot); ?>manuscripts/mscr_Submit.php">Submit manuscript</a></li>
<li><a href="<?php echo($assumedRoot); ?>users/users_Logout.php">Logout</a></li>
</ul>

<script type="text/javascript">if(window.attachEvent) { window.attachEvent("onload", function() { cssmw2.intializeMenu('cssmw2',{select_current: 1, orientation: 2}); }); } else if(window.addEventListener) { window.addEventListener("load", function() { cssmw2.intializeMenu('cssmw2',{select_current: 1, orientation: 2}); }, true); }</script>