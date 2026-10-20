<?php
$remove = array();
$remove[]  = "";
$remove[]  = "x";
$remove[]  = "y";

$removeBegins = array();
$removeBegins[] = "Security";

$removeEnds = array();
$removeEnds[] = "_x";
$removeEnds[] = "_y";

$removeIncludes = array();
$removeIncludes[] = "Security";
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Blank Template</title>
</head>
<body style="padding: 20px; text-align: center;">
  <div id="background" style="padding: 20px; text-align: center; font-size: 12px; width:97%">
	<div id="page" style="padding: 5px; margin: 0 auto; width: 660px; text-align: left;">
		<div id="header" style="padding: 10px;">
        	<h1 style="padding: 0px; margin: 0px 0px 2px 0px; font-size: 18px; text-decoration: none; font-weight: bold;"><span style="margin: 0px; padding: 0px 0px 3px 0px;"><?php echo((isset($_POST["txtReminder"]))?$_POST["txtReminder"]:"") ?></span> </h1>
		</div>
		<div id="contentWrapper" style="padding: 0px 0px 40px 0px;">        </div>
    </div>
  </div>
</body>
</html>