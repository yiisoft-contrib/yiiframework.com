/*------------------------------
 * Copyright 2014 Pixelized
 * http://www.pixelized.cz
 *
 * Roxie theme v1.1
------------------------------*/

$(document).ready(function() {
	//SCROLLING
	$("a[href^='#']").on('click', function(e) {
		e.preventDefault();
		var hash = this.hash;
		$('html, body').animate({ scrollTop: $(this.hash).offset().top }, 250, function(){window.location.hash = hash;});
	});

	//TOOLTIP
	$('.tooltip-init').tooltip();

	//POPOVER
	$('.popover-init').popover();

});
