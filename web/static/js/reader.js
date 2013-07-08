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
    Mark: function (func, action, id) {
        Reader.Site.Loader(true);

        $.post('/i/' + [func, action, id].join('/'), null, function (result) {
            Reader.Site.Loader(false);
        }, 'json');
    }
}