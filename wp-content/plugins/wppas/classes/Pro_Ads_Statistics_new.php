<?php
class Pro_Ads_Statistics {	

	public function __construct() 
	{
		//add_filter( 'template_include', array($this, 'wpproads_pdf_template'), 1 );
		add_action( 'wp_loaded', array( $this, 'wpproads_pdf_template' ) );	
	}
	
	
	
	
	/*
	 * Load Statistics
	 *
	 * @access public
	 * @return array
	*/
	public function load_statistics( $query = '', $select = '*', $db = 'wpproads_user_stats' )
	{
		global $wpdb;
		
		$res = $wpdb->get_results("SELECT ".$select." FROM " . $wpdb->prefix . $db.$query );
		
		return $res;
	}
	
	/*
	 * Count Statistics
	 *
	 * @access public
	 * @return array
	*/
	public function count_statistics( $query = '', $select = 'SUM(hits)', $db = 'wpproads_user_stats' )
	{
		global $wpdb;
		
		$res = $wpdb->get_var("SELECT ".$select." FROM " . $wpdb->prefix . $db.$query );
		
		return $res;
	}
	
	
	
	
	/*
	 * Statistic types
	 *
	 * @access public
	 * @return array
	*/
	public function stat_types( $type )
	{
		return $type == 'click' ? __('Clicks', 'wpproads') : __('Impressions', 'wpproads');
	}
	public function stat_type_color( $type )
	{
		return $type == 'click' ? '#66b71a' : '#77B7C5';
	}
	
	
	
	
	
	/*
	 * IP Addresses blocked?
	 *
	 * @access public
	 * @return bool
	*/
	public function ip_blocked()
	{
		global $pro_ads_main, $pro_ads_multisite;
		
		$ip_adress = $pro_ads_main->IP;
		$wpproads_stats_blocked_ips = $pro_ads_multisite->wpproads_get_option('wpproads_stats_blocked_ips', '');
		$blocked_ips = explode(',', $wpproads_stats_blocked_ips);
		
		$is_blocked = in_array($ip_adress, $blocked_ips) ? 1 : 0;
		
		return $is_blocked;
	}
	
	
	
	
	/*
	 * Statistic names by date
	 *
	 * @access public
	 * @return array
	*/
	public function wpproads_stats_header_data( $res )
	{	
		global $wpdb;
		
		if( $res['rid'] == 1 )
		{
			//$all_years = $wpdb->get_results('SELECT YEAR(FROM_UNIXTIME(time)) as year FROM '.$wpdb->prefix.'wpproads_stats GROUP BY year');
			$all_years = $wpdb->get_results('SELECT YEAR(FROM_UNIXTIME(time)) as year FROM '.$wpdb->prefix.'wpproads_user_stats GROUP BY year');
			$first_year = reset($all_years);
    		$last_year = end($all_years);
			
			$fyear = !empty($first_year->year) ? $first_year->year : date('Y');
			$lyear = !empty($last_year->year) ? $last_year->year : date('Y');
			
			$str = array(
				'click'      => __('Total amount of clicks:', 'wpproads'),
				'impression' => __('Total amount of impressions:', 'wpproads'),
				'ctr'        => __('CTR:', 'wpproads'),
				'stats'      => __('All Statistics', 'wpproads'),
				'sdate'      => mktime(0,0,0,1, 1, $fyear),
				'edate'      => mktime(23,59,59,12, 31, $lyear)
			);
		}
		elseif( $res['rid'] == 2 )
		{
			$str = array(
				'click'      => sprintf(__('Total amount of clicks in %s:', 'wpproads'), $res['year']),
				'impression' => sprintf(__('Total amount of impressions in %s:', 'wpproads'), $res['year']),
				'ctr'        => sprintf(__('CTR (%s):', 'wpproads'), $res['year']),
				'stats'      => sprintf(__('Statistics for %s', 'wpproads'), $res['year']),
				'sdate'      => mktime(0,0,0, 1,1, $res['year']),
				'edate'      => mktime(23,59,59, 12,31, $res['year'])
			);
		}
		elseif( $res['rid'] == 3)
		{
			$am_days = cal_days_in_month(CAL_GREGORIAN, $res['month'], $res['year']);
			$sdate = mktime(0,0,0, $res['month'],1, $res['year']);
			$edate = mktime(23,59,59, $res['month'],$am_days, $res['year']);
			
			$str = array(
				'click'      => sprintf(__('All clicks on %s:', 'wpproads'), date_i18n('F', $sdate)),
				'impression' => sprintf(__('All impressions on %s:', 'wpproads'), date_i18n('F', $sdate)),
				'ctr'        => sprintf(__('CTR (%s):', 'wpproads'), date_i18n('F', $sdate)),
				'stats'      => sprintf(__('Statistics for %s', 'wpproads'), date_i18n('F', $sdate)),
				'sdate'      => $sdate,
				'edate'      => $edate
			);
		}
		else
		{
			$sdate = mktime(0,0,0, $res['month'],$res['day'], $res['year']);
			$edate = mktime(23,59,59, $res['month'],$res['day'], $res['year']);
			
			$str = array(
				'click'      => sprintf(__('Clicks on %s:', 'wpproads'), date_i18n('l, F d', $sdate)),
				'impression' => sprintf(__('Impressions on %s:', 'wpproads'),date_i18n('l, F d', $sdate)),
				'ctr'        => sprintf(__('CTR (%s):', 'wpproads'),date_i18n('l, F d', $sdate)),
				'stats'      => sprintf(__('Statistics for %s', 'wpproads'),date_i18n('l, F d', $sdate)),
				'sdate'      => $sdate,
				'edate'      => $edate
			);
		}
		
		
		return $str;
	}
	
	
	
	
	
