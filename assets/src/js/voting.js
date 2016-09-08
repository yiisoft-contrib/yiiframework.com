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
                voting.children('.votes-up').children('.votes').html(data.up);
                voting.children('.votes-down').children('.votes').html(data.down);
            },
            error: function() {
                // TODO redirect when user is not logged in
                alert('Error: failed to cast vote.'); // TODO make this nicer
            }
        });


        return false;
    });

});