$(function () {
    $(document).on('click', '.btn-reply-comment', function (e) {
        var commentId = $(this).data('comment');
        var complaintId = $(this).data('complaint');

        var formBlock = $('#form-block-' + commentId);
        if (formBlock.is(':empty')) {
            formBlock.hide();

            var link = Routing.generate('info_comment_reply', {
                'complaint': complaintId,
                'comment': commentId
            });
            var result = tmpl("reply_form", {id: commentId, link: link});
            formBlock.append(result);
            formBlock.find('textarea.autosize').autosize({append: ''});
        }
        formBlock.toggle('fast');
    });

    $(document).on('submit','.comment-form-block form',function (e) {
        var comment = $(this).find('textarea').val();
        if (comment.trim()) {
            var formBlock = $(this).parents('.comment-form-block');
            $.post($(this).attr('action'), $(this).serialize()).done(function (data) {
                var list = formBlock.parent().children('ul');
                if (list.length == 0) {
                    list = formBlock.parent().append('<ul/>').find('ul');
                }
                var newComment = $('<li/>').html(data);
                newComment.hide();
                list.append(newComment);
                formBlock.hide('fast');
                formBlock.empty();
                newComment.show('fast');
            });
        }
        else {
            toastr.error("Комментарий не может быть пустым")
        }
        return false;
    });

    $(document).on('click','.btn-cancel-form',function(e) {
        $(this).parents('.comment-form-block').toggle('fast');
        return false;
    });

    $('#comment-form').submit(function(e){

        var textarea = $(this).find('textarea');
        var comment = textarea.val();
        if (comment.trim()) {
            $.post($(this).attr('action'), $(this).serialize()).done(function (data) {
                var list = $('.list-comments');
                var newComment = $('<li/>').html(data);
                newComment.hide();
                list.append(newComment);
                newComment.show('fast');
                textarea.val('').css('height', 0);
            });
        }
        else {
            toastr.error("Комментарий не может быть пустым")
        }
        return false;
    });
});