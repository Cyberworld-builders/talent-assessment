jQuery(document).ready(function($){

    // Set headers for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
        }
    });

    // Start the timer
    timelimit = 0;
    now = 0;
    getTimeLimit(1);
});

// Get time limit from server
function getTimeLimit(init) {

    var url = window.location.pathname+'/update_time';

    $.ajax({
        type: 'post',
        url: url,
        data: {},
        dataType: 'json',
        success: function(data) {
            timelimit = data * 1000;
            now = Date.parse(new Date());

            console.log('time limit synced: '+timelimit+' ms left');

            if (init) initializeClock('countdown');
        },
        error: function(data) {
            console.log(data.status+' '+data.statusText);
            $('html').prepend(data.responseText);
        }
    });
}

// Initialize clock
function initializeClock(id) {
    var clock = document.getElementById(id);
    var time = clock.querySelector('.time');
    var times_up = false;

    function updateClock() {
        if (! times_up) {
            var t = getTimeRemaining();
            time.innerHTML = ('0' + t.minutes).slice(-2) + ':' + ('0' + t.seconds).slice(-2);
            console.log(t.days+':'+ t.hours+':'+ t.minutes+':'+ t.seconds);

            if (t.total <= 0) {
                clearInterval(timeinterval);
                time.innerHTML = '00:00';
                clearInterval(synctime);
                timeEnded();
                times_up = true;
            }
        }
    }

    updateClock();
    var timeinterval = setInterval(updateClock, 1000);
    var synctime = setInterval(getTimeLimit, 1000 * 10);
}

// Get the time remaining until time limit is reached
function getTimeRemaining() {
    var t = (timelimit + now) - Date.parse(new Date());
    var seconds = Math.floor((t / 1000) % 60);
    var minutes = Math.floor((t / 1000 / 60) % 60);
    var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
    var days = Math.floor(t / (1000 * 60 * 60 * 24));
    return {
        'total': t,
        'days': days,
        'hours': hours,
        'minutes': minutes,
        'seconds': seconds
    };
}

// Time limit reached, time to submit the form
function timeEnded() {
    alert('Times Up!');

    var data = {'complete': 1};
    var url = window.location.pathname;

    $.ajax({
        type: 'post',
        url: url,
        data: data,
        dataType: 'json',
        success: function(data) {
            console.log(data);
            if (data['reload']) {
                window.location.reload();
            }
        },
        error: function(data) {
            console.log(data.status+' '+data.statusText);
            $('html').prepend(data.responseText);
        }
    });
}
//# sourceMappingURL=timer.js.map
