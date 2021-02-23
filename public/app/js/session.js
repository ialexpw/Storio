/**
 * Heartbeat singleton
 */
var Heart = {
    url:         'https://storio.uk', // server script to hit
    logging:     true, // log to console for debugging
    pulse:       300, // heartbeat interval in seconds
    maxTimeouts: 3, // max timeouts before "heart attack" (stop)
    sessionName: 'PHPSESSID', // session cookie name

    // leave these alone
    timeouts:    0,
    timer:       null,
    sessionId:   null,

    /**
     * Begin heartbeats
     */
    start: function() {
        Heart.log('Started');
        Heart.getSessionId();
        Heart.timer = setInterval(Heart.beat, Heart.pulse * 1000);
    },

    /**
     * Stop heartbeats
     */
    stop: function() {
        clearInterval(Heart.timer);
    },

    /**
     * Send single heartbeat
     */
    beat: function() {
        $.ajax({
            url:     Heart.url,
            headers: {
                'X-Heartbeat-Session': Heart.sessionId
            },
            success:  Heart.thump,
            timeout:  Heart.arrhythmia,
            error:    Heart.infarction
        });
    },

    /**
     * Successful heartbeat handler
     */
    thump: function() {
        Heart.log('thump thump');
        if (Heart.timeouts > 0)
            Heart.timeouts = 0;
    },

    /**
     * Heartbeat timeout handler
     */
    arrhythmia: function() {
        if (++Heart.timeouts >= Heart.maxTimeouts)
            Heart.infarction();
        else
            Heart.log('skipped beat')
                 .beat();
    },

    /**
     * Heartbeat failure handler
     */
    infarction: function() {
        Heart.log('CODE BLUE!! GET THE CRASH CART!!')
             .stop();
    },

    /**
     * Log to console if Heart.logging == true
     */
    log: function(msg) {
        if (Heart.logging)
            console.log(msg);

        return Heart;
    },

    /**
     * Parse cookie string and retrieve PHP session ID
     */
    getSessionId: function() {
        var name    = Heart.sessionName + '=',
            cookies = document.cookie.split(';');

        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i];

            while (cookie.charAt(0) == ' ')
                cookie = cookie.substr(1);

            if (cookie.indexOf(name) == 0) {
                Heart.sessionId = cookie.substr(name.length, cookie.length);
                break;
            }
        }
    }
};

// Start the heart!
Heart.start();