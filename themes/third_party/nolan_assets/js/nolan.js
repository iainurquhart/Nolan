$(document).ready(function() {

	var col_count = 100;

		var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		var col_count = ui.children().size();
		return ui;
	}
	
	function setup_nolan(cell, type) {

		if(type == 'grid')
		{
			container = cell.find(".nolan_table tbody").not('.roland-on-the-ropes').roland();
		}else if(type == 'matrix'){
			container = $(".matrix .nolan_table tbody").not('.roland-on-the-ropes').roland();
		}else{
			container = $("tbody.native_nolan").not('.roland-on-the-ropes').roland();
		}

		opts = $.extend({}, $.fn.roland.defaults);

		container.sortable({
			helper: fixHelper, // fix widths
			handle: '.nolan_drag_handle',
			axis:'y',
			cursor: 'move',
			update: function(event, ui) { 
				$.fn.roland.updateIndexes(container, opts); 
			},
			'start': function (event, ui) {
		        ui.placeholder.html('<td colspan="'+col_count+'"></td>');
		    }
		});
	
		container.addClass('roland-on-the-ropes');
		
	}


	setup_nolan(null, 'native');

 
	if (typeof Grid != 'undefined') {
		Grid.bind("nolan", "display", function(cell){
			setup_nolan(cell, 'grid')
		});
	}
	if (typeof Matrix != 'undefined') {
		Matrix.bind('nolan', 'display', function(cell){
		 	setup_nolan(cell, 'matrix')
		});
	}

	

});