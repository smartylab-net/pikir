/**
 * Created by User on 25.05.14.
 */
$('label.tree-toggler').click(function () {
    $(this).parent().children('ul.tree').toggle(300);
});