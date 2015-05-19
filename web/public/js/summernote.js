var Summernote = {
    sendFile: function (files, editor, welEditable) {
        var file = files[0];
        var data = new FormData();
        data.append("file", file);
        $.ajax({
            data: data,
            type: "POST",
            url: Routing.generate('upload_image'),
            cache: false,
            contentType: false,
            processData: false,
            success: function (url) {
                editor.insertImage(welEditable, url);
            },
            fail: function (xhr) {
                toastr.error("Ошибка при загрузке изображения");
            }
        });
    }
};