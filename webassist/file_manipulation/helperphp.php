<?php

$WA_DFP_UploadStatus = array();
$WA_ImageContentTypes = array("image/gif" => true , "image/jpeg" => true, "image/pjpeg" => true, "image/x-png" => true, "image/png" => true);

define('WADFP_FIT_TO_BOX', '1');
define('WADFP_FIT_TO_WIDTH', '2');
define('WADFP_FIT_TO_HEIGHT', '3');
define('WADFP_FIXED_SIZE', '4');

define('WADFP_PNG', 'PNG');
define('WADFP_JPEG', 'JPG');
define('WADFP_GIF', 'GIF');

function dumpUploadStatus($statusName){
	global $WA_DFP_UploadStatus;
	
	foreach($WA_DFP_UploadStatus[$statusName] as $key=>$val){
		echo("$key : $val<br />");
	}
}

function makeFolder($thePath)  {
  $thePath = str_replace("\\","/",$thePath);
  $theParentPath = substr($thePath,0,strrpos($thePath,"/"));
  if ($theParentPath!="" && !file_exists($theParentPath))  {
    makeFolder($theParentPath);
  }
  if (!file_exists($thePath) && $thePath!="")  {
      mkdir($thePath);
  }
}

/*
 Upload status codes and error messages:
 -1	:	Trigger did not run				: ""
  0	:	File not uploaded					: "No file uploaded"
  1 	:	All is well, upload processed	: ""
 10 	:	failed image file type check	: "File not an image"
 11	:	failed image width limit			: "Image width of imageWidth exceeded limit of maxImageWidth"
 12		:	failed image height limit		: "Image height of imageHeight  exceeded limit of maxImageHeight"
 20		:	failed file size limit			: "File size of fileSize exceeded limit of fileSizeLimitKB"
 30	:	upload skipped						: "File upload skipped"
*/

