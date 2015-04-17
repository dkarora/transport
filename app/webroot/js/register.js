$(function () {	
	$('#part-of-group').click(function () {
		$('.org-target').attr('disabled', !$('#part-of-group').is(':checked'));
		
		// if neither are checked then mark off 'existing' 
		if (!$('.new-or-existing')[0].checked && !$('.new-or-existing')[1].checked && $('#part-of-group').checked)
		{
			existingSelected = true;
			$('.new-or-existing')[0].checked = existingSelected;
			
			$('.existing-group-target').attr('disabled', !existingSelected);
			$('.new-group-target').attr('disabled', existingSelected);
		}
	});
	
	$('.new-or-existing').click(function () {
		existingSelected = ($('.new-or-existing')[0].checked);
		
		$('.existing-group-target').attr('disabled', !existingSelected);
		$('.new-group-target').attr('disabled', existingSelected);
	});
	
	if (!$('#part-of-group').checked)
	{
		$('.org-target').attr('disabled', true);
	}
});