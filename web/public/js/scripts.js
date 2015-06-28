$(document).on('click', '.vote', function(e) {

    var parent = $(this).parent();
    var self = $(this);
    var url =Routing.generate('info_vote', {
        'type':$(this).attr('data-type'),
        'id': $(this).attr('data-id'),
        'voteType': $(this).attr('data-votetype')
    });

    $.getJSON(url)
        .done(function (data) {
            parent.find('.vote-value').html(data.voteValue);
            parent.find('.vote').removeClass('text-primary');
            self.addClass('text-primary');
        }).fail(function(data){
            toastr.error(data.responseJSON.error);
        });
});