function WA_DFP_UploadFiles(){ // V2 : Not defining variables to allow for chaning them down the road
global $WA_DFP_UploadStatus;
global $WA_ImageContentTypes;

	$statusName = func_get_arg(0);
	$fileField = func_get_arg(1);
	$fileExistsAction = func_get_arg(2);
	$renameFileTo = func_get_arg(3);	
	$imageRestrictions = func_get_arg(4);
		
	$newFileNameDefault = "[FileName]";
	$newFileNameDefault2 = "[NewFileName]";
	$existingFileNameDefault = "[ExistingFileName]";
	$renameIncrementToken = "[Increment]";
	$separator = WA_DFP_GetFileSeparator();
	
	$pageNameParts = pathinfo($_SERVER['PHP_SELF']);
	$pageName = preg_replace('/\.[^.]*$/', '', $pageNameParts['basename']);
	if (is_numeric(substr($pageName, 0, 1))) {
		$pageName = "file_".$pageName;
	}
	$sessionName = $pageName."_".$fileField;
    
	@session_start();
	
	$fileArray = array();
	if (is_array($_FILES[$fileField]['tmp_name'])) {
	  for ($x=0; $x<sizeof($_FILES[$fileField]['tmp_name']); $x++) {
		if (is_uploaded_file($_FILES[$fileField]['tmp_name'][$x])) {
			$arrFileName = $_FILES[$fileField]['tmp_name'][$x];
			$arrName = $_FILES[$fileField]['name'][$x];
			$arrType = $_FILES[$fileField]['type'][$x];
			$arrSize = $_FILES[$fileField]['size'][$x];
			$fileArray[] = array('tmp_name'=>$fileName ,'name'=>$arrName,'type'=>$arrType,'size'=>$arrSize);
		}
	  }
	} else {
	  if(isset($_FILES[$fileField]) && is_uploaded_file($_FILES[$fileField]['tmp_name'])) $fileArray[] = $_FILES[$fileField];
	}
	
	for ($x=0; $x<sizeof($fileArray); $x++) {
		$uploadedFile = $fileArray[$x];		
		$path_parts = pathinfo($uploadedFile["name"]);
		$extension = isset($path_parts['extension']) ? $path_parts['extension'] : '';
		$fileParams = func_get_arg(5);
		$firstFile = "";
		$counter = 0;
		foreach($fileParams as $key=>$val){
			$folderPath = rootRelativeToFullFileURL($val['UploadFolder']);
			$keyname = $key . (($x>0)?"_".$x:"");
			makeFolder($folderPath);
			$baseFileName = preg_replace('/\.[^.]*$/', '', $uploadedFile["name"]);
			$attemptedFileName = str_replace($newFileNameDefault, $baseFileName, $val['FileName']);
			$attemptedFileName = str_replace($newFileNameDefault2, $baseFileName, $attemptedFileName);
			$attemptedFileName = str_replace($existingFileNameDefault, $baseFileName, $attemptedFileName);
			$tmpFileBaseName = md5(uniqid(rand(), true));
			
			if(strpos($folderPath, $separator) !== (strlen($folderPath)-1)){ // add a trailing slash
				$folderPath.=$separator;
			}
			$tmpFileNamePath = $folderPath.$tmpFileBaseName.'.'.$extension;
			$tmpFirstFilePath = $folderPath.$tmpFileBaseName.'_tmp.'.$extension;
			if ($firstFile == "")  {
			  $firstFile = $tmpFirstFilePath;
			  $theCopy = move_uploaded_file($uploadedFile["tmp_name"], $tmpFirstFilePath);
			}
			$theCopy = copy($firstFile , $tmpFileNamePath);
			if ($theCopy) {
				
				if($imageRestrictions !== "false"){
					if($imageRestrictions !== "true"){
						$val['imageFormat'] = $imageRestrictions;
					}
					else{
						$val['imageFormat'] = '';
					}
					if(isset($WA_ImageContentTypes[$uploadedFile["type"]])) {
						$WA_DFP_UploadStatus[$statusName]["isImage"] = TRUE;
						if($val['ResizeType'] != ''){
							// create a copy of the image with the requested dimension changes and any format changes
							$resizeResult = WA_DFP_ResizeImage($tmpFileNamePath, $val);
							$resizePathParts = pathinfo( $resizeResult['endServerPath']);
							$endServerPath = $resizeResult['endServerPath'];
							
							$WA_DFP_UploadStatus[$statusName][$keyname]['clientFileName'] =  $uploadedFile["name"];
							$WA_DFP_UploadStatus[$statusName][$keyname]['fileExtension'] = $resizeResult['endExtension'];
							$WA_DFP_UploadStatus[$statusName][$keyname]['serverDirectory'] = $resizePathParts['dirname'].$separator;
							$WA_DFP_UploadStatus[$statusName][$keyname]['contentType'] = $resizeResult['contentType'];
							$WA_DFP_UploadStatus[$statusName][$keyname]['fileSize'] = $resizeResult['fileSize'];								
							$WA_DFP_UploadStatus[$statusName][$keyname]['imageWidth'] = $resizeResult['endWidth'];
							$WA_DFP_UploadStatus[$statusName][$keyname]['imageHeight'] = $resizeResult['endHeight'];
							$WA_DFP_UploadStatus[$statusName][$keyname]["isImage"] = TRUE;
							
							$renameFileName = str_replace($newFileNameDefault, $baseFileName, $renameFileTo);
							$renameFileName = str_replace($newFileNameDefault2, $attemptedFileName, $renameFileName);
							$renameFileName = str_replace($existingFileNameDefault, $baseFileName, $renameFileName);
							if(strpos($renameFileName, $renameIncrementToken) !== FALSE ){
								$renameFileName = WA_DFP_GenerateUniqueFileName($folderPath, $renameFileName, $resizeResult['endExtension'], $renameIncrementToken);
							}
							
							if($extension != "" && strpos($renameFileName, ".")=== FALSE){
								$renameFileName = $renameFileName.'.'.$resizeResult['endExtension'];
							}
							if($resizeResult['endExtension'] != ""){
								$attemptedFileName.='.'.$resizeResult['endExtension'];
							}
							$renameServerFilePath = $folderPath.$renameFileName;
							$attemptedServerFilePath = str_replace($tmpFileBaseName.'.'.$extension, $attemptedFileName, $tmpFileNamePath);
							$fileExists = file_exists($attemptedServerFilePath);
							
							if($fileExists){
								switch($fileExistsAction){
									case "0": // "Overwrite"
										unlink($attemptedServerFilePath);
										rename($endServerPath , $attemptedServerFilePath);
										$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasOverwritten"] = TRUE;
										$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasSkipped"] = FALSE;
										$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasRenamed"] = FALSE;
										$WA_DFP_UploadStatus[$statusName][$keyname]["serverFileName"] = preg_replace('/^.*(\/|\\|:)/', '', $attemptedFileName);
										$WA_DFP_UploadStatus[$statusName][$keyname]["serverSimpleFileName"] = preg_replace('/\\.[^.]*$/', '', $attemptedFileName);
										$WA_DFP_UploadStatus[$statusName][$keyname]["statusCode"] = 1;
										$_SESSION[$sessionName] = $attemptedFileName;
										break; 
									case "1": // "Skip"
										unlink($endServerPath);  // also remove the resized image
										$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasOverwritten"] = FALSE;
										$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasSkipped"] = TRUE;
										$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasRenamed"] = FALSE;
										$WA_DFP_UploadStatus[$statusName][$keyname]["serverFileName"] = preg_replace('/^.*(\/|\\|:)/', '', $attemptedFileName);
										$WA_DFP_UploadStatus[$statusName][$keyname]["serverSimpleFileName"] = preg_replace('/\\.[^.]*$/', '', $attemptedFileName);
										$WA_DFP_UploadStatus[$statusName][$keyname]["statusCode"] = 30;
										$WA_DFP_UploadStatus[$statusName][$keyname]["errorMessage"] = "File upload skipped";
										$_SESSION[$sessionName] = "";
	
										break; 
									case "2": // "Rename new"
										rename($endServerPath , $renameServerFilePath);
										$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasOverwritten"] = FALSE;
										$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasSkipped"] = FALSE;
										$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasRenamed"] = TRUE;
										$WA_DFP_UploadStatus[$statusName][$keyname]["serverFileName"] = preg_replace('/^.*(\/|\\|:)/', '', $renameFileName);
										$WA_DFP_UploadStatus[$statusName][$keyname]["serverSimpleFileName"] = preg_replace('/\\.[^.]*$/', '', $renameFileName);
										$WA_DFP_UploadStatus[$statusName][$keyname]["statusCode"] = 1;
										$_SESSION[$sessionName] = $renameFileName;
										break;
										 
									case "3": // "Rename old"
										rename($attemptedServerFilePath, $renameServerFilePath);
										rename($endServerPath, $attemptedServerFilePath);
										$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasOverwritten"] = FALSE;
										$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasSkipped"] = FALSE;
										$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasRenamed"] = FALSE;
										$WA_DFP_UploadStatus[$statusName][$keyname]["serverFileName"] = preg_replace('/^.*(\/|\\|:)/', '', $attemptedFileName);
										$WA_DFP_UploadStatus[$statusName][$keyname]["serverSimpleFileName"] = preg_replace('/\\.[^.]*$/', '', $attemptedFileName);
										$WA_DFP_UploadStatus[$statusName][$keyname]["statusCode"] = 1;
										$_SESSION[$sessionName] = $attemptedFileName;
										break;
								}
							}
							else{
								rename($endServerPath , $attemptedServerFilePath);
								$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasOverwritten"] = FALSE;
								$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasSkipped"] = FALSE;
								$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasRenamed"] = FALSE;
								$WA_DFP_UploadStatus[$statusName][$keyname]["serverFileName"] = preg_replace('/^.*(\/|\\|:)/', '', $attemptedFileName);
								$WA_DFP_UploadStatus[$statusName][$keyname]["serverSimpleFileName"] = preg_replace('/\\.[^.]*$/', '', $attemptedFileName);
								$WA_DFP_UploadStatus[$statusName][$keyname]["statusCode"] = 1;
								$_SESSION[$sessionName] = $attemptedFileName;
							}
						}
					}
					else{ // Requires an image, but the type didn't match what is accepted.
						$WA_DFP_UploadStatus[$statusName][$keyname]["serverDirectory"] = realpath($folderPath).$separator;
						$WA_DFP_UploadStatus[$statusName][$keyname]["statusCode"]	= 10;
						$WA_DFP_UploadStatus[$statusName][$keyname]["errorMessage"] = "File not an image";
						$WA_DFP_UploadStatus[$statusName][$keyname]["clientFileName"] = $uploadedFile["name"];
						$WA_DFP_UploadStatus[$statusName][$keyname]["fileExtension"] =  $extension;
						$WA_DFP_UploadStatus[$statusName][$keyname]["contentType"] = $uploadedFile["type"];
						$WA_DFP_UploadStatus[$statusName][$keyname]["fileSize"] = $uploadedFile["size"];
					}
					if(file_exists($tmpFileNamePath)) {
					  unlink($tmpFileNamePath);
					}
				}
				else{ // Non-image related uploads
					$WA_DFP_UploadStatus[$statusName][$keyname]["serverDirectory"] = realpath($folderPath).$separator;
					$WA_DFP_UploadStatus[$statusName][$keyname]["clientFileName"] = $uploadedFile["name"];
					$WA_DFP_UploadStatus[$statusName][$keyname]["fileExtension"] =  $extension;
					$WA_DFP_UploadStatus[$statusName][$keyname]["contentType"] = $uploadedFile["type"];
					$WA_DFP_UploadStatus[$statusName][$keyname]["fileSize"] = $uploadedFile["size"];
					
					$renameFileName = str_replace($newFileNameDefault, $baseFileName, $renameFileTo);
					$renameFileName = str_replace($newFileNameDefault2, $attemptedFileName, $renameFileName);
					$renameFileName = str_replace($existingFileNameDefault, $baseFileName, $renameFileName);
					if(strpos($renameFileName, $renameIncrementToken) !== FALSE ){
						$renameFileName = WA_DFP_GenerateUniqueFileName($folderPath, $renameFileName, $extension, $renameIncrementToken);
					}
					
					if($extension != "" && strpos($renameFileName, ".")=== FALSE){
						$renameFileName = $renameFileName.".".$extension;
					}
					if($extension != ""){
						$attemptedFileName.='.'.$extension;
					}
					
					$renameServerFilePath = $folderPath.$renameFileName;
					$attemptedServerFilePath = str_replace($tmpFileBaseName.'.'.$extension, $attemptedFileName, $tmpFileNamePath);
					$fileExists = file_exists($attemptedServerFilePath);
					
					if($fileExists){
						switch($fileExistsAction){
							case "0": // "Overwrite"
								unlink($attemptedServerFilePath);
								rename($tmpFileNamePath , $attemptedServerFilePath);
								$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasOverwritten"] = TRUE;
								$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasSkipped"] = FALSE;
								$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasRenamed"] = FALSE;
								$WA_DFP_UploadStatus[$statusName][$keyname]["serverFileName"] = preg_replace('/^.*(\/|\\|:)/', '', $attemptedFileName);
								$WA_DFP_UploadStatus[$statusName][$keyname]["serverSimpleFileName"] = preg_replace('/\\.[^.]*$/', '', $attemptedFileName);
								$WA_DFP_UploadStatus[$statusName][$keyname]["statusCode"] = 1;
								$_SESSION[$sessionName] = $attemptedFileName;
								break; 
							case "1": // "Skip"
								unlink($tmpFileNamePath);
								$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasOverwritten"] = FALSE;
								$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasSkipped"] = TRUE;
								$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasRenamed"] = FALSE;
								$WA_DFP_UploadStatus[$statusName][$keyname]["serverFileName"] = preg_replace('/^.*(\/|\\|:)/', '', $attemptedFileName);
								$WA_DFP_UploadStatus[$statusName][$keyname]["serverSimpleFileName"] = preg_replace('/\\.[^.]*$/', '', $attemptedFileName);
								$WA_DFP_UploadStatus[$statusName][$keyname]["statusCode"] = 30;
								$WA_DFP_UploadStatus[$statusName][$keyname]["errorMessage"] = "File upload skipped";
								$_SESSION[$sessionName] = "";

								break; 
							case "2": // "Rename new"
								rename($tmpFileNamePath , $renameServerFilePath);
								$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasOverwritten"] = FALSE;
								$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasSkipped"] = FALSE;
								$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasRenamed"] = TRUE;
								$WA_DFP_UploadStatus[$statusName][$keyname]["serverFileName"] = preg_replace('/^.*(\/|\\|:)/', '', $renameFileName);
								$WA_DFP_UploadStatus[$statusName][$keyname]["serverSimpleFileName"] = preg_replace('/\\.[^.]*$/', '', $renameFileName);
								$WA_DFP_UploadStatus[$statusName][$keyname]["statusCode"] = 1;
								$_SESSION[$sessionName] = $renameFileName;
								break;
								 
							case "3": // "Rename old"
								rename($attemptedServerFilePath, $renameServerFilePath);
								rename($tmpFileNamePath, $attemptedServerFilePath);
								$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasOverwritten"] = FALSE;
								$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasSkipped"] = FALSE;
								$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasRenamed"] = FALSE;
								$WA_DFP_UploadStatus[$statusName][$keyname]["serverFileName"] = preg_replace('/^.*(\/|\\|:)/', '', $attemptedFileName);
								$WA_DFP_UploadStatus[$statusName][$keyname]["serverSimpleFileName"] = preg_replace('/\\.[^.]*$/', '', $attemptedFileName);
								$WA_DFP_UploadStatus[$statusName][$keyname]["statusCode"] = 1;
								$_SESSION[$sessionName] = $attemptedFileName;
								break;
						}
					}
					else{
						rename($tmpFileNamePath , $attemptedServerFilePath);
						$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasOverwritten"] = FALSE;
						$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasSkipped"] = FALSE;
						$WA_DFP_UploadStatus[$statusName][$keyname]["fileWasRenamed"] = FALSE;
						$WA_DFP_UploadStatus[$statusName][$keyname]["serverFileName"] = preg_replace('/^.*(\/|\\|:)/', '', $attemptedFileName);
						$WA_DFP_UploadStatus[$statusName][$keyname]["serverSimpleFileName"] = preg_replace('/\\.[^.]*$/', '', $attemptedFileName);
						$WA_DFP_UploadStatus[$statusName][$keyname]["statusCode"] = 1;
						$_SESSION[$sessionName] = $attemptedFileName;
					}
				}
				
				if($counter == 0){ // set first upload values into V1 status values
					foreach($WA_DFP_UploadStatus[$statusName][$keyname] as $subkey=>$subval){
						$WA_DFP_UploadStatus[$statusName][$subkey] = $subval;
					}
				}
				
				$counter++;
			}
		}
	  if (file_exists($firstFile))  {
	    unlink($firstFile);
	  }
	}
	if (sizeof($fileArray) == 0) {
		$fileParams = func_get_arg(5);
		$counter = 0;
		foreach($fileParams as $key=>$val){
			$defaultFileName = $val["DefaultFileName"];
			$WA_DFP_UploadStatus[$statusName][$key]["statusCode"] = 0;
			$WA_DFP_UploadStatus[$statusName][$key]["serverFileName"] = $defaultFileName;
			$WA_DFP_UploadStatus[$statusName][$key]["fileExtension"] = '';
			$WA_DFP_UploadStatus[$statusName][$key]["errorMessage"] = "No file uploaded";
			if($counter == 0){ // set first upload values into V1 status values
				$_SESSION[$sessionName] = $defaultFileName;
			    $WA_DFP_UploadStatus[$statusName]["statusCode"] = 0;
			    $WA_DFP_UploadStatus[$statusName]["serverFileName"] = $defaultFileName;
			    $WA_DFP_UploadStatus[$statusName]["fileExtension"] = '';
			    $WA_DFP_UploadStatus[$statusName]["errorMessage"] = "No file uploaded";
			}
			$counter++;
		}
	}
	return;
}


