// Set thickbox loading image
tb_pathToImage = "img/loading-thickbox.gif";


$(function(){
    var menu = $('.menu').find('li');
	var menuImg = $('.menu').find('img');
    var self = $(this);
	 $('#slider').jcarousel({
		scroll: 6,
		wrap: 'circular'
	});

	 $('#slider2').jcarousel({
		scroll: 6,
		wrap: 'circular'
	});

	
    menu.hover(function() {
        $(this).children().children().children('img').animate({'margin-top':'-4px'},100);        
	}, function() {
        $(this).children().children().children('img').animate({'margin-top':'0'},100);
	});
	HeaderBackground(5000 /*Pause between slides*/, 600 /*Time animation*/);
});
var HeaderBackground = function(pause, time){
	var $himg = $('.himg');
		$himg.img = $himg.find('img');
	
	// alignment
	$himg.img
		.css({
			'position': 'absolute',
			'left': '50%'
		})
		.each(function(){
			$(this).css('margin-left', '-'+$(this).width()/2+'px')
		});
	
	// set z-index
	for ( var i = $himg.img.length-1; i >= 0; i-- ) {
		$himg.img.eq(i).css('z-index', $himg.img.length-i);
	}
	
	// Hide except for the first
	for ( var i = 1; i < $himg.img.length; i++ ) {
		$himg.img.eq(i).hide();
	}
	
	// Next slide
	var NextSlide = function(){
		clearInterval(timer);
		
		var indexVisibleImg = IndexVisibleImg();
		var newIndexVisibleImg = indexVisibleImg+1;
		if ( newIndexVisibleImg === $himg.img.length ) {
			newIndexVisibleImg = 0;
		}
		$himg.img.eq(indexVisibleImg).fadeOut(time).end().eq(newIndexVisibleImg).fadeIn(time, function(){
			timer = setInterval(NextSlide, pause);
		});
	}
	timer = setInterval(NextSlide, pause);
	
	// Index visible img
	var IndexVisibleImg = function(){
		var index = '';
		$himg.img.each(function(n){
			if ( $(this).css('display') === 'block' ) {
				index = n;
			}
		});
		return index;
	}
}