var paspopupStatus = 0; // set value
var clickable_paszone;
var ajaxurl = wppas_ajax_script.ajaxurl;


function PASfunctions(){
jQuery(function($){
	

	/* ----------------------------------------------------------------
	 * POPUP
	 * ---------------------------------------------------------------- */
	$("body").on("click", "a.paspopup", function(event){
		
		var adzone_id = $(this).attr('adzone_id');
		var popup_type = $(this).attr('popup_type');
		//var ajaxurl = $(this).attr('ajaxurl');
		
		setTimeout(function(){ 
			loadPASPopup( adzone_id, popup_type, ajaxurl ); // function show popup
		}, 0); // 500 = .5 second delay
	return false;
	});


	$("body").on("click", "div.close_paspopup", function(event){	
		disablePASPopup();  // function close pop up
	});

	$(this).keyup(function(event) {
		if (event.which == 27) { // 27 is 'Ecs' in the keyboard
			disablePASPopup();  // function close pop up
		}
	});
		
	$("body").on("click", "div#backgroundPasPopup", function(event){	
		disablePASPopup();  // function close pop up
	});
	
	
	
	
	
	/* ----------------------------------------------------------------
	 * FLY IN
	 * ---------------------------------------------------------------- */
	$("body").on("click", "div.close_pasflyin", function(event){		
		disablePASFlyIn();  // function close pop up
	});
	
	
	
	
	/* ----------------------------------------------------------------
	 * BACKGROUND ADS
	 * ---------------------------------------------------------------- */
	// Redirect to banner url when pas_container is clicked
	//$(clickable_paszone.pas_container).on('click', function(event){
	$("body").on('click', function(event){
		
		var target = $(event.target);
		var target_id = event.target.id;
		var bgpas_defaults = { link_full: '', link_full_target: "_blank", pas_container: "body", link_left: '', link_right: '' };
		var _clickable_paszone = $.extend( {}, bgpas_defaults, clickable_paszone );
		//console.log(target+' '+target_id);
		
		if (target.is(_clickable_paszone.pas_container) || target_id == _clickable_paszone.pas_container){
			
			// Add links to left and right sides
			if (_clickable_paszone.link_full == "" || _clickable_paszone.link_full == null){
				
				var width = $(document).width();

				if (event.pageX <= (width / 2)){
					// Left side
					if( _clickable_paszone.link_left != '' ){
						var adlink = window.open(_clickable_paszone.link_left, _clickable_paszone.link_left_target);
					}
				}else{
					// Right side
					if( _clickable_paszone.link_right != '' ){
						var adlink = window.open(_clickable_paszone.link_right, _clickable_paszone.link_right_target);
					}
				}
			}else{
				
				if( _clickable_paszone.link_full != "" ){
					// Add link to both sides
					var adlink = window.open(_clickable_paszone.link_full, _clickable_paszone.link_full_target);
				}
			}
			adlink.focus();
		}
	});
	
	
	
	/* ----------------------------------------------------------------
	 * FIXED/STICKY ADS
	 * ---------------------------------------------------------------- */
	 
	/*if( $('#pas-sticky-div').length > 0 ){
		
		var $window = $(window),
			$stickyEl = $('#pas-sticky-div'),
			elTop = $stickyEl.offset().top;
			
			$window.scroll(function() {
				$stickyEl.toggleClass('pas_sticky', $window.scrollTop() > elTop);
			});
	}*/
	
	/*
	// Fix ads till specific point
	if( $('#pas-sticky-div').length > 0 ){ 
		$.each( $('#pas-sticky-div'), function( i, val ) {
			
			//var item_class = $(this).attr('stick_item');
			var cont = $(this).attr('pas_sticky');
			console.log('oioi'+cont);
			
			if( cont != '.'){
				$(this).scrollToFixed({ marginTop: 10, limit: $(cont).offset().top - $(this).height() });
			}else{
				$(this).scrollToFixed();
			}
		});
	}*/
	
	// http://jsfiddle.net/Tgm6Y/1447/
	//var windw = window;

	$.fn.followTo = function ( elem ) {
		//console.log(windw);
		var $this = this,
			$window = $(window),
			$adTop = $this.offset().top;
			$bumper = $(elem),
			bumperPos = $this.offset().bottom;
		
		if( $bumper.length ){	
			bumperPos = $bumper.offset().top;
		}
		
		var thisHeight = $this.outerHeight(),
			setPosition = function(){
				//console.log($this);
				
				//$this.toggleClass('pas_sticky', $window.scrollTop() > $adTop);
				
				if ($window.scrollTop() > (bumperPos - (thisHeight+50))) {
					
					$this.toggleClass('pas_sticky', $window.scrollTop() < $adTop);
					/*$this.css({
						position: 'absolute',
						top: (bumperPos - thisHeight)
					});*/
				} else {
					
					/*if($window.scrollTop() > $adTop){
						$this.css({
							position: 'static',
							//top: 0
						});
					}*/
					$this.toggleClass('pas_sticky', $window.scrollTop() > $adTop);
				}
			};
		/*$window.resize(function()
		{
			bumperPos = $this.offset().top;
			thisHeight = $this.outerHeight();
			setPosition();
		});*/
		$window.scroll(setPosition);
		setPosition();
	};
	
	if( $('#pas-sticky-div').length > 0 ){ 
		var stick_till = $('#pas-sticky-div').attr('stick_till');
		$('#pas-sticky-div').followTo( stick_till );
	}
	
	
	
	
	
	/* ----------------------------------------------------------------
	 * AD BLOCKER Detection
	 * ---------------------------------------------------------------- */
	setTimeout(function() {
		if( !checkAdStatus() ){
			console.log('You are using AD Blocker!');
			
			$.ajax({
			   type: "POST",
			   url: ajaxurl,
			   data: "action=adblocker_detected"
			}).done(function( obj ) {
			   //success: function( obj ){
					// nothing gets returned.
					msg = JSON.parse( obj );
					
					if( msg.alert ){ alert(msg.alert); }
			   //}
			});	
		}
	}, 500);
	
});
}

