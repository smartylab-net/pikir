$(document).ready(function() {
    var prBar = $('#progress .progress-bar');
    $('#fileupload').fileupload({
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
        var boxCount = $('.box').size();
        if (boxCount % 4 == 0) {
            $('<div class="card"/>').html($('<div class="card-body"/>')).appendTo('#files');
        }
        var box = $('<div class="box">').appendTo($('#files .card').last().find('.card-body'));
        $('<img class="col-lg-3"/>').attr('src', data.result.filepath).appendTo(box);
    }).on('fileuploadfail', function (event, data) {
        prBar.removeClass('progress-bar-success').addClass('progress-bar-danger').css('width', '100%');
    }).on('fileuploadcompleted', function (event, data) {
        $('#progress').hide();
    })
        .prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
});