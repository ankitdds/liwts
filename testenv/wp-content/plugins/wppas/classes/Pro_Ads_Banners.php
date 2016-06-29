<?php
class Pro_Ads_Banners {	

	public function __construct() 
	{
		// Banner click ---------------------------------------------------
		//add_action( 'wp_loaded', array( $this, 'pro_ad_click_action' ) );	
		add_action( 'wp', array( $this, 'pro_ad_click_action' ) );	
	}
	
	
	
	
	/*
	 * Get all banners
	 *
	 * @access public
	 * @return array
	*/
	public function get_banners( $custom_args = array() ) 
	{	
		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'banners',
			'post_status'      => 'publish'
		);
		
		$query = new WP_Query( array_merge( $args, $custom_args ) );
		return $query->get_posts();
		//return get_posts( wp_parse_args( $custom_args, $args ) );
	}
	
	
	
	
	
	/*
	 * Check banner status
	 *
	 * @access public
	 * @return array
	*/
	public function get_status( $status_nr ) 
	{	
		if( $status_nr == 1 )
		{
			$status = array( 
				'name'       => __('Active','wpproads'), 
				'name_clean' => 'active', 
			);
		}
		elseif( $status_nr == 2 )
		{
			$status = array( 
				'name'       => __('Inactive','wpproads'), 
				'name_clean' => 'inactive', 
			);
		}
		elseif( $status_nr == 3 )
		{
			$status = array( 
				'name'       => __('Awaiting Review','wpproads'), 
				'name_clean' => 'awaiting-review', 
			);
		}
		else
		{
			$status = array( 
				'name'       => __('Draft','wpproads'), 
				'name_clean' => 'draft', 
			);
		}
		
		return $status;
	}
	
	
	
	
	
	
	
	
	
	
	/*
	 * Update banner Status - for updating
	 *
	 * status: 0 = draft, 1 = active, 2 = inactive, 3 = awaiting payment
	 * contract: 0 = no contract, 1 = pay per click, 2 = pay per view, 3 = duration (days)
	 *
	 * @access public
	 * @param int $status, string $sdate, string $edate
	 * @return int $status
	*/
	public function update_banner_status( $banner_id )
	{
		global $pro_ads_main;
		
		$banner_contract     = get_post_meta( $banner_id, '_banner_contract', true );
		$banner_duration     = get_post_meta( $banner_id, '_banner_duration', true );
		$status              = get_post_meta( $banner_id, '_banner_status', true );
		
		if( !empty($banner_contract) && !empty($banner_duration) )
		{
			if($banner_contract == 1)
			{
				$banner_clicks = get_post_meta( $banner_id, '_banner_clicks', true );
				if($banner_clicks >= $banner_duration)
				{
					$status = 2;
					$this->remove_banner_from_adzone( $banner_id );
				}
			}
			elseif($banner_contract == 2)
			{
				$banner_impressions = get_post_meta( $banner_id, '_banner_impressions', true );
				if($banner_impressions >= $banner_duration)
				{
					$status = 2;
					$this->remove_banner_from_adzone( $banner_id );
				}
			}
			elseif($banner_contract == 3)
			{
				$banner_start_date = get_post_meta( $banner_id, '_banner_start_date', true );
				$banner_start_date = empty($banner_start_date) ? time() : $banner_start_date;
				$day_str = $banner_duration > 1 ? 'days' : 'day';
				$end_date = strtotime('+'.$banner_duration.' '.$day_str, $banner_start_date);
				if( $end_date < $pro_ads_main->time_by_timezone() )
				{
					$status = 2;
					$this->remove_banner_from_adzone( $banner_id );
				}
			}
			
			update_post_meta( $banner_id, '_banner_status', $status );
		}
		
		return $status;
	}
	
	
	
	
	
	
	
	/*
	 * Remove Banner from all adzones it's linked to
	 *
	 * @param 
	 * @access public
	 * @return null
	*/
	public function remove_banner_from_adzone( $banner_id ) 
	{
		$linked_adzones = get_post_meta( $banner_id, '_linked_adzones', true );
		
		if(!empty($linked_adzones))
		{
			foreach($linked_adzones as $adzone_id)
			{
				$linked_banners = get_post_meta( $adzone_id, '_linked_banners', true );
				if( !empty( $linked_banners ))
				{
					if (($key = array_search($banner_id, $linked_banners)) !== false) unset($linked_banners[$key]);
					//print_r(array_values(array_filter($linked_banners)));
					update_post_meta( $adzone_id, '_linked_banners', array_values(array_filter($linked_banners)) );
				}
			}
			
			update_post_meta( $banner_id, '_linked_adzones', ''  );
		}
	}
	
	
	
	
	
	
	
	
	
	/*
	 * Preview banner
	 *
	 * @access public
	 * @return array
	*/
	public function check_if_banner_is_image( $type ) 
	{	
		$res = 0;
		
		if( $type == 'jpg' || $type == 'jpeg' || $type == 'png' || $type == 'gif' )
		{
			$res = 1;
		}
		
		return $res;
	}
	
	
	
	
	
	
	
	/*
	 * Load banner IDs linked by specific adzone
	 *
	 * @access public
	 * @return array
	*/
	
	public function load_banner_ids_by_adzone( $aid ) 
	{	
		$args = array(
			'numberposts' => -1,
			'post_type' => 'banners'
		 );		
		$banners = get_posts( $args );
		$ids = array();
		
		foreach( $banners as $banner)
		{
			$links = get_post_meta($banner->ID, "_linked_adzones", true);
			$res = !empty($links) ? in_array( $aid, $links ) ? $banner->ID : '' : '';
			if( !empty($res) )
			{
				$ids[] = $res;
			}
		}
		
		return $ids;
	}
	
	
	
	
	
	
	/*
	 * Get banner preview, image - object - or placeholder
	 *
	 * @access public
	 * $img_view (background | url)
	 * @return html
	*/
	public function get_banner_preview( $id, $screen = '', $show_html = 0, $img_view = 'background', $size = array('width' => '', 'height' => '') ) 
	{	
		$html = '';
		$banner_type = get_post_meta( $id, '_banner_type'.$screen, true );
		$banner_url = get_post_meta( $id, '_banner_url'.$screen, true );
		$banner_html = get_post_meta( $id, '_banner_html'.$screen, true );
		$banner_is_html5 = get_post_meta( $id, '_banner_is_html5'.$screen, true );
		$banner_is_image = $this->check_if_banner_is_image($banner_type);
		
		if( $banner_is_image )
		{
			$img = !empty($banner_url) ? $banner_url : WP_ADS_URL.'images/placeholder.png';
			if( $img_view == 'background' )
			{
				$html.= '<div class="preview_banner" style="background: url('.$img.') no-repeat center center; width:'.$size['width'].'px; height:'.$size['height'].'px;"></div>';
			}
			else
			{
				$html.= '<img src="'.$img.'" width="'.$size['width'].'" style="max-width:100%;" />';
			}
		}
		elseif( $banner_type == 'swf')
		{
			$html.= "<object>";
				$html.=  "<embed allowscriptaccess='always' id='banner-swf' width='".$size['width']."' height='".$size['height']."' src='".$banner_url."'>";
			$html.= "</object>";
		}
		elseif( $banner_is_html5 )
		{
			$html.= !empty($banner_html) ? '<iframe src="'.get_bloginfo('url').'?wpproads-html5='.$id.'"></iframe>' : __('No banner Selected.','wpproads');
		}
		else
		{
			if( $show_html )
			{
				$html.= !empty($banner_html) ? do_shortcode($banner_html) : __('No banner Selected.','wpproads');
			}
			else
			{
				$html.= '<img src="'.WP_ADS_URL.'images/placeholder.png" width="'.$size['width'].'" />';
			}
		}
		
		return $html;
	}
	
	
	
	
	
	
	/*
	 * Get banner, image - object - or html
	 *
	 * @access public
	 * @return html
	*/
	public function get_banner_item( $args = array() ) // $id, $aid = '', $force_size = '', $screen = '', $ref_url = ''
	{
		global $pro_ads_main;
		
		$defaults = array(
			'id'             => 0,
			'aid'            => 0,
			'force_size'     => 0,
			'screen'         => '',
			'ref_url'        => '',
			'container'      => 1,
			'container_only' => 0,
			'random_str'     => '?pas='.rand().date('ymdHi')
		);
		$data = wp_parse_args($args, $defaults);
		extract( $data );
		
		$banner_type          = get_post_meta( $id, '_banner_type'.$screen, true );
		$banner_type          = !empty($banner_type) ? $banner_type : get_post_meta( $id, '_banner_type', true );
		$banner_url           = get_post_meta( $id, '_banner_url'.$screen, true );
		$banner_url           = !empty($banner_url) ? $banner_url : get_post_meta( $id, '_banner_url', true );
		$banner_url           = !empty($banner_url) ? $banner_url.$random_str : '';
		$banner_size          = get_post_meta( $id, '_banner_size'.$screen, true );
		$banner_size          = !empty($banner_size) ? $banner_size : get_post_meta( $id, '_banner_size', true );
		$banner_link          = get_post_meta( $id, '_banner_link', true );
		$banner_target        = get_post_meta( $id, '_banner_target', true );
		$banner_no_follow     = get_post_meta( $id, '_banner_no_follow', true );
		$banner_start_date    = get_post_meta( $id, '_banner_start_date', true );
		$fallback_image       = get_post_meta( $id, '_banner_fallback_image', true );
		$transition_duration  = get_post_meta( $id, '_banner_transition_duration', true );
		$banner_is_html5      = get_post_meta( $id, '_banner_is_html5'.$screen, true );
		$adzone_rotation_time = $aid && empty($transition_duration) ? get_post_meta( $aid, '_adzone_rotation_time', true ) : $transition_duration;
		$adzone_rotation_time = !empty($adzone_rotation_time) ? $adzone_rotation_time*1000 : 5000;
		
		$transition_duration = !empty($transition_duration) ? $transition_duration*1000 : 5000;
		$transition_duration = !empty($adzone_rotation_time) ? $adzone_rotation_time : $transition_duration;
		$rel = $banner_no_follow ? 'rel="nofollow"' : '';
		$banner_is_image = $this->check_if_banner_is_image($banner_type);
		$click_tag = !empty( $banner_link ) ? '&clickTAG='.$this->pro_ads_create_banner_link(array('banner_id' => $id, 'adzone_id' => $aid, 'ref_url' => $ref_url)) : '';
		
		$size = !empty($banner_size) ? explode('x', $banner_size ) : '';
		$size = !empty($force_size) ? explode('x', $force_size ) : $size;
		$size_str = !empty($force_size) ? 'width="'.$size[0].'" ' : '';
		$today = mktime(0, 0, 0, $pro_ads_main->time_by_timezone('m')  , $pro_ads_main->time_by_timezone('d'), $pro_ads_main->time_by_timezone('Y'));
		
		$html = '';
		
		if( !$container_only )
		{
			if( $banner_is_image )
			{
				$img = !empty($banner_url) ? $banner_url : WP_ADS_URL.'images/placeholder.png';
				$html.= '<img src="'.$img.'" alt="'.get_the_title($id).'" border="0" '.$size_str.' />'; //?t='.time().'
			}
			elseif( $banner_type == 'swf')
			{
				$fallback_link = $this->pro_ads_create_banner_link(array('banner_id' => $id, 'adzone_id' => $aid, 'ref_url' => $ref_url));
				$fallback_and_link = !empty( $fallback_image ) ? '<a href="'.$fallback_link.'" target="'.$banner_target.'" '.$rel.'><img src="'.$fallback_image.'" /></a>' : '';
				$banner_url = !empty($banner_url) ? $banner_url.$click_tag : '';
				
				$flash_width = !empty($size[0]) ? 'width="'.$size[0].'"' : '';
				$flash_height = !empty($size[1]) ? 'height="'.$size[1].'"' : '';
				
				// http://www.eionet.europa.eu/software/design/flashembedding
				// http://www.flashclicktag.com/
				$html.= '<object id="flash_'.$id.'" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" '.$flash_width.' '.$flash_height.'>';
				$html.= '<param name="movie" value="'.$banner_url.'" />';
				$html.= '<param name="allowFullScreen" value="true"></param>';
				$html.= '<param name="allowscriptaccess" value="always"></param>';
				$html.= '<param name="wmode" value="transparent"></param>';
				$html.= '<param name="quality" value="high" />';
				$html.= '<!--[if !IE]> <-->';
				$html.= '<object name="flash_'.$id.'" data="'.$banner_url.'" '.$flash_width.' '.$flash_height.' type="application/x-shockwave-flash">';
				$html.= '<param name="allowFullScreen" value="true"></param>';
				$html.= '<param name="allowscriptaccess" value="always"></param>';
				$html.= '<param name="wmode" value="transparent"></param>';
				$html.= '<param name="quality" value="high" />';
				$html.= '<param name="pluginurl" value="http://www.macromedia.com/go/getflashplayer" />';
				$html.= '<!--> <![endif]-->';
				$html.= $fallback_and_link;
				$html.= '<!--[if !IE]> <-->';
				$html.= '</object>';
				$html.= '<!--> <![endif]-->';
				$html.= '</object>';
			}
			elseif( $banner_is_html5 )
			{
				$banner_html5_w = get_post_meta( $id, '_banner_html5_w'.$screen, true );
				$banner_html5_h = get_post_meta( $id, '_banner_html5_h'.$screen, true );
				$html.= '<iframe src="'.get_bloginfo('url').'?wpproads-html5='.$id.'" width="'.$banner_html5_w.'" height="'.$banner_html5_h.'"></iframe>';
			}
			else
			{
				$banner_html = get_post_meta( $id, '_banner_html'.$screen, true );
				// If banner html for the specific screen is empty we load the original html.
				$banner_html = empty($banner_html) ? get_post_meta( $id, '_banner_html', true ) : $banner_html;
				
				$html.= do_shortcode($banner_html);
			}
			
			/* ---------------------------------------------------------------
			 * Create link
			 * --------------------------------------------------------------- */
			 
			/*
			 * ADD-ON: Buy and Sell
			 *
			 *_______________________________________________________________________________________________________________
			 * Check if "Buy and Sell Plugin" is installed.
			*/
			$buyandsell_link = '';
			
			if( $pro_ads_main->buyandsell_is_active() )
			{
				global $pro_ads_bs_main, $pro_ads_bs_templates;
				
				$buyandsell_order_screen_url = $pro_ads_bs_main->buyandsell_product_url();
				$buyandsell_popup = $banner_link == '%buyandsell_popup%' ? '<a adzone_id="'.$aid.'" popup_type="buy" ajaxurl="'.admin_url('admin-ajax.php').'" href="" class="buyandsell topopup" target="_top">' : '';
				$buyandsell_page = $banner_link == '%buyandsell_page%' ? '<a adzone_id="'.$aid.'" href="'.$buyandsell_order_screen_url.'?adzone_id='.$aid.'" class="buyandsell nopop">' : '';
				$buyandsell_link = !empty($buyandsell_popup) ? $buyandsell_popup : $buyandsell_page;
				$html.= !empty($buyandsell_popup) && empty($buyandsell_order_screen_url) ? $pro_ads_bs_templates->pro_ad_buy_and_sell_popup_screen( array('adzone_id' => $aid, 'popup_type' => 'buy', 'container_text' => __('Advertise Here','wpproads')) ) : '';
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
				global $pro_ads_bs_woo_main;
				
				$url = add_query_arg( 'adzone_id', $aid, $pro_ads_bs_woo_main->buyandsell_product_url() );
				$buyandsell_woo_link = $banner_link == '%buyandsell_page%' ? '<a adzone_id="'.$aid.'" href="'.$url.'" class="buyandsell nopop">' : '';
				
				$buyandsell_link = !empty($buyandsell_woo_link) ? $buyandsell_woo_link : '';
			}
			/*
			 *_______________________________________________________________________________________________________________
			*/
			
			
			$link = !empty( $banner_link ) && $banner_type != 'swf' && empty($buyandsell_link) ? '<a class="wpproaddlink" href="'.$this->pro_ads_create_banner_link(array('banner_id' => $id, 'adzone_id' => $aid, 'ref_url' => $ref_url)).'" target="'.$banner_target.'" '.$rel.'>' : $buyandsell_link;
			
			$html = !empty($link) ? $link.$html.'</a>' : $html;
		}
		
		// Container Only options.
		$placeholder = $container_only ? 'placeholder' : '';
		$default_size = $container_only ? 'style="width:'.$size[0].'px; height:'.$size[1].'px;"' : '';
		
		return $container ? '<div class="pasli pasli-'.$id.' '.$placeholder.'" '.$default_size.'  duration="'.$transition_duration.'" bid="'.$id.'" aid="'.$aid.'">'.$html.'</div>' : $html;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	 * Create banner link
	 *
	 * @access public
	 * @return html
	*/
	public function pro_ads_create_banner_link($args = array()) // $banner_id, $adzone_id, $ref_url = ''
	{
		global $pro_ads_main, $pro_ads_multisite;
		
		$defaults = array(
			'banner_id'      => 0,
			'adzone_id'      => 0,
			'ref_url'        => ''
		);
		$data = wp_parse_args($args, $defaults);
		extract( $data );
		
		$banner_link          = get_post_meta( $banner_id, '_banner_link', true );
		$banner_target        = get_post_meta( $banner_id, '_banner_target', true );
		$remove_link_masking  = get_post_meta( $banner_id, '_banner_remove_link_masking', true );
		
		$mod_rewrite       = get_option( 'wpproads_enable_mod_rewrite', 0);
		$mod_rewrite_str   = get_option('wp_ads_mod_rewrite', 'pas');
		
		// Create link
		if( empty($remove_link_masking) )
		{
			if( !$mod_rewrite )
			{
				$adzone_str = !empty($adzone_id) ? '&amp;pasZONE='.base64_encode($adzone_id) : '';
				$ref_url_str = !empty($ref_url) ? '&amp;pasREF='.base64_encode($ref_url) : '';
				$link = !empty($banner_link) ? $pro_ads_multisite->wpproads_get_site_url().'?pasID='.base64_encode($banner_id).$adzone_str.$ref_url_str : '';
			}
			else
			{
				$adzone_str = !empty( $adzone_id ) ? $adzone_id : 0;
				$banner_slug = get_post( $banner_id )->post_name;
				$adzone_slug = !empty($adzone_str ) ? get_post( $adzone_str )->post_name : '0';
				$ref_url_slug = !empty($ref_url) ? '/'.base64_encode($ref_url) : '/0';
				$link = !empty( $banner_link ) ? $pro_ads_multisite->wpproads_get_site_url().'/'.$mod_rewrite_str.'/'.$banner_slug.'/'.$adzone_slug.$ref_url_slug : '';
			}
		}
		else
		{
			$link = !empty( $banner_link ) ? $banner_link : '';
		}
		
		return $link;
	}
	
	
	
	
	
	
	/*
	 * Banner Click - Redirect
	 *
	 * @access public
	 * @return null
	*/
	public function pro_ad_click_action()
	{
		global $wpdb, $wp_query, $pro_ads_main, $pro_ads_browser, $pro_ads_statistics;
	
		if( isset( $_GET['pasID'] ) && !empty( $_GET['pasID'] ) )
		{
			$banner_id = base64_decode($_GET['pasID']);
			$adzone_id = isset($_GET['pasZONE']) && !empty($_GET['pasZONE']) ? base64_decode($_GET['pasZONE']) : '';
			$ref_url = isset($_GET['pasREF']) && !empty($_GET['pasREF']) ? base64_decode($_GET['pasREF']) : '';
			
			$banner_link = get_post_meta( $banner_id, '_banner_link', true );
			
			$pro_ads_statistics->save_clicks( $banner_id, $adzone_id, $ref_url );
			header('Location: '. $banner_link);
			exit;
		}
		elseif( isset($wp_query->query_vars['pasSLUG']) && !empty($wp_query->query_vars['pasSLUG'] ) )
		{
			$banner = get_page_by_path($wp_query->query_vars['pasSLUG'], OBJECT, 'banners');
			$adzone = !empty($wp_query->query_vars['pasZONE']) ? get_page_by_path($wp_query->query_vars['pasZONE'], OBJECT, 'adzones') : 0;
			$ref_url = !empty($wp_query->query_vars['pasREF']) ? base64_decode($wp_query->query_vars['pasREF']) : '';
			$banner_id = $banner->ID;
			$adzone_id = !empty($adzone) ? $adzone->ID : 0;
			$banner_link = get_post_meta( $banner_id, '_banner_link', true );
			
			$pro_ads_statistics->save_clicks( $banner_id, $adzone_id, $ref_url );
			header('Location: '. $banner_link);
			exit;
		}
		
		
	}
	
	
	
	
	
	
	
	
	
	/*
	 * Link Adzone to Banner
	 *
	 * @access public
	 * @param 
	 * @return void
	*/
	public function pro_ad_link_adzone_to_banner( $banner_id, $adzone_id, $action_type = '' )
	{
		global $pro_ads_adzones;
	
		// link adzone to banner
		//update_post_meta( $_POST['aid'], 'linked_banners', ''  );
		$linked_adzones = get_post_meta( $banner_id, '_linked_adzones', true );
		$banner_status  = get_post_meta( $banner_id, '_banner_status', true );
		
		if( empty( $linked_adzones ))
		{
			if( $banner_status == 1 || $banner_status == 3)
			{
				$linked_adzones = array( $adzone_id );
				update_post_meta( $banner_id, '_linked_adzones', array_values(array_filter($linked_adzones))  );
			}
		}
		else
		{
			if( $action_type == 'remove' )
			{
				if (($key = array_search($adzone_id, $linked_adzones)) !== false) unset($linked_adzones[$key]);
			}
			else
			{
				if( $banner_status == 1 || $banner_status == 3)
				{
					array_push($linked_adzones, $adzone_id);
				}
			}
			update_post_meta( $banner_id, '_linked_adzones', array_values(array_filter($linked_adzones)) );
		}
	}
	
	
	
}
?>