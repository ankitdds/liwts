jQuery(document).ready(function($) {

	$('.chosen-select').chosen();
	$('.chosen-select-ad-dashboard').chosen({width: '100%'}); 
	$('#filter-by-date').chosen();
	
	$("#local_start_date").datepicker({dateFormat: wppas_local.date_format, altField: "#start_date", altFormat: "mm.dd.yy", minDate: 0});
	$("#local_end_date").datepicker({dateFormat: wppas_local.date_format, altField: "#end_date", altFormat: "mm.dd.yy", minDate: -1});
	//$("#local_start_date").datepicker({dateFormat: "d MM yy", altField: "#start_date", altFormat: "mm.dd.yy", minDate: 0});
	//$("#local_end_date").datepicker({dateFormat: "d MM yy", altField: "#end_date", altFormat: "mm.dd.yy", minDate: -1});
	
	
	$(".chosen-select.select-adzone").on('change', function(event, params) {
		
		// Get the banner id
		if( params.deselected ){
			bid = $('option[value="'+ params.deselected +'"]', this).attr('bid');
			aid = $('option[value="'+ params.deselected +'"]', this).val();
			action_type = 'remove'; 
		}else{
			bid = $('option[value="'+ params.selected +'"]', this).attr('bid');
			aid = $('option[value="'+ params.selected +'"]', this).val();
			action_type = 'add'; 
		}
		
		$('.select-adzone-cont-'+bid).css({'opacity': .3});
		$('.loading_adzone_'+bid).show();
		
		// Get all selected options
		var result = "";
		$('.select-adzone-'+bid+' option:selected').each(function(i, item){ 
			
			comma = i == 0 ? "" : ",";
			result += comma+$(this).val();
		});
		
		$.ajax({
		   type: "POST",
		   url: ajaxurl,
		   data: "action=link_to_adzone&aid="+ aid +"&bid="+bid +"&result=" + result +"&action_type="+ action_type
		}).done(function( msg ) {
		   //success: function( msg ){
				
				//alert(msg);
				$('.loading_adzone_'+bid).hide();
				$('.select-adzone-cont-'+bid).css({'opacity': 1});
		  // }
		});
		
	});
	

	
	$(".select_banner_advertiser").change(function() {
		
		$('.hide_row').hide();
		
		if( $(this).val() != ''){
			$.ajax({
			   type: "POST",
			   url: ajaxurl,
			   data: "action=load_advertiser_campaigns&uid="+ $(this).val()
			}).done(function( msg ) {
			   //success: function( msg ){
					
					$('.select_advertiser_td .chosen-single').css({'border-color': '#AAAAAA'});
					//$('.select_advertiser_required').css({'color': '#858585'});
					$('.select_advertiser_required').hide();
							
					$('.hide_row').show();
					$('#select_cont').html( msg );
					$('.chosen-select').chosen(); 
					
					// Campaign form settings
					if($('.select_banner_campaign').val() == '' ){
						$('.select_campaign_td .chosen-single').css({'border-color': '#FF006B'});
						$('.select_campaign_required').css({'color': '#FF006B'});
						$('.select_campaign_required').show();
					}
					
					$('.select_banner_campaign').on('change', function(){
						if($(this).val() != ''){
							$('.select_campaign_td .chosen-single').css({'border-color': '#AAAAAA'});
							$('.select_campaign_required').hide();
						}else{
							$('.select_campaign_td .chosen-single').css({'border-color': '#FF006B'});
							$('.select_campaign_required').show();
						}
					});
			  // }
			});
		}else{
			$('.select_advertiser_td .chosen-single').css({'border-color': '#FF006B'});
			$('.select_advertiser_required').css({'color': '#FF006B'});
			$('.select_advertiser_required').show();
		}
	});
	
	if( $('.select_banner_advertiser').val() == '' ){
		$('.select_advertiser_td .chosen-single').css({'border-color': '#FF006B'});
		$('.select_advertiser_required').css({'color': '#FF006B'});
		$('.select_advertiser_required').show();
	}
	$('.select_banner_campaign').on('change', function(){
		if($('.select_banner_campaign').val() == '' ){
			$('.select_campaign_td .chosen-single').css({'border-color': '#FF006B'});
			$('.select_campaign_required').css({'color': '#FF006B'});
			$('.select_campaign_required').show();
		}else{
			$('.select_campaign_td .chosen-single').css({'border-color': '#AAAAAA'});
			$('.select_campaign_required').hide();
		}
	});
	
	
	$("#filter_advertisers").change(function() {
		
		$.ajax({
		   type: "POST",
		   url: ajaxurl,
		   data: "action=filter_advertiser_campaigns&uid="+ $(this).val()
		}).done(function( msg ) {
	
			$('#select_cont').html( msg );
			$('.chosen-select').chosen(); 
		});
	});


	
	/*
	 * Media Popup - works for admins only
	*/
	var sizes = ['', '_tablet_landscape', '_tablet_portrait', '_phone_landscape', '_phone_portrait', '_device_small'];
	$.each(sizes, function( index, value ) {
		$('.upload_image_button'+value).on('click', function()
		{
			wp.media.editor.send.attachment = function(props, attachment)
			{
				console.log(attachment);
				$('#banner_url'+value).val(attachment.url);
				$('#banner_width'+value).val(attachment.width);
				$('#banner_height'+value).val(attachment.height);
				$('#banner-img-preview'+value).html('<img src="'+attachment.url+'" />');
				//$('#banner-img-preview'+value).attr("src",attachment.url);
			}
			wp.media.editor.open(this);
			
			return false;
		});
	});
	$('.upload_fallback_image_button').on('click', function()
	{
		wp.media.editor.send.attachment = function(props, attachment)
		{
			$('#fallback_image').val(attachment.url);
			$('#fallback-img-preview').html('<img src="'+attachment.url+'" />');
			//$('#fallback-img-preview').attr("src",attachment.url);
		}
		wp.media.editor.open(this);
		
		return false;
	});
	$('.upload_default_adzone_button').on('click', function()
	{
		wp.media.editor.send.attachment = function(props, attachment)
		{
			$('#adzone_default_url').val(attachment.url);
			$('#adzone_default_url-preview').html('<img src="'+attachment.url+'" />');
			//$('#fallback-img-preview').attr("src",attachment.url);
		}
		wp.media.editor.open(this);
		
		return false;
	});
	
	
	
	
	var sizes = ['', '_tablet_landscape', '_tablet_portrait', '_phone_landscape', '_phone_portrait', '_device_small'];
	$.each(sizes, function( index, value ) {
		
		$('#size_list'+value).on('change', function(){
		
			var val = $('#size_list'+value).val();
			if( val == 'custom'){
				$('#custom_size'+value).show();
			}else{
				$('#custom_size'+value).hide();
			}
		});
	});
	
	
	/* Show/Hide Rotation Options */
	$('#adzone_rotation_btn').on('change', function(){
		
		var val = $(this).val();
		if( val == 1 ){
			$('#adzone_rotation_options').show();
		}else{
			$('#adzone_rotation_options').hide();
		}
	});
	
	
	/* Show/Hide Banner HTML5 size */
	$('#banner_is_html5_btn').on('change', function(){
		
		if( $(this).is(':checked') ){
			$('.html5_banner_size_cont').show();
		}else{
			$('.html5_banner_size_cont').hide();
		}
	});
	
	
	
	$('#wpproads_enable_stats').change(function(){
		
		var val = $('#wpproads_enable_stats').val();
		if( val == '1'){
			$('#enable_userdata_stats').show();
		}else{
			$('#enable_userdata_stats').hide();
		}
	});
	
	$('#wpproads_enable_mod_rewrite').change(function(){
		
		var val = $('#wpproads_enable_mod_rewrite').val();
		if( val == '1'){
			$('#wpproads_enable_mod_rewrite_box').show();
		}else{
			$('#wpproads_enable_mod_rewrite_box').hide();
		}
	});
	
	
	
	var txt = $('#banner_contract option:selected').attr('txt');
	$('.banner_contract_duration').html(txt);
	
	if( $('#banner_contract option:selected').val() == 0 ){
		$('#banner_duration_tr').hide();
	}else{
		$('#banner_duration_tr').show();
	}
	
	$('#banner_contract').change(function(){
	
		var val = $('#banner_contract').val();
		var txt = $('#banner_contract option:selected').attr('txt');
		
		$('.banner_contract_duration').html(txt);
		
		if( $('#banner_contract option:selected').val() == 0 ){
			$('#banner_duration_tr').hide();
		}else{
			$('#banner_duration_tr').show();
		}
	});
	
	
	
	
	// Sortable banners in adzones
	$('ul#adzone_order_sortable').sortable({
        axis: 'y',
		placeholder: "ui-state-highlight",
        stop: function (event, ui) {
	        //var postdata = $(this).sortable('serialize');
			var id_order = $(this).sortable('toArray', {attribute: 'bid'});
			var adzone_id = $(this).attr('aid');
			
			$('.order_banners_'+adzone_id).css({ 'opacity':.5 });
			$('.order_banners_'+adzone_id+' .loading').show();
			
           	$.ajax({
                type: 'POST',
                url: ajaxurl,
				data: 'action=order_banners_in_adzone&aid='+adzone_id+'&id_order='+id_order
			}).done(function( msg ) {
				//success: function( msg ){
				
					//alert(msg);
					$('.order_banners_'+adzone_id+' .loading').hide();
					$('.order_banners_'+ adzone_id).css({ 'opacity':1 });
			   //}
            });
		}
    });
	
	
	
	
	
	// Statistics
	$("body").on("click", "a.stats_btn", function(event){
		
		$('.pro_ad_stats_graph').css({opacity: .3});
		$('.bubblingG').show();
		var group = $('#stats_group').val() != '' ? $('#stats_group').val() : '';
		var group_id = $('#stats_group_id').val() != '' ? $('#stats_group_id').val() : '';
		
		$.ajax({
		   type: "POST",
		   url: ajaxurl,
		   data: "action=load_stats&type="+ $(this).attr('type') +"&name="+ $(this).text() +"&color="+ $(this).attr('color')+"&rid="+$(this).attr('rid')+"&year="+ $(this).attr('year')+"&month="+ $(this).attr('month')+"&day="+ $(this).attr('day')+"&group="+group+"&group_id="+group_id //&group="+$(this).attr('group')+"&group_id="+$(this).attr('group_id')
		}).done(function( msg ) {
		   //success: function( msg ){
				$('.pro_ad_stats_graph').html(msg);
				$('.bubblingG').hide();
				$('.pro_ad_stats_graph').css({opacity: 1});
		   //}
		});
		
	});
	
	
	
	$("body").on("click", "a.stats_date", function(event){
		
		$('.pro_ad_stats_graph').css({opacity: .3});
		$('.bubblingG').show();
		var group = $('#stats_group').val() != '' ? $('#stats_group').val() : '';
		var group_id = $('#stats_group_id').val() != '' ? $('#stats_group_id').val() : '';
		
		$.ajax({
		   type: "POST",
		   url: ajaxurl,
		   data: "action=load_stats_from_day&type="+ $(this).attr('type') +"&color="+$(this).attr('color')+"&year="+ $(this).attr('year')+"&month="+ $(this).attr('month')+"&day="+ $(this).attr('day')+"&group="+group+"&group_id="+group_id
		}).done(function( msg ) {
		   //success: function( msg ){
				
				$('.pro_ad_stats_graph').html(msg);
				$('.bubblingG').hide();
				$('.pro_ad_stats_graph').css({opacity: 1});
		  // }
		});
		
	});
	
	
	$("body").on("click", "a.time_frame_btn", function(event){
		
		$('.pro_ad_stats_graph').css({opacity: .3});
		$('.bubblingG').show();
		var type = $(this).attr('type') != '' && $(this).attr('type') != null ? $(this).attr('type') : 'click';
		var group = $('#stats_group').val() != '' ? $('#stats_group').val() : '';
		var group_id = $('#stats_group_id').val() != '' ? $('#stats_group_id').val() : '';
		
		$.ajax({
		   type: "POST",
		   url: ajaxurl,
		   data: "action=load_stats&type="+type+"&name=Clicks&rid="+$(this).attr('rid')+"&year="+ $(this).attr('year')+"&month="+ $(this).attr('month')+"&group="+group+"&group_id="+group_id
		}).done(function( msg ) {
		  // success: function( msg ){
				
				$('.pro_ad_stats_graph').html(msg);
				$('.bubblingG').hide();
				$('.pro_ad_stats_graph').css({opacity: 1});
		   //}
		});
		
	});
	
	
	
	
	
	/* Update Campaigns/Banners (dashboard) */
	//$("#manual_update_campaings_banners").on('click', function(){
	$("body").on("click", "#manual_update_campaings_banners", function(event){
		
		$.ajax({
		   type: "POST",
		   url: ajaxurl,
		   data: "action=manual_update_campaigns_banners"
		}).done(function( msg ) {
		  // success: function( msg ){
				
				$('.manual_update_info').html( msg );
		  // }
		});
		
	});
	
	
	
	
	
	
	/* Adzone size tabs */
	/*
	$(".tabs-menu a").on('click', function(e) {
        e.preventDefault();
        $(this).parent().addClass("current");
        $(this).parent().siblings().removeClass("current");
        var tab = $(this).attr("href");
        $(".tab-content").not(tab).css("display", "none");
        $(tab).fadeIn();
    }); */
	
	$('.pas_size_menu_icons a').on('click', function(){
		
		size_menu_actions( this );
		
	});
	function size_menu_actions( item ){
		
		$('.pas_size_menu_icons a').removeClass('selected');
		$(item).addClass('selected');
		$('.pas_menu_box').hide();
		$('.'+$(item).attr('data-target')).show();
	}
	
	
	
	
	// SWITCH BUTTON
	$('.switch_btn input').switchButton({ 
		on_label : wppas_local.variable,
		off_label : wppas_local.fixed,
		width: 50,
  		height: 25,
		button_width: 30,
		on_callback: function(){  $(this.element).val(1); }, 
		off_callback: function(){  $(this.element).val(0);} 
	});
	
	
	
	
	// PAS PATTERN BUTTON - Shortcode generator
	$('.pas_pattern_btn').on('click', function(){
		$('.pas_pattern_btn').removeClass('selected');
		$(this).addClass('selected');
		$('.adzone_background_pattern').val( $(this).attr('pattern') );
	});
	
	
});
	