$(document).ready(function() {
    var prBar = $('#progress .progress-bar');
    $('#fileupload').fileupload({
        url: '{{ oneup_uploader_endpoint("gallery") }}',
        dataType: 'json',
        limitMultiFileUploads: 4,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 4000000, // 4 MB
        disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator.userAgent)
    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        prBar.css('width', progress + '%');
    }).on('fileuploadstart', function (e) {
        $('#progress').show();
        prBar.addClass('progress-bar-success').removeClass('progress-bar-danger');
    }).on('fileuploaddone', function (event, data) {
        $.each(data.files, function (index, file) {
            $('<div class="box">').appendTo('#files');
            $('.box').last().html('<span>'+file.name+'</span>');
        });
    }).on('fileuploadfail', function (event, data) {
        prBar.removeClass('progress-bar-success').addClass('progress-bar-danger').css('width', '100%');
    }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
});