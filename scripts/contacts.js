// JavaScript Document
$(document).ready(function(){
	$('#contact_form').show();
	$('#cform').html('Show Contact Form');	
	$('#cform').click(function() {
		if($('#contact_form').is(':hidden')) {
			$('#contact_form').show(300);
			$('#cform').html('Hide Contact Form')
		 		}
		 else if (!$('#contact_form').is(':hidden')) {
			$('#contact_form').hide(300);
			$('#cform').html('Show Contact Form')
				}		
		});
	
});