	/*
	 * Statistics Header
	 *
	 * @access public
	 * @return html
	*/
	public function pro_ad_show_statistics_header( $res )
	{
		global $wpdb, $pro_ads_advertisers;
		
		$click_str = '';
		$impr_str = '';
		$ctr_str = '';
		$group_query = !empty($res['group']) && !empty($res['group_id']) ? ' AND '.$res['group'].'_id = '.$res['group_id'] : '';
		$date_names = $this->wpproads_stats_header_data( $res );
		$sdate = $date_names['sdate'];
		$edate = $date_names['edate'];
		
		if( $res['rid'] == 1 )
		{	
			$click_str = '<div>'.$date_names['click'].'</div>';
			$impr_str = '<div>'.$date_names['impression'].'</div>';
			$ctr_str = '<div>'.$date_names['ctr'].'</div>';
		}
		elseif( $res['rid'] == 2 )
		{
			$click_str = '<div>'.$date_names['click'].'</div>';
			$impr_str = '<div>'.$date_names['impression'].'</div>';
			$ctr_str = '<div>'.$date_names['ctr'].'</div>';
		}
		elseif( $res['rid'] == 3 )
		{
			$click_str = '<div>'.$date_names['click'].'</div>';
			$impr_str = '<div>'.$date_names['impression'].'</div>';
			$ctr_str = '<div>'.$date_names['ctr'].'</div>';
		}
		elseif( $res['rid'] == 4 )
		{
			$click_str = empty($res['text']['click']) ? '<div>'.$date_names['click'].'</div>' : '<div>'.$res['text']['click'].'</div>';
			$impr_str = empty($res['text']['impr']) ? '<div>'.$date_names['impression'].'</div>' : '<div>'.$res['text']['impr'].'</div>';
			$ctr_str = empty($res['text']['ctr']) ? '<div>'.$date_names['ctr'].'</div>' : '<div>'.$res['text']['ctr'].'</div>';
		}
		
		
		
		if( current_user_can(WP_ADS_ROLE_ADMIN))
		{
			$clicks = $this->count_statistics( ' WHERE type = "click" AND time >= '.$sdate.' AND time <= '.$edate.$group_query );
			$impr = $this->count_statistics( ' WHERE type = "impression" AND time >= '.$sdate.' AND time <= '.$edate.$group_query );
		}
		else
		{
			$advertiser = $pro_ads_advertisers->get_advertisers( array('meta_key' => '_proad_advertiser_wpuser', 'meta_value' => get_current_user_id()) );
				
			$clicks = $this->count_statistics( ' WHERE type = "click" AND time >= '.$sdate.' AND time <= '.$edate.' AND advertiser_id='.$advertiser[0]->ID.$group_query );
			$impr = $this->count_statistics( ' WHERE type = "impression" AND time >= '.$sdate.' AND time <= '.$edate.' AND advertiser_id='.$advertiser[0]->ID.$group_query );
		}
		
		$clicks = !empty($clicks) ? $clicks : 0;
		$impr = !empty($impr) ? $impr : 0;
		
		
		// CTR
		$ctr = !empty($clicks) && !empty($impr) ? $clicks / $impr * 100 : 0;
		$round_ctr = round($ctr,2).'%';
											
		$html = '';
		$html.= '<div class="stats_header_cont">';
			$html.= '<div class="stats_header_box">';
				$html.= $click_str;
				$html.= '<div class="am_data">'.$clicks.'</div>';
			$html.= '</div>';
			$html.= '<div class="stats_header_box">';
				$html.= $impr_str;
				$html.= '<div class="am_data">'.$impr.'</div>';
			$html.= '</div>';
			$html.= '<div class="stats_header_box" style="margin:0;">';
				$html.= $ctr_str;
				$html.= '<div class="am_data">'.$round_ctr.'</div>';
			$html.= '</div>';
			$html.= '<div class="clearFix"></div>';
		$html.= '</div>';
		
		return $html;
	}
	
	
	
	
	
	/*
	 * Show Statistics
	 *
	 * @access public
	 * @return html
	*/
	public function pro_ad_show_statistics( $arr = array() )
	{
		global $pro_ads_main, $pro_ads_multisite;
		
		$array = array(
			'type'     => array('slug' => 'click', 'name' => __('Clicks', 'wpproads')),
			'color'    => '#66b71a',
			'range'    => 'month',
			'rid'      => 3, // 1 = all, 2 = year, 3 = month, 4 = day
			'day'      => $pro_ads_main->time_by_timezone('d'),
			'month'    => $pro_ads_main->time_by_timezone('m'),
			'year'     => $pro_ads_main->time_by_timezone('Y'),
			'group'    => '',
			'group_id' => ''
		);
		$res = array_merge($array, $arr);
		
		$wpproads_enable_userdata_stats = $pro_ads_multisite->wpproads_get_option('wpproads_enable_userdata_stats', 0);
		
		// STATISTICS GRAPH/TABLE HOLDER
		$style = $wpproads_enable_userdata_stats ? 'style="width:78%; float:left;"' : '';
		echo '<div class="pro_ad_main_stats_graph" '.$style.'>'; // class="pro_ad_stats_graph"
			echo $this->pro_ad_statistics( $res );
			$this->get_stats_table( $res );
		echo '</div>';
		
		// SIDEBAR
		if( $wpproads_enable_userdata_stats ){
			echo '<div class="pro_ad_stats_sidebar" style="width:20%; margin:20px 0; float:right; background:#FFF;">';
				
				// Device stats
				echo '<div class="device_stats" style="text-align:center; padding:10px;">';
					echo '<h2 style="background:#808080; color:#FFF; padding: 2px 0; border-radius: 5px;">'.__('Device Stats','wpproads').'</h2>';
					$data = array_merge($res, array('channel' => 'device'));
					echo $this->pro_ad_statistics( $data );
				echo '</div>';
				
			echo '</div>';
		}
	}
	


