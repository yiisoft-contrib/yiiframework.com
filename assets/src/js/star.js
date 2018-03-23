$(function($) {
    $('[data-star-url]').on('click', function() {
        var el = $(this);
        el.addClass('fa-spin');
        $.ajax({
            url: el.data('star-url'),
            method: 'post',
            dataType: 'json',
            success: function(data) {
                el.removeClass('fa-star fa-star-o');
                setTimeout(function() { el.removeClass('fa-spin'); }, 300);
                if (data['star'] == 1) {
                    el.addClass('fa-star');
                } else {
                    el.addClass('fa-star-o');
                }
                var count = el.parent().find('.star-count');
                if (count) {
                    count.text(data['starCount']);
                }
            },
            error: function() {}
        });
    });
});