function WA_DFP_UploadFile($statusName, $fileField, $defaultFileName, $folderPath, $newFileName, $fileExistsAction, $renameFileTo, $fileSizeLimit, $limitToImages, $maxImageWidth, $maxImageHeight){

global $WA_DFP_UploadStatus;
global $WA_ImageContentTypes;
	
	$newFileNameDefault = "[FileName]";
	$renameIncrementToken = "[Increment]";
	$separator = WA_DFP_GetFileSeparator();
	
	$uploadedFile = $_FILES[$fileField];
	
	$folderPath = rootRelativeToFullFileURL($folderPath);
	makeFolder($folderPath);
	$path = $folderPath."/".$newFileName;
    $path = str_replace("\\","/",$path);
	$finalDir = substr($path,0,strrpos($path,"/"));
	makeFolder($finalDir);
	$pageNameParts = pathinfo($_SERVER['PHP_SELF']);
	$pageName = preg_replace('/\.[^.]*$/', '', $pageNameParts['basename']);
	if (is_numeric(substr($pageName, 0, 1))) {
		$pageName = "file_".$pageName;
	}
	$sessionName = $pageName."_".$fileField;
	
	@session_start();
	
	if($uploadedFile["name"]!="" && $uploadedFile["size"] > 0){
		$path_parts = pathinfo($uploadedFile["name"]);
		$extension = (isset($path_parts['extension'])) ? $path_parts['extension'] : "";
	
		$WA_DFP_UploadStatus[$statusName]["clientFileName"] = $uploadedFile["name"];
		$WA_DFP_UploadStatus[$statusName]["fileExtension"] =  $extension;
		$WA_DFP_UploadStatus[$statusName]["contentType"] = $uploadedFile["type"];

		$baseFileName = preg_replace('/\.[^.]*$/', '', $uploadedFile["name"]);
				
		if(isset($WA_ImageContentTypes[$uploadedFile["type"]])){
			$WA_DFP_UploadStatus[$statusName]["isImage"] = TRUE;
		}
		$WA_DFP_UploadStatus[$statusName]["fileSize"] = $uploadedFile["size"];
		$WA_DFP_UploadStatus[$statusName]["serverDirectory"] = realpath($folderPath).$separator;
		$passedValidations = TRUE;
	// Need to set errorMessage for all failure types: file size, isImage, image width, image height.
		if($fileSizeLimit > 0 && $uploadedFile["size"] > ($fileSizeLimit*1000)){
			$WA_DFP_UploadStatus[$statusName]["statusCode"]	= 20;
			$WA_DFP_UploadStatus[$statusName]["errorMessage"] = "File size of ".$uploadedFile["size"]." exceeded limit of ".$fileSizeLimit."KB";
			$passedValidations = FALSE;
		}
		if($limitToImages === "true"){
			if($WA_DFP_UploadStatus[$statusName]["isImage"]){
				$imageSize = getimagesize($uploadedFile["tmp_name"]);
				if($imageSize !== FALSE){
					$WA_DFP_UploadStatus[$statusName]["imageWidth"] = $imageSize[0];
					$WA_DFP_UploadStatus[$statusName]["imageHeight"] = $imageSize[1];
					
					if(!is_numeric($maxImageWidth)){
						$maxImageWidth = 0;
					}
					if(!is_numeric($maxImageHeight)){
						$maxImageHeight = 0;
					}
					
					if($maxImageWidth > 0 && $WA_DFP_UploadStatus[$statusName]["imageWidth"] > $maxImageWidth){
						// Set error code to failed width		
						$WA_DFP_UploadStatus[$statusName]["statusCode"]	= 11;
						$WA_DFP_UploadStatus[$statusName]["errorMessage"] = "Image width of ".$WA_DFP_UploadStatus[$statusName]["imageWidth"]." exceeded limit of ".$maxImageWidth."";
						$passedValidations = FALSE;
					}
					if($maxImageHeight > 0 && $WA_DFP_UploadStatus[$statusName]["imageHeight"] > $maxImageHeight){
						// Set error code to failed height
						$WA_DFP_UploadStatus[$statusName]["statusCode"]	= 12;
						$WA_DFP_UploadStatus[$statusName]["errorMessage"] = "Image height of ".$WA_DFP_UploadStatus[$statusName]["imageHeight"]." exceeded limit of ".$maxImageHeight."";
						$passedValidations = FALSE;
					}
				}
			}
			else{
				$WA_DFP_UploadStatus[$statusName]["statusCode"]	= 10;
				$WA_DFP_UploadStatus[$statusName]["errorMessage"] = "File not an image";
				$passedValidations = FALSE;
			}

		}

		if($passedValidations){

			$attemptedFileName = str_replace($newFileNameDefault, $baseFileName, $newFileName);
			if($extension != "" && strpos($newFileName, ".") === FALSE){
				$attemptedFileName = $attemptedFileName.".".$extension;
			}
			$attemptedServerFilePath = $WA_DFP_UploadStatus[$statusName]["serverDirectory"].$attemptedFileName;
			$renameFileName = str_replace($newFileNameDefault, $baseFileName, $renameFileTo);
			if(strpos($renameFileName, $renameIncrementToken) !== FALSE ){
				$renameFileName = WA_DFP_GenerateUniqueFileName($WA_DFP_UploadStatus[$statusName]["serverDirectory"], $renameFileName, $extension, $renameIncrementToken);
			}
			if($extension != "" && strpos($renameFileName, ".")=== FALSE){
				$renameFileName = $renameFileName.".".$extension;
			}
			$renameServerFilePath = $WA_DFP_UploadStatus[$statusName]["serverDirectory"].$renameFileName;
			$fileExists = file_exists($attemptedServerFilePath);
			switch($fileExistsAction){
				case "0": // "Overwrite"
					if($fileExists){
						unlink($attemptedServerFilePath);
						$WA_DFP_UploadStatus[$statusName]["fileWasOverwritten"] = TRUE;
					}
					if (move_uploaded_file($uploadedFile["tmp_name"] , $attemptedServerFilePath)) {
						$WA_DFP_UploadStatus[$statusName]["serverFileName"] = preg_replace('/^.*(\/|\\|:)/', '', $attemptedFileName);
						$WA_DFP_UploadStatus[$statusName]["statusCode"] = 1;
						$_SESSION[$sessionName] = $attemptedFileName;
					}
					break; 
				case "1": // "Skip"
					if($fileExists){
						$WA_DFP_UploadStatus[$statusName]["fileWasSkipped"] = TRUE;
						$WA_DFP_UploadStatus[$statusName]["statusCode"] = 30;
						$WA_DFP_UploadStatus[$statusName]["errorMessage"] = "File upload skipped";
						$_SESSION[$sessionName] = "";
					}
					else{
						if (move_uploaded_file($uploadedFile["tmp_name"] , $attemptedServerFilePath)) {
							$WA_DFP_UploadStatus[$statusName]["statusCode"] = 1;
							$_SESSION[$sessionName] = $attemptedFileName;
						}
					}
					break; 
				case "2": // "Rename new"
					$statusSuccess = false;
					if($fileExists){
						$statusSuccess = move_uploaded_file($uploadedFile["tmp_name"] , $renameServerFilePath);
						if ($statusSuccess) {
							$WA_DFP_UploadStatus[$statusName]["serverFileName"] = preg_replace('/^.*(\/|\\|:)/', '', $renameFileName);
							$WA_DFP_UploadStatus[$statusName]["statusCode"] = 1;
							$_SESSION[$sessionName] = $renameFileName;
						}
					}
					else{
						$statusSuccess = move_uploaded_file($uploadedFile["tmp_name"] , $attemptedServerFilePath);
						if ($statusSuccess) {
							$WA_DFP_UploadStatus[$statusName]["serverFileName"] = preg_replace('/^.*(\/|\\|:)/', '', $attemptedFileName);
							$WA_DFP_UploadStatus[$statusName]["statusCode"] = 1;
							$_SESSION[$sessionName] = $attemptedFileName;
						}
					}
					break;
					 
				case "3": // "Rename old"
					if($fileExists){
						rename($attemptedServerFilePath, $renameServerFilePath);			
					}
					if (move_uploaded_file($uploadedFile["tmp_name"] , $attemptedServerFilePath)) {
						$WA_DFP_UploadStatus[$statusName]["serverFileName"] = preg_replace('/^.*(\/|\\|:)/', '', $attemptedFileName);
						$WA_DFP_UploadStatus[$statusName]["statusCode"] = 1;
						$_SESSION[$sessionName] = $attemptedFileName;
					}
					break;
			}
		}
	}
	else{
		$WA_DFP_UploadStatus[$statusName]["statusCode"] = 0;
		$WA_DFP_UploadStatus[$statusName]["serverFileName"] = '';
		$path_parts = pathinfo($defaultFileName);
		$WA_DFP_UploadStatus[$statusName]["fileExtension"] = '';
		$WA_DFP_UploadStatus[$statusName]["errorMessage"] = "No file uploaded";
		$_SESSION[$sessionName] = $defaultFileName;
	}
}

