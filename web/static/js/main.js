var resizeTimer;
$(window).resize(function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(Reader.Layout.Fix, 100);
});
Reader.Layout.Fix();

$(document).on('click', '#site-collect ul.nav-pills li a', function (e) {
    e.preventDefault();
});
$(document).on('click', '#site-collect ul.nav-pills li', function (e) {
    $(this).toggleClass('active');
});
$(document).on('click', '#site-collect button', function (e) {
    var processable = $('#site-collect ul.nav-pills li.active'),
        progressBar = $('#site-collect .progress .bar'),
        percentParts = Math.ceil(100 / processable.length),
        percent = 0;

    progressBar.css('width', 0);

    processable.each(function (i) {
        percent += (i === processable.length ? 100 : percentParts);

        $('div.collect-result').append('<b>' + $(this).text() + '</b><br />');

		$.get('/collect/' + $(this).data('sub-id'), null, function (results) {
//            progressBar.animate({width: percent + '%'}, 250);
			$('div.collect-result').append(results[0].subscription.name + ': ' + JSON.stringify(results) + '<br />');
		}, 'json');
    });
});

$(document).on('click', 'a.tag-container', function (e) {
    e.preventDefault();

    var $this = $(this),
        icon = $('> i', $this);

    $this.next('ul').slideToggle(200);

    if (icon.hasClass('icon-folder-close')) {
        icon.addClass('icon-folder-open').removeClass('icon-folder-close');
    } else {
        icon.addClass('icon-folder-close').removeClass('icon-folder-open');
    }
});

$(document).on('click', 'div.item a.item-favourite i', function (e) {
    e.preventDefault();

    var $this = $(this),
        $item = $this.parents('div.item'),
        add = $this.hasClass('icon-star-empty');

    Reader.Items.Mark(Reader.Items.Funcs.FAVOURITES, (add ? 'add' : 'del'), $item.data('item-id'), $item, $this);
});

$(document).on('click', 'div.item a.item-markread i', function (e) {
    e.preventDefault();

    var $this = $(this),
        $item = $this.parents('div.item'),
        add = $this.hasClass('icon-eye-open');

    Reader.Items.Mark(Reader.Items.Funcs.READ, (add ? 'add' : 'del'), $item.data('item-id'), $item, $this);
});

$(document).on('click', 'div.item a.item-save i', function (e) {
    e.preventDefault();

    var $this = $(this),
        $item = $this.parents('div.item'),
        add = $this.hasClass('icon-time');

    Reader.Items.Mark(Reader.Items.Funcs.SAVED, (add ? 'add' : 'del'), $item.data('item-id'), $item, $this);
});

$(document).on('click', 'span.item-title, span.item-preview, div.item-sub-name, div.item-date', function (e) {
    var $this = $(this),
        $item = $this.parents('div.item');

    $('div.item-collapsed', $item).toggleClass('collapsed');
    $('div.item-container', $item).toggleClass('hide');

    Reader.Items.Mark(Reader.Items.Funcs.READ, 'add', $item.data('item-id'), $item, $('a.item-markread i', $item));
});

//$(window).scroll(function () {
//    console.log(123);
//});
//
//window.onscroll = function (oEvent) {
//    console.log(321);
//}

var loadPercent = 75;

function needLoad(totalHeight, currentHeight, tolerance) {
    return (currentHeight / totalHeight) * 100 > tolerance;
}

$('div.main-content').scroll(function () {
    var type = 'subscription',
        typeId = 2,
        lastId = 59;
    console.log($('div.main-content div.span12').height());
    if (needLoad($('div.main-content div.span12').height(), $('div.main-content').scrollTop(), loadPercent)) {
        console.log('LOAD!');
    } else {
        console.log('NOT LOAD!');
        return;
    }

    Reader.Items.fetchData(type, typeId, lastId, function (result) {
        $('div.main-content div.span12').append(result);
    });
//    console.log($(window).scrollTop());
//    console.log($(document).height());
//    console.log($(window).height());
//    if ($(window).scrollTop() == $(document).height() - $(window).height()) {
//        var type = 'subscription',
//            typeId = 98,
//            lastId = $('div#main-content div.item:last').data('item-id');
//
//        Reader.Items.fetchData(type, typeId, lastId);
//    }
});