$(function() {
	$('.link-section').hide();
	
	if(self.document.location.hash != '')
		$(self.document.location.hash).show();
	
	$('<p>Click a category to begin.</p>').insertAfter('h2');
	
	$('.categories a').click(function() {
		if ($($(this).attr('href')).is(':hidden'))
		{
			// sliding is for losers amirite
			$('.link-section').filter(':visible').hide().end().filter($(this).attr('href')).show();
		}
	});
});