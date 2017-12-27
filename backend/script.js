$(function() {
	$( "#sbb_sortable" ).sortable();
	$( "#sbb_sortable" ).disableSelection();

	autocomplete_url = "http://transport.opendata.ch/v1/locations";

	$( "#sbb_station" ).autocomplete({
			source: function (request, response) {
				$.ajax({
					url: autocomplete_url,
					data: "query=" + request.term,
					success: function (data) {
						stations = [];

						$.each(data.stations, function(index, el) {
							stations.push(el.name);
						});

						response(stations);
					}
				});
			},
      minLength: 3
    });

});

$('#sbb__edit').click(function() {

	var success = true;
	cols = [];
	cols_width = [];

	$.each($("[name='cols[]']:checked"), function(el){
		cols.push($(this).val());

		var width = $(this).nextAll('input').last().val();
		if (width == ""){ width = 10}
		cols_width.push(width);
		$(this).nextAll('input').last().val(width);
	});

	$.each($("[name='cols[]']:not(:checked)"), function(el){
		$(this).nextAll('input').last().val("");
	});

	$.post('setConfigValueAjax.php', {'key': 'sbb_station', 'value': $("#sbb_station").val()}).fail(function(){ success = false });
	$.post('setConfigValueAjax.php', {'key': 'sbb_limit', 'value': $("#sbb_limit").val()}).fail(function(){ success = false });
	$.post('setConfigValueAjax.php', {'key': 'sbb_time_to_station', 'value': $("#sbb_time_to_station").val()}).fail(function(){ success = false });
	$.post('setConfigValueAjax.php', {'key': 'sbb_cols', 'value': cols.join(",")}).fail(function(){ success = false });
	$.post('setConfigValueAjax.php', {'key': 'sbb_cols_width', 'value': cols_width.join(",")}).fail(function(){ success = false });
	$.post('setConfigValueAjax.php', {'key': 'reload', 'value': 1 }).fail(function(){ success = false });
	//$.post('setConfigValueAjax.php', {'key': 'sbb_categories', 'value': $("#categories").val()}).fail(function(){ success = false });
	//$.post('setConfigValueAjax.php', {'key': 'sbb_lines', 'value': $("#lines").val()}).fail(function(){ success = false });

	if (success){
		$('#ok').show(30, function() {
	 		$(this).hide('slow');
	 	});
	} else {
		$('#error').show(30, function() {
			$(this).hide('slow');
		});
	}
});
