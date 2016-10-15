
$(document).ready(function() {
    //SCROLLING
    $("a[href^='#']").on('click', function(e) {
        e.preventDefault();
        var hash = this.hash;
        if (hash != '') {
            $('html, body').animate({scrollTop: $(this.hash).offset().top}, 250, function () {
                window.location.hash = hash;
            });
        }
    });

    $(function () {
      $.scrollUp({
          scrollName: 'scrollUp', // Element ID
          scrollDistance: 140, // Distance from top/bottom before showing element (px)
          scrollFrom: 'top', // 'top' or 'bottom'
          scrollSpeed: 300, // Speed back to top (ms)
          easingType: 'linear', // Scroll to top easing (see http://easings.net/)
          animation: 'fade', // Fade, slide, none
          animationInSpeed: 200, // Animation in speed (ms)
          animationOutSpeed: 200, // Animation out speed (ms)
          scrollText: 'Scroll to top', // Text for element, can contain HTML
          scrollTitle: false, // Set a custom <a> title if required. Defaults to scrollText
          scrollImg: true, // Set true to use image
          activeOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
          zIndex: 2147483647 // Z-Index for the overlay
      });
  });

});
