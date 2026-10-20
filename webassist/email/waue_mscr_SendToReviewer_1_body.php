<html>
<head></head>
<body>
<p>Dear Dr. <?php echo $row_rsReviewer_1['ReviewerFullName']?>,<br />
  <br />
In view of your expertise I would be very   grateful if you could review the following manuscript which has been   submitted to Online Manuscript Submission,            Review and Tracking System            for the journal            Propagation of Ornamental Plants.</p>
<p><br />
  Manuscript Number:  <strong><?php echo $row_rsMnscrpt['mscrCodeU']?></strong><br />
  <br />
  Title:  <strong><?php echo $row_rsMnscrpt['mscrFullTitle']?></strong><br />
  <br />
  In case you are interested in reviewing this submission please click on this   link: <br />
  <br />
  <a href="http://jpop.klaro-bg.com/manuscripts/mscr_Reviewer1ConfirmYes.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']?>&code=<?php echo $_SESSION['confirmYes']?>">I agree to review this submission.</a>
  <br />
  <br />
  If you do not have time to do this, or do not feel qualified, please click on this link: <br />
  <br />
  <a href="http://jpop.klaro-bg.com/manuscripts/mscr_Reviewer1ConfirmNo.php?mnscrpt_id=<?php echo $row_rsMnscrpt['mscrID']?>&code=<?php echo $_SESSION['confirmNo']?>">I decline to review this submission.</a>
  
  </p>
<p><br />
  We   hope you are willing to review the manuscript. If so, would you be so   kind as to return your review to us within 30 days of agreeing to   review? Thank you.<br />
  <br />
  You are requested to submit your review online by using<br />
  the Editorial Manager system which can be found at:<br />
  http://jpop.klaro-bg.com<br />
  <br />
  <br />
  IN ORDER TO KEEP DELAYS TO A MINIMUM, PLEASE ACCEPT OR DECLINE THIS ASSIGNMENT ONLINE AS SOON AS POSSIBLE!<br />
  <br />
  In   case you wish to annotate the manuscript, you may upload the attachment   when you send your comments. Please click 'Upload Reviewer   Attachments'.<br />
  <br />
  I hope you are willing to review the manuscript.  Thank you for your   assistance.<br />
  <br />
  Journal POP<br />
  Editorial Office </p>


</body>
</html>