$(function($) {
    $('[data-star-url]').on('click', function() {
        var el = $(this);
        $.ajax({
            url: el.data('star-url'),
            method: 'post',
            dataType: 'json',
            success: function(data) {
                el.removeClass('icon-star icon-star-empty');
                if (data['star'] == 1) {
                    el.addClass('icon-star');
                } else {
                    el.addClass('icon-star-empty');
                }
                var count = el.parent().find('.star-count');
                if (count) {
                    count.text(data['starCount']);
                }
            },
            error: function() {
                // TODO redirect when user is not logged in
                alert('Error: failed to bookmark item.'); // TODO make this nicer
            }
        });
    });
});
