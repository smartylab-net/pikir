$(function () {
    $(document).on('click', '.btn-reply-comment', function (e) {
        var commentId = $(this).data('comment');
        var complaintId = $(this).data('complaint');

        var link = Routing.generate('info_comment_reply', {
            'complaint': complaintId,
            'comment': commentId
        });
        Comment.showForm(commentId, Comment.obj.type_reply, link);
    });

    $(document).on('click','.btn-cancel-form',function(e) {
        $(this).parents('.comment-form-block').toggle('fast');
        return false;
    });
});

var Comment = {
    obj : {
        type_main: 'main-comment-form',
        type_reply: 'reply',
        type_edit: 'edit'
    },

    showForm : function(commentId, type, link) {
        var formBlock = $('#form-block-' + commentId);
        if (formBlock.is(':empty')) {
            formBlock.hide();

            var result = tmpl("reply_form", {id: commentId, type: type, link: link});
            formBlock.append(result);
            formBlock.find('textarea.autosize').autosize({append: ''});
        } else {
            formBlock.find('textarea').val('');
        }
        if (type == Comment.obj.type_reply) {
            formBlock.find('button').html('Ответить').val('Ответить');
        } else if(type == Comment.obj.type_edit) {
            formBlock.find('button').html('Сохранить').val('Сохранить');
        }
        formBlock.show('fast');
    },

    replyOnSuccess: function (form, data) {
        var formBlock = form.parents('.comment-form-block');
        var list = formBlock.parent('li').children('ul.sub-comments');
        if (list.length == 0) {
            list = formBlock.parent().append($('<ul/>', {class: 'sub-comments'})).find('ul.sub-comments');
        }
        var newComment = $('<li/>', {class: 'sub-li-comments'}).html(data);
        list.append(newComment);
        formBlock.hide('fast');
        newComment.show('fast');
    },

    mainFormOnSuccess: function (form, data) {
        var list = $('.list-comments'),
            textarea = form.find('textarea'),
            newComment = $('<li/>', {class:'sub-li-comments'}).html(data);
        newComment.hide();
        list.append(newComment);
        newComment.show('fast');
        textarea.val('').css('height', 0);
    },

    editOnSuccess: function (commentId, comment) {
        $('#comment_body_'+commentId).empty().html(comment);
        $('#form-block-'+commentId).hide('fast');
        var commentTitleBlock = $('#comment_' + commentId).find('.comment-title');
        if (commentTitleBlock.find('.edited').length == 0) {
            commentTitleBlock.append($('<small/>').html(' · ')).append($('<small/>',{class:'edited', 'onclick':'return Comment.showHistory('+commentId+')'}).html('отредактировано'));
        }
        toastr.success("Комментарий изменен.");
    },

    sendComment : function (form) {
        var comment = form.find('textarea').val().trim(),
            type = form.data('type'),
            commentId = form.data('id');
        if (comment) {
            Auth.showLoginFormAndRefreshToken('comment', form, {
                    always: function () {
                        $.ajax({
                            url: form.attr('action'),
                            type: 'POST',
                            data: form.serialize(),
                            async: true,
                            success: function (data) {
                                if (type == Comment.obj.type_reply) {
                                    Comment.replyOnSuccess(form, data);
                                } else if (type == Comment.obj.type_edit) {
                                    Comment.editOnSuccess(commentId, comment);
                                } else if (type == Comment.obj.type_main) {
                                    Comment.mainFormOnSuccess(form, data);
                                }
                            },
                            error: function (xhr) {
                                toastr.error(xhr.responseJSON.msg);
                                console.log(xhr);
                            }
                        });
                    }
                }
            );
        } else {
            toastr.error("Комментарий не может быть пустым")
        }
        return false;
    },

    editComment : function(commentId) {
        Comment.showForm(commentId, Comment.obj.type_edit, Routing.generate('info_comment_edit', {'comment':commentId}));
        var formBlock = $('#form-block-' + commentId);
        formBlock.find('textarea').focus().val($('#comment_body_'+commentId).html().trim());
    },

    removeComment: function(commentId) {
        $.ajax({
            url: Routing.generate('info_comment_delete', {'comment':commentId}),
            dataType: "json",
            success: function (data) {
                var commentBlock = $('#comment_' + commentId);
                if (data.remove) {
                    commentBlock.remove();
                } else {
                    commentBlock.find('.card-head').remove();
                    commentBlock.find('.btn-reply-comment').remove();
                    $('#comment_body_'+commentId).empty().html('<span class="text-default-light">Комментарий был удален.</span>');
                }
                toastr.success("Комментарий удален.");
            },
            error: function (xhr) {
                toastr.error("Ошибка при удалении, попробуйте позже.");
                console.log(xhr);
            }
        });

        return false;
    },

    showHistory: function(commentId) {
        var historyModal = $('#reportModal');
        historyModal.removeData('bs.modal');
        historyModal.modal({remote: Routing.generate('info_comment_history', {'comment':commentId}) });
        historyModal.modal('show');
        return false;
    }
};