"use strict";
$(function(){
	function likeBtnClick() {
		var id = urlParam('id', this.href);
		var gdo = urlParam('gdo', this.href).rsubstrFrom('\\');
		$.ajax({
			url: this.href+'&_ajax=1&_fmt=json',
			method: 'post',
			context: this,
		}).then(function(result){
			// likes
			let id2 = "." + gdo + "-" + id + "-" + 'likes';
			$(id2).parent().parent().replaceWith(result.likes['html_like']);
			let a = $(id2).parent();
			a.click(likeBtnClick.bind(a.get(0)));
			// dislikes
			id2 = "." + gdo + "-" + id + "-" + 'dislikes';
			$(id2).parent().parent().replaceWith(result.likes['html_dislike']);
			a = $(id2).parent();
			a.click(likeBtnClick.bind(a.get(0)));
		}, function(error) {
			window.GDO.error(error.responseJSON);
		});
		return false;
	}

	$('.gdt-like-button a').each(function() {
		$(this).click(likeBtnClick.bind(this));
	});

	$('.gdt-dislike-button a').each(function() {
		$(this).click(likeBtnClick.bind(this));
	});

});
