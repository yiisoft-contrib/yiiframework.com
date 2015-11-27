/*----------------------------------------------------
 * Copyright 2014 Pixelized Studio
 * http://www.pixelized.cz
 * creative-brands.js
 *
 * Version 1.2
 * Released: 17.6.2014
 * http://creativecommons.org/licenses/by/2.0/
----------------------------------------------------*/

$(document).ready(function() {
	if($(window).width() > 767) {
		/* Brands-pulse */
		$('.brands-pulse li a').mouseenter(function() {$(this).find('i').addClass('animated pulse');});
		$('.brands-pulse li a').mouseleave(function() {$(this).find('i').removeClass('animated pulse');});
		
		/* Brands-bounce */
		$('.brands-bounce li a').mouseenter(function() {$(this).find('i').addClass('animated bounce');});
		$('.brands-bounce li a').mouseleave(function() {$(this).find('i').removeClass('animated bounce');});
		
		/* Brands-flash */
		$('.brands-flash li a').mouseenter(function() {$(this).find('i').addClass('animated flash');});
		$('.brands-flash li a').mouseleave(function() {$(this).find('i').removeClass('animated flash');});
		
		/* Brands-shake */
		$('.brands-shake li a').mouseenter(function() {$(this).find('i').addClass('animated shake');});
		$('.brands-shake li a').mouseleave(function() {$(this).find('i').removeClass('animated shake');});
		
		/* Brands-swing */
		$('.brands-swing li a').mouseenter(function() {$(this).find('i').addClass('animated swing');});
		$('.brands-swing li a').mouseleave(function() {$(this).find('i').removeClass('animated swing');});
		
		/* Brands-tada */
		$('.brands-tada li a').mouseenter(function() {$(this).find('i').addClass('animated tada');});
		$('.brands-tada li a').mouseleave(function() {$(this).find('i').removeClass('animated tada');});
		
		/* Brands-wobble */
		$('.brands-wobble li a').mouseenter(function() {$(this).find('i').addClass('animated wobble');});
		$('.brands-wobble li a').mouseleave(function() {$(this).find('i').removeClass('animated wobble');});
	}
});



