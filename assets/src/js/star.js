$(function($) {
    $('[data-star-url]').on('click', function() {
        var el = $(this);
        $.ajax({
            url: el.data('star-url'),
            method: 'post',
            dataType: 'json',
            success: function(data) {
                var wrapper = el.parents('.star-wrapper');

                el.removeClass('icon-star icon-star-empty');
                if (data['star'] == 1) {
                    el.addClass('icon-star');
                } else {
                    el.addClass('icon-star-empty');
                }
            },
            error: function() {
                location.href = '/login';
            }
        });
    });
});
