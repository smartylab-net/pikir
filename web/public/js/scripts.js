var Vote = {

    sendVote: function (type, id, voteType) {
        var url;
        if (type == 'complaint') {
            url = Routing.generate('info_complaint_vote', {'complaint': id, 'voteType': voteType});
        } else {
            url = Routing.generate('info_comment_vote', {'comment': id, 'voteType': voteType});
        }

        $.getJSON(url, function (data) {

            if (!data.error) {
                if (type == 'complaint') {
                    idValue = 'vote-complaint' + id;
                    $("#" + idValue).html(data.voteValue);
                } else {
                    idValue = 'vote-comment' + id;
                    $("#" + idValue).html(data.voteValue);
                }

            } else {
                toastr.error(data.errorType);
            }

        });
    }

};