	/*
	 * Statistics
	 *
	 * @access public
	 * @param string $range [day|month|year]
	 * @return html
	*/
	public function pro_ad_statistics( $arr = array() )
	{
		global $pro_ads_multisite, $pro_ads_main;
		
		$array = array(
			'channel'  => '',
			'type'     => array('slug' => 'click', 'name' => __('Clicks', 'wpproads')),
			'color'    => '#66b71a',
			'range'    => 'month',
			'rid'      => 3,
			'day'      => $pro_ads_main->time_by_timezone('d'),
			'month'    => $pro_ads_main->time_by_timezone('m'),
			'year'     => $pro_ads_main->time_by_timezone('Y'),
			'group'    => '',
			'group_id' => ''
		);
		$res = array_merge($array, $arr);
		
		$html = '';
		
		if( empty($res['channel'] ))
		{
			$html.= '<input type="hidden" id="stats_group" value="'.$res['group'].'" />';
			$html.= '<input type="hidden" id="stats_group_id" value="'.$res['group_id'].'" />'; 
			
			$html.= !empty($res['group']) && !empty($res['group_id']) ? sprintf(__('<div class="stats_group_info_box"><h2>%s stats for: %s</h2></div>', 'wpproads'), ucfirst($res['group']), '<a href="post.php?post='.$res['group_id'].'&action=edit">'.get_the_title($res['group_id']).'</a>') : '';
			
			$html.= $this->pro_ad_show_statistics_header( $res );
			$html.= $this->statistics_menu( $res );
			
			$wpproads_stats_data = $pro_ads_multisite->wpproads_get_option('wpproads_stats_data', 'hour');
			
			if( $wpproads_stats_data != 'hour' && $res['rid'] == 4 )
			{
				
			}
			else
			{
				/*
				 * Flot stats
				 * http://designmodo.com/create-interactive-graph-css3-jquery/
				*/
				$html.= '<div id="graph-wrapper">';
					$html.= '<div class="graph-container">';
						$html.= '<div id="graph-lines"></div>';
					$html.= '</div>';
				$html.= '</div>';
				
				// start JS
				$html.= '<script type="text/javascript">';
					
					$html.= 'jQuery(document).ready(function($) {';	
						$html.= $this->get_statistics_data($res);
					$html.= '});';
					
					$html.= 'function five_multiple( i, v){';
						$html.= 'return v*(Math.round(i/v));';
					$html.= '}';
				
				$html.= '</script>';
			}
		}
		elseif( $res['channel'] == 'device')
		{
			$html.= '<style>#device-graph-pie { width: 200px; height: 200px; }.legend table, .legend > div { height: 82px !important; opacity: 1 !important; left: 170px; top: 10px; }.legend table { border: none; padding: 5px; }</style>';
			
			$html.= '<div id="device-graph-wrapper">';
				$html.= '<div class="device-graph-container">';
					$html.= '<div id="device-graph-pie"></div>';
				$html.= '</div>';
			$html.= '</div>';
			
			// start JS
			$html.= '<script type="text/javascript">';
				
				$html.= 'jQuery(document).ready(function($) {';	
					$html.= $this->get_statistics_data($res);
				$html.= '});';
			
			$html.= '</script>';
		}
		
		return $html;
	}
	
	
	
	
	
	
	/*
	 * Statistics Menu
	 *
	 * @access public
	 * @param array $res
	 * @return html
	*/
	public function statistics_menu( $res )
	{
		$get = serialize($res);
		$get = htmlspecialchars($get);
		$get = base64_encode($get);
		//print_r( unserialize(htmlspecialchars_decode(base64_decode($get))) );
		$html = '';
		$html.= '<div class="graph-info">';
			$html.= '<a href="javascript:void(0)" class="stats_btn clicks" type="click" color="#66b71a" rid="'.$res['rid'].'" year="'.$res['year'].'" month="'.$res['month'].'" day="'.$res['day'].'" group="'.$res['group'].'" group_id="'.$res['group_id'].'">'.__('Clicks', 'wpproads').'</a>';
			$html.= '<a href="javascript:void(0)" class="stats_btn impressions" type="impression" color="#77b7c5" rid="'.$res['rid'].'" year="'.$res['year'].'" month="'.$res['month'].'" day="'.$res['day'].'" group="'.$res['group'].'" group_id="'.$res['group_id'].'">'.__('Impressions', 'wpproads').'</a>';
			// pdf btn
			$html.= '<a href="'.get_bloginfo('url').'?stats_pdf=1&amp;data='.$get.'" target="_blank" class="stats_pdf" type="pdf" color="#77b7c5" rid="'.$res['rid'].'" year="'.$res['year'].'" month="'.$res['month'].'" day="'.$res['day'].'" group="'.$res['group'].'" group_id="'.$res['group_id'].'">'.__('Export PDF', 'wpproads').'</a>';
			
			$html.= $res['rid'] != 1 ? '<a href="javascript:void(0)" class="time_frame_btn" style="margin: 0 0 0 20px;" rid="1" year="'.$res['year'].'" month="'.$res['month'].'" group="'.$res['group'].'" group_id="'.$res['group_id'].'">'.__('All Time', 'wpproads').'</a>' : '<strong style="display:inline-block; margin: 7px 0 0 20px;">'.__('All Time', 'wpproads').'</strong>';
			$html.= $res['rid'] >= 2 ? ' / ' : '';
			if( $res['rid'] > 2 )
			{
				$html.= '<a href="javascript:void(0)" class="time_frame_btn" rid="2" year="'.$res['year'].'" month="'.$res['month'].'" group="'.$res['group'].'" group_id="'.$res['group_id'].'">'.$res['year'].'</a>';
			}
			elseif( $res['rid'] == 2 )
			{
				$html.= '<strong>'.$res['year'].'</strong>';
			}
			
			$html.= $res['rid'] >= 3 ? ' / ' : '';
			if( $res['rid'] > 3 )
			{
				$html.= '<a href="javascript:void(0)" class="time_frame_btn" rid="3" year="'.$res['year'].'" month="'.$res['month'].'" group="'.$res['group'].'" group_id="'.$res['group_id'].'">'.date_i18n('F', mktime(0,0,0, $res['month'], $res['day'], $res['year'])).'</a>';
			}
			elseif( $res['rid'] == 3 )
			{
				$html.= '<strong>'.date_i18n('F', mktime(0,0,0, $res['month'], $res['day'], $res['year'])).'</strong>';
			}
			$html.= $res['rid'] >= 4 ? ' / ' : '';
			$html.= $res['rid'] >= 4 ? '<strong>'.date_i18n('l, F d', mktime(0,0,0, $res['month'], $res['day'], $res['year'])).'</strong>' : '';
			
		$html.= '</div>';
		
		return $html;
	}
	
	
	
	
	
	
	
