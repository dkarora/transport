function CheckIfAllSelected() {
	// if all the boxes are going to be checked anyway,
	// set the select all box to checked as well
	if ($(".selectalltarget").filter(function (index) { return $(this).is(':checked'); }).length == $(".selectalltarget").length)
		$("#selectall").attr('checked', true);
	// otherwise if one of the targets is unselected then clear the select all box
	else
		$("#selectall").attr('checked', false);
}

$(function () {
	CheckIfAllSelected();
	
	// set all elements with class=selectalltarget
	// to the select all box's check value when clicked
	$("#selectall").click( function () {
		if ($(this).is(':checked'))
			$(".selectalltarget").attr('checked', true);
		else
			$(".selectalltarget").attr('checked', false);
	});
	
	$('.selectalltarget').click( function () {
		CheckIfAllSelected();
	});
});