function WA_DFP_AllUploadsDebug(){
	global $WA_DFP_UploadStatus;

	$retStr = "";
	foreach($WA_DFP_UploadStatus as $key=>$val){
		$retStr = $retStr.WA_DFP_UploadStatusItem_debug($key);
	}
	
	return $retStr;
}

function WA_DFP_UploadStatusItem_debug($statusName){
	global $WA_DFP_UploadStatus;
	$retStr = "";
	
	if(isset($WA_DFP_UploadStatus[$statusName])){
		$retStr = "Status object: ".$statusName."<br />";
		foreach($WA_DFP_UploadStatus[$statusName] as $key=>$val){
			if(is_array($WA_DFP_UploadStatus[$statusName][$key])){
				$retStr.= $key.":<br />"; 	
				foreach($WA_DFP_UploadStatus[$statusName][$key] as $subkey=>$subval){
					$retStr = $retStr."&nbsp;&nbsp;&nbsp;".$subkey." : ".$WA_DFP_UploadStatus[$statusName][$key][$subkey]."<br />";					
				}
			}
			else{
				$retStr = $retStr.$key." : ".$WA_DFP_UploadStatus[$statusName][$key]."<br />";
			}
		}
	}
	else{
		$retStr = "Status object named ".$statusName." not present within upload status object<br />";
	}
	
	return $retStr;
}


