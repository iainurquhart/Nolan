$(document).ready(function() {

	var col_count = 100;
	
 	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		var col_count = ui.children().size();
		return ui;
	}; 

	var $container = $(".nolan_table tbody").roland();
	var opts = $.extend({}, $.fn.roland.defaults);

		$(".nolan_table tbody").sortable({
			helper: fixHelper, // fix widths
			handle: '.nolan_drag_handle',
			cursor: 'move',
			update: function(event, ui) { 
				$.fn.roland.updateIndexes($container, opts); 
			},
			'start': function (event, ui) {
		        ui.placeholder.html('<td colspan="100"></td>');
		    }
		});

	$(".nolan_table tbody").addClass('rolandified');

	
	Matrix.bind('nolan', 'display', function(cell){
	  
	 	var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
			var col_count = ui.children().size();
			return ui;
		};

		var $container = $(".nolan_table tbody").not('.rolandified').roland();
		var opts = $.extend({}, $.fn.roland.defaults);
		
		$(".nolan_table tbody").sortable({
			helper: fixHelper, // fix widths
			handle: '.nolan_drag_handle',
			cursor: 'move',
			update: function(event, ui) { 
				$.fn.roland.updateIndexes($container, opts); 
			},
			'start': function (event, ui) {
		        ui.placeholder.html('<td colspan="'+col_count+'"></td>');
		    }
		});
		
		$(".nolan_table tbody").addClass('rolandified');

	});
	

});