var Reader = {}

Reader.Site = {
    Loader: function (show) {
        var logo = $('a.brand > i');

        if (show) {
            logo.addClass('icon-spin');
            logo.css('color', '#ccc');
        } else {
            logo.removeClass('icon-spin');
            logo.css('color', '#777');
        }
    }
}

Reader.Layout = {
    Fix: function () {
        var height = $(window).height(),
            headerForm = $('header div.navbar form');

        $('div.sidebar').height(height - 43);
        $('div.main-content').height(height - 88);
        headerForm.css('margin-left', -headerForm.outerWidth() / 2);
    }
}

Reader.Items = {
    Funcs: {
        FAVOURITES: 'favs',
        SAVED: 'saved',
        READ: 'read'
    },
    Mark: function (func, action, id, $item, $icon) {
        Reader.Site.Loader(true);

        if ($item) {
            var add = (action === 'add');

            switch (func) {
                case this.Funcs.READ:
                    this.markRead($item, $icon, add);
                    break;
                case this.Funcs.SAVED:
                    this.markSaved($icon, add);
                    break;
                case this.Funcs.FAVOURITES:
                    this.markFavourite($icon, add);
                    break;
            }
        }

        $.post('/i/' + [func, action, id].join('/'), null, function (result) {
            Reader.Site.Loader(false);
        }, 'json');
    },
    markRead: function ($item, $icon, add) {
        if (add) {
            $icon.addClass('icon-eye-close').removeClass('icon-eye-open');
            $item.find('span.item-title').removeClass('unread');
        } else {
            $icon.addClass('icon-eye-open').removeClass('icon-eye-close');
            $item.find('span.item-title').addClass('unread');
        }
    },
    markSaved: function ($icon, add) {
        if (add) {
            $icon.addClass('icon-ok-sign').removeClass('icon-time');
        } else {
            $icon.addClass('icon-time').removeClass('icon-ok-sign');
        }
    },
    markFavourite: function ($icon, add) {
        if (add) {
            $icon.addClass('icon-star').removeClass('icon-star-empty');
        } else {
            $icon.addClass('icon-star-empty').removeClass('icon-star');
        }
    },
    fetchData: function (type, typeId, lastDate, callback) {
        Reader.Site.Loader(true);

        var format = 'html';
        var url = '/l/' + type + '/' + typeId + '?format=' + format + '&last-date=' + lastDate + '&amount=25&sort=desc';

        $.get(url, null, function (result) {
            Reader.Site.Loader(false);
            callback(result);
        }, (format === 'json') ? 'json' : null);
    },
	scrollToItem: function ($item) {
		var pos = ($('div.main-content .item-list').offset().top * -1) + $item.offset().top + 10;
		$('div.main-content').animate({ scrollTop: pos + "px" }, 350);
		//$('div.main-content').scrollTop(($('div.main-content .item-list').offset().top * -1) + $item.offset().top + 10)
	}
}