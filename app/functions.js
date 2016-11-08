function SmoothlyMenu() {
    if (!$('body').hasClass('mini-navbar') || $('body').hasClass('body-small')) {
        // Hide menu in order to smoothly turn on when maximize menu
        $('#side-menu').hide();
        $('.logo-pittogramma.white').hide();
        // For smoothly turn on menu
        setTimeout(
            function () {
                $('#side-menu').fadeIn(400);
                $('.logo-pittogramma.white').fadeIn(400);
            }, 200);
    } else if ($('body').hasClass('fixed-sidebar')) {
        $('#side-menu').hide();
        $('.profile-element').hide();
        setTimeout(
            function () {
                $('#side-menu').fadeIn(400);
                $('.logo-pittogramma.white').fadeIn(400);
            }, 100);
    } else {
        // Remove all inline style from jquery fadeIn function to reset menu state
        $('#side-menu').removeAttr('style');
        $('.logo-pittogramma.white').hide();
    }
}

$('#demo').daterangepicker({
    "ranges": {
        "Today": [
            "2016-11-08T09:48:26.475Z",
            "2016-11-08T09:48:26.475Z"
        ],
        "Yesterday": [
            "2016-11-07T09:48:26.475Z",
            "2016-11-07T09:48:26.475Z"
        ],
        "Last 7 Days": [
            "2016-11-02T09:48:26.475Z",
            "2016-11-08T09:48:26.475Z"
        ],
        "Last 30 Days": [
            "2016-10-10T08:48:26.475Z",
            "2016-11-08T09:48:26.475Z"
        ],
        "This Month": [
            "2016-10-31T23:00:00.000Z",
            "2016-11-30T22:59:59.999Z"
        ],
        "Last Month": [
            "2016-09-30T22:00:00.000Z",
            "2016-10-31T22:59:59.999Z"
        ]
    },
    "startDate": "11/02/2016",
    "endDate": "11/08/2016",
    "opens": "left"
}, function(start, end, label) {
  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
});