	/*
	 * Get Statistics Data
	 *
	 * @access public
	 * @param string $range [day|month|year]
	 * @return html
	*/
	public function get_statistics_data($arr = array())
	{
		global $wpdb, $pro_ads_advertisers, $pro_ads_main;
		
		$array = array(
			'type'     => array('slug' => 'click', 'name' => __('Clicks', 'wpproads')),
			'color'    => '#66b71a',
			'range'    => 'month',
			'rid'      => 3,
			'day'      => $pro_ads_main->time_by_timezone('d'),
			'month'    => $pro_ads_main->time_by_timezone('m'),
			'year'     => $pro_ads_main->time_by_timezone('Y'),
			'group'    => '',
			'group_id' => ''
		);
		$res = array_merge($array, $arr);
		
		$html = '';
		
		if( empty($res['channel'] ))
		{
			// Points Text array
			if( $res['rid'] == 1 )
			{
				$html.= 'var point_txt = [];';
				$all_years = $wpdb->get_results('SELECT YEAR(FROM_UNIXTIME(time)) as year FROM '.$wpdb->prefix.'wpproads_user_stats GROUP BY year');
				
				if(count($all_years) < 2)
				{
					$year = !empty($all_years[0]->year) ? $all_years[0]->year-1 : date('Y')-1;
					$html.= 'point_txt.push("'.__('in', 'wpproads').' '.date('Y', mktime(0,0,0, 1, 1, $year)).'");';
				}
					
				foreach( $all_years as $i => $year )
				{
					$html.= 'point_txt.push("'.__('in', 'wpproads').' '.date('Y', mktime(0,0,0, 1, 1, $year->year)).'");';
				}
			}
			elseif( $res['rid'] == 2 )
			{
				$html.= 'var point_txt = [];';
				
				for( $i = 1; $i <= 12; $i++ )
				{
					$html.= 'point_txt.push("'.__('in', 'wpproads').' '.date_i18n('F', mktime(0,0,0, $i, $res['day'], $res['year'])).'");';
				}
			}
			elseif( $res['rid'] == 3 )
			{
				$am_days = cal_days_in_month(CAL_GREGORIAN, $res['month'], $res['year']);
				$html.= 'var point_txt = [];';
				
				for( $i = 1; $i <= $am_days; $i++ )
				{
					$html.= 'point_txt.push("'.__('on', 'wpproads').' '.date_i18n('F', mktime(0,0,0, $res['month'], $i, $res['year'])).' '.$i.'");';
				}
			}
			elseif( $res['rid'] == 4 )
			{
				$html.= 'var point_txt = [];';
				
				for( $i = 0; $i < 24; $i++ )
				{
					$u = $i+1;
					$html.= 'point_txt.push("'.__('between', 'wpproads').' '.date_i18n('G:i', mktime($i,0,0, $res['month'], $res['day'], $res['year'])).' and '.date_i18n('G:i', mktime($u,0,0, $res['month'], $res['day'], $res['year'])).'");';
				}
			}
			
			// Graph Data ##############################################
			$data_arr = array();
			$group_query = !empty($res['group']) && !empty($res['group_id']) ? ' AND '.$res['group'].'_id = '.$res['group_id'] : '';
			
			$html.= 'var graphData = [{';
				
				
				$dta = $this->wpproads_stats_data( $res );
				$html.= $dta['html'];
			
				
				$html.= 'color: "'.$res['color'].'"';
				// $html.= 'points: { radius: 4, fillColor: '#77b7c5' }'; 
			
			$html.= '}];';
			
			$html.= $this->get_lines_graph($dta['data_arr'], $res);
			$html.= $this->get_point_tooltip( $res['type']['name'] );
		}
		elseif( $res['channel'] == 'device')
		{
			$data = $this->wpproads_device_stats_data( $res );
			$html.= $data['html'];
			/*$html.= 'var data = [';
    			$html.= '{ label: "Desktop",  data: 19.5, color: "#006EA6"},';
    			$html.= '{ label: "Tablet",  data: 4.5, color: "#35AF9B"},';
    			$html.= '{ label: "Mobile",  data: 36.6, color: "#875497"},';
			$html.= '];';*/
			
			$html.= 'var count_device_stats = 0;';
			$html.= 'jQuery.each( data, function( i, val ) {';
				$html.= 'count_device_stats += val["data"];';
			$html.= '});';
			
 			$html.= 'if(count_device_stats > 0 ){';
				$html.= '$.plot("#device-graph-pie", data, {';
					$html.= 'series: {';
						$html.= 'pie: {';
							$html.= 'radius: 1,';
							$html.= 'innerRadius: 0.5,';
							$html.= 'show: true,';
							$html.= 'label: {';
								$html.= 'show: true,';
								$html.= 'radius: 1,';
						
								$html.= 'formatter: function(label, series) {';
									$html.= 'return \'<div style="font-size:11px; text-align:center; padding:2px; color:white;">\'+label+\'<br/>\'+Math.round(series.percent)+\'%</div>\';';
								$html.= '},';
								$html.= 'background: {';
									$html.= 'opacity: 0.8';
								$html.= '}';
							$html.= '}';
						$html.= '}';
					$html.= '},';
					$html.= 'legend: {';
						$html.= 'show: false';
					$html.= '}';
				$html.= '});';
			$html.= '}else{';
				$html.= '$("#device-graph-pie").html("<br>'.__('No stats available.','wpproads').'");';
			$html.= '}';
	
		}
		
		return $html;
	}
	
	
	
	
	
	
	
	
	
	
	/*
	 * Get Lines Graph
	 *
	 * @access public
	 * @param array $data_arr
	 * @return html
	*/
	public function get_lines_graph( $data_arr, $res )
	{
		global $wpdb;
		
		$max = !empty($data_arr) ? max($data_arr) : 0;
		$html = '';
		
		// Lines Graph #############################################
		$html.= '$.plot($("#graph-lines"), graphData, {';
			$html.= 'series: {';
				$html.= 'points: {';
					$html.= 'show: true,';
					$html.= 'radius: 3';
				$html.= '},';
				$html.= 'lines: {';
					$html.= 'show: true,';
					$html.= 'fill:.2,';
				$html.= '},';
				$html.= 'shadowSize: 0';
			$html.= '},';
			$html.= 'grid: {';
				//$html.= 'color: "#646464",';
				$html.= 'borderColor: {bottom: "#EEEEEE", left: "#EEEEEE"},';
				$html.= 'borderWidth: {top: 0, right: 0, bottom: 2, left: 2},';
				$html.= 'hoverable: true';
			$html.= '},';
			
			if( $res['rid'] == 4 )
			{
				$html.= 'xaxis: {';
					$html.= 'tickColor: "#F5F5F5",';
					$html.= 'mode: "time",';
					$html.= 'tickSize: [1, "hour"],';
					//$html.= 'min: (new Date('.$res['year'].', '.($res['month']-1).', '.$res['day'].', 00, 00, 00, 00)).getTime(),'; // months in javascript Date() start from 0 to 11!
					//$html.= 'max: (new Date('.$res['year'].', '.($res['month']-1).', '.$res['day'].', 23, 00, 00, 00)).getTime(),'; // months in javascript Date() start from 0 to 11!
				$html.= '},';
			}
			elseif( $res['rid'] == 1 )
			{
				$all_years = $wpdb->get_results('SELECT YEAR(FROM_UNIXTIME(time)) as year FROM '.$wpdb->prefix.'wpproads_user_stats GROUP BY year');
				if( !empty($all_years))
				{
					$start_year = count($all_years) > 1 ? $all_years[0]->year : $all_years[0]->year-1;
				}
				else
				{
					$start_year = date('Y')-1;
				}
				
				$html.= 'xaxis: {';
					$html.= 'tickColor: "#F5F5F5",';
					$html.= 'tickDecimals: 0,';
					$html.= 'tickSize:1,';
					$html.= 'min:'.$start_year;
				$html.= '},';
			}
			else
			{
				$html.= 'xaxis: {';
					$html.= 'tickColor: "#F5F5F5",';
					$html.= 'tickDecimals: 0,';
					$html.= 'tickSize:1,';
					$html.= 'min:1';
				$html.= '},';
			}
			$html.= 'yaxis: {';
				$html.= 'tickColor: "#EEEEEE",';
				$html.= 'tickSize: five_multiple( five_multiple( '.$max.', 5)/4, 5 ),';
				$html.= 'tickDecimals: 0,'; 
				$html.= 'min:0';
			$html.= '}';
		$html.= '});';
		
		return $html;
	}
	
	
	
	
	
	/*
	 * Get tooltip
	 *
	 * @access public
	 * @param array $data_arr
	 * @return html
	*/
	public function get_point_tooltip( $type )
	{
		$html = '';
		$html.= 'function showTooltip(x, y, contents) {';
			$html.= '$(\'<div id="tooltip">\' + contents + \'</div>\').css({';
				$html.= 'top: y - 16,';
				$html.= 'left: x + 20';
			$html.= '}).appendTo("body").fadeIn();';
		$html.= '}';
	
		$html.= 'var previousPoint = null;';
	
		$html.= '$("#graph-lines").bind("plothover", function (event, pos, item) {';
			$html.= 'if (item) {';
				$html.= 'if (previousPoint != item.dataIndex) {';
					$html.= 'previousPoint = item.dataIndex;';
					$html.= '$("#tooltip").remove();';
					$html.= 'var x = item.datapoint[0],';
						$html.= 'y = item.datapoint[1];';
						$html.= 'showTooltip(item.pageX, item.pageY, y + " '.$type.' " + point_txt[item.dataIndex]);';
				$html.= '}';
			$html.= '} else {';
				$html.= '$("#tooltip").remove();';
				$html.= 'previousPoint = null;';
			$html.= '}';
		$html.= '});';
		
		return $html;
	}
	
	
	
	
	
	/*
	 * stats table
	 *
	 * @access public
	 * @param array $data_arr
	 * @return html
	*/
	public function get_stats_table( $arr = array() )
	{
		global $hook_suffix, $pro_ads_main;
		
		$array = array(
			'type'   => array('slug' => 'click'), 
			'range'  => 'month',
			'rid'    => 3,
			'day'    => $pro_ads_main->time_by_timezone('d'),
			'month'  => $pro_ads_main->time_by_timezone('m'),
			'year'   => $pro_ads_main->time_by_timezone('Y'),
			'group'    => '',
			'group_id' => ''
		);
		$res = array_merge($array, $arr);
		
		echo '<div class="pro_ad_stats_table">';
			
			if( $res['rid'] == 1 )
			{
				$statsTable = new Pro_Ad_All_Stats_List_Table();
				$filter_str = 'all-stats-filter';
				$range = 'all';
			}
			elseif( $res['rid'] == 2 )
			{
				$statsTable = new Pro_Ad_Stats_Year_List_Table();
				$filter_str = 'year-stats-filter';
				$range = 'year';
			}
			elseif( $res['rid'] == 3)
			{
				$statsTable = new Pro_Ad_Stats_List_Table();
				$filter_str = 'month-stats-filter';
				$range = 'month';
			}
			elseif( $res['rid'] == 4 )
			{
				$statsTable = new Pro_Ad_Stats_Day_List_Table();
				$filter_str = 'day-stats-filter';
				$range = 'day';
			}
			
			$statsTable->prepare_items( $res );
			
			echo '<form id="'.$filter_str.'" class="stats-filter" range="'.$range.'" rid="'.$res['rid'].'" type="'.$res['type']['slug'].'" day="'.$res['day'].'" month="'.$res['month'].'" year="'.$res['year'].'" group="'.$res['group'].'" group_id="'.$res['group_id'].'" method="get">';
				//echo '<input type="hidden" name="page" value="'.$_REQUEST['page'].'" />';
				$statsTable->display();
			echo '</form>';
        echo '</div>';
	}
	
	
	
	
	
	
	
	
	
