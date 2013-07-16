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
        $item = $this.parents('div.item'),
        prevItem = $('div.item.selected');

    if (prevItem.length > 0) {
        $('div.item-container', prevItem).addClass('hide');
        $('div.item-collapsed', prevItem).removeClass('collapsed');
    }

    $item.toggleClass('selected');
    $('div.item-collapsed', $item).toggleClass('collapsed');
    $('div.item-container', $item).toggleClass('hide');

    Reader.Items.Mark(Reader.Items.Funcs.READ, 'add', $item.data('item-id'), $item, $('a.item-markread i', $item));
});

var loadTolerance = 98,
    loadLock = false;

function needLoad(totalHeight, currentHeight, tolerance) {
    console.log(totalHeight, currentHeight, tolerance);
    return (currentHeight / totalHeight) * 100 > tolerance;
}

$('div.main-content').scroll(function () {
    var type = 'subscription',
        typeId = $('div.item-list').data('type-id'),
        lastDate = $('div.item-list div.item:last').data('item-date');

    var currentLoad = ($('div.main-content div.span12').offset().top * -1) + $(window).height();

    if (needLoad($('div.main-content div.span12').height(), currentLoad, loadTolerance)) {
        console.log('LOAD!');
    } else {
        console.log('NOT LOAD!');
        return;
    }

    if (loadLock) {
        return;
    }

    loadLock = true;

    console.log('+ LOCKED!');

    Reader.Items.fetchData(type, typeId, lastDate, function (result) {
        $('div.main-content div.span12').append(result);
        loadLock = false;
        console.log('- UNLOCKED!');
    });

});