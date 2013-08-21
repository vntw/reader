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
    var processable = $('#site-collect ul.nav-pills li.active');

    processable.each(function (i) {
		var $sub = $(this);
		$('div.collect-result').append('<b>' + $sub.text() + '...</b><br />');

		// async
        $.get('/collect/' + $(this).data('sub-id'), null, function (results) {
            $('div.collect-result').append('Inserted: ' + results[0].inserted + ' | Existed' + results[0].existing + ' | Error: ' + (results[0].error ? results[0].error : 'None!') + '<br /><br />');
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

$(document).on('click', 'div.list-toolbar button.mark-read-all', function (e) {
    console.log(1);
    Reader.Items.markReadAll();
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

    if (prevItem.length > 0 && prevItem.data('item-id') !== $item.data('item-id')) {
        $('div.item-container', prevItem).addClass('hide');
        $('div.item-collapsed', prevItem).removeClass('collapsed');
    }

    $item.toggleClass('selected');
    $('div.item-collapsed', $item).toggleClass('collapsed');
    $('div.item-container', $item).toggleClass('hide');

	if ($item.hasClass('selected')) {
        Reader.Items.scrollToItem($item);
        Reader.Items.Mark(Reader.Items.Funcs.READ, 'add', $item.data('item-id'), $item, $('a.item-markread i', $item));
	}
});

var loadTolerance = 98,
    loadLock = false;

function needLoad(totalHeight, currentHeight, tolerance) {
    console.log(totalHeight, currentHeight, tolerance);
    return (currentHeight / totalHeight) * 100 > tolerance;
}

$('div.main-content').scroll(function () {
    var list = $('div.item-list'),
		type = list.data('type'),
        typeId = list.data('type-id') || null,
        lastDate = $('div.item:last', list).data('item-date');

	if (loadLock || list.data('end')) {
		return;
	}

    var currentLoad = ($('div.main-content div.span12').offset().top * -1) + $(window).height();

    if (needLoad($('div.main-content div.span12').height(), currentLoad, loadTolerance)) {
        console.log('LOAD!');
    } else {
        console.log('NOT LOAD!');
        return;
    }

    loadLock = true;

    console.log('+ LOCKED!');

    Reader.Items.fetchData(type, typeId, lastDate, function (result) {
		if (result === '') {
			list.data('end', true);
			list.append('<div class="alert text-center">End reached. No more items for you!</div>');
			return;
		}

        $('div.main-content div.span12').append(result);
        loadLock = false;
        console.log('- UNLOCKED!');
    });

});