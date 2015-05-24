var Vote = {

    sendVote: function (type, id, voteType) {
        var url =Routing.generate('info_vote', {'type':type,'id': id, 'voteType': voteType});

        $.getJSON(url)
            .done(function (data) {
                if (type == 'complaint') {
                    idValue = 'vote-complaint' + id;
                    $("#" + idValue).html(data.voteValue);
                } else {
                    idValue = 'vote-comment' + id;
                    $("#" + idValue).html(data.voteValue);
                }
            }).fail(function(data){
                toastr.error(data.responseJSON.error);
            });
    }

};

