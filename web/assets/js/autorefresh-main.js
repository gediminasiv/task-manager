var tabChosen = '';

function updatePartial()
{
    $('.nav-tabs li').each(function(){
        if ($(this).attr('class') == 'active') {
            tabChosen = $(this).find('a').attr('href');
        }
    });

    $.get('/update-partial', function (data) {
        $('.tab-content').html(data);
        $('.tab-content div').each(function(){
            if ('#' + $(this).attr('id') == tabChosen) {
                $(this).addClass('active');
            }
        });
        activateTables();
    });
}

$(document).ready(function(){
    var refreshInterval = parseInt($('#refreshInterval').val()) * 1000;

    setInterval(function(){
        updatePartial();
    }, refreshInterval);
});
