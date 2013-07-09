var resizeTimer;
$(window).resize(function () {
	clearTimeout(resizeTimer);
	resizeTimer = setTimeout(Reader.Layout.Fix, 100);
});
Reader.Layout.Fix();

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