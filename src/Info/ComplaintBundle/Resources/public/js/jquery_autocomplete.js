$(function(){
    $(function() {
        $("#company").autocomplete({
            source: Routing.generate("search_auto"),
            focus: function( event, ui ) {
                $( "#company" ).val( ui.item.label );
                return false;
            },
            search: function() {
                $( "#Complaint_company").val(null);
            },
            select: function( event, ui ) {
                $( "#company" ).val( ui.item.label );
                $( "#Complaint_company").val(ui.item.value);
                return false;
            }
        });
    });
});