$(document).ready(function(){
    var calendar = $('.calendar').calendar({
        'first_day': 1,
        tmpl_path: "/components/bootstrap-calendar/tmpls/",
        events_source: '/calendar/events',
        language: 'lt-LT',
        onAfterViewLoad: function(view) {
            $('.title').text(this.getTitle());
            $('.btn-group button').removeClass('active');
            $('button[data-calendar-view="' + view + '"]').addClass('active');
        },
    });

    $('.date-control').click(function(){
        $('.date-control').removeClass('active');

        calendar.navigate($(this).data('calendar-nav'));
    });

    $('.btn-group button[data-calendar-view]').each(function() {
        var $this = $(this);
        $this.click(function() {
            calendar.view($this.data('calendar-view'));
        });
    });
});
