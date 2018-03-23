$(function($) {

    $('.voting a').on('click', function() {

        var link = $(this);
        $.ajax({
            url: link.attr('data-vote-url'),
            method: 'post',
            dataType: "json",
            success: function(data) {

                console.log(data);

                var voting = link.parents('.voting');
                console.log(voting);
                voting.children('.votes-up').removeClass('voted').children('.votes').html(data.up);
                voting.children('.votes-down').removeClass('voted').children('.votes').html(data.down);
                if (data.userVote == 1) {
                    voting.children('.votes-up').addClass('voted');
                }
                if (data.userVote == 0) {
                    voting.children('.votes-down').addClass('voted');
                }
            },
            error: function() {}
        });


        return false;
    });

});