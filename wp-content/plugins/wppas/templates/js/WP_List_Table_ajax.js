jQuery(document).ready(function($) {
	var timer;
	var delay = 500;
	
	//$("body").on("click", "#month-stats-filter .tablenav-pages a, #month-stats-filter .manage-column.sortable a, #month-stats-filter .manage-column.sorted a", function(e){
	$("body").on("click", ".stats-filter .tablenav-pages a, .stats-filter .manage-column.sortable a, .stats-filter .manage-column.sorted a", function(e){
		
		// We don't want to actually follow these links
		e.preventDefault();
		
		var query = this.search.substring( 1 );
		var group = $('#stats_group').val() != '' ? $('#stats_group').val() : '';
 		var group_id = $('#stats_group_id').val() != '' ? $('#stats_group_id').val() : '';
		
		var data = {
			paged: __query( query, 'paged' ) || '1',
			order: __query( query, 'order' ) || 'asc',
			orderby: __query( query, 'orderby' ) || 'title',
			day: $('.stats-filter').attr('day'),
			month: $('.stats-filter').attr('month'),
			year: $('.stats-filter').attr('year'),
			type: $('.stats-filter').attr('type'),
			group: group,
 			group_id: group_id
		};
		
		update_list( data );
	});
	
	// Page number input
	$("body").on('keyup', '.stats-filter input[name=paged]', function(e) {
	//$('#day-stats-filter input[name=paged]').on('keyup', function(e) {
		// If user hit enter, we don't want to submit the form
		// We don't preventDefault() for all keys because it would
		// also prevent to get the page number!
		if ( 13 == e.which )
			e.preventDefault();
		// This time we fetch the variables in inputs
		var data = {
			paged: parseInt( $('.stats-filter input[name=paged]').val() ) || '1',
			order: $('.stats-filter input[name=order]').val() || 'asc',
			orderby: $('.stats-filter input[name=orderby]').val() || 'title'
		};
		// Now the timer comes to use: we wait half a second after
		// the user stopped typing to actually send the call. If
		// we don't, the keyup event will trigger instantly and
		// thus may cause duplicate calls before sending the intended
		// value
		
		window.clearTimeout( timer );
		timer = window.setTimeout(function() {
			update_list( data );
		}, delay);
	});
	
	
	function update_list( data ){
		
		var range = $('.stats-filter').attr('range');
			
		$.ajax({
		   type: "POST",
		   url: ajaxurl,
		   //data: "action=_ajax_fetch_custom_list_day&_ajax_custom_list_nonce="+$('#_ajax_custom_list_nonce').val()+"&paged="+ paged,
		   data: $.extend(
				{
					_ajax_custom_list_nonce: $('#_ajax_custom_list_nonce').val(),
					action: '_ajax_fetch_custom_list_'+range,
				},
				data
		   )
		}).done(function( msg ) {
		   //success: function( msg ){
				//alert(msg)
				var response = $.parseJSON( msg );
				
				// Add the requested rows
				if ( response.rows.length )
					$('#the-list').html( response.rows );
				// Update column headers for sorting
				if ( response.column_headers.length )
					$('thead tr, tfoot tr').html( response.column_headers );
				// Update pagination for navigation
				if ( response.pagination.bottom.length )
					$('.tablenav.top .tablenav-pages').html( $(response.pagination.top).html() );
				if ( response.pagination.top.length )
					$('.tablenav.bottom .tablenav-pages').html( $(response.pagination.bottom).html() );
		   //}
		});
	}// end update list
	
});







__query = function( query, variable ) {
	var vars = query.split("&");
	for ( var i = 0; i <vars.length; i++ ) {
		var pair = vars[ i ].split("=");
		if ( pair[0] == variable )
			return pair[1];
	}
	return false;
}