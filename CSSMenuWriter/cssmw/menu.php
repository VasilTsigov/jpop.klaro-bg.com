<?php
$WA_localRoot = "/ivan/";
$WA_remoteRoot = "/ivan/";
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
<ul class="level-0" id="cssmw">
<li class="parent"><a href="#">Item 1</a>
<ul class="level-1">
<li><a href="#">Sub Item 1</a></li>
<li><a href="#">Sub Item 2</a></li>
<li><a href="#">Sub Item 3</a></li>
</ul>
</li>
<li><a href="#">Item 2</a></li>
<li><a href="#">Item 3</a></li>
</ul>

<script type="text/javascript">if(window.attachEvent) { window.attachEvent("onload", function() { cssmw.intializeMenu('cssmw',{select_current: 0, orientation: 2}); }); } else if(window.addEventListener) { window.addEventListener("load", function() { cssmw.intializeMenu('cssmw',{select_current: 0, orientation: 2}); }, true); }</script>