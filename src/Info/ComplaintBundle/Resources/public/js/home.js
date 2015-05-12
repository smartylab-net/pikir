$(document).ready(function() {
    var $loading = $("#loader");

    var infinite = new Waypoint.Infinite({
        element: $('.timeline')[0],
        items: '.complaint-item',
        onBeforePageLoad: function() {
            $loading.show();
        },
        onAfterPageLoad: function() {
            $loading.hide();
        }
    })
});