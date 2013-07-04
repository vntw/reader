$(document).pjax('ul#proto li a', 'div.main-content');

$(document).on('pjax:send', function () {
    activity(true);
});
$(document).on('pjax:complete', function () {
    activity(false);
});
$(document).on('pjax:click', function (e) {
    $('ul#proto li').removeClass('active');
    $(e.target).parent('li').addClass('active');
});

var pathArray = window.location.pathname.split('/');
$('ul#proto li').removeClass('active');
$('ul#proto li a[data-section="' + (pathArray[1] ? pathArray[1] : 'home') + '"]').parent('li').addClass('active');

function activity(show) {
    var logo = $('a.brand > i');

    if (show) {
        logo.addClass('icon-spin');
        logo.css('color', '#ccc');
    } else {
        logo.removeClass('icon-spin');
        logo.css('color', '#777');
    }
}