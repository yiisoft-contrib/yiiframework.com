/*------------------------------
 * Copyright 2014 Pixelized
 * http://www.pixelized.cz
 *
 * Roxie theme v1.1
------------------------------*/

//FIXED NAVBAR
$(window).scroll(function(){
	if($(window).width() > 991) {
		if($(window).scrollTop() > 50) {
			$('.navbar').addClass('navbar-offset');
			$('.navbar').removeClass('navbar-static-top');
			$('.navbar').addClass('navbar-fixed-top');
			$('body').css("padding-top","70px");
		}
		else {
			$('.navbar').removeClass('navbar-offset');
			$('.navbar').removeClass('navbar-fixed-top');
			$('.navbar').addClass('navbar-static-top');
			$('body').css("padding-top","0px");
		}
	}
	
	else {
		if($(window).scrollTop()) {
			$('.navbar').addClass('navbar-offset');
			$('.navbar').removeClass('navbar-static-top');
			$('.navbar').addClass('navbar-fixed-top');
			$('body').css("padding-top","40px");
		}
		else {
			$('.navbar').removeClass('navbar-offset');
			$('.navbar').removeClass('navbar-fixed-top');
			$('.navbar').addClass('navbar-static-top');
			$('body').css("padding-top","0px");
		}
	}
});

//TWITTER SHARE BUTTON
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");

