$(function() {
    $( "#searching" ).autocomplete({
        source: Routing.generate("search_auto"),
        minLength: 	2,
        select: function( event, ui ) {
                window.location = Routing.generate(ui.item.route,{"id":ui.item.value})

        }
    });
});