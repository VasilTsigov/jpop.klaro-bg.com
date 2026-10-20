<?php

/////////////////////////////////////////////////////////////////////////////
// оцветява номера на статията н червено, ако е отхвърлена 2 или повече пъти
/////////////////////////////////////////////////////////////////////////////
function colorRecommendation($recom1, $recom2, $recom3) 
{
	if(($recom1 == 'Rejected' && $recom2 == 'Rejected' && $recom3 == 'Rejected') ||
								($recom1 == 'Rejected' && $recom2 == 'Rejected') ||
								($recom1 == 'Rejected' && $recom3 == 'Rejected') ||
								($recom2 == 'Rejected' && $recom3 == 'Rejected'))
	{
		echo("#FF0000");
	}
	else 
	{
		echo("#FEED77");	
	}
}
?>