function WA_DFP_SetupUploadStatusStruct($statusName){
	global $WA_DFP_UploadStatus;
	$WA_DFP_UploadStatus[$statusName] = array( "statusCode" => -1, "errorMessage" => "", "clientFileName" => "", "fileExtension" => "", "serverFileName" => "", "serverSimpleFileName" => "", "serverDirectory" => "", "contentType" => "", "fileWasOverwritten" => FALSE, "fileWasSkipped" => FALSE, "fileWasRenamed" => FALSE, "fileSize" => -1, "isImage" => FALSE, "imageWidth" => -1, "imageHeight" => -1 );
	
	// Setup parameter values
	$paramsName = $statusName.'_Params';
	global ${$paramsName};
	
	if(isset(${$paramsName})){
		$params = ${$paramsName};
		foreach($params as $key=>$val){
			$WA_DFP_UploadStatus[$statusName][$key] = array("clientFileName" => "", "fileExtension" => "", "serverFileName" => "", "serverSimpleFileName" => "", "serverDirectory" => "", "contentType" => "", "fileSize" => -1, "imageWidth" => -1, "imageHeight" => -1 );
		}
	}
}

function WA_DFP_AnyUploadSuccess(){
	global $WA_DFP_UploadStatus;

	$anySuccess = FALSE;
	foreach($WA_DFP_UploadStatus as $key=>$val){
		if($WA_DFP_UploadStatus[$key]["statusCode"] == 1 ){
			$anySuccess = TRUE;
		}
	}
	
	return $anySuccess;
}

function WA_DFP_AllUploadsSuccess(){
	global $WA_DFP_UploadStatus;
	
	$allSuccess = TRUE;
	foreach($WA_DFP_UploadStatus as $key=>$val){
		if($WA_DFP_UploadStatus[$key]["statusCode"] != 1 ){
			$allSuccess = FALSE;
			break;
		}
	}	
	
	return $allSuccess;
}

function WA_DFP_AnyUploadFailed(){
	global $WA_DFP_UploadStatus;
	
	$anyFailed = FALSE;
	foreach($WA_DFP_UploadStatus as $key=>$val){
		if(abs($WA_DFP_UploadStatus[$key]["statusCode"]) != 1 ){
			$anyFailed = TRUE;
			break;
		}
	}		
	
	return $anyFailed;
}

function WA_DFP_AllUploadsFailed(){
	global $WA_DFP_UploadStatus;
	
	$allFailed = TRUE;
	foreach($WA_DFP_UploadStatus as $key=>$val){
		if($WA_DFP_UploadStatus[$key]["statusCode"] == 1 ){
			$allFailed = FALSE;
			break;
		}
	}		
	
	return $allFailed;
}

function WA_DFP_GenerateUniqueFileName($folderPath, $fileName, $fileExt, $incrementToken){
	if($fileExt != ""){
		$fileExt = ".".$fileExt;
	}
	$counter = 1;
	while(file_exists($folderPath.str_replace($incrementToken,$counter,$fileName).$fileExt)){
		$counter = $counter + 1;
	}
	return str_replace($incrementToken,$counter, $fileName);
}


$WA_DFP_DownloadStatus = array();

/*
 Download status codes and error messages: 
 -1	:	Trigger did not run			: ""
  0	:	File not found					: "File not found"
  1 	:	Download processed				: ""
*/