	/*
	 * Stats data
	 *
	 * @access public
	 * @param 
	 * @return 
	*/
	public function wpproads_stats_data( $res )
	{
		global $wpdb, $pro_ads_advertisers;
		
		$html = '';
		$data_arr = array();
		$group_query = !empty($res['group']) && !empty($res['group_id']) ? ' AND '.$res['group'].'_id = '.$res['group_id'] : '';
		
		//print_r($res);
		
		if( $res['rid'] == 1 )
		{
			$all_years = $wpdb->get_results('SELECT YEAR(FROM_UNIXTIME(time)) as year FROM '.$wpdb->prefix.'wpproads_user_stats GROUP BY year');
			
			$html.= 'data: [';
			
			if(count($all_years) < 2)
			{
				$year = !empty($all_years[0]->year) ? $all_years[0]->year-1 : date('Y')-1;
				$html.= '["'.($year).'", 0],';
			}
			
			foreach( $all_years as $i => $year )
			{
				$stime = mktime(0,0,0,1, 1, $year->year);
				$etime = mktime(23,59,59,12, 31, $year->year);
				if( current_user_can(WP_ADS_ROLE_ADMIN))
				{
					$res_data = $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" '.$group_query );
				}
				else
				{
					$advertiser = $pro_ads_advertisers->get_advertisers( array('meta_key' => '_proad_advertiser_wpuser', 'meta_value' => get_current_user_id()) );
					$res_data = $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND advertiser_id = '.$advertiser[0]->ID.$group_query );
				}
				$res_data = !empty($res_data) ? $res_data : 0;
				
				$data_arr[] = $res_data;
				$stats_data[] = array('label' => $year->year, 'count' => $res_data);
				$html.= '["'.$year->year.'", '.$res_data.'],';
			}
			
			$html.= '],';
		}
		elseif( $res['rid'] == 2 )
		{
			$data_arr = array();
			
			// Data
			$html.= 'data: [';
			
			for( $i = 1; $i <= 12; $i++ )
			{
				$am_days = cal_days_in_month(CAL_GREGORIAN, $i, $res['year']);
				
				$stime = mktime(0,0,0,$i, 1, $res['year']);
				$etime = mktime(23,59,59,$i, $am_days, $res['year']);
				
				if( current_user_can(WP_ADS_ROLE_ADMIN))
				{
					$res_data = $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" '.$group_query );
				}
				else
				{
					$advertiser = $pro_ads_advertisers->get_advertisers( array('meta_key' => '_proad_advertiser_wpuser', 'meta_value' => get_current_user_id()) );
					$res_data = $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND advertiser_id = '.$advertiser[0]->ID.$group_query );
				}
				$res_data = !empty($res_data) ? $res_data : 0;
				$data_arr[] = $res_data;
				$stats_data[] = array('label' => $i, 'count' => $res_data);
				$html.= '["'.$i.'", '.$res_data.'],';
			}
			
			$html.= '],';
		}
		elseif( $res['rid'] == 3 )
		{
			$am_days = cal_days_in_month(CAL_GREGORIAN, $res['month'], $res['year']);
			$data_arr = array();
			
			// Data
			$html.= 'data: [';
			
			for( $i = 1; $i <= $am_days; $i++ )
			{
				if( current_user_can(WP_ADS_ROLE_ADMIN))
				{
					$res_data = $this->count_statistics( ' WHERE date = '.mktime(0,0,0, $res['month'], $i, $res['year']).' AND type = "'.$res['type']['slug'].'" '.$group_query ); //WHERE time >= '.$sdate.' AND time <= '.$edate 
				}
				else
				{
					$advertiser = $pro_ads_advertisers->get_advertisers( array('meta_key' => '_proad_advertiser_wpuser', 'meta_value' => get_current_user_id()) );
					$res_data = $this->count_statistics( ' WHERE date = '.mktime(0,0,0, $res['month'], $i, $res['year']).' AND type = "'.$res['type']['slug'].'" AND advertiser_id = '.$advertiser[0]->ID.$group_query );
				}
				$res_data = !empty($res_data) ? $res_data : 0;
		
				$data_arr[] = $res_data;
				$stats_data[] = array('label' => $i, 'count' => $res_data);
				$html.= '["'.$i.'", '.$res_data.'],';
				 //[6, 3], [7, 2], [8, 40], [9, 14], [10, 5] ';
			}
			
			$html.= '],';
		}
		elseif( $res['rid'] == 4 )
		{
			$data_arr = array();
			
			// Data
			$html.= 'data: [';
			
			for( $i = 0; $i < 24; $i++ )
			{
				$stime = mktime($i,0,0,$res['month'], $res['day'], $res['year']);
				$etime = mktime($i,59,59,$res['month'], $res['day'], $res['year']);
				
				$group_query = !empty($res['group']) && !empty($res['group_id']) ? ' AND '.$res['group'].'_id = '.$res['group_id'] : '';
				
				if( current_user_can(WP_ADS_ROLE_ADMIN))
				{
					$res_data = $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" '.$group_query );
				}
				else
				{
					$advertiser = $pro_ads_advertisers->get_advertisers( array('meta_key' => '_proad_advertiser_wpuser', 'meta_value' => get_current_user_id()) );
					$res_data = $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND advertiser_id = '.$advertiser[0]->ID.$group_query );
				}
				$res_data = !empty($res_data) ? $res_data : 0;
				
				$data_arr[] = $res_data;
				$stats_data[] = array('label' => mktime($i,0,0,$res['month'], $res['day'], $res['year']), 'count' => $res_data);
				$html.= '["'.mktime($i,0,0,$res['month'], $res['day'], $res['year']).'000", '.$res_data.'],';
			}
			
			$html.= '],';
		}
		
		$arr = array(
			'html'       => $html,
			'stats_data' => $stats_data,
			//'stats'      => $res_data,
			'data_arr'   => $data_arr
		);
		
		return $arr;
	}
	
	
	
	
	
	
	
