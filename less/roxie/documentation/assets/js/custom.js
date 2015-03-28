/*------------------------------
 * Copyright 2014 Pixelized
 * http://www.pixelized.cz
 *
 * Documentation for themes
------------------------------*/

$(document).ready(function() {
	$('#menu-open').click(function(e) {
        $(this).toggleClass('show2 hidden');
		$('#menu-close').toggleClass('show2 hidden');
		$(".page-menu").toggleClass('page-menu-open');
		$(".page-content").toggleClass('menu-is-open');
    });
	
	$('#menu-close').click(function(e) {
        $(this).toggleClass('show2 hidden');
		$('#menu-open').toggleClass('show2 hidden');
		$(".page-menu").toggleClass('page-menu-open');
		$(".page-content").toggleClass('menu-is-open');
    });
		
	$('.dropdown').on('show.bs.dropdown', function(e){
		$(this).find('.dropdown-menu').first().stop(true, true).slideDown();
	});
	
	$('.dropdown').on('hide.bs.dropdown', function(e){
		$(this).find('.dropdown-menu').first().stop(true, true).slideUp();
	});
	
	$("a.scroll[href^='#']").on('click', function(e) {
		e.preventDefault();
		var hash = this.hash;
		$('html, body').animate({ scrollTop: $(this.hash).offset().top - 70}, 700, function(){window.location.hash = hash;});
	});
	
	//MAGNIFIC POPUP
	$('.show-image').magnificPopup({type:'image'});
});



