<?php
$WA_menuDir = getcwd();
chdir(dirname(__FILE__));
require_once("../../webassist/framework/library.php");
require_once("../../webassist/framework/framework.php");
chdir($WA_menuDir);
$WA_Menu = new WA_Include(__FILE__);
?>
<ul class="level-0" id="cssmw0">
<li><a href="../../profile/profile_Index.php">Your Profile</a></li>
<li><a href="../../manuscripts/mscr_Index.php">Your manuscripts</a></li>
<li><a href="../../manuscripts/mscr_Submit.php">Submit manuscript</a></li>
<li><a href="../../manuscripts/mscr_Reviewer.php">Manuscripts for review</a></li>
<li><a href="../../manuscripts/mscr_All.php">All manuscripts</a></li>
<li><a href="../../manuscripts/mscr_Published.php">Published Manuscript</a></li>
<li><a href="../../manuscripts/mscr_Rejected.php">Rejected Manuscript</a></li>
<li><a href="../../search/main.php">Search</a></li>
<li><a href="../../sendemails/index.php">Send Emails</a></li>
<li><a href="../../users/users_Index.php">Users</a></li>
<li><a href="../../Logos/logos_Index.php">Logos</a></li>
<li><a href="../../users/users_Logout.php">Logout</a></li>
</ul>

<script type="text/javascript">if(window.attachEvent) { window.attachEvent("onload", function() { cssmw0.intializeMenu('cssmw0',{select_current: 1, orientation: 2}); }); } else if(window.addEventListener) { window.addEventListener("load", function() { cssmw0.intializeMenu('cssmw0',{select_current: 1, orientation: 2}); }, true); }</script>
<?php
$WA_Menu->Initialize(true);
echo($WA_Menu->Body);
?>