$(document).ready(function() {	
	//NAVBAR
	$('.navbar-main-menu > li.dropdown').mouseenter(function() {
		$(this).addClass('open');
	});
	
	$('.navbar-main-menu > li.dropdown').mouseleave(function() {
		$(this).removeClass('open');
	});
	
	var minimum = 1250;
	var maximum = 1500;
	
	$( "#slider-range" ).slider({
      range: true,
      min: minimum,
      max: maximum,
      values: [ minimum, maximum ],
      slide: function( event, ui ) {
        $( "#amount" ).val( "$" + ui.values[ 0 ] );
		$( "#amount2" ).val( "$" + ui.values[ 1 ] );
      }
    });
    $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ));
	$( "#amount2" ).val( "$" + $( "#slider-range" ).slider( "values", 1 ));
		
	//SCROLLING
	$("a.scroll[href^='#']").on('click', function(e) {
		e.preventDefault();
		var hash = this.hash;
		$('html, body').animate({ scrollTop: $(this.hash).offset().top - 110}, 1000, function(){window.location.hash = hash;});
	});
	
	//TOOLTIP
	$('.tooltip-init').tooltip();
	
	//POPOVER
	$('.popover-init').popover();
	
	//PORTFOLIO - ISOTOPE
	var $container = $('.portfolio-wrapper');
	$container.isotope({
	  	itemSelector: '.portfolio-item',
	});
	
	$('.portfolio-filter li a').click(function(e) {
		$('.portfolio-filter li a').removeClass('active');
		$(this).addClass('active');
		
        var category = $(this).attr('data-filter');
		$container.isotope({
			filter: category
		});
    });
	
	//BLOG - ISOTOPE
	var $container2 = $('.blog-wrapper');
	$container2.isotope({
	  	itemSelector: '.blog-item',
	});
	
	$('.blog-filter li a').click(function(e) {
		$('.blog-filter li a').removeClass('active');
		$(this).addClass('active');
		
        var category = $(this).attr('data-filter');
		$container2.isotope({
			filter: category
		});
    });
	
	//FORM TOGGLE
	$('#reset-password-toggle').click(function() {
        $('#reset-password').slideToggle(500);
    });
	
	//ESHOP TOGGLE
	$(".addtocart").click(function() {
        $("#eshop-cart-alert").toggleClass("active");
    });
	
	$("#eshop-cart-alert .close").click(function() {
        $("#eshop-cart-alert").toggleClass("active");
    });
	
	$('#billing-address-toggle').click(function() {
        $('#billing-address').slideToggle(500);
    });	
		
	//MAGNIFIC POPUP
	$('.show-image').magnificPopup({type:'image'});
		
	//OWL CAROUSEL
	$("#section-partners #partners-slider").owlCarousel({
		autoPlay: 3000,
		pagination : false,
		items : 4,
		itemsDesktop : [1199,3],
		itemsDesktopSmall : [991,2]
  	});
		
	$("#jumbotron-slider").owlCarousel({
		autoPlay: 5000, 
		navigation : true,
		singleItem : true,
		pagination : false,
		transitionStyle : "fade",
		autoPlay: 5000,
		slideSpeed : 500,
		navigationText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"]
  	});
	
	$("#about-slider").owlCarousel({
		autoPlay: 5000, 
		singleItem : true
  	});
	
	$("#jumbotron-eshop-slider").owlCarousel({
		autoPlay: 5000, 
		navigation : true,
		singleItem : true,
		transitionStyle : "fade",
		navigationText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"]
  	});
	
	$("#eshop-slider").owlCarousel({
		autoPlay: 5000, 
		scrollPerPage : true,
		items : 3,
		itemsDesktop : [1199,3],
		itemsDesktopSmall : [991,2]
  	});
	
	$('#eshop-slider .item img').mouseenter(function(e) {
		var source = $(this).attr("src");
		$("#product-detail-image").attr("src",source);
		$("#product-detail-image-link").attr("href",source);
    });
	
	$("#portfolio-slider").owlCarousel({
		autoPlay: 5000, 
		navigation : true,
		singleItem : true,
		slideSpeed : 500,
		navigationText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"]
  	});
	
	$("#about-us-slider").owlCarousel({
		autoPlay: 5000, 
		singleItem : true,
		transitionStyle : "fade"
  	});
	
	$("#testimonials-slider").owlCarousel({
		autoPlay: 5000,
		singleItem : true,
		transitionStyle : "fadeUp"
  	});
	
	$("#features-default-carousel #owl-carousel-default").owlCarousel({
		autoPlay: 5000, 
		navigation : true,
		singleItem : true,
		slideSpeed : 500,
		navigationText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"]
  	});
	
	$("#features-fade-carousel #owl-carousel-fade").owlCarousel({
		autoPlay: 5000, 
		navigation : true,
		singleItem : true,
		transitionStyle : "fade",
		navigationText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"]
  	});
	
	$("#features-owl-carousel #carousel-wrapper").owlCarousel({
		autoPlay: 3000, 
		items : 4,
		itemsDesktop : [1199,3],
		itemsDesktopSmall : [991,2],
  	});
	
	//OWL CAROUSEL - HIDDEN CONTROLS
	$(".owl-carousel.hidden-control").mouseenter(function(e) {
		$(this).find(".owl-prev").animate({opacity:1,left:"20px"});
		$(this).find(".owl-next").animate({opacity:1,right:"20px"});
    });
	
	$(".owl-carousel.hidden-control").mouseleave(function(e) {
		$(this).find(".owl-prev").animate({opacity:0,left:"40px"});
		$(this).find(".owl-next").animate({opacity:0,right:"40px"});
    });
		
	//PARTNER BRANDS
	$('#partner_001').mouseenter(function() {$(this).attr("src","image/partner_001.jpg");});
	$('#partner_001').mouseleave(function() {$(this).attr("src","image/partner_001_bw.jpg");});
	$('#partner_002').mouseenter(function() {$(this).attr("src","image/partner_002.jpg");});
	$('#partner_002').mouseleave(function() {$(this).attr("src","image/partner_002_bw.jpg");});
	$('#partner_003').mouseenter(function() {$(this).attr("src","image/partner_003.jpg");});
	$('#partner_003').mouseleave(function() {$(this).attr("src","image/partner_003_bw.jpg");});
	$('#partner_004').mouseenter(function() {$(this).attr("src","image/partner_004.jpg");});
	$('#partner_004').mouseleave(function() {$(this).attr("src","image/partner_004_bw.jpg");});
	$('#partner_005').mouseenter(function() {$(this).attr("src","image/partner_005.jpg");});
	$('#partner_005').mouseleave(function() {$(this).attr("src","image/partner_005_bw.jpg");});
	$('#partner_006').mouseenter(function() {$(this).attr("src","image/partner_006.jpg");});
	$('#partner_006').mouseleave(function() {$(this).attr("src","image/partner_006_bw.jpg");});
	$('#partner_007').mouseenter(function() {$(this).attr("src","image/partner_007.jpg");});
	$('#partner_007').mouseleave(function() {$(this).attr("src","image/partner_007_bw.jpg");});
	$('#partner_008').mouseenter(function() {$(this).attr("src","image/partner_008.jpg");});
	$('#partner_008').mouseleave(function() {$(this).attr("src","image/partner_008_bw.jpg");});
	$('#partner_009').mouseenter(function() {$(this).attr("src","image/partner_009.jpg");});
	$('#partner_009').mouseleave(function() {$(this).attr("src","image/partner_009_bw.jpg");});	
	
	$('#section-statistics').waypoint(function(){
		$('#section-statistics .number').countTo();
		},{offset:'85%'}
	);
	
	//GOOGLE MAP
	var myLatlng = new google.maps.LatLng(40.710968,-74.0084713);
	var mapOptions = {
	  zoom: 17,
	  center: myLatlng,
	  navigationControl: false,
	  mapTypeControl: false,
	  scaleControl: false,
	  draggable: true,
	  scrollwheel: false
	}

	var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

	var marker = new google.maps.Marker({
		position: myLatlng,
		map: map,
		title:"Your Marker!"
	});
		
});