function WA_DFP_DownloadFile($statusName, $folderPath, $fileName, $newFileName, $updateDB, $dbName, $connectionName, $tableName, $keyColumn, $recordID, $countColumn){
	global $WA_DFP_DownloadStatus;

	if($folderPath == ""){
		$folderPath = "./";
	}
	
	if (strpos($fileName, "/") !== false) {
		$folderPath .= substr($fileName, 0, strrpos($fileName, "/") + 1);
		$fileName = substr($fileName, strrpos($fileName, "/") + 1);
	}
	
	$separator = WA_DFP_GetFileSeparator();
	$folderPath = rootRelativeToFullFileURL($folderPath);
	if($folderPath !==FALSE){
		$path = $folderPath.$separator.$fileName;
	}
	else{
		$path = $fileName;
	}
	
	$path_parts = pathinfo($fileName);	
	$path_parts = pathinfo($fileName);

	$WA_DFP_DownloadStatus[$statusName]["fileNotPresent"] = true;
	$WA_DFP_DownloadStatus[$statusName]["fileName"] = preg_replace('/\.[^.]*$/', '', $fileName);
	$WA_DFP_DownloadStatus[$statusName]["fileFullName"] = $fileName;
	$WA_DFP_DownloadStatus[$statusName]["fileExtension"]=  (isset($path_parts['extension']))?$path_parts['extension']:"";
	$WA_DFP_DownloadStatus[$statusName]["serverDirectory"] = realpath($folderPath);
	$WA_DFP_DownloadStatus[$statusName]["serverFilePath"] =  $WA_DFP_DownloadStatus[$statusName]["serverDirectory"].	$separator.$WA_DFP_DownloadStatus[$statusName]["fileFullName"];
	
	if(file_exists($path)){
		if($updateDB){
			$query_cmd = "UPDATE ".$tableName." SET ".$countColumn."=".$countColumn."+1 WHERE ".$keyColumn."=".$recordID.";";
			mysql_select_db($dbName, $connectionName);
			$cmd = mysql_query($query_cmd, $connectionName) or die(mysql_error());
		}

		$fileNameDefault = "[FileName]";
		if($newFileName!=$fileNameDefault){
			if($path_parts["extension"] != "" && strpos($newFileName, ".")=== FALSE){
				$fileName = $fileName.".".$path_parts["extension"];
			}
			$fileName =  preg_replace('/\[FileName\]/', preg_replace('/\.[^.]*$/', '', $fileName), $newFileName);
		}
		else {
			$fileName = $fileName;
		}
		if (strpos($fileName,".") === false && $WA_DFP_DownloadStatus[$statusName]["fileExtension"] != "") {
		  $fileName .= ".".$WA_DFP_DownloadStatus[$statusName]["fileExtension"];
		}
		$WA_DFP_DownloadStatus[$statusName]["statusCode"] = 1;
      header('Cache-Control:');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="' . $fileName . '"');
      header("Content-length: ".filesize($path));
      readfile_chunked("$path");
		die();
	}
	else{
		$WA_DFP_DownloadStatus[$statusName]["statusCode"] = 0;
		$WA_DFP_DownloadStatus[$statusName]["errorMessage"] = "File: '".$path."' not found";
	}
}


function readfile_chunked( $filename, $retbytes = true ) {
    $chunksize = 1 * (1024 * 1024); // how many bytes per chunk
    $buffer = '';
    $cnt = 0;
    $handle = fopen( $filename, 'rb' );
    if ( $handle === false ) {
        return false;
    }
    while ( !feof( $handle ) ) {
        $buffer = fread( $handle, $chunksize );
        //$buffer = str_replace("ï»¿","",$buffer);
        echo $buffer;
        ob_flush();
        flush();
        if ( $retbytes ) {
            $cnt += strlen( $buffer );
        }
    }
    $status = fclose( $handle );
    if ( $retbytes && $status ) {
        return $cnt; // return num. bytes delivered like readfile() does.
    }
    return $status;
} 

function WA_DFP_SetupDownloadStatusStruct($statusName){
	global $WA_DFP_DownloadStatus;
	$WA_DFP_DownloadStatus[] = $statusName;
	$WA_DFP_DownloadStatus[$statusName] = array("statusCode" => -1, 	"errorMessage" => "", "fileName" => "", "fileFullName" => "", "fileExtension" => "", "serverDirectory" => "", "serverFilePath" => "");
}

function WA_FileAssist_RenameFile($folderPath, $fileName, $newFileName){
	$folderPath = rootRelativeToFullFileURL($folderPath);
	if(empty($newFileName)){
		$newFileName = "";
	}
	if($newFileName == preg_replace('/\.[^.]*$/', '', $newFileName)){
		$pathInfo = pathinfo($fileName);
		if(isset($pathInfo["extension"])){
			$newFileName = $newFileName.".".$pathInfo["extension"];		
		}
	}
	$separator = WA_DFP_GetFileSeparator();
	$oldFilePath = $folderPath.$separator.$fileName;
	$newFileNameDefault = "[FileName]";
	$newFileName = str_replace($newFileNameDefault, preg_replace('/\.[^.]*$/', '', $fileName), $newFileName);
	$newFilePath = $folderPath.$separator.$newFileName;
	if(file_exists($oldFilePath)){
		return rename($oldFilePath, $newFilePath);
	}
	else{
		return FALSE;
	}
}

function WA_FileAssist_DeleteFile($folderPath, $filePath){

	$folderPath = rootRelativeToFullFileURL($folderPath);
	$result = FALSE;
	$separator = WA_DFP_GetFileSeparator();
	$path = $folderPath.$separator.$filePath;
	$fullPath = realpath($path);
	if(file_exists($path) && is_writable($fullPath)){
		unlink($fullPath);
		$result = TRUE;
	}
	return $result;
}

function WA_DFP_GetFileSeparator(){
	$separator = "\\";
	$real_path = realpath("./");
	if(strpos($real_path, "/") !== FALSE){
		$separator = "/";
	}
	else if(strpos($real_path, "\\") !== FALSE){
		$separator = "\\";
	}
	
	return $separator;
}

function rootRelativeToFullFileURL($folderPath){
	if(strpos($folderPath, "/") === 0){
		$scriptName = $_SERVER['SCRIPT_NAME'];
		$currentFilePath = realpath(basename($scriptName));
		$replaceBackSlash = preg_replace('/\\\/', '/', $currentFilePath);
		$scriptNameRegEx = "/".preg_replace("/\\//", "\\/", $scriptName)."$/";
		$folderPath = preg_replace($scriptNameRegEx,"", $replaceBackSlash).$folderPath;
	}
	else{
		$separator= WA_DFP_GetFileSeparator();
		// Use correct separator
		$origFolderPath = preg_replace('/\\\|\\//', $separator, $folderPath);
		$folderPath = realpath('./').$separator.$origFolderPath;
		// drop any trailing slash, as realpath doesn't return it either.
		$folderPath = preg_replace("/\\".$separator."$/", "", $folderPath);
	}
	return $folderPath;
}

/*

add .jpe file extension check as IE will sometimes upload as .jpe instead of .jpg or .jpeg

*/

function WA_DFP_Resize($uploadStatus,$maxSize,$minSize=-1)  {
  if ($uploadStatus["statusCode"] == 1) { 
    $uploadedfile = $uploadStatus["serverDirectory"].$uploadStatus["serverFileName"];
    $width = $uploadStatus["imageWidth"];
    $height = $uploadStatus["imageHeight"];
    if ($width > $maxSize || $width < $minSize)  {
      $newwidth = $maxSize;
	  if ($width < $minSize) $newwidth = $minSize;
      if (strtolower($uploadStatus["fileExtension"]) == "jpg" || strtolower($uploadStatus["fileExtension"]) == "jpeg" )  {
        $src = imagecreatefromjpeg($uploadedfile);
      }
      else if (strtolower($uploadStatus["fileExtension"]) == "gif")  {
        $src = imagecreatefromgif($uploadedfile);
      }
      else if (strtolower($uploadStatus["fileExtension"]) == "png")  {
        $src = imagecreatefrompng($uploadedfile);
      }
	  else  {
	    return;
	  }
      $newheight=round(($height/$width)*$newwidth);
      $tmp=imagecreatetruecolor($newwidth,$newheight);
      imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
      if (strtolower($uploadStatus["fileExtension"]) == "jpg" || strtolower($uploadStatus["fileExtension"]) == "jpeg" )  {
        imagejpeg($tmp,$uploadedfile,100);
      }
      else if (strtolower($uploadStatus["fileExtension"]) == "gif")  {
        imagegif($tmp,$uploadedfile);
      }
      else if (strtolower($uploadStatus["fileExtension"]) == "png")  {
        imagepng($tmp,$uploadedfile,0);
      }
      imagedestroy($src);
      imagedestroy($tmp);
    }
  }
}