jQuery(document).ready(function($){
	var pas_function = new PASfunctions();
});


/************** start: popup functions. **************/
function loadPASPopup( adzone_id, popup_type, ajaxurl, cookie, delay ) {

	jQuery(function($){
		if( cookie ){
			if( !Cookies.get('wpproads-popup-'+adzone_id) ){
				Cookies.set('wpproads-popup-'+adzone_id, new Date($.now()) );
				
				delayPASPopup(delay);
				/*if(paspopupStatus == 0) { // if value is 0, show popup
					$("html").addClass("wppas-model-open");
					$(".PasPopupCont").fadeIn(0500); // fadein popup div$
					$(".PasPopupCont").css({visibility: 'visible'});
					//$("#backgroundPasPopup").css("opacity", "0.7"); // css opacity, supports IE7, IE8
					$("#backgroundPasPopup").fadeIn(0001);
					paspopupStatus = 1; // and set value to 1
				}*/
			}else{
				disablePASPopup();
			}
		}else{
			delayPASPopup(delay);
			/*if(paspopupStatus == 0) { // if value is 0, show popup
				
				$("html").addClass("wppas-model-open");
				$(".PasPopupCont").fadeIn(0500); // fadein popup div$
				$(".PasPopupCont").css({visibility: 'visible'});
				//$("#backgroundPasPopup").css("opacity", "0.7"); // css opacity, supports IE7, IE8
				$("#backgroundPasPopup").fadeIn(0001);
				paspopupStatus = 1; // and set value to 1
				
				if( $("#backgroundPasPopup").hasClass( "autoclose" )){ 
					var closesec = $("#backgroundPasPopup").attr('closesec');
					
					setTimeout(function(){ 
						closePASPopup();
					}, closesec); // 500 = .5 second delay
				}*/
				/*
				$.ajax({
				   type: "POST",
				   url: ajaxurl,
				   data: "action=buyandsell_popup_ajax_content&adzone_id="+adzone_id+"&popup_type="+popup_type,
				   success: function( msg ){
						//$('.hide_row').show();
						$('.pro_ads_buyandsell_'+popup_type+'_popup_'+adzone_id+'_ajax_content').html( msg );
						
						// Activate Ajax Uploads
						if(popup_type == 'buy'){
							load_ajax_upload(ajaxurl);
							
						}
				   }
				});
				*/
			//}
		}
	
	});
}


function delayPASPopup(delay){
	if( delay ){
		var delay_sec = delay*1000;
		
		setTimeout(function(){ 
			showPASPopup();
		}, delay_sec); // 500 = .5 second delay
	}else{
		showPASPopup();
	}	
}

function showPASPopup(){
	if(paspopupStatus == 0) { // if value is 0, show popup
				
		jQuery("html").addClass("wppas-model-open");
		jQuery(".PasPopupCont").fadeIn(0500); // fadein popup div$
		jQuery(".PasPopupCont").css({visibility: 'visible', opacity:1});
		jQuery("#backgroundPasPopup").fadeIn(0001);
		paspopupStatus = 1; // and set value to 1
		
		if( jQuery("#backgroundPasPopup").hasClass( "autoclose" )){ 
			var closesec = jQuery("#backgroundPasPopup").attr('closesec');
			
			setTimeout(function(){ 
				closePASPopup();
			}, closesec); // 500 = .5 second delay
		}
	}
}


function disablePASPopup() {
	jQuery(function($){
	if(paspopupStatus == 1) { // if value is 1, close popup
		
		if( !$("#backgroundPasPopup").hasClass( "autoclose" )){ 
			closePASPopup();
		}
	}
	
	});
}


function closePASPopup() {
	jQuery(function($){
	if(paspopupStatus == 1) { // if value is 1, close popup
		
		$(".PasPopupCont").fadeOut("normal");
		$("#backgroundPasPopup").fadeOut("normal");
		$("html").removeClass("wppas-model-open");
		paspopupStatus = 0;  // and set value to 0
	}
	
	});
}
/************** end: popup functions. **************/



/************** start: Fly in functions. **************/
function loadPASFlyIn( adzone_id, delay, ajaxurl, cookie ) {

	jQuery(function($){
		
		var delay_sec = delay*1000;
		
		setTimeout(function(){ 
			if( cookie ){
				if( !Cookies.get('wpproads-flyin-'+adzone_id) ){
					Cookies.set('wpproads-flyin-'+adzone_id, new Date($.now()) );
					$('.pas_fly_in').css({'visibility': 'visible'}).effect( "shake" );
					$('.pas_fly_in').addClass('showing');
				}
			}else{
				$('.pas_fly_in').css({'visibility': 'visible'}).effect( "shake" );
				$('.pas_fly_in').addClass('showing');
			}
			
		}, delay_sec); // 500 = .5 second delay
		
		
	});
}

function disablePASFlyIn(){
	jQuery(function($){
		$('.pas_fly_in').fadeOut("normal");
	});
}
/************** end: Fly in functions. **************/



/* Adblocker detection */
function checkAdStatus() {
	var adsActive = true;
	
	if (window.wpproads_no_adblock !== true) {
		adsActive = false;
	}
	
	return adsActive;
}