<?php
class Pro_Ads_Adzones {	

	public function __construct() 
	{
		require_once( WP_ADS_DIR.'classes/Pro_Ads_Adzone_Data.php');
	}
	
	
	
	
	
	/*
	 * Get all adzones
	 *
	 * @access public
	 * @return null
	*/
	public function get_adzones( $custom_args = array() ) 
	{
		global $pro_ads_multisite;
		
		/***
		 * Multisite ___________________________________________________________________ */
		$pro_ads_multisite->wpproads_wpmu_load_from_main_start();
		
		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'adzones',
			'post_status'      => 'publish'
		);
		
		$adzones = get_posts( array_merge( $args, $custom_args ) );
		
		/***
		  * Multisite ___________________________________________________________________ */
		$pro_ads_multisite->wpproads_wpmu_load_from_main_stop();
		
		return $adzones;
	}
	
	
	
	
	
	
	
	/*
	 * Get all adzones
	 *
	 * @access public
	 * @return array
	*/
	public function get_adzone_data( $adzone_id, $args = array() ) 
	{
		global $pro_ads_main, $pro_ads_shortcodes;
		
		$atts = wp_parse_args($args, $pro_ads_shortcodes->default_atts($adzone_id));
		$device_size = !empty($atts['screen']) ? $atts['screen'] : '';
		
		//echo date('d m Y - H:i:s', current_time('timestamp'));
		
		/* ----------------
		 * Queries
		 * ----------------- */
		 
		// shortcode options       
		$is_popup                      = !empty($atts['popup']) ? 1 : 0;
		 
		// Rotation
		$adzone_rotation               = get_post_meta( $adzone_id, '_adzone_rotation', true );
		$adzone_rotation_type          = $adzone_rotation ? get_post_meta( $adzone_id, '_adzone_rotation_type', true ) : 'bxslider';
		$adzone_rotation_ajax          = $adzone_rotation ? get_post_meta( $adzone_id, '_adzone_rotation_ajax', true ) : 0;
		$adzone_rotation_order         = $adzone_rotation ? get_post_meta( $adzone_id, '_adzone_rotation_order', true ) : 'fixed';
		
		// Linked banners
		$linked_banner_ids   		   = get_post_meta( $adzone_id, '_linked_banners', true );
		$linked_banner_ids             = $this->wpproads_reset_linked_banner_ids( $linked_banner_ids );
		$linked_banner_ids             = $adzone_rotation_order == 'equal' ? $this->wpproads_reorder_linked_banner_ids( $linked_banner_ids, $adzone_id ) : $linked_banner_ids;
		
		// Options
		$rotate_class                  = $adzone_rotation && count($linked_banner_ids) > 1 ? 'wppasrotate'.rand() : '';
		$adzone_description  		   = get_post_meta( $adzone_id, '_adzone_description', true );
		$adzone_size                   = get_post_meta( $adzone_id, '_adzone_size'.$device_size, true );
		$adzone_size                   = !empty($adzone_size) ? $adzone_size : get_post_meta( $adzone_id, '_adzone_size', true );
		//$adzone_fix_size               = $this->is_responsive(array('adzone_id' => $adzone_id, 'device_size' => $device_size));
		$adzone_fix_size               = WPPAS_Adzone_Data::is_responsive(array('adzone_id' => $adzone_id, 'device_size' => $device_size));
		
		// The actual responive value is $adzone_fix_size! This one ($responsive) only is used to create full width adzones.
		$responsive          		   = get_post_meta( $adzone_id, '_adzone_responsive'.$device_size, true );
		$responsive                    = !empty($responsive) ? $responsive : get_post_meta( $adzone_id, '_adzone_responsive', true );
		$custom_size                   = get_post_meta( $adzone_id, '_adzone_custom_size'.$device_size, true );
		$custom_size                   = !empty($custom_size) ? $custom_size : get_post_meta( $adzone_id, '_adzone_custom_size', true );
		$grid_horizontal     		   = get_post_meta( $adzone_id, '_adzone_grid_horizontal', true );
		$grid_vertical      		   = get_post_meta( $adzone_id, '_adzone_grid_vertical', true );
		$max_banners        		   = get_post_meta( $adzone_id, '_adzone_max_banners', true );
		$adzone_center      		   = get_post_meta( $adzone_id, '_adzone_center', true );
		$adzone_hide_empty   		   = WPPAS_Adzone_Data::hide_empty_adzone(array('adzone_id' => $adzone_id)); //get_post_meta( $adzone_id, '_adzone_hide_empty', true );
		$adzone_default_url   		   = get_post_meta( $adzone_id, '_adzone_default_url', true );
		$adzone_default_url_link   	   = get_post_meta( $adzone_id, '_adzone_default_url_link', true );
		
		
		//Buy and Sell Options
		if( $pro_ads_main->buyandsell_is_active() || $pro_ads_main->buyandsell_woo_is_active() )
		{
			$adzone_no_buyandsell              = get_post_meta( $adzone_id, '_adzone_no_buyandsell', true );
			$adzone_buyandsell_contract        = get_post_meta( $adzone_id, 'adzone_buyandsell_contract', true );
			$adzone_buyandsell_duration        = get_post_meta( $adzone_id, 'adzone_buyandsell_duration', true );
			$adzone_buyandsell_price           = get_post_meta( $adzone_id, 'adzone_buyandsell_price', true );
			$adzone_buyandsell_est_impressions = get_post_meta( $adzone_id, 'adzone_buyandsell_est_impressions', true );
		}
		else
		{
			$adzone_no_buyandsell              = 0;
			$adzone_buyandsell_contract        = '';
			$adzone_buyandsell_duration        = '';
			$adzone_buyandsell_price           = 0;
			$adzone_buyandsell_est_impressions = 0;
		}
		
		$grid_horizontal = !empty($grid_horizontal) ? $grid_horizontal : 1;
		$grid_vertical = !empty($grid_vertical) ? $grid_vertical : 1;
		
		$limit = $adzone_rotation ? count($linked_banner_ids) : $grid_horizontal * $grid_vertical;
		
		$size = !empty($adzone_size) ? explode('x', $adzone_size) : '';
		
		// If adzone is rotating popup we need to force the size.
		$pop_w = $is_popup && $adzone_rotation ? !empty($size) ? ' width:'.$size[0].'px;' : '' : '';
		
		if( !$adzone_center )
		{
			if( $adzone_fix_size ) // $adzone_fix_size = 1 means it IS responsive
			{
				
				$size_str = !empty($size) ? 'max-width:'.$size[0].'px;'.$pop_w.' max-height:'.$size[1].'px;' : ' width:100%; height:auto; display:inline-block; '; // width:auto; (till v4.6.5)
			}
			else
			{
				$size_str = !empty($size) ? 'width:'.$size[0].'px; height:'.$size[1].'px;' : ' width:100%; height:auto; display:inline-block; '; // width:auto; (till v4.6.5)
			}
		}
		else
		{
			if( $adzone_fix_size ) // $adzone_fix_size = 1 means it IS responsive
			{
				$size_str = !empty($size) ? 'max-width:'.$size[0].'px;'.$pop_w.' max-height:'.$size[1].'px;' : ' width:100%; height:auto; ';
			}
			else
			{
				$size_str = !empty($size) ? 'width:'.$size[0].'px; height:'.$size[1].'px;' : ' width:100%; height:auto; '; 
			} 
		}
		
		$orderby = $adzone_rotation && $adzone_rotation_order != 'random' ? 'post__in' : 'rand';
		$margin = 3;
		
		// Adzone Options
		$css_center = $responsive ? 'text-align:center;' : 'margin: 0 auto; text-align:center;';
		$center_css = $adzone_center ? $css_center : '';
		
		$align_margin = !empty($atts['align']) && $atts['align'] == 'left' ? ' margin:10px 10px 10px 0;' : ' margin:10px 0 10px 10px;';
		$align_css = !empty($atts['align']) ? ' float:'.$atts['align'].'; '.$align_margin : '';
		if(!empty($atts['fixed']))
		{
			$fixed_class = 'id="pas-sticky-div"';
		}
		else
		{
			$fixed_class = '';
		}
		$rotation_class_main = $rotate_class ? 'rotating_paszone' : '';
		$container_max_width = !empty($size) ? 'max-width:'.$size[0].'px;' : '';
		
		// Adzone border style
		$paszone_container_style = '';
		if(!empty($atts['padding']) && !empty($atts['info_text']))
		{
			$paszone_container_style.= $atts['info_text_position'] != 'below' ? 'padding:0 '.$atts['padding'].'px '.$atts['padding'].'px; ' :  'padding:'.$atts['padding'].'px '.$atts['padding'].'px 0; ';
		}
		elseif( !empty($atts['padding']) )
		{
			$paszone_container_style.= 'padding:'.$atts['padding'].'px; ';
		}
		$paszone_container_style.= !empty($atts['background_color']) ? 'background-color:'.$atts['background_color'].'; ' : $paszone_container_style;
		$paszone_container_style.= !empty($atts['border_radius']) ? 'border-radius:'.$atts['border_radius'].'px; ' : $paszone_container_style;
		$paszone_container_style.= !empty($atts['border']) && !empty($atts['border_color']) ? 'border: solid '.$atts['border'].'px '.$atts['border_color'].'; ' : $paszone_container_style;
		
		$adzone_options = array(
			'center_css'              => $center_css,
			'align_margin'            => $align_margin,
			'align_css'               => $align_css,
			'fixed_class'             => $fixed_class,
			'rotation_class_main'     => $rotation_class_main,
			'container_max_width'     => $container_max_width,
			'paszone_container_style' => $paszone_container_style
		);
		
		// Adzone info text
		$info_text = array(
			'info_text'       => !empty($atts['info_text']) ? $atts['info_text'] : '',
			'info_text_img'   => !empty($atts['info_text_img']) ? $atts['info_text_img'] : '',
			'info_text_url'   => !empty($atts['info_text_url']) ? $atts['info_text_url'] : '',
			'position'        => !empty($atts['info_text_position']) ? $atts['info_text_position'] : '',
			'font_size'       => !empty($atts['font_size']) ? $atts['font_size'] : '',
			'font_color'      => !empty($atts['font_color']) ? $atts['font_color'] : '',
			'text_decoration' => !empty($atts['text_decoration']) ? $atts['text_decoration'] : ''
		);
		
		$array = array(
			'adzone_id'                         => $adzone_id,       
			'linked_banner_ids'                 => $linked_banner_ids,
			'adzone_description'                => $adzone_description,
			'adzone_rotation_type'              => !empty($adzone_rotation_type) ? $adzone_rotation_type : 'flexslider',
			'adzone_rotation'                   => $adzone_rotation,
			'rotate_class'                      => $rotate_class,
			'rotate_ajax'                       => $adzone_rotation_ajax,
			'adzone_size'                       => $adzone_size,
			'responsive'                        => $responsive,
			'custom'                            => $custom_size,
			'orderby'                           => $orderby,
			'grid_horizontal'                   => $grid_horizontal,
			'grid_vertical'                     => $grid_vertical,
			'max_banners'                       => $max_banners,
			'limit'                             => $limit,
			'size'                              => $size,
			'size_str'                          => $size_str,
			'margin'                            => $margin,
			'adzone_center'                     => $adzone_center,
			'adzone_hide_empty'                 => $adzone_hide_empty,
			'adzone_default_url'                => $adzone_default_url,
			'adzone_default_url_link'           => $adzone_default_url_link,
			'adzone_no_buyandsell'              => $adzone_no_buyandsell,
			'adzone_buyandsell_contract'        => $adzone_buyandsell_contract,
			'adzone_buyandsell_duration'        => $adzone_buyandsell_duration,
			'adzone_buyandsell_price'           => $adzone_buyandsell_price,
			'adzone_buyandsell_est_impressions' => $adzone_buyandsell_est_impressions,
			'adzone_options'                    => $adzone_options,
			'info_text'                         => $info_text,
			'atts'                              => $atts
		);
		
		return $array;
	}
	

	
	
	
	/*
	 * FILTER/RESET Linked banner ids based on the user defined options. (does the linked banner has to be showen?)
	 *
	 * @param array $linked_banner_ids, int adzone_id
	 * @access public
	 * @return array
	*/
	public function wpproads_reset_linked_banner_ids( $linked_banner_ids )
	{
		global $pro_ads_main, $pro_ads_campaigns, $pro_ads_responsive, $pro_geo_targeting_main;
		
		if( !empty( $linked_banner_ids ))
		{
			foreach( $linked_banner_ids as $i => $bid )
			{
				// Check if campaign is active
				$campaign_id = get_post_meta( $bid, '_banner_campaign_id', true );
				$campaign_status = $pro_ads_campaigns->check_campaign_status( $campaign_id );
				if($campaign_status != 1 )
				{
					$linked_banner_ids = array_diff($linked_banner_ids, array($bid));
				}
				
				// Hide for device
				$device = $pro_ads_responsive->get_device_type();
				$hide_for_device = get_post_meta( $bid, '_banner_hide_for_device'.$device['prefix'], true );
				if( $hide_for_device )
				{
					$linked_banner_ids = array_diff($linked_banner_ids, array($bid));
				}
				
				// Specific category
				$taxonomies = get_taxonomies();
				$banner_categories = get_post_meta( $bid, '_banner_categories', true );
				
				if( !empty($banner_categories))
				{
					$show = 0;
					foreach($banner_categories as $cat )
					{
						foreach($taxonomies as $taxonomy)
						{
							$terms = wp_get_post_terms( get_the_ID(), $taxonomy );
							foreach($terms as $term)
							{
								if($cat == $term->term_id)
								{
									$show = 1;
								}
							}
						}
					}
					if(!$show)
					{
						$linked_banner_ids = array_diff($linked_banner_ids, array($bid));
					}
				}
						
				/*
				 * ADD-ON: Geo Targeting
				 *
				 *_______________________________________________________________________________________________________________
				 * Check if "Geo Targeting Plugin" is installed.
				*/
				if( $pro_ads_main->pro_geo_targeting_is_active() )
				{
					if( !$pro_geo_targeting_main->show_content( $bid ) )
					{
						$linked_banner_ids = array_diff($linked_banner_ids, array($bid));
					}
				}
				/*
				 *_______________________________________________________________________________________________________________
				*/
			}
		}
		
		/*
		 * Filter for developers to adjust adzone linked banners.
		*/
		$linked_banner_ids = apply_filters( 'wp_pro_ads_adzone_linked_banners', $linked_banner_ids);
		
		return $linked_banner_ids;
	}
	
	
	
	
	/*
	 * Reorder Linked banner Ids based on banner impressions in adzone.
	 *
	 * @param array $linked_banner_ids, int adzone_id
	 * @access public
	 * @return array
	*/
	public function wpproads_reorder_linked_banner_ids( $linked_banner_ids, $adzone_id = 0 )
	{
		if( !empty( $linked_banner_ids ))
		{
			$n = array();
			foreach( $linked_banner_ids as $i => $bid )
			{
				$n[$i]['count'] = get_post_meta( $bid, '_adzone_'.$adzone_id.'_impressions', true );
				$n[$i]['id'] = $bid;
			}
			sort($n);
			
			$linked_banner_ids = array();
			foreach( $n as $id )
			{
				$linked_banner_ids[] = $id['id'];
			}
		}
			
		return $linked_banner_ids;
	}
	
	
	
	
	/*
	 * Check if adzone is sill available
	 *
	 * @param int $adzone_id, int $force_active_if_selected  (default: 0), int $banner_id (default: 0 // only used if $force_active_if_selected = 1)
	 * @access public
	 * @return int
	*/
	public function check_if_adzone_is_active( $adzone_id, $force_active_if_selected = 0, $banner_id = 0 ) 
	{
		$active = 1;
		$linked_banners = get_post_meta( $adzone_id, '_linked_banners', true );
		$max_banners    = get_post_meta( $adzone_id, '_adzone_max_banners', true );
		
		if( !empty($max_banners) && is_array($linked_banners) && count($linked_banners) >= $max_banners )
		{
			if( $force_active_if_selected )
			{
				$linked_adzones = get_post_meta( $banner_id, '_linked_adzones', true );
				$active = !empty($linked_adzones) ? in_array($adzone_id, $linked_adzones) ? 1 : 0 : 0;
			}
			else
			{
				$active = 0;
			}
		}
		
		return $active;
	}
	
	
	
	
	
	
	
	/*
	 * Check if adzone has available spots
	 *
	 * @param int $adzone_id
	 * @access public
	 * @return int
	*/
	public function check_if_adzone_has_available_spots( $adzone_id ) 
	{
		global $pro_ads_banners;
		
		$arr = $this->get_adzone_data( $adzone_id );	
		
		$max_ads = !empty($arr['max_banners']) ? $arr['max_banners'] : '';
		$active_banners = 0;
		
		// Count active banners
		if( !empty($arr['linked_banner_ids']))
		{
			$banners = $pro_ads_banners->get_banners( 
				array(
					'posts_per_page' => -1,
					'post__in'       => $arr['linked_banner_ids'], 
					'orderby'        => 'rand', 
					'meta_key'       => '_banner_status',
					'meta_value'     => 1
				)
			);
		}
		
		for($i = 0; $i < $arr['limit']; $i++ )
		{
			if( !empty($banners[$i]) )
			{
				$campaign_id = get_post_meta( $banners[$i]->ID, '_banner_campaign_id', true );
				$campaign_status = get_post_meta( $campaign_id, '_campaign_status', true );
				
				$active_banners = $campaign_status == 1 ? $active_banners+1 : $active_banners;
			}
		}
		
		$available_spots = !empty($max_ads) ? $max_ads - $active_banners : 1;
		
		return $available_spots;
	}
	
	
	
	
	
	/*
	 * Adzone container
	 *
	 * @param int $adzone_id, array $atts
	 * @access public
	 * @return string
	*/
	public function wpproads_adzone_container( $adzone_id, $arr, $banners = array() )
	{
		global $pro_ads_templates;
		
		$html = '';
		$wppasrotate_style = empty($banners) ? 'width:100%; height:100%;' : '';
		
		// attributes
		//$attr_fixed_cont = !empty($arr['atts']['fixed_cont']) ? 'stick_cont="'. $arr['atts']['fixed_cont'].'"' : '';
		$attr_fixed_till = !empty($arr['atts']['fixed_till']) ? 'stick_till="'.$arr['atts']['fixed_till'].'"' : '';
		$html_attributes = $attr_fixed_till;
		
		
		$html.= '<div '.$arr['adzone_options']['fixed_class'].' class="paszone_container paszone-container-'.$adzone_id.' '.$arr['atts']['screen'].' '.$arr['atts']['class'].' '.$arr['atts']['background_pattern'].'" id="paszonecont_'.$adzone_id.'" style="overflow:hidden; '.$arr['adzone_options']['container_max_width'].' '.$arr['adzone_options']['center_css'].' '.$arr['adzone_options']['align_css'].' '.$arr['adzone_options']['paszone_container_style'].'" '.$html_attributes.'>';
					
			// info text - above
			$html.= $pro_ads_templates->adzone_info_text( $arr['info_text'], array('above', 'top-right') );
			
			$html.= '<div class="wppaszone proadszone-'.$adzone_id.' '.$arr['atts']['screen'].'" id="'.$adzone_id.'" style="overflow:hidden; '.$arr['size_str'].' '.$arr['adzone_options']['center_css'].' ">'; //'.$arr['adzone_options']['fixed_class'].'
				$html .= '<div class="wppasrotate '.$arr['adzone_options']['rotation_class_main'].' '.$arr['rotate_class'].' proadszoneholder-'.$adzone_id.'" style="'.$wppasrotate_style.'" >';	
					
					$html.= '%s%';
					
				$html.= '</div>';
				// <!-- end .wppasrotate -->
			$html.= '</div>';
			// <!-- end .wpproadszone -->
			
			// info text - below
			$html.= $pro_ads_templates->adzone_info_text( $arr['info_text'], array('below') );
			
		$html.= '</div>';
		// <!-- end .paszone_container -->
		
		
		return $html;
	}
	
	
	
	
	
	
	/*
	 * Adzone rotation script
	 *
	 * @param int $id, array $arr
	 * @access public
	 * @return string
	*/
	public function wpproads_adzone_rotation_script( $id, $arr )
	{
		extract( $arr['atts'] );
		$html = '';
		
		$rotation_effect = get_post_meta( $id, '_adzone_rotation_effect', true );
		$rotation_effect = $rotation_effect == 'slide' && $arr['adzone_rotation_type'] == 'showoff' ? 'slideLeft' : $rotation_effect;
		$rotation_effect = $rotation_effect == 'slide' && $arr['adzone_rotation_type'] == 'bxslider' || $rotation_effect == 'slide' && $arr['adzone_rotation_type'] == 'flexslider' ? 'horizontal' : $rotation_effect;
		$rotation_effect = $rotation_effect == 'vertical' && $arr['adzone_rotation_type'] == 'showoff' ? 'slideLeft' : $rotation_effect;
		$rotation_effect = !empty($rotation_effect) ? $rotation_effect : 'fade';
		$rotation_time = get_post_meta( $id, '_adzone_rotation_time', true );
		$rotation_time = !empty($rotation_time) ? $rotation_time*1000 : 5000;
		
		
		if( $arr['adzone_rotation_type'] == 'showoff' || $corner_curl )
		{
			// Load jshowoff javascript file
			wp_enqueue_script('wppas_jshowoff');
			
			$html.= '<script type="text/javascript">';
				$html.= 'jQuery(document).ready(function($){';
					$html.= '$(".proadszoneholder-'.$id.'").jshowoff({';
						$html.= 'speed: '.$rotation_time.',';
						$html.= 'effect: "'.$rotation_effect.'",';
						//$html.= 'hoverPause: false,';
						$html.= 'controls: false,';
						$html.= 'links: false';
					$html.= '});'; 
				$html.= '});';
			$html.= '</script>';
		}
		else
		{
			// Load bxslider javascript file
			wp_enqueue_script('wppas_bxslider');
			
			// Check if flyin delay
			$delay = $flyin_delay*1000;
			
			// New rotation option bxslider - @since v4.3.4
			$html.= '<script type="text/javascript">';
				$html.= 'jQuery(window).load(function(){';
					$html.= 'jQuery(function($){';
					
						$html.= $flyin ? 'setTimeout(function(){ ' : '';
						
						$html.= 'var slider_'.$arr['rotate_class'].' = $(".'.$arr['rotate_class'].'").bxSlider({ ';
							$html.= 'mode: "'.$rotation_effect.'",';
							$html.= 'slideMargin: 5,';
							$html.= 'autoHover: true,';
							$html.= 'adaptiveHeight: true,';
							$html.= 'pager: false,';
							$html.= 'controls: false,';
							$html.= 'auto: true,';
							$html.= 'pause: $(".'.$arr['rotate_class'].'").find(".pasli").first().attr("duration"),';
							$html.= 'preloadImages: "all",';
							$html.= 'onSliderLoad: function(currentIndex){ ';
								$html.= '$(".'.$arr['rotate_class'].'").find(".pasli").css("visibility", "visible");';
								
								// already preload the next banner @since v4.5.6.
								if( $arr['rotate_ajax'] )
								{
									$html.= 'var next_banner = $(".'.$arr['rotate_class'].'>.pasli").eq(currentIndex+1).attr("bid");';
									//$html.= 'console.log(next_banner);';
									//$html.= 'next_banner = next_banner != null ? next_banner : currentIndex+1;';
									$html.= 'if( $(".pasli-"+next_banner).hasClass("placeholder")){';
										$html.= 'var banner_id = $(".pasli-"+next_banner).attr("bid");';
										$html.= 'var adzone_id = $(".pasli-"+next_banner).attr("aid");';
										$html.= 'var bclass = "pasli-"+banner_id;';
										$html.= '$.ajax({';
										   $html.= 'cache: false,';
										   $html.= 'type: "POST",';
										   $html.= 'url: "'.admin_url('admin-ajax.php').'",';
										   $html.= 'data: "action=rotation_load_banner&id="+ banner_id+"&aid="+adzone_id';
										$html.= '}).done(function( msg ) {';
										  
											//$html.= 'console.log(msg);';
											$html.= '$("."+bclass).html( msg ); $(".pasli-"+next_banner).removeClass("placeholder")';
										 
										$html.= '});';
									$html.= '}';
								}
								
							$html.= '},';
							$html.= 'onSlideAfter: function( $slideElement, oldIndex, newIndex ){ ';
								$html.= 'slider_'.$arr['rotate_class'].'.setPause($($slideElement).attr("duration"));';
								//$html.= 'console.log();';
								// already preload the next banner @since v4.5.6.
								if( $arr['rotate_ajax'] )
								{
									$html.= 'if( $slideElement.next().hasClass("placeholder")){';
										//$html.= 'console.log("is_placeholder");';
										$html.= 'var banner_id = $slideElement.next().attr("bid");';
										$html.= 'var adzone_id = $slideElement.next().attr("aid");';
										$html.= 'var bclass = "pasli-"+banner_id;';
										$html.= '$.ajax({';
										   $html.= 'cache: false,';
										   $html.= 'type: "POST",';
										   $html.= 'url: "'.admin_url('admin-ajax.php').'",';
										   $html.= 'data: "action=rotation_load_banner&id="+ banner_id+"&aid="+adzone_id';
										$html.= '}).done(function( msg ) {';
										   
											//$html.= 'console.log(msg);';
											$html.= '$("."+bclass).html( msg ); $slideElement.next().removeClass("placeholder")';
										   
										$html.= '});';
									$html.= '}';
								}
								
								//$html.= 'console.log($slideElement.attr("bid")+" "+$slideElement.next().attr("bid"));';
							$html.= '},';
							
						$html.= '});';
						
						$html.= $flyin ? '}, '.$delay.');' : '';
					
					$html.= '});';
				$html.= '});';
			$html.= '</script>';
		}
		
		return $html;
	}	
	
	
	
	
	
	
	/*
	 * Display Adzone
	 *
	 * @access public
	 * @return null
	*/
	public function display_adzone( $id, $atts = array(), $ref_url = '' ) 
	{	
		global $pro_ads_main, $pro_ads_codex, $pro_ads_templates, $pro_ads_banners, $pro_ads_statistics, $pro_ads_multisite, $pro_ads_responsive, $pro_ads_bs_templates, $pro_ads_bs_woo_templates, $pro_geo_targeting_main, $pro_ads_shortcodes;
		
		if( isset( $id ) )
		{
			$show = 1;
			$html = '';
			
			// Shortcode options
			extract( $atts );
			
			$show = WPPAS_Adzone_Data::show_adzone(array('adzone_id' => $id, 'hide_if_loggedin' => $hide_if_loggedin));
			
			if( $show )
			{
				$arr = $this->get_adzone_data($id, $atts);
				$banners = $pro_ads_codex->wpproads_load_adzone_banners( $arr );
				$show = WPPAS_Adzone_Data::show_adzone_banners(array('adzone_id' => $id, 'banners' => $banners));
				$bhtml = '';
				$active_banners = 0;
					
				if( $show )
				{
					if(!empty($banners))
					{
						foreach( $banners as $b => $banner )
						{
							if( $arr['rotate_ajax'] && $b || $atts['ajax_load'] && !$arr['rotate_ajax'] || $atts['ajax_load'] && $arr['rotate_ajax'] && !$b )
							{
								// Add banner container for ajax loads and ajax rotation.
								$bhtml.= $pro_ads_banners->get_banner_item( array(
									'id' => $banner->ID, 
									'aid' => $id, 
									'force_size' => 0, 
									'screen' => $screen, 
									'ref_url' => $ref_url,
									'container' => 1,
									'container_only' => 1
								));
								
								// Load first banner if ajax load from the shortcode is selected.
								if( $atts['ajax_load'] && !$arr['rotate_ajax'] || $atts['ajax_load'] && $arr['rotate_ajax'] && !$b )
								{
									$html.= '<script type="text/javascript">';
									$html.= 'jQuery(window).load(function(){';
										$html.= 'jQuery(function($){';
											$html.= '$.ajax({';
											   $html.= 'cache: false,';
											   $html.= 'type: "POST",';
											   $html.= 'url: "'.admin_url('admin-ajax.php').'",';
											   $html.= 'data: "action=rotation_load_banner&id='.$banner->ID.'&aid='.$id.'"';
											$html.= '}).done(function( msg ) {';
											   
												$html.= '$(".pasli-'.$banner->ID.'").html( msg ); $(".pasli-'.$banner->ID.'").removeClass("placeholder");';
												
											$html.= '});';
										$html.= '});';
									$html.= '});';
									$html.= '</script>';
								}
							}
							else
							{
								$bhtml.= $pro_ads_banners->get_banner_item( array(
									'id' => $banner->ID, 
									'aid' => $id, 
									'force_size' => 0, 
									'screen' => $screen, 
									'ref_url' => $ref_url,
									'container' => 1
								));
								$pro_ads_statistics->save_impression( $banner->ID, $id, $ref_url );
							}
							$active_banners++;
							
						}
					}
					else
					{
						if( !$pro_ads_main->buyandsell_is_active() && !$pro_ads_main->buyandsell_woo_is_active() && !empty($arr['adzone_default_url']) || $arr['adzone_no_buyandsell'] && !empty($arr['adzone_default_url']) )
						{
							$bhtml.= !empty($arr['adzone_default_url_link']) ? '<a href="'.$arr['adzone_default_url_link'].'">' : '';
							$bhtml.= '<img src="'.$arr['adzone_default_url'].'" />';
							$bhtml.= !empty($arr['adzone_default_url_link']) ? '</a>' : '';
						}
						 
						
						if( !$arr['adzone_no_buyandsell'] )
						{
							/*
							 * ADD-ON: Buy and Sell
							 *
							 *_______________________________________________________________________________________________________________
							 * Check if "Buy and Sell Plugin" is installed.
							*/
							if( $pro_ads_main->buyandsell_is_active() )
							{
								$bhtml.= $pro_ads_bs_templates->buyandsell_placeholder( $id );
							}
							/*
							 *_______________________________________________________________________________________________________________
							*/
							
							/*
							 * ADD-ON: Buy and Sell Woowommerce
							 *
							 *_______________________________________________________________________________________________________________
							 * Check if "Buy and Sell Woocommerce Plugin" is installed.
							*/
							if( $pro_ads_main->buyandsell_woo_is_active() )
							{
								$bhtml.= $pro_ads_bs_woo_templates->buyandsell_placeholder( $id );
							}
							/*
							 *_______________________________________________________________________________________________________________
							*/
						}
						
					}
					
					$html.= $pro_ads_codex->wpproads_join_adzone_html( $this->wpproads_adzone_container( $id, $arr, $banners ), $bhtml );
					
					
					// ADzone Rotation
					if( $arr['adzone_rotation'] && count($arr['linked_banner_ids']) > 1 && $active_banners > 1 )
					{
						$html.= $this->wpproads_adzone_rotation_script( $id, $arr );
					}
				}
				
				/*
				 * Filter for developers to adjust adzone.
				*/
				$data = apply_filters( 'wp_pro_ads_adzone_html', array(
					'adzone_id' => $id,
					'options' => $arr, 
					'active_banners' => $active_banners,
					'html' => $html
				));
				
				
				return $data;
			}
		}
		else
		{
			return __('Woops! we cannot find the adzone your looking for!', 'wpproads');	
		}
	}
	
	
	
	
	
	/*
	 * Display Adzone Grid
	 *
	 * @access public
	 * @return null
	*/
	public function display_adzone_grid( $id, $atts = array() )
	{
		global $pro_ads_main, $pro_ads_codex, $pro_ads_banners, $pro_ads_statistics, $pro_ads_templates, $pro_ads_responsive, $pro_ads_bs_templates, $pro_ads_bs_woo_templates, $pro_geo_targeting_main, $pro_ads_multisite, $pro_ads_shortcodes;
		
		if( isset( $id ) )
		{
			$html = '';
			extract( $atts );
	
			$show = WPPAS_Adzone_Data::show_adzone(array('adzone_id' => $id, 'hide_if_loggedin' => $hide_if_loggedin));
			
			if( $show )
			{
				$arr = $this->get_adzone_data($id);
				$banners = $pro_ads_codex->wpproads_load_adzone_banners( $arr );
				$show = WPPAS_Adzone_Data::show_adzone_banners(array('adzone_id' => $id, 'banners' => $banners));
				
				if( $show )
				{
					$grid_limit = $arr['grid_horizontal'] * $arr['grid_vertical'];
					$responsive = 0;
					$active_banners = 0;
				
					if( !empty($arr['size']) )
					{
						$size_str = 'width:'.$arr['size'][0].'px; height:'.$arr['size'][1].'px;';
					}
					else
					{
						$responsive = 1;
						$w = 100/$arr['grid_horizontal'] - $arr['margin']*$arr['grid_horizontal'];
						$size_str = 'width:'.$w.'%; height:auto; min-height:80px;';
					}
				
					$html.= '<div '.$arr['adzone_options']['fixed_class'].' class="wppaszone proadszone-'.$id.' wpproadgrid '.$class.'" style="overflow:hidden; '.$arr['adzone_options']['center_css'].' '.$arr['adzone_options']['align_css'].'">';
						$html.= !empty($info_text) ? '<div class="pasinfotxt"><small>'.$info_text.'</small></div>' : '';
						$html.= '<div style="display:inline-block;">';
							
							$responsive_class = $responsive ? 'grid_container_responsive' : '';
							$b = 1;
							for($i = 0; $i < $grid_limit; $i++ )
							{
								// check if campaign is active
								$campaign_status = 1;
								if( !empty($banners[$i]) )
								{
									$campaign_status = $pro_ads_codex->wpproads_campaign_is_active( $banners[$i]->ID );
								}
								
								if( !empty($banners[$i]) && $campaign_status == 1 )
								{
									$force_sice = !$responsive ? $arr['adzone_size'] : 0;
									
									
									$html.= '<div class="grid_container '.$responsive_class.' grid_container_'.$id.'" style="float:left; '.$size_str.' margin:'.$arr['margin'].'px;">';
										$html.= $pro_ads_banners->get_banner_item( array('id' => $banners[$i]->ID, 'aid' => $id, 'force_size' => $arr['adzone_size']));
									$html.= '</div>';
									
									$pro_ads_statistics->save_impression( $banners[$i]->ID, $id );
									$active_banners++;
								}
								else
								{
									if( !$arr['adzone_hide_empty'] ) // || !$arr['adzone_no_buyandsell'] 
									{
										$html.= '<div class="grid_container '.$responsive_class.' grid_container_'.$id.'" style="float:left; '.$size_str.' margin:'.$arr['margin'].'px; background:#EEE;">';
											
											if( !$pro_ads_main->buyandsell_is_active() && !$pro_ads_main->buyandsell_woo_is_active() && !empty($arr['adzone_default_url']) || $arr['adzone_no_buyandsell'] && !empty($arr['adzone_default_url']) )
											{
												$html.= '<img src="'.$arr['adzone_default_url'].'" />';
											}
											
											if( !$arr['adzone_no_buyandsell'] )
											{
												/*
												 * ADD-ON: Buy and Sell
												 *
												 *_______________________________________________________________________________________________________________
												 * Check if "Buy and Sell Plugin" is installed.
												*/
												if( $pro_ads_main->buyandsell_is_active() )
												{
													$html.= $pro_ads_bs_templates->buyandsell_placeholder( $id );
												}
												/*
												 *_______________________________________________________________________________________________________________
												*/
												
												/*
												 * ADD-ON: Buy and Sell Woowommerce
												 *
												 *_______________________________________________________________________________________________________________
												 * Check if "Buy and Sell Woocommerce Plugin" is installed.
												*/
												if( $pro_ads_main->buyandsell_woo_is_active() )
												{
													$html.= $pro_ads_bs_woo_templates->buyandsell_placeholder( $id );
												}
												/*
												 *_______________________________________________________________________________________________________________
												*/
											}
											
										$html.= '</div>';
									}
								}
								
								if( $b == $arr['grid_horizontal'] )
								{
									$html.= '<div style="clear:both;"></div>';
									$b = 0;
								}
								$b++;
							}
							
							$html.= '<div style="clear:both;"></div>';
						$html.= '</div>';
					$html.= '</div>';
				}
				
				/*
				 * Filter for developers to adjust adzone.
				*/
				$html = apply_filters( 'wp_pro_ads_adzone_html', array(
					'adzone_id' => $id,
					'options' => $arr, 
					'html' => $html
				));
				
				return $html['html'];
			}
			
		}	
	}
	
	
	
	
	
	
	
	/*
	 * Display Adzone as Background ad
	 *
	 * @access public
	 * @return html
	*/
	public function display_adzone_as_background( $atts ) 
	{	
		global $pro_ads_main, $pro_ads_banners, $pro_ads_codex, $pro_ads_statistics, $pro_ads_responsive;
		
		extract( $atts );
		
		$html = '';
		$adzone_id = $id;
		
		if( !empty($adzone_id))
		{
			$show = WPPAS_Adzone_Data::show_adzone(array('adzone_id' => $id, 'hide_if_loggedin' => $hide_if_loggedin));
			
			if( $show )
			{
				$arr = $this->get_adzone_data($adzone_id);
				$banners = $pro_ads_codex->wpproads_load_adzone_banners( $arr );
				$show = WPPAS_Adzone_Data::show_adzone_banners(array('adzone_id' => $adzone_id, 'banners' => $banners));
					
				if( $show )
				{
					if( !empty($banners) || empty($banners) && !empty($arr['adzone_default_url']) )
					{
						if( !empty($banners) )
						{
							$active_banners = 0;
							$banner_type = get_post_meta( $banners[0]->ID, '_banner_type', true );
							$banner_is_image = $pro_ads_banners->check_if_banner_is_image($banner_type);
							$banner_url = get_post_meta( $banners[0]->ID, '_banner_url', true );
							//$banner_link = get_post_meta( $banners[0]->ID, '_banner_link', true );
							$banner_link = addslashes( $pro_ads_banners->pro_ads_create_banner_link(array('banner_id' => $banners[0]->ID, 'adzone_id' => $adzone_id)) );
							$banner_target = get_post_meta( $banners[0]->ID, '_banner_target', true );
							
							// Save Impression
							$pro_ads_statistics->save_impression( $banners[0]->ID, $adzone_id );
						}
						else
						{
							// Show Default adzone image.
							if( !empty($arr['adzone_default_url']) )
							{
								$banner_link = !empty($arr['adzone_default_url_link']) ? $arr['adzone_default_url_link'] : '';
								$banner_url = $arr['adzone_default_url'];
								$banner_target = '_blank';
							}
						}
					
					
						$pas_container = empty($container) ? 'body' : $container;
						$pas_container_type = empty($container_type) && $pas_container != 'body' ? '#' : $container_type;
						$pas_container_prefix = $pas_container != 'body' ? $pas_container_type : '';
						$bg_repeat = empty($repeat) ? 'no-repeat' : 'repeat';
						$bg_stretch = empty($stretch) ? '' : '-webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;';
						$bg_color = empty($bg_color) ? '' : $bg_color;
						//http://work.tunasite.com/wp-content/uploads/2014/12/bg_ad_example.jpg
						$html.= '<style type="text/css" id="custom-background-css">';
						$html.= $pas_container_prefix.$pas_container.' { background-position:top center !important; background-color: '.$bg_color.' !important; background-image: url("'.$banner_url.'") !important; background-repeat: '.$bg_repeat.' !important; background-attachment: fixed !important; '.$bg_stretch.'}';
						$html.= '</style>';
						$html.= '<script type="text/javascript">/* <![CDATA[ */';
							$html.= 'var clickable_paszone = {';
							//$html.= '"link_left":"http://www.tunasite.com",';
							//$html.= '"link_right":"http://www.tunasite.com",';
							//$html.= '"link_left_target":"_blank",';
							//$html.= '"link_right_target":"_blank",';
							$html.= '"link_full":"'.$banner_link.'",';
							$html.= '"link_full_target":"'.$banner_target.'",';
							$html.= '"pas_container":"'.$pas_container.'"';
							$html.= '};';
						$html.= '/* ]]> */'; 
						$html.= 'jQuery(document).ready(function($){';
							$html.= '$(document).mousemove(function(event){';
								$html.= 'var target = $( event.target );';
								$html.= 'var target_id = event.target.id;';
								$html.= 'if(target.is(clickable_paszone.pas_container) || target_id == clickable_paszone.pas_container){';
									$html.= 'target.css("cursor", "pointer");';
									$html.= '$("#"+target_id).css("cursor", "pointer");';
								$html.= '}else{';
									$html.= '$(clickable_paszone.pas_container).css("cursor","auto");';
									$html.= '$("#"+clickable_paszone.pas_container).css("cursor","auto");';
								$html.= '}';
							$html.= '});'; 
						$html.= '});';
						$html.= '</script>';
					}
				}
			}
		}
		else
		{
			$html.= __('Woops! we cannot find the adzone your looking for!', 'wpproads');	
		}
		
		return $html;
	}
	
	
	
	
	
	
	/*
	 * Display Adzone as Corner Curl
	 *
	 * @access public
	 * @return html
	*/
	public function display_adzone_as_corner_curl($atts)
	{
		global $pro_ads_main, $pro_ads_banners, $pro_ads_statistics, $pro_ads_responsive, $pro_ads_bs_templates, $pro_geo_targeting_main;
		
		extract( $atts );
		
		wp_enqueue_script('wp_pro_add_corncurl');
		
		$html = '';
		$adzone_id = $id;
		
		if( !empty($adzone_id))
		{
			$show = WPPAS_Adzone_Data::show_adzone(array('adzone_id' => $id, 'hide_if_loggedin' => $hide_if_loggedin));
			
			if( $show )
			{
				$adzone = $this->display_adzone( $adzone_id, $atts );
				$html.= '<div id="corncurl-cont"></div>';
				$html.= '<div id="corncurl-peel"><img src="'.WP_ADS_URL.'images/corner_curl.png"></div>';
				$html.= '<div id="corncurl-small-img"></div>';
				$html.= '<div id="corncurl-bg"><div class="corncurl-content">'.$adzone['html'].'</div></div>';
				
				$html.= '<script type="text/javascript">';	
					$html.= 'jQuery(document).ready(function($){ PAScorncurl({ corncurlSmall: '.$corner_small.', corncurlBig: '.$corner_big.', cornerAnimate:'.$corner_animate.' }); });';
				$html.= '</script>';
			}
		}
		else
		{
			$html.= __('Woops! we cannot find the adzone your looking for!', 'wpproads');	
		}
		
		return $html;
	}
	
	
	
	
	
	
	
	
	
	/*
	 * Output Adzones
	 *
	 * @access public
	 * @param string $size, int $custom (default: 0), int $responsive (default:0)
	 * @return array or string
	*/
	public function pro_ad_output_adzone_size( $size, $custom = 0, $responsive = 0 )
	{
		if( !$custom && !$responsive )
		{
			$arr = array(
				'468x60'  => 'IAB Full Banner (468 x 60)',
				'120x600' => 'IAB Skyscraper (120 x 600)',
				'728x90'  => 'IAB Leaderboard (728 x 90)',
				'300x250' => 'IAB Medium Rectangle (300 x 250)',
				'120x90'  => 'IAB Button 1 (120 x 90)',
				'160x600' => 'IAB Wide Skyscraper (160 x 600)',
				'120x60'  => 'IAB Button 2 (120 x 60)',
				'125x125' => 'IAB Square Button (125 x 125)',
				'180x150' => 'IAB Rectangle (180 x 150)'
			);
			
			return $arr[$size];
		}
		elseif( $custom )
		{
			$sz = explode('x', $size);	
			return 'Custom ('.$sz[0].' x '.$sz[1].')';
		}
		else
		{
			return __('Full Width (100%)','wpproads');
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	/*
	 * Link Banner to adzone
	 *
	 * @access public
	 * @param 
	 * @return void
	*/
	public function pro_ad_link_banner_to_adzone( $adzone_id, $banner_id, $action_type = '' )
	{
		global $pro_ads_adzones, $pro_ads_banners;
	
		// link banner to adzone
		//update_post_meta( $_POST['aid'], '_linked_banners', ''  );
		$this->pro_ad_adzone_clean_linked_banners_array( $adzone_id );
		$linked_banners = get_post_meta( $adzone_id, '_linked_banners', true );
		$max_banners    = get_post_meta( $adzone_id, '_adzone_max_banners', true );
		$banner_status  = get_post_meta( $banner_id, '_banner_status', true );
		
		if( empty( $linked_banners ))
		{
			if( $pro_ads_adzones->check_if_adzone_is_active( $adzone_id ) && $banner_status == 1 || $pro_ads_adzones->check_if_adzone_is_active( $adzone_id ) && $banner_status == 3)
			{
				$linked_banners = array( $banner_id );
				update_post_meta( $adzone_id, '_linked_banners', array_values(array_filter($linked_banners))  );
				
				// link adzone to banner
				$pro_ads_banners->pro_ad_link_adzone_to_banner( $banner_id, $adzone_id, $action_type );
			}
		}
		else
		{
			if( $action_type == 'remove' )
			{
				if (($key = array_search($banner_id, $linked_banners)) !== false) unset($linked_banners[$key]);
				// link adzone to banner
				$pro_ads_banners->pro_ad_link_adzone_to_banner( $banner_id, $adzone_id, $action_type );
			}
			else
			{
				if( $pro_ads_adzones->check_if_adzone_is_active( $adzone_id ) && $banner_status == 1 || $pro_ads_adzones->check_if_adzone_is_active( $adzone_id ) && $banner_status == 3)
				{
					array_push($linked_banners, $banner_id);
					// link adzone to banner
					$pro_ads_banners->pro_ad_link_adzone_to_banner( $banner_id, $adzone_id, $action_type );
				}
			}
			update_post_meta( $adzone_id, '_linked_banners', array_values(array_filter($linked_banners)) );
		}
	}
	
	
	
	
	
	
	
	/*
	 * Clean linkedbanners array
	 *
	 * @access public
	 * @param 
	 * @return void
	*/
	public function pro_ad_adzone_clean_linked_banners_array( $adzone_id )
	{
		global $pro_ads_adzones, $pro_ads_banners;
		
		$linked_banners = get_post_meta( $adzone_id, '_linked_banners', true );
		
		if( !empty( $linked_banners ))
		{
			foreach( $linked_banners as $banner )
			{
				$check_banner = $pro_ads_banners->get_banners( array( 'post__in' => array( $banner ) ) );
				
				if(empty($check_banner))
				{
					if (($key = array_search($banner, $linked_banners)) !== false) unset($linked_banners[$key]);
					update_post_meta( $adzone_id, '_linked_banners', array_values(array_filter($linked_banners)) );	
				}
			}
		}
		
	}
	
}
?>