function WA_DFP_RetainTransparent ($imgSrc, $imgTmp, $imageInfo) {
  $transparent_index = imagecolortransparent($imgSrc);
  $transparent_color = false;
  if($transparent_index != -1) {
	$transparent_old = imagecolorsforindex($imgTmp,$transparent_index);
	$transparent_color = imagecolorallocate($imgTmp, $transparent_old['red'], $transparent_old['green'], $transparent_old['blue'] );
  } else if ($imageInfo[2] == IMAGETYPE_PNG) {
	$transparent_color = imagecolorallocatealpha($imgTmp, 186, 144, 112, 127);
  }
  if ($transparent_color)  {
	if ($imageInfo[2] == IMAGETYPE_PNG)  {
	  imagealphablending($imgTmp, false);
	  imagefill($imgTmp, 0, 0, $transparent_color);
	  imagesavealpha($imgTmp, true);
	  imagealphablending($imgTmp, false);
	  imagecolortransparent($imgTmp, $transparent_color);
	} else {
	  imagefill($imgTmp, 0, 0, $transparent_color);
	  imagecolortransparent($imgTmp, $transparent_color);
	}
  }
  return $imgTmp;
}
// TODO: Delete source image if the file type is changed?
// TODO: Determine what to return so that the new height and width can be used elsewhere, as well as the new file name and extension if converted.

function WA_DFP_ResizeImage($imageFilePath, $options){

	$imageInfo = getimagesize($imageFilePath);
	$mime = $imageInfo['mime'];
	$width = $imageInfo[0];
	$height = $imageInfo[1];
	$newWidth = $width;
	$newHeight = $height;
	$maxWidth = $options['ResizeWidth'];
	$maxHeight = $options['ResizeHeight'];
	$aspect_ratio = $width/$height;
	$fillColor = $options['ResizeFillColor'];
	$uniqueFileNamePart = uniqid(rand(), true);
	
	switch($imageInfo[2]){
		case IMAGETYPE_PNG:
			$src = imagecreatefrompng($imageFilePath);
			break;
			
		case IMAGETYPE_JPEG:
			$src = imagecreatefromjpeg($imageFilePath);
			break;
			
		case IMAGETYPE_GIF:
			$src = imagecreatefromgif($imageFilePath);
			break;
		
		default :
			$src = imagecreatetruecolor($width, $height);
			break;
	}
	
	$tmpIsSrc = false;
	
	
	switch($options['ResizeType']){
		case WADFP_FIT_TO_BOX:
		// Fit to box: image resized down to fit within a box with dimensions of $maxSize x $maxHeight
			if($width > $maxWidth || $height > $maxHeight){ // need to resize down
				
				if ($maxWidth/$maxHeight> $aspect_ratio) {
					$newWidth = round($maxHeight*$aspect_ratio);
					$newHeight = $maxHeight;
				}
				else {
					$newWidth = $maxWidth;
					$newHeight = round($maxWidth/$aspect_ratio);
				}
				$src = resetTransparency($src,$options);
				$tmp = imagecreatetruecolor($newWidth,$newHeight);
				$tmp = WA_DFP_RetainTransparent ($src, $tmp, $imageInfo);
				$tmp = resetTransparency($tmp,$options);
				imagecopyresampled($tmp,$src,0,0,0,0,$newWidth,$newHeight,$width,$height);
			}
			else{
				$tmp = imagecreatetruecolor($width,$height);
				$src = resetTransparency($src,$options);
				$tmp = WA_DFP_RetainTransparent ($src, $tmp, $imageInfo);
				$tmp = resetTransparency($tmp,$options);
				imagecopyresampled($tmp,$src,0,0,0,0,$width,$height,$width,$height);
			}
			
			break;
			
		case WADFP_FIT_TO_WIDTH:
		// Fit to Width: image resize to $maxWidth and height modified to keep aspect ratio
			if($width > $maxWidth ){ // need to resize down
				$newWidth = $maxWidth;
				$newHeight = round($newWidth/$aspect_ratio);
				$tmp = imagecreatetruecolor($maxWidth,$newHeight);
				$src = resetTransparency($src,$options);
				$tmp = WA_DFP_RetainTransparent ($src, $tmp, $imageInfo);
				$tmp = resetTransparency($tmp,$options);
				imagecopyresampled($tmp,$src,0,0,0,0,$maxWidth,$newHeight,$width,$height);
			}
			else{
				$tmp = imagecreatetruecolor($width,$height);
				$src = resetTransparency($src,$options);
				$tmp = WA_DFP_RetainTransparent ($src, $tmp, $imageInfo);
				$tmp = resetTransparency($tmp,$options);
				imagecopyresampled($tmp,$src,0,0,0,0,$width,$height,$width,$height);
			}
			break;
		
		case WADFP_FIT_TO_HEIGHT:
		// Fit height: image resize down to $maxHeight and width modified to keep the aspect ratio
			if($height > $maxHeight ){ // need to resize down
				$newHeight = $maxHeight;
				$newWidth = round($newHeight*$aspect_ratio);
				$tmp = imagecreatetruecolor($newWidth,$maxHeight);
				$src = resetTransparency($src,$options);
				$tmp = WA_DFP_RetainTransparent ($src, $tmp, $imageInfo);
				$tmp = resetTransparency($tmp,$options);
				imagecopyresampled($tmp,$src,0,0,0,0,$newWidth,$maxHeight,$width,$height);
			}
			else{
				$tmp = imagecreatetruecolor($width,$height);
				$src = resetTransparency($src,$options);
				$tmp = WA_DFP_RetainTransparent ($src, $tmp, $imageInfo);
				$tmp = resetTransparency($tmp,$options);
				imagecopyresampled($tmp,$src,0,0,0,0,$width,$height,$width,$height);
			}
			break;
		
		case WADFP_FIXED_SIZE:
		// Fixed width and height: image resize to exact width and height and filled with fill color			
			$newWidth = $width;
			$newHeight = $height;
			
			if ($maxWidth/$maxHeight> $aspect_ratio) {
				$newWidth = round($maxHeight*$aspect_ratio);
				$newHeight = $maxHeight;
			}
			else {
				$newWidth = $maxWidth;
				$newHeight = round($maxWidth/$aspect_ratio);
			}
			$tmp=imagecreatetruecolor($maxWidth,$maxHeight);
			if($fillColor == ''){
				$fillColor = '#FFFFFF';
			}
			$fillColors = html2rgb($fillColor);
			$allocatedColor = imagecolorallocate($tmp, $fillColors[0], $fillColors[1], $fillColors[2]);
			imagefilledrectangle($tmp, 0, 0, $maxWidth, $maxHeight, $allocatedColor);
			$src = resetTransparency($src,$options);	
			$tmp = WA_DFP_RetainTransparent ($src, $tmp, $imageInfo);
			$tmp = resetTransparency($tmp,$options);
			imagecopyresampled($tmp,$src,0,0,0,0,$newWidth,$newHeight,$width,$height);
			$newWidth = $maxWidth;
			$newHeight = $maxHeight;
			break;
			
		default :
			$tmp = $src;
			$tmpIsSrc = true;
			break;
	}
	
	$path_parts = pathinfo($imageFilePath);
	$extension = isset($path_parts["extension"]) ? $path_parts["extension"] : '';
	$startExtension = strtolower($extension);
	$endExtension = $startExtension;
	
	// Adding new extension to end in case there isn't a file extension isn't present in file path
	$fileExtensionRegExp = '/'.$path_parts["basename"].'$/';
		
	if($options['imageFormat'] && $options['imageFormat'] !== ''){ // convert to a specific format
		$imageOptions = explode(':', $options['imageFormat']);
		

		switch($imageOptions[0]){
			case WADFP_PNG:
				if($startExtension != 'png'){
					$newFilePath = preg_replace( $fileExtensionRegExp, $uniqueFileNamePart, $imageFilePath).'.png';
				}
				else{
					$newFilePath = $imageFilePath;
				}
				$endExtension = 'png';
				$imageQuality = intval($imageOptions[1]);
				
				if($imageQuality > 9){
					$imageQuality = 9;
				}
				if($imageQuality < 0 ){
					$imageQuality = 0;
				}
				
			    $tmp = WA_DFP_RetainTransparent ($src, $tmp, $imageInfo);
				imagepng($tmp,$newFilePath,$imageQuality);
				break;
				
			case WADFP_JPEG:
				$newFilePath = preg_replace( $fileExtensionRegExp, $uniqueFileNamePart, $imageFilePath).'.jpg';
				$endExtension = 'jpg';
				$imageQuality = intval($imageOptions[1]);
				
				if($imageQuality > 100){
					$imageQuality = 100;
				}
				if($imageQuality < 0 ){
					$imageQuality = 0;
				}
				$tmp = resetTransparency($tmp,$options);
				imagejpeg($tmp,$newFilePath,$imageQuality);
				break;
				
			case WADFP_GIF:
				$newFilePath = preg_replace( $fileExtensionRegExp, $uniqueFileNamePart, $imageFilePath).'.gif'; 
				$endExtension = 'gif';
			    $tmp = WA_DFP_RetainTransparent ($src, $tmp, $imageInfo);
				imagegif($tmp,$newFilePath);
				break;
		}
		$endServerPath = $newFilePath;
		$fileSize = filesize($endServerPath);
		$imageInfo = getimagesize($endServerPath); 
		$mime = $imageInfo['mime'];
	}
	else{ // no conversion
		switch($imageInfo[2]){
			case IMAGETYPE_PNG:
				$endServerPath = preg_replace( $fileExtensionRegExp, $uniqueFileNamePart, $imageFilePath).'.png';
	  			if ($options['ResizeType'] != "0") {
				  imagepng($tmp,$endServerPath);
				} else  {
				  copy($imageFilePath,$endServerPath);
				}
				break;
				
			case IMAGETYPE_JPEG:
				$endServerPath = preg_replace( $fileExtensionRegExp, $uniqueFileNamePart, $imageFilePath).'.jpg';
	  			if ($options['ResizeType'] != "0") {
				  imagejpeg($tmp,$endServerPath);
				} else  {
				  copy($imageFilePath,$endServerPath);
				}
				break;
				
			case IMAGETYPE_GIF:
				$endServerPath = preg_replace( $fileExtensionRegExp, $uniqueFileNamePart, $imageFilePath).'.gif';
	  			if ($options['ResizeType'] != "0") {
				  imagegif($tmp, $endServerPath);
				} else  {
				  copy($imageFilePath,$endServerPath);
				}
				break;
		}

		$fileSize = filesize($endServerPath);
				
	}
	
	if($src) {
		imagedestroy($src);
	}
	if($tmp && !$tmpIsSrc) {
		imagedestroy($tmp);
	}
	
	return array('fileSize' => $fileSize, 'startWidth'=> $width, 'startHeight'=> $height,  'endWidth' => $newWidth, 'endHeight' => $newHeight, 'startServerPath' =>  $imageFilePath , 'endServerPath' =>  $endServerPath, 'startExtension' => $startExtension , 'endExtension' => $endExtension, 'contentType' => $mime );
}

