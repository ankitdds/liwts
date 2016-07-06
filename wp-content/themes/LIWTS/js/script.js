jQuery(function(){
	jQuery("#strain-review-home").owlCarousel({
	    loop:false,
	    margin:10,
	    nav:true,
	    responsive:{
	        0:{
	            items:1
	        },
	        480:{
	            items:2
	        },
	        768:{
	            items:3
	        }
	    }
	});
	setTimeout(function(){
		(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6&appId=1701340016749229";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	}, 5000);
});

jQuery( window ).resize(function() {
	fusion_resize_page_widget();
});

function fusion_resize_page_widget() {
	var $container_width = jQuery( '.widget_text' ).width();

	if ( $container_width != jQuery('.widget_text .fb-page' ).data( 'width' ) ) {
		jQuery('.widget_text .fb-page' ).attr( 'data-width', $container_width );
		FB.XFBML.parse();
	}
}