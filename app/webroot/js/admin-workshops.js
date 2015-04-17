var $agendaIndex = 0;

$(function() {
	$('#add-agenda-item-link').click(function() {
		AddAgendaItem();
	});
});

function AddAgendaItem()
{
	$.post("get_agenda_item", { agendaIndex: ++$agendaIndex }, function(data) { $('#WorkshopAddForm table').append(data); });
}