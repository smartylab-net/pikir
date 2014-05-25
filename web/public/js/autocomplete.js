$(function() {
    $( "#searching" ).autocomplete({
        source: "http://localhost/hackaton/web/app_dev.php/autocomplete/",
        minLength: 	2,
        select: function( event, ui ) {
                window.location = Routing.generate(ui.item.route,{"id":ui.item.value})

        }
    });
});