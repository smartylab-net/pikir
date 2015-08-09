$(function () {
    $('.card-head .tools .btn-collapse').on('click', function (e) {
        var card = $(e.currentTarget).closest('.card');
        materialadmin.AppCard.toggleCardCollapse(card);
    });

    $('#complaint-form form').on('submit', function(e) {
        e.preventDefault();
        var  form = $(this);
        Auth.showLoginFormAndRefreshToken('Complaint', form, {
            always: function() {
                $.post(form.attr('action'), form.serialize())
                    .done(function (data) {
                        if (data.complaint) {
                            var list = $("#company_complaints");
                            var newComplaint = $('<li/>').html(data.complaint).hide();
                            list.prepend(newComplaint);
                            form.find('#Complaint_text').val('');
                            form.find('.note-editable').empty();
                            newComplaint.show(500);
                            toastr.success("Ваш отзыв добавлен");
                            $('#files').empty();
                            $('#progress').hide();
                        } else {
                            toastr.error("Ошибка при добавлении отзыва");
                        }
                    });
            }
        })
    });
});