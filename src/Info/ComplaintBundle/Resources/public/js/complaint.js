var Complaint = {
    showHistory: function(complaintId) {
        var historyModal = $('#reportModal');
        historyModal.removeData('bs.modal');
        historyModal.modal({remote: Routing.generate('info_complaint_history', {'complaint':complaintId}) });
        historyModal.modal('show');
        return false;
    },

    removeComplaint : function (t, id, route) {
        if (!confirm("Вы уверены что хотите удалить отзыв?")) {
            return false;
        }
        if (route == 'info_complaint_complaint') {
            return true;
        }
        $.ajax({
            url: t.attr('href'),
            type: 'GET',
            success: function () {
                $('#complaint_' + id).hide(500, function () {
                    $(this).remove();
                });
                toastr.success("Отзыв удален");
            },
            error: function () {
                toastr.error("Ошибка при удалении отзыва");
            }
        });
        return false;
    }
};