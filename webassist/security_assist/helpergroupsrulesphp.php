<?php

function WA_Auth_GetComparisonsForRule($ruleName){
	$comparisons = array();
	
	switch ($ruleName){
		case "Autors":
			$comparisons[0] = array(TRUE, "".((isset($_SESSION['UserLavel']))?$_SESSION['UserLavel']:"")  ."", 1, "1");
			break;
		case "Autors_Reviewer_and_Editors":
			$comparisons[0] = array(TRUE, "".((isset($_SESSION['UserLavel']))?$_SESSION['UserLavel']:"")  ."", 20, "Autors_Reviewer_and_Editors");
			break;
		case "checkout rule":
			$comparisons[0] = array(TRUE, "".((isset($_GET['checkout']))?$_GET['checkout']:"")  ."", 1, "1");
			break;
		case "Editors":
			$comparisons[0] = array(TRUE, "".((isset($_SESSION['UserLavel']))?$_SESSION['UserLavel']:"")  ."", 1, "3");
			break;
		case "Reviewer":
			$comparisons[0] = array(TRUE, "".((isset($_SESSION['UserLavel']))?$_SESSION['UserLavel']:"")  ."", 1, "2");
			break;
		case "Reviewer_and_Editors":
			$comparisons[0] = array(TRUE, "".((isset($_SESSION['UserLavel']))?$_SESSION['UserLavel']:"")  ."", 20, "Reviewer_and_ Editors");
			break;
		case "ShowReviewerBtn":
			$comparisons[0] = array(TRUE, "".((isset($_GET['usertype']))?$_GET['usertype']:"")  ."", 1, "Reviewer");
			break;
	}
	return $comparisons;	
}


function WA_Auth_GetGroup($groupName){
	$group = Array();
	
											switch ($groupName){
		case "Autors_Reviewer_and_Editors":
			$group = array("2","3","1");
			break;
		case "Reviewer_and_ Editors":
			$group = array("2","3");
			break;
	}
	return $group;
}

?>
