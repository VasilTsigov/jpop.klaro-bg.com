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
	$xmlFile = "../_promaps_cache/_promaps_geocache.xml";
	if(file_exists($xmlFile)){
		$dom = new DOMDocument();
		$dom->load($xmlFile);
		$root = $dom->getElementsByTagName('geocode_data')->item(0);
	}
	else
	{
		$dom = new DOMDocument('1.0', 'UTF-8');
		$root = $dom->createElement('geocode_data');
		$root = $dom->appendChild($root);
		$rootAttr = $dom->createAttribute('version');
		$rootAttr = $root->appendChild($rootAttr);
		$rootValue = $dom->createTextNode('1.0');
		$rootValue = $rootAttr->appendChild($rootValue); 
	}
	//create a new geocode entry, and set the id attribute
	$geocodeEntry = $dom->createElement("geocode_entry");
	$geocodeEntry = $root->appendChild($geocodeEntry);
	$geocodeEntry->setAttribute('ID',$addressID);
	//create the address entry
	$addressNode = $dom->createElement("address");
	$addressNode = $geocodeEntry->appendChild($addressNode);
	$addressData = $dom->createCDATASection($address);
	$addressData = $addressNode->appendChild($addressData);
	//create the geocode entry
	$geocodeNode = $dom->createElement('geocode');
	$geocodeNode = $geocodeEntry->appendChild($geocodeNode);
	$geocodeData = $dom->createCDATASection($geocode);
	$geocodeNode = $geocodeNode->appendChild($geocodeData);
	echo $dom->save($xmlFile);
}
?>