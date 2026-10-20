<?php

//////////////////////////////////////////////////////////
//
//  Letter to Reviewer 1
//
//////////////////////////////////////////////////////////

function LeterToReviwer1($rev1, $authname, $mnscrCode, $recom1, $recom2, $recom3, $tittle){
	
?>	
	<p>Dear Dr. <?php echo $rev1 ?>,</p>
    <p>Thank you once again for reviewing manuscript No <?php echo $mnscrCode; ?> ("<?php echo $tittle ;?>") from <?php echo $authname ;?>.
    <p>We appreciate your time and effort in reviewing this manuscript and greatly value your assistance as a reviewer for Propagation of Ornamental Plants.</p>
    <p>We would like to remind you that you have recommended "<?php echo $recom1; ?>".</p>  
    
	<?php
		if(strlen($recom2) !== 0 && strlen($recom3) !== 0)
		{
			echo '<p>The other two reviewers have suggested - "'.$recom2.'" and "'.$recom3.'".</p>';	
		}
		elseif (strlen($recom2) !== 0 && strlen($recom3) == 0)
		{
			echo '<p>Another reviewer suggested "'.$recom2.'".</p>';	
		}
		elseif (strlen($recom2) == 0 && strlen($recom3) !== 0)
		{
			echo '<p>Another reviewer suggested "'.$recom3.'".</p>';	
		}
		elseif (strlen($recom2) == 0 && strlen($recom3) == 0)
		{
			echo '';	
		}
		else echo 'Има някаква грешка';
	 ?>
    <p>Due to above mentioned recommendations we have sent to the corresponding author the attached "Editor's Decision Letter"</p>
    <p>With best regards</p>
    <p>Prof. Ivan Iliev</p>
    <p>Editor-in-Chief</p>
    <p>Propagation of Ornamental Plants (www.journal-pop.org)</p>
	
<?php
}
?>

<?php

//////////////////////////////////////////////////////////
//
//  Letter to Reviewer 2
//
//////////////////////////////////////////////////////////

function LeterToReviwer2($rev2, $authname, $mnscrCode, $recom1, $recom2, $recom3, $tittle){
	
?>	
	<p>Dear Dr. <?php echo $rev2 ?>,</p>
    <p>Thank you once again for reviewing manuscript No <?php echo $mnscrCode; ?> ("<?php echo $tittle ;?>") from <?php echo $authname ;?>.
    <p>We appreciate your time and effort in reviewing this manuscript and greatly value your assistance as a reviewer for Propagation of Ornamental Plants.</p>
    <p>We would like to remind you that you have recommended "<?php echo $recom2; ?>".</p>  
    
	<?php
		if(strlen($recom1) !== 0 && strlen($recom3) !== 0)
		{
			echo '<p>The other two reviewers have suggested - "'.$recom1.'" and "'.$recom3.'".</p>';	
		}
		elseif (strlen($recom1) !== 0 && strlen($recom3) == 0)
		{
			echo '<p>Another reviewer suggested "'.$recom1.'".</p>';	
		}
		elseif (strlen($recom1) == 0 && strlen($recom3) !== 0)
		{
			echo '<p>Another reviewer suggested "'.$recom3.'".</p>';	
		}
		elseif (strlen($recom1) == 0 && strlen($recom3) == 0)
		{
			echo '';	
		}
		else echo 'Има някаква грешка';
	 ?>
    <p>Due to above mentioned recommendations we have sent to the corresponding author the attached "Editor's Decision Letter"</p>
    <p>With best regards</p>
    <p>Prof. Ivan Iliev</p>
    <p>Editor-in-Chief</p>
    <p>Propagation of Ornamental Plants (www.journal-pop.org)</p>
	
<?php
}
?>

<?php

//////////////////////////////////////////////////////////
//
//  Letter to Reviewer 3
//
//////////////////////////////////////////////////////////

function LeterToReviwer3($rev3, $authname, $mnscrCode, $recom1, $recom2, $recom3, $tittle){

?>	
	<p>Dear Dr. <?php echo $rev3 ?>,</p>
    <p>Thank you once again for reviewing manuscript No <?php echo $mnscrCode; ?> ("<?php echo $tittle ;?>") from <?php echo $authname ;?>.
    <p>We appreciate your time and effort in reviewing this manuscript and greatly value your assistance as a reviewer for Propagation of Ornamental Plants.</p>
    <p>We would like to remind you that you have recommended "<?php echo $recom3; ?>".</p>  
    
	<?php
		if(strlen($recom1) !== 0 && strlen($recom2) !== 0)
		{
			echo '<p>The other two reviewers have suggested - "'.$recom1.'" and "'.$recom2.'".</p>';	
		}
		elseif (strlen($recom1) !== 0 && strlen($recom2) == 0)
		{
			echo '<p>Another reviewer suggested "'.$recom1.'".</p>';	
		}
		elseif (strlen($recom1) == 0 && strlen($recom2) !== 0)
		{
			echo '<p>Another reviewer suggested "'.$recom2.'".</p>';	
		}
		elseif (strlen($recom1) == 0 && strlen($recom2) == 0)
		{
			echo '';	
		}
		else echo 'Има някаква грешка';
	 ?>
    <p>Due to above mentioned recommendations we have sent to the corresponding author the attached "Editor's Decision Letter"</p>
    <p>With best regards</p>
    <p>Prof. Ivan Iliev</p>
    <p>Editor-in-Chief</p>
    <p>Propagation of Ornamental Plants (www.journal-pop.org)</p>
	
<?php
}
?>
