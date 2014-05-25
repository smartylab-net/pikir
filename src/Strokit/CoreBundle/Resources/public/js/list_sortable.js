/**
 * Created by Admin on 16.01.14.
 */
jQuery(document).ready(function(){
    jQuery('div.sonata-ba-list table.table tbody').sortable({
        axis: 'y',
        opacity: 0.6,
        items: 'tr',
        stop: apply_position_value
    });

    function apply_position_value() {
        // update the input value position
        jQuery('div.sonata-ba-list table.table tbody td.sonata-ba-list-field-sortable').each(function(index, element) {
            // remove the sortable handler and put it back
            jQuery('span.sonata-ba-sortable-handler', element).remove();
            jQuery(element).append('<span class="sonata-ba-sortable-handler ui-icon ui-icon-grip-solid-horizontal"></span>');

            var link = jQuery(element).children('input').val();
            jQuery.ajax({
                url:link,
                data:{"value":index + 1},
                type:'POST',
                success:function(json){
                    jQuery('span.sonata-admin-list-value', element).html(index + 1);
                }
            });
        });
    }
});