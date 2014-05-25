$(function() {
    $( "#searching" ).autocomplete({
        source: Routing.generate("search_auto"),
        minLength: 	2,
        focus: function( event, ui ) {
            $( "#searching" ).val( ui.item.label );
            return false;
        },
        select: function( event, ui ) {
                window.location = Routing.generate(ui.item.route,{"id":ui.item.value})
            return false;

        }
    });
});