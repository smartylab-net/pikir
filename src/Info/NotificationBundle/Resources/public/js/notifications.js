try {
    var autobahn = require('autobahn');
} catch (e) {
    console.log(e);
    // when running in browser, AutobahnJS will
    // be included without a module system
}
$(function () {
    var notifications = {

        original: document.title,
        interval: null,
        _step: function (firstMessage, secondMessage, howManyTimes) {
            document.title = (document.title == secondMessage) ? firstMessage : secondMessage;

            if (!isNaN(howManyTimes) && --howManyTimes == 0) {
                this.cancelFlashTitle();
            }
        },

        flashTitle: function (firstMessage, secondMessage, howManyTimes) {

            howManyTimes = parseInt(howManyTimes);
            var that = this;
            this.cancelFlashTitle(this.interval);
            this.interval = setInterval(function() {
                that._step(firstMessage, secondMessage, howManyTimes);
            }, 1000);
        },

        cancelFlashTitle: function (title) {
            clearInterval(this.interval);
            document.title = title;
        }
    };


    var connection = new autobahn.Connection({
            url: settings.notification_url,
            realm: settings.notification_realm
        }
    );

    connection.onopen = function (session) {

        function onReceiveNotification(args) {
            $.get(Routing.generate("info_notification_show", {'notification': args[0]}))
                .success(function (data) {
                    var notificationBadge = $('#notification-count');
                    var notificationCount = parseInt(notificationBadge.html());
                    notificationBadge.html(notificationCount + 1);
                    if (notificationCount == 0) {
                        notificationBadge.show("fast");
                    }
                    $("<li/>").html(data).insertAfter("#notification-header");
                    var firstMessage = "(" + (notificationCount + 1) + ") " + notifications.original;
                    notifications.flashTitle(firstMessage, "Новое уведомление");
                    $(window).focus(function() {
                        notifications.cancelFlashTitle(firstMessage);
                    });
                });
        }

        session.subscribe(settings.notification_prefix + 'pikir.notification' + settings.user_id, onReceiveNotification);
    };

    connection.open();

    $("#notification-mark-as-read").click(function () {
        $.get($(this).attr('href'))
            .success(function (data) {
                $('.notification').addClass('read-notification');
                var notificationBadge = $('#notification-count');
                notificationBadge.html(0);
                notificationBadge.hide('fast');
                notifications.cancelFlashTitle(notifications.original);
            }).error(function (xhr) {
                toastr.error("Неизвестная ошибка, попробуйте через некоторое время")
            });
        return false;
    });
});
