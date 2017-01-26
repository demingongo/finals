if($(window).width() > 760){
		$('.subhead-collapse').each(function(){
			var attr = $(this).attr('style');
			if (typeof attr !== typeof undefined && attr !== false) {
				$(this).removeAttr('style');
			}
		});
	}
	else{
		$('.btn.to-xs').each(function(){
			$(this).addClass('btn-xs');
		});
	}

$(window).resize(function(){
	if($(window).width() > 760){
		$('.subhead-collapse').each(function(){
			var attr = $(this).attr('style');
			if (typeof attr !== typeof undefined && attr !== false) {
				$(this).removeAttr('style');
			}
		});

		$('.btn.to-xs').each(function(){
			$(this).removeClass('btn-xs');
		});
	}
	else{
		$('.btn.to-xs').each(function(){
			$(this).addClass('btn-xs');
		});
	}
});