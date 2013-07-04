$(function () {

    /* globals */
    var resizeTimer,
        headerForm = $('header div.navbar form');

    $(window).resize(function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(fixLayout, 100);
    });

    function fixLayout() {
        var height = $(window).height();

        $('div.sidebar').height(height - 43);
        $('div.main-content').height(height - 88);
        headerForm.css('margin-left', -headerForm.outerWidth() / 2);
    }

    $('a.tag-container').on('click', function (e) {
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

    $('div.item i.icon-star-empty').on('click', function (e) {
        e.preventDefault();

        var $this = $(this),
            $item = $this.parents('div.item');

        if ($this.hasClass('icon-star-empty')) {
            $this.addClass('icon-star').removeClass('icon-star-empty');
        } else {
            $this.addClass('icon-star-empty').removeClass('icon-star');
        }

        console.log('Saving favourite: ' + $item.data('item-id'));

    });

    $('div.item i.icon-eye-open').on('click', function (e) {
        e.preventDefault();

        var $this = $(this),
            $item = $this.parents('div.item');

        if ($this.hasClass('icon-eye-open')) {
            $this.addClass('icon-eye-close').removeClass('icon-eye-open');
            $item.find('span.item-title').removeClass('unread');
        } else {
            $this.addClass('icon-eye-open').removeClass('icon-eye-close');
            $item.find('span.item-title').addClass('unread');
        }

        console.log('Marking read: ' + $item.data('item-id'));
    });

    $('div.item-title, div.item-sub-name, div.item-preview, div.item-date').on('click', function () {
        var $this = $(this),
            $item = $this.parents('div.item');

        $('div.item-container', $item).toggleClass('hide');
    });

    $('div#login-panel form').on('submit', function (e) {
        e.preventDefault();

        var $this = $(this);

        $.post($this.prop('action'), $this.serialize(), function (result) {
            var html = '';

            if (!result.success) {
                html = '<div class="alert alert-error">' + result.error + '</div>';
                $('div.login-result').html(html).fadeIn();
            } else {
                html = '<div class="alert alert-success">Successfully logged in!</div>';
                $('div.login-result').html(html).fadeIn();
                window.location = '/';
            }
        }, 'json');
    });

    fixLayout();
});