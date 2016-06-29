function PAScorncurl(options){
jQuery(function($){
	
	var defaults = { corncurlSmall: 26, corncurlBig: 100, cornerAnimate: 1, corncurlOpenTime: 500, corncurlCloseTime: 1000,  corncurlSize: 1.16 };
	var settings = $.extend( {}, defaults, options );
	
	$('#corncurl-cont').click(function(){ 
		var url = $('.corncurl-content a').attr('href'); 
		var target = $('.corncurl-content a').attr('target');
		if( url != null){ 
			window.open( url, target ) 
		}
	}).css('opacity',0).hover(corncurlOpen, corncurlClose);
	corncurlAnimate(settings.corncurlOpenTime); 
	
	
	function corncurnPercenttoPixels( percent ){
		return percent*3;
	}
	function corncurlAnimate(speed){
		var corner_speed = speed ? speed : settings.corncurlOpenTime;
		var cornerAnimateRandSize = settings.cornerAnimate ? corncurnPercenttoPixels(settings.corncurlSmall) + Math.floor(Math.random()*5) : corncurnPercenttoPixels(settings.corncurlSmall);
		var new_corner_size = jQuery('#corncurl-bg').width() > corncurnPercenttoPixels(settings.corncurlSmall) ? corncurnPercenttoPixels(settings.corncurlSmall) : cornerAnimateRandSize;
		var cnrw = Math.floor(new_corner_size * settings.corncurlSize);
		var cnrh = Math.floor(new_corner_size * settings.corncurlSize);
	
		jQuery('#corncurl-peel').stop().animate({'opacity':1, 'width':cnrw, 'height':cnrh}, corner_speed);
		jQuery('#corncurl-cont').stop().animate({'opacity':0, 'width':cnrw, 'height':cnrh}, corner_speed);
		jQuery('#corncurl-bg').stop().animate({'width':new_corner_size, 'height':new_corner_size}, corner_speed, '', corncurlAnimate);
		jQuery('#corncurl-small-img').stop().animate({'width':new_corner_size, 'height':new_corner_size}, corner_speed);
	}		   
	function corncurlOpen(){
		jQuery('#corncurl-peel, #corncurl-cont').stop();
		jQuery('#corncurl-bg').stop();
		jQuery('#corncurl-small-img').stop().hide();
		jQuery('#corncurl-peel, #corncurl-cont').animate({'width':corncurnPercenttoPixels(settings.corncurlBig) * settings.corncurlSize, 'height':corncurnPercenttoPixels(settings.corncurlBig) * settings.corncurlSize}, settings.corncurlOpenTime);
		jQuery('#corncurl-bg').animate({'width':corncurnPercenttoPixels(settings.corncurlBig), 'height':corncurnPercenttoPixels(settings.corncurlBig)}, settings.corncurlOpenTime);
	}
	function corncurlClose(){
		jQuery('#corncurl-peel, #corncurl-cont').stop();
		jQuery('#corncurl-bg').stop();
		jQuery('#corncurl-peel, #corncurl-cont').animate({'width':corncurnPercenttoPixels(settings.corncurlSmall) * settings.corncurlSize, 'height':corncurnPercenttoPixels(settings.corncurlSmall) * settings.corncurlSize}, settings.corncurlCloseTime);
		jQuery('#corncurl-bg').animate({'width':corncurnPercenttoPixels(settings.corncurlSmall), 'height':corncurnPercenttoPixels(settings.corncurlSmall)}, settings.corncurlCloseTime, function(){jQuery('#corncurl-small-img').show().width(corncurnPercenttoPixels(corncurlSmall)).height(corncurnPercenttoPixels(settings.corncurlSmall));});
		corncurlAnimate(settings.corncurlOpenTime); 
	}
	
});
}