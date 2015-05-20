$('label.tree-toggler').click(function () {
    $(this).parent().children('ul.tree').toggle(300);
});

$('.be_manager').click(function(){
   url = $(this).prop('href');
    $.post(url,
        function(data)
        {
            toastr.info(data.message);
        }
    ).fail(function(data){
            toastr.error(data.responseJSON.message);
        });
    return false;
});

$('.gui-folder').mouseenter(function() {
    $(this).addClass('expanded')
});

$('.gui-folder').mouseleave(function() {
    $(this).removeClass('expanded')
});

$('.share-popover').popover({
    html : true,
    content: function() {
        var id = $(this).data('id');
        return $('#ya-share-'+id).html();
    }
});

$(":input").inputmask();