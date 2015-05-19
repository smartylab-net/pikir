$(function () {
    $('#Complaint_text').summernote(
        {
            oninit: function () {
                $('.note-editable').css('min-height', '150px');
            },
            onImageUpload: function (files, editor, welEditable) {
                sendFile(files[0], editor, welEditable);
            }
        });
    function sendFile(file, editor, welEditable) {
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
            fail: function(xhr) {
                toastr.error("Ошибка при загрузке изображения");
            }
        });
    }
});