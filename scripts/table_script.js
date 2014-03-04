$(function(){
	$('tr.highlight').removeClass('highlight');
});

function redirect(target, id){
	if(id == ''){
		window.location.href= './' + target + '?' + HTML_STRING;
	}
	else{
		window.location.href= './' + target + '?' + HTML_STRING + id + '=' + $('tr.highlight td').html();
	}
};

function script(target, id){
	$('tr.rows').click(function(){
		//If this is being unhighlighted
		if($(this).hasClass('highlight')){
			$(this).toggleClass('highlight');
		}
		//else highlight this, unhighlight others
		else{
			$('tr.highlight').removeClass('highlight');
			$(this).addClass('highlight');
			
			//Redirect to a page where the schedule can be viewed
			//redirect(target, id);
		}
	});
};