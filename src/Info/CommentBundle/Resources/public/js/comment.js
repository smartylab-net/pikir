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
            $(formBlock).find('textarea').summernote(
                {
                    onImageUpload: Summernote.sendFile
                });
        }
        formBlock.toggle('fast');
    });

    $(document).on('submit','.comment-form-block form',function (e) {
        var code = $(this).find('textarea').code();
        if (code) {
            var formBlock = $(this).parents('.comment-form-block');
            $(this).find('textarea').val(code);
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

        var code = $(this).find('textarea').code();
        if (code) {
            var formBlock = $(this).parents('.card');
            $(this).find('textarea').val(code);

            $.post($(this).attr('action'), $(this).serialize()).done(function (data) {
                var list = $('<ul/>').addClass("list-comments").insertBefore(formBlock);
                var newComment = $('<li/>').html(data);
                newComment.hide();
                list.append(newComment);
                $('#comment_comment').code("");
                newComment.show('fast');
            });
        }
        else {
            toastr.error("Комментарий не может быть пустым")
        }
        return false;
    });

    $('#comment_comment').summernote(
        {
            onImageUpload: Summernote.sendFile
        });
});