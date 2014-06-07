/**
 * Created by User on 25.05.14.
 */
$('label.tree-toggler').click(function () {
    $(this).parent().children('ul.tree').toggle(300);
});

$('.be_manager').click(function(){
   url = $(this).prop('href');
    $.post(url,
        function(data)
        {
            alert(data.message);
        }
    ).fail(function(data){
            alert(data.responseJSON.message);
        });
    return false;
});