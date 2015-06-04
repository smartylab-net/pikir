try {
    var autobahn = require('autobahn');
} catch (e) {
    // when running in browser, AutobahnJS will
    // be included without a module system
}

var connection = new autobahn.Connection({
        url: 'ws://127.0.0.1:3000',
        realm: 'realm'
    }
);

connection.onopen = function (session) {

    function onevent1(args) {
        console.log("new message: "+args[0]);
    }

    session.subscribe('pikir.notification' + settings.user_id, onevent1);
};

connection.open();