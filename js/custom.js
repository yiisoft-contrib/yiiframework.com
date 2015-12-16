
$(document).ready(function() {
	//SCROLLING
	$("a[href^='#']").on('click', function(e) {
		e.preventDefault();
		var hash = this.hash;
		$('html, body').animate({ scrollTop: $(this.hash).offset().top }, 250, function(){window.location.hash = hash;});
	});

	function fader() {
	  var r = $('.blurred'),
	    wh = $(window).height(),
	    dt = $(document).scrollTop(),
	    elView, opacity;

	  // Loop elements with class "blurred"
	  r.each(function() {
	    elView = wh - ($(this).offset().top - dt + 700);
	    if (elView > 0) { // Top of DIV above bottom of window.
	      opacity = 1 / (wh + $(this).height()) * elView * 4
	      if (opacity < 1) // Bottom of DIV below top of window.
	        $(this).css('opacity', opacity);
	    }
	  });
	}

	$(document).bind('scroll', fader);

});
