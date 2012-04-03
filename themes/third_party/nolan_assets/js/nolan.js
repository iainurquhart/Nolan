$(document).ready(function() {
	
 	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
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
			}
		});

	$(".nolan_table tbody").addClass('rolandified');

	
	Matrix.bind('nolan', 'display', function(cell){
	  
	 	var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
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
			}
		});
		
		$(".nolan_table tbody").addClass('rolandified');

	});
	

});