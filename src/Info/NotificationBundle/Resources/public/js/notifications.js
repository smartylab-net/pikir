try {
    var autobahn = require('autobahn');
} catch (e) {
    // when running in browser, AutobahnJS will
    // be included without a module system
}

var connection = new autobahn.Connection({
        url: settings.notification_url,
        realm: settings.notification_realm
    }
);

connection.onopen = function (session) {

    function onReceiveNotification(args) {
        $.get(Routing.generate("info_notification_show", {'notification': args[0]}))
            .success(function(data) {
                var notificationBadge = $('#notification-count');
                var notificationCount = parseInt(notificationBadge.html());
                notificationBadge.html(notificationCount + 1);
                if(notificationCount == 0) {
                    notificationBadge.show("fast");
                }
                $("<li/>").html(data).insertAfter( "#notification-header" );
            });
    }

    session.subscribe(settings.notification_prefix + 'pikir.notification' + settings.user_id, onReceiveNotification);
};

connection.open();

$("#notification-mark-as-read").click(function(){
    $.get($(this).attr('href'))
        .success(function(data) {
            $('.notification').addClass('read-notification');
            var notificationBadge = $('#notification-count');
            notificationBadge.html(0);
            notificationBadge.hide('fast');

        }).error(function(xhr) {
            toastr.error("Неизвестная ошибка, попробуйте через некоторое время")
        });
    return false;
});