	/*
	 * Device Stats data
	 *
	 * @access public
	 * @param 
	 * @return 
	*/
	public function wpproads_device_stats_data( $res )
	{
		global $wpdb, $pro_ads_advertisers;
		
		$html = '';
		$desktop = 0;
		$tablet = 0;
		$mobile = 0;
		$device_colors = array('desktop' => '#006EA6', 'tablet' => '#35AF9B', 'mobile' => '#875497');
		$group_query = !empty($res['group']) && !empty($res['group_id']) ? ' AND '.$res['group'].'_id = '.$res['group_id'] : '';
		
		$html.= 'var data = [';
		
		if( $res['rid'] == 1 )
		{
			$all_years = $wpdb->get_results('SELECT YEAR(FROM_UNIXTIME(time)) as year FROM '.$wpdb->prefix.'wpproads_user_stats GROUP BY year');

			foreach( $all_years as $i => $year )
			{
				$stime = mktime(0,0,0,1, 1, $year->year);
				$etime = mktime(23,59,59,12, 31, $year->year);
				if( current_user_can(WP_ADS_ROLE_ADMIN))
				{
					$desktop += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "desktop" '.$group_query );
					$tablet += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "tablet" '.$group_query );
					$mobile += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "mobile" '.$group_query );
				}
				else
				{
					$advertiser = $pro_ads_advertisers->get_advertisers( array('meta_key' => '_proad_advertiser_wpuser', 'meta_value' => get_current_user_id()) );
					$desktop += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "desktop" AND advertiser_id = '.$advertiser[0]->ID.$group_query );
					$tablet += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "desktop" AND advertiser_id = '.$advertiser[0]->ID.$group_query );
					$mobile += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "desktop" AND advertiser_id = '.$advertiser[0]->ID.$group_query );
				}
			}
		}
		elseif( $res['rid'] == 2 )
		{
			$data_arr = array();
			
			for( $i = 1; $i <= 12; $i++ )
			{
				$am_days = cal_days_in_month(CAL_GREGORIAN, $i, $res['year']);
				
				$stime = mktime(0,0,0,$i, 1, $res['year']);
				$etime = mktime(23,59,59,$i, $am_days, $res['year']);
				
				if( current_user_can(WP_ADS_ROLE_ADMIN))
				{
					$desktop += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "desktop" '.$group_query );
					$tablet += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "tablet" '.$group_query );
					$mobile += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "mobile" '.$group_query );
				}
				else
				{
					$advertiser = $pro_ads_advertisers->get_advertisers( array('meta_key' => '_proad_advertiser_wpuser', 'meta_value' => get_current_user_id()) );
					$desktop += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "desktop" AND advertiser_id = '.$advertiser[0]->ID.$group_query );
					$tablet += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "tablet" AND advertiser_id = '.$advertiser[0]->ID.$group_query );
					$mobile += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "mobile" AND advertiser_id = '.$advertiser[0]->ID.$group_query );
				}
			}
		}
		elseif( $res['rid'] == 3 )
		{
			$am_days = cal_days_in_month(CAL_GREGORIAN, $res['month'], $res['year']);
			$data_arr = array();
			
			for( $i = 1; $i <= $am_days; $i++ )
			{
				if( current_user_can(WP_ADS_ROLE_ADMIN))
				{
					$desktop += $this->count_statistics( ' WHERE date = '.mktime(0,0,0, $res['month'], $i, $res['year']).' AND type = "'.$res['type']['slug'].'" AND device = "desktop" '.$group_query );
					$tablet += $this->count_statistics( ' WHERE date = '.mktime(0,0,0, $res['month'], $i, $res['year']).' AND type = "'.$res['type']['slug'].'" AND device = "tablet" '.$group_query );
					$mobile += $this->count_statistics( ' WHERE date = '.mktime(0,0,0, $res['month'], $i, $res['year']).' AND type = "'.$res['type']['slug'].'" AND device = "mobile" '.$group_query );
				}
				else
				{
					$advertiser = $pro_ads_advertisers->get_advertisers( array('meta_key' => '_proad_advertiser_wpuser', 'meta_value' => get_current_user_id()) );
					$desktop += $this->count_statistics( ' WHERE date = '.mktime(0,0,0, $res['month'], $i, $res['year']).' AND type = "'.$res['type']['slug'].'" AND device = "desktop" AND advertiser_id = '.$advertiser[0]->ID.$group_query );
					$tablet += $this->count_statistics( ' WHERE date = '.mktime(0,0,0, $res['month'], $i, $res['year']).' AND type = "'.$res['type']['slug'].'" AND device = "tablet" AND advertiser_id = '.$advertiser[0]->ID.$group_query );
					$mobile += $this->count_statistics( ' WHERE date = '.mktime(0,0,0, $res['month'], $i, $res['year']).' AND type = "'.$res['type']['slug'].'" AND device = "mobile" AND advertiser_id = '.$advertiser[0]->ID.$group_query );
				}
			}
		}
		elseif( $res['rid'] == 4 )
		{	
			for( $i = 0; $i < 24; $i++ ){
				$stime = mktime($i,0,0,$res['month'], $res['day'], $res['year']);
				$etime = mktime($i,59,59,$res['month'], $res['day'], $res['year']);
				
				$group_query = !empty($res['group']) && !empty($res['group_id']) ? ' AND '.$res['group'].'_id = '.$res['group_id'] : '';
				
				if( current_user_can(WP_ADS_ROLE_ADMIN))
				{
					$desktop += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "desktop" '.$group_query );
					$tablet += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "tablet" '.$group_query );
					$mobile += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "mobile" '.$group_query );
				}
				else
				{
					$advertiser = $pro_ads_advertisers->get_advertisers( array('meta_key' => '_proad_advertiser_wpuser', 'meta_value' => get_current_user_id()) );
					$desktop += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "desktop" AND advertiser_id = '.$advertiser[0]->ID.$group_query );
					$tablet += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "tablet" AND advertiser_id = '.$advertiser[0]->ID.$group_query );
					$mobile += $this->count_statistics( ' WHERE time >= '.$stime.' AND time <= '.$etime.' AND type = "'.$res['type']['slug'].'" AND device = "mobile" AND advertiser_id = '.$advertiser[0]->ID.$group_query );
				}
			}
		}
		
		$html.= '{ label: "'.__('Desktop','wpproads').'",  data: '.$desktop.', color: "'.$device_colors['desktop'].'"},';
		$html.= '{ label: "'.__('Tablet','wpproads').'",  data: '.$tablet.', color: "'.$device_colors['tablet'].'"},';
		$html.= '{ label: "'.__('Mobile','wpproads').'",  data: '.$mobile.', color: "'.$device_colors['mobile'].'"}';
		
		$html.= '];';
		
		$arr = array(
			'html' => $html,
		);
		
		return $arr;
	}


	
	
	
	
	
	/*
	 * Save Impression
	 *
	 * @access public
	 * @param int $banner_id
	 * @return null
	*/
	public function save_impression( $banner_id, $adzone_id = '', $ref_url = '' )
	{
		global $wpdb, $pro_ads_main, $pro_ads_browser, $pro_ads_banners, $pro_ads_multisite;
		
		// Check if stats need to be saved for this IP
		if( !$this->ip_blocked() )
		{
			// Check if satistics are enabled
			$wpproads_enable_stats = $pro_ads_multisite->wpproads_get_option('wpproads_enable_stats', 0);
			$wpproads_enable_impr = $pro_ads_multisite->wpproads_get_option('wpproads_enable_impr', 1);
			
			
			// Google Analytics
			$wpproads_google_analytics_id = get_option('wpproads_google_analytics_id', '');
			
			$advertiser_id = get_post_meta( $banner_id, '_banner_advertiser_id', true );
			$campaign_id   = get_post_meta( $banner_id, '_banner_campaign_id', true );
			
			$stats_data = array(
				'type'                => 'impression',
				'banner_id'           => $banner_id, 
				'adzone_id'           => $adzone_id, 
				'advertiser_id'       => $advertiser_id,
				'campaign_id'         => $campaign_id,
				'google_analytics_id' => $wpproads_google_analytics_id
			);
			
			if( $wpproads_enable_stats && $wpproads_enable_impr )
			{
				$wpproads_enable_userdata_stats = $pro_ads_multisite->wpproads_get_option('wpproads_enable_userdata_stats', 0);
				// Get save type
				$wpproads_save_impressions_type = $pro_ads_multisite->wpproads_get_option('wpproads_save_impressions_type', 'unique');
			
				$today = mktime(0, 0, 0, $pro_ads_main->time_by_timezone('m')  , $pro_ads_main->time_by_timezone('d'), $pro_ads_main->time_by_timezone('Y'));
				$hour = date_i18n("G");
				$ip_adress = $pro_ads_main->IP;
				
				$isbot = $pro_ads_main->detect_bots();
				if( !$isbot )
				{
					// Load user data.	
					$stats_data['userdata'] = $wpproads_enable_userdata_stats ? $pro_ads_main->get_user_data() : $pro_ads_main->get_user_data(1);
					
					$wpproads_stats_data = $pro_ads_multisite->wpproads_get_option('wpproads_stats_data', 'hour');
					$hour_query = $wpproads_stats_data == 'hour' ? ' AND hour = "'.$hour.'" ' : '';
						
					// Select if row already exists
					$res = $wpdb->get_results("SELECT id, hits FROM " . $pro_ads_multisite->wpproads_db_prefix() . "wpproads_user_stats USE INDEX(banner_id_date)
						WHERE 
							type = 'impression' AND 
							advertiser_id = '".$advertiser_id."' AND 
							campaign_id = '".$campaign_id."' AND 
							adzone_id = '".$adzone_id."' AND
							banner_id = '".$banner_id."' AND 
							ip_address = '".$ip_adress."' AND 
							date = ".$today." 
							".$hour_query." 
						LIMIT 1"
					);
					
					
					// Update statistics ...
					if( !$res )
					{	
						$wpdb->query("INSERT INTO " . $pro_ads_multisite->wpproads_db_prefix() . "wpproads_user_stats 
							SET 
								advertiser_id   = '".$advertiser_id."',
								campaign_id     = '".$campaign_id."',
								banner_id       = '".$banner_id."',
								adzone_id       = '".$adzone_id."',
								date            = '".$today."',
								time            = '".current_time( 'timestamp' )."',
								hour            = '".$hour."',
								type            = 'impression',
								ip_address      = '".$ip_adress."',
								city            = '".$stats_data['userdata']['geo_city']."',
								country         = '".$stats_data['userdata']['geo_country']."',
								country_cd      = '".$stats_data['userdata']['geo_country_cd']."',
								browser         = '".$stats_data['userdata']['browser']."',
								platform        = '".$stats_data['userdata']['platform']."',
								device		    = '".$stats_data['userdata']['device']."',
								refferal_url    = '".$ref_url."',
								refferal_host   = '".$pro_ads_main->get_domain_url($ref_url)."'	,
								hits            = 1														 
						");
					}
					else
					{
						if( $wpproads_save_impressions_type != 'unique' )
						{
							$hits = $res[0]->hits + 1;
							$wpdb->query("UPDATE " . $pro_ads_multisite->wpproads_db_prefix() . "wpproads_user_stats SET hits = ".$hits." WHERE id = ".$res[0]->id." ");
						}
					}
				}
			}
			
			// Update banner impressions
			$banner_impressions = get_post_meta( $banner_id, '_banner_impressions', true );
			$banner_impressions = $banner_impressions+1;
			update_post_meta( $banner_id, '_banner_impressions', $banner_impressions );
			// Update banner in adzone impressions
			if( !empty($adzone_id) )
			{
				$banner_adzone_impressions = get_post_meta( $banner_id, '_adzone_'.$adzone_id.'_impressions', true );
				$banner_adzone_impressions = $banner_adzone_impressions+1;
				update_post_meta( $banner_id, '_adzone_'.$adzone_id.'_impressions', $banner_adzone_impressions );
			}
			// check banner contract
			$banner_contract = get_post_meta( $banner_id, '_banner_contract', true );
			if( $banner_contract == 2 )
			{
				// Update banner status
				$pro_ads_banners->update_banner_status( $banner_id );
			}
			
			// GA
			if( !empty($wpproads_google_analytics_id) )
			{
				$this->save_ga_action( $stats_data );
			}
			
			// Filter -------------------------------------------------------
			apply_filters('wp_pro_ads_save_stats', $stats_data);
		}
	}
	
	
	
	
	
	
	
	
	/*
	 * Save Clicks
	 *
	 * @access public
	 * @param int $banner_id, $adzone_id
	 * @return null
	*/
	public function save_clicks( $banner_id, $adzone_id = '', $ref_url = '' )
	{
		global $wpdb, $pro_ads_main, $pro_ads_browser, $pro_ads_banners, $pro_ads_multisite;
		
		// Check if stats need to be saved for this IP
		if( !$this->ip_blocked() )
		{
		
			// Check if satistics are enabled
			$wpproads_enable_stats = $pro_ads_multisite->wpproads_get_option('wpproads_enable_stats', 0);
			$wpproads_enable_clicks = $pro_ads_multisite->wpproads_get_option('wpproads_enable_clicks', 1);
			
			// Google Analytics
			$wpproads_google_analytics_id = $pro_ads_multisite->wpproads_get_option('wpproads_google_analytics_id', '');
			
			$advertiser_id = get_post_meta( $banner_id, '_banner_advertiser_id', true );
			$campaign_id   = get_post_meta( $banner_id, '_banner_campaign_id', true );
			
			$stats_data = array(
				'type'                => 'click',
				'banner_id'           => $banner_id, 
				'adzone_id'           => $adzone_id, 
				'advertiser_id'       => $advertiser_id,
				'campaign_id'         => $campaign_id,
				'google_analytics_id' => $wpproads_google_analytics_id
			);
			
			if( $wpproads_enable_stats && $wpproads_enable_clicks )
			{
				$wpproads_enable_userdata_stats = get_option('wpproads_enable_userdata_stats', 0);
				// Get save type 
				$wpproads_save_clicks_type = get_option('wpproads_save_clicks_type', 'unique');
				
				$today = mktime(0, 0, 0, $pro_ads_main->time_by_timezone('m')  , $pro_ads_main->time_by_timezone('d'), $pro_ads_main->time_by_timezone('Y'));
				$hour = date_i18n("G");
				$ip_adress = $pro_ads_main->IP;
				
				
				$isbot = $pro_ads_main->detect_bots();
				if( !$isbot )
				{
					/*$geo             = $wpproads_enable_userdata_stats ? $pro_ads_main->get_geo_info() : '';
					$geo_city        = $wpproads_enable_userdata_stats ? $geo['city'] : '';
					$geo_country     = $wpproads_enable_userdata_stats ? $geo['country'] : '';
					$geo_country_cd  = $wpproads_enable_userdata_stats ? $geo['country_cd'] : '';
					$browser         = $wpproads_enable_userdata_stats ? $pro_ads_browser->getBrowser() : '';
					$platform        = $wpproads_enable_userdata_stats ? $pro_ads_browser->getPlatform() : '';
					$device          = $wpproads_enable_userdata_stats ? $pro_ads_main->get_visitor_device() : '';*/
					$stats_data['userdata'] = $wpproads_enable_userdata_stats ? $pro_ads_main->get_user_data() : $pro_ads_main->get_user_data(1);
					
					$wpproads_stats_data = $pro_ads_multisite->wpproads_get_option('wpproads_stats_data', 'hour');
					$hour_query = $wpproads_stats_data == 'hour' ? ' AND hour = "'.$hour.'" ' : '';
					
					// Select if row already exists
					$res = $wpdb->get_results("SELECT id, hits FROM " . $pro_ads_multisite->wpproads_db_prefix() . "wpproads_user_stats USE INDEX(banner_id_date)
						WHERE 
							type = 'click' AND 
							advertiser_id = '".$advertiser_id."' AND 
							campaign_id = '".$campaign_id."' AND 
							adzone_id = '".$adzone_id."' AND
							banner_id = '".$banner_id."' AND 
							ip_address = '".$ip_adress."' AND 
							date = ".$today." 
							".$hour_query." 
						LIMIT 1"
					);
					
					// Update statistics ...
					if( !$res )
					{	
						$wpdb->query("INSERT INTO " . $pro_ads_multisite->wpproads_db_prefix() . "wpproads_user_stats 
							SET 
								advertiser_id   = '".$advertiser_id."',
								campaign_id     = '".$campaign_id."',
								banner_id       = '".$banner_id."',
								adzone_id       = '".$adzone_id."',
								date            = '".$today."',
								time            = '".current_time( 'timestamp' )."',
								hour            = '".$hour."', 
								type            = 'click',
								ip_address      = '".$ip_adress."',
								city            = '".$stats_data['userdata']['geo_city']."',
								country         = '".$stats_data['userdata']['geo_country']."',
								country_cd      = '".$stats_data['userdata']['geo_country_cd']."',
								browser         = '".$stats_data['userdata']['browser']."',
								platform        = '".$stats_data['userdata']['platform']."',
								device		    = '".$stats_data['userdata']['device']."',
								refferal_url    = '".$ref_url."',
								refferal_host   = '".$pro_ads_main->get_domain_url($ref_url)."',
								hits            = 1														 
						");
					}
					else
					{
						if( $wpproads_save_clicks_type != 'unique' )
						{
							$hits = $res[0]->hits + 1;
							$wpdb->query("UPDATE " . $pro_ads_multisite->wpproads_db_prefix() . "wpproads_user_stats SET hits = ".$hits." WHERE id = ".$res[0]->id." ");
						}
					}
				}
			}
			
			// Update banner clicks
			$banner_clicks = get_post_meta( $banner_id, '_banner_clicks', true );
			$banner_clicks = $banner_clicks+1;
			update_post_meta( $banner_id, '_banner_clicks', $banner_clicks );
			
			// Update banner status
			$pro_ads_banners->update_banner_status( $banner_id );
			
			
			// GA
			if( !empty($wpproads_google_analytics_id) )
			{
				$this->save_ga_action( $stats_data );
			}
			
			// Filter -------------------------------------------------------
			apply_filters('wp_pro_ads_save_stats', $stats_data);
		}
	}
	
	
	
	
	
	
	
	
	
	/*
	 * Save action with Google Analitics
	 *
	 * https://support.google.com/analytics/answer/1033068?hl=en 
	 *
	 * @access public
	 * @return html
	*/
	public function save_ga_action( $args )
	{
		if( !empty(	$args['google_analytics_id'] ))
		{
			$loops = array('banner', 'adzone', 'advertiser');
			
			//set POST variables
			$url = 'http://www.google-analytics.com/collect';
			
			foreach( $loops as $loop )
			{
				$data = get_post($args[$loop.'_id'], ARRAY_A);
				
				if($args['type'] != 'click')
				{
					// http://tunasite.com/helpdesk/ftopic/google-analytics-integration-is-unusable/
					echo "<script>wppas_ga('send', 'event', 'wpproads ".$loop." statistics', '".$args['type']."', '".$loop.': '.$data['post_name'].'-'.$args[$loop.'_id']."',{nonInteraction: true});</script>";
				}
				else
				{
					// https://developers.google.com/analytics/devguides/collection/protocol/v1/parameters
					$fields_string = '';
					$fields = array(
						'v'   => 1,
						'tid' => $args['google_analytics_id'],
						'cid' => 5555,
						't'   => 'event',
						'ec'  => 'wpproads '.$loop.' statistics',
						'ea'  => $args['type'],
						'el'  => $loop.': '.$data['post_name'].'-'.$args[$loop.'_id'],
						'ni'  => 1
					);
					
					//url-ify the data for the POST
					foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
					rtrim($fields_string, '&');
					
					$ch = curl_init();
					curl_setopt($ch,CURLOPT_URL, $url); //curl the url
					curl_setopt($ch,CURLOPT_POST, count($fields));
					curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
					curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE); //return the transfer to be later saved to a variable
					$result = curl_exec($ch);
					
					//close connection
					curl_close($ch);
				}
			}
		}
	}
	
	
	
	
	
	
	
	
	/*
	 * Save PDF
	 *
	 * This function creates the PDF invoice
	 * there are 2 options
	 * - view (will show the invoice in the browser)
	 * - save (will save the invoice to your computer)
	 *
	 * @access public
	 * @return html
	*/
	public function wpproads_save_stats_pdf( $args = array() )
	{
		if(isset($_GET['stats_pdf']) && !empty($_GET['stats_pdf'])) 
		{
			if( !defined('DOMPDF_DIR') )
				require_once( WP_ADS_INC_DIR.'/dompdf/dompdf_config.inc.php');
			
			$defaults = array(
				'type' => 'view'
			);
			$args = wp_parse_args( $args, $defaults );
			
			// If custom file is found in users theme -------------------------------------
			if ( $theme_file = locate_template( array( 'wpproads/stats-pdf.php' ) ) ) 
			{
				include( $theme_file );
			}
			else
			{
				include( WP_ADS_TPL_DIR.'/pdf/stats-pdf.php');
			}
			
			// Create PDF -----------------------------------------------------------------
			$dompdf = new DOMPDF();
			$dompdf->load_html($theme_file);
			$dompdf->render();
			
			
			if( $args['type'] == 'view' )
			{
				$dompdf->stream('wpproads-stats.pdf', array('Attachment' => 0));
			}
			elseif( $args['type'] == 'mail' )
			{
				$upload = wp_upload_dir();
				$upload_dir = $upload['basedir'];
				$upload_dir = $upload_dir . '/wpproads_uploads/';
		
				file_put_contents($upload_dir.'wpproads-stats.pdf', $dompdf->output());
				return $upload_dir.'wpproads-stats.pdf';
			}
			else
			{
				$dompdf->stream('wpproads-stats.pdf');
			}
			
			exit();
		}
	}
	
	
	
	
	
	
	
	/*
	 * PDF Invoice template
	*/ 
	function wpproads_pdf_template( $pdf_template_path )
	{
		global $pro_ads_statistics;
		
		if(isset($_GET['stats_pdf']) && !empty($_GET['stats_pdf'])) 
		{
			$pro_ads_statistics->wpproads_save_stats_pdf();
		}
		
		return $pdf_template_path;
	}
	
}
?>