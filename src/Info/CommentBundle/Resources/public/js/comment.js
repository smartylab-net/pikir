var Comment = {
    config : {

    },

    init : function(config){
        $.extend(Comment.config, config);
    },

    showForm : function(t){
        var formBlock = $('#form-block-'+ t.data('comment')), form, label, btn;
        if (formBlock.is(':empty')){
            label = $('<div/>').addClass('form-group')
                .append($('<div/>').addClass('col-md-2').append($('<label/>').addClass('required').html('Текст комментария')))
                .append($('<div/>').addClass('col-md-10').append($('<textarea/>').addClass('form-control').attr('placeholder', 'Комментарий').attr('required', 'required').attr('name', 'comment')));
            form = $("<form/>").addClass('form-horizontal hide mb-10').attr('action', Routing.generate('info_comment_reply', {'complaint':t.data('complaint'), 'comment':t.data('comment')})).attr('method', 'post');
            btn = $('<div/>').append($('<button/>').addClass('btn btn-primary btn-xs pull-right').attr('name', 'save').attr('type', 'submit').html('Ответить'));
            formBlock.append(form.append(label).append(btn).append($('<div/>').addClass('clear')));
        }else{
            form = formBlock.children('form');
        }
        if (t.hasClass('reply')){
            form.addClass('hide');
            t.removeClass('reply');
        }else{
            form.removeClass('hide');
            t.addClass('reply');
        }
    }
};