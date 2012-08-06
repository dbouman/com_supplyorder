// ****************************************************************************
// * Javascript code used for all tables that list data and allow for sorting *
// ****************************************************************************
jQuery(document).ready(function() {
		
	// Alternate table row color
	jQuery('.so_table tr:even').addClass('alt'); 
	
	// Handles highlighting row when box is checked
	jQuery(".so_table").delegate('input[id^="request"]','click', function(e) {
		jQuery(this).parents("tr").toggleClass("selected_color");
	});

	jQuery("a.popup").fancybox({
		fitToView	: false,
		width		: 830,
		height		: 600,
		autoSize	: false,
		closeClick	: false,
		scrolling	: 'no',
		openEffect	: 'elastic',
		closeEffect	: 'elastic',
		openSpeed	: 'normal', 
		closeSpeed	: 'normal'
	});
	
  });

function tableOrdering( order, dir, task )
{
	var form = document.requested_requests;
 
	form.filter_order.value = order;
	form.filter_order_dir.value = dir;
	document.requested_requests.submit( task );
}