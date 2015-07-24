var Report = {
    obj : {
        type: '',
        typeId: ''
    },

    showForm : function(type, typeId) {
        Report.obj.type = type;
        Report.obj.typeId = typeId;
        var $reportModal = $('#reportModal');
        $reportModal.removeData('bs.modal');
        $reportModal.modal({remote: Routing.generate('info_report_getForm') });
        $reportModal.modal('show');
        $reportModal.on('hidden.bs.modal', function () {
            Report.obj.type = null;
            Report.obj.typeId = null;
        });

    },

    send: function(t) {
        $.ajax({
            url: Routing.generate('info_report_save', {'type':Report.obj.type, 'typeId': Report.obj.typeId}),
            type: 'POST',
            data: t.serialize(),
            async: true,
            dataType: "json",
            success: function () {
                $('#reportModal').modal('hide');
                toastr.success("Ваша жалоба получена, в скором времени его рассмотрят.");
            },
            error: function (xhr) {
                toastr.error("Ошибка при отправке жалобы, попробуйте позже.");
                console.log(xhr);
            }
        });

        return false;
    }
};