function resetTransparency($img,$options) {
	if($options['imageFormat'] && $options['imageFormat'] !== ''){ // convert to a specific format
		$imageOptions = explode(':', $options['imageFormat']);
		if ($imageOptions[0] == WADFP_JPEG){
		  $transparent_index = imagecolortransparent($img);	
		  if ($transparent_index==-1) {
			  $tmp = imagecreatetruecolor(imagesx($img),imagesy($img));
			  $white = imagecolorallocate($tmp,255,255,255);
			  imagefilledrectangle($tmp,0,0,imagesx($img),imagesy($img),$white);
			  imagecopy($tmp,$img,0,0,0,0,imagesx($img),imagesy($img));
			  $img = $tmp;
		  }
		  if ($transparent_index >= 0)  {
			imagetruecolortopalette($img, true, 255);
			$transparent_index = imagecolortransparent($img);
			$transparent_old = imagecolorsforindex($img,$transparent_index);
			imagecolorset($img, $transparent_index, 255, 255, 255);
		  }
		}
	}
	return $img;
}

// Source: http://www.anyexample.com/programming/php/php_convert_rgb_from_to_html_hex_color.xml
function html2rgb($color)
{
    if ($color[0] == '#')
        $color = substr($color, 1);

    if (strlen($color) == 6)
        list($r, $g, $b) = array($color[0].$color[1],
                                 $color[2].$color[3],
                                 $color[4].$color[5]);
    elseif (strlen($color) == 3)
        list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
    else
        return false;

    $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

    return array($r, $g, $b);
}

// Source: http://www.anyexample.com/programming/php/php_convert_rgb_from_to_html_hex_color.xml
function rgb2html($r, $g=-1, $b=-1)
{
    if (is_array($r) && sizeof($r) == 3)
        list($r, $g, $b) = $r;

    $r = intval($r); $g = intval($g);
    $b = intval($b);

    $r = dechex($r<0?0:($r>255?255:$r));
    $g = dechex($g<0?0:($g>255?255:$g));
    $b = dechex($b<0?0:($b>255?255:$b));

    $color = (strlen($r) < 2?'0':'').$r;
    $color .= (strlen($g) < 2?'0':'').$g;
    $color .= (strlen($b) < 2?'0':'').$b;
    return '#'.$color;
}

?>