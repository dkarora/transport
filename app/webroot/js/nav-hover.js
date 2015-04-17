$(function() {
	var hideit = false;
	var hoveredId = '';

	function doHide(h)
	{
		if (hideit)
		{
			$('#subnav-hidden-container').hide();
			if (hoveredId != '')
			{
				$(hoveredId).removeClass('nav-tab-hot');
			}
		}
	}
	
	// add the down arrows to indicate a rollover
	var dropdown_indicator = ' &#x25BC;';
	$('.hidden-subnav-trigger').append(dropdown_indicator);
	
	// we need this to keep the hidden subnav visible while we mouse over
	$('#subnav-hidden-container').hover(
		function()
		{
			$(hoveredId).addClass('nav-tab-hot');
			$('#subnav-hidden-container').show();
			hideit = false;
		},
		
		function()
		{
			hideit = true;
			setTimeout(function() { doHide(hideit) }, 500);
		}
	);
	
	$('.hidden-subnav-trigger').hover(
		// hover in
		function ()
		{
			hideit = true;
			
			// if it's here then the subnav is already shown
			if (!$(this).hasClass('here'))
			{
				hideit = false;
				
				// find the right subnav to show
				$('.subnav-hidden').hide();
				// the ids of the triggers are prefixed with the 8-character-long string 'trigger-'.
				// just in case you were wondering why 8 was chosen
				hoveredId = '#' + $(this).attr('id');
				$('#subnav-hidden-' + $(this).attr('id').substr(8)).show();
				$(hoveredId).css('background-color', 'auto');
				
				$('#subnav-hidden-container').show();
			}
			else
				hoveredId = '';
		},
		
		// hover out
		function()
		{
			hideit = true;
			setTimeout(function() { doHide(hideit) }, 500);
		}
	);
})