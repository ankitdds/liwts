jQuery(function(){
	jQuery("#lounge-loadmore").click(function(){
		var data = {
			'action' : 'loadprevlounge',
			'previousday' : jQuery(this).attr('data-previousday')
		};
		jQuery.post(liwtsAjax.ajaxurl, data, function(res){
			var resObject = jQuery.parseJSON(res);
			jQuery( resObject.html ).insertBefore( ".loungemore-btn" );
			jQuery("#lounge-loadmore").attr('data-previousday',resObject.prevday);
		});
		return false;
	});
});