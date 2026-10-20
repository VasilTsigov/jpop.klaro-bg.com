<?php
$WA_menuDir = getcwd();
chdir(dirname(__FILE__));
require_once("../../webassist/framework/library.php");
require_once("../../webassist/framework/framework.php");
chdir($WA_menuDir);
$WA_Menu = new WA_Include(__FILE__);
?>
<ul class="level-0" id="cssmw3">
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

<script type="text/javascript">if(window.attachEvent) { window.attachEvent("onload", function() { cssmw3.intializeMenu('cssmw3',{select_current: 0, orientation: 1}); }); } else if(window.addEventListener) { window.addEventListener("load", function() { cssmw3.intializeMenu('cssmw3',{select_current: 0, orientation: 1}); }, true); }</script>
<?php
$WA_Menu->Initialize(true);
echo($WA_Menu->Body);
?>