<?php
if($_POST['action'] == "add"){
	if($_POST['sender'] == 'promapsforgoogle'){
		if($_POST['entryId'] != ''){
			$addressID = $_POST['entryId'];
			$address = trim($_POST['address']);
			$geocode = trim($_POST['geocode']);
			writeXML($addressID, $address, $geocode);
		}
	}
}
echo '';
exit();
?>
<?php
//add the address to the xml cache if it is not there already
function writeXML($addressID, $address, $geocode){
	if (!$geocode) return;
	$xmlFile = "../_promaps_cache/_promaps_geocache.xml";
	if(file_exists($xmlFile)){
		$geoCache = file_get_contents($xmlFile);
	}
	else
	{
		$geoCache = '<?xml version="1.0" encoding="UTF-8"?>
<geocode_data version="1.0"></geocode_data>';
	}
	$addEntry = '<geocode_entry ID="'.$addressID.'">';
	$addEntry .= "<address>";
	$addEntry .= $address;
	$addEntry .= "</address>";
	$addEntry .= "<geocode>";
	$addEntry .= $geocode;
	$addEntry .= "</geocode>";
	$addEntry .= "</geocode_entry>\n";
	
	$geoCache = str_replace("</geocode_data>",$addEntry."</geocode_data>",$geoCache);
	
	file_put_contents($xmlFile, $geoCache);
}
?>