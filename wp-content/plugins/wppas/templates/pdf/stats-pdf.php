<?php
global $pro_ads_statistics;

$data = unserialize(htmlspecialchars_decode(base64_decode($_GET['data'])));
$click_data = $array = array(
	'type'     => array('slug' => 'click', 'name' => __('Clicks', 'wpproads')),
	'color'    => $data['color'],
	'range'    => $data['month'],
	'rid'      => $data['rid'],
	'day'      => $data['day'],
	'month'    => $data['month'],
	'year'     => $data['year'],
	'group'    => $data['group'],
	'group_id' => $data['group_id']
);
$impression_data = $array = array(
	'type'     => array('slug' => 'impression', 'name' => __('Impressions', 'wpproads')),
	'color'    => $data['color'],
	'range'    => $data['month'],
	'rid'      => $data['rid'],
	'day'      => $data['day'],
	'month'    => $data['month'],
	'year'     => $data['year'],
	'group'    => $data['group'],
	'group_id' => $data['group_id']
);
$clicks = $pro_ads_statistics->wpproads_stats_data( $click_data );
$impressions = $pro_ads_statistics->wpproads_stats_data( $impression_data );

$html = $pro_ads_statistics->pro_ad_show_statistics_header( 
	array( 
		'text'  => array(
			'click' => __('Clicks','wpproads'),
			'impr'  => __('Impressions','wpproads'),
			'ctr'   => __('CTR','wpproads'),
		), 
		'rid'      => $data['rid'], 
		'day'      => $data['day'], 
		'month'    => $data['month'], 
		'year'     => $data['year'],
		'group'    => $data['group'],
		'group_id' => $data['group_id']
	) 
);


$total_clicks = 0;
$total_impressions = 0;
if( phpversion() > '5.3' )
{
	$sumDetail = 'count';
	$total_clicks = array_reduce($clicks['stats_data'], function($runningTotal, $record) use($sumDetail) { $runningTotal += $record[$sumDetail]; return $runningTotal;},0);
	$total_impressions = array_reduce($impressions['stats_data'], function($runningTotal, $record) use($sumDetail) { $runningTotal += $record[$sumDetail]; return $runningTotal;},0);
}
else
{
	foreach( $clicks['stats_data'] as $i => $click )
	{
		$total_clicks += $click['count'];
	}
	foreach( $impressions['stats_data'] as $i => $impression )
	{
		$total_impressions += $impression['count'];
	}
}

$header_data = $pro_ads_statistics->wpproads_stats_header_data( $data );


/*
 * Template
*/
$theme_file.= !empty($data['group']) && !empty($data['group_id']) ? sprintf(__('<h3 style="margin:0;">%s stats for: %s</h3>', 'wpproads'), ucfirst($data['group']), get_the_title($data['group_id'])) : '';
$theme_file.= '<h2 style="margin:5px 0 10px;">'.$header_data['stats'].'</h2>';
$theme_file.= "<link rel='stylesheet' href='".WP_ADS_TPL_URL."/css/admin.css' type='text/css' media='all' />";
$theme_file.= '<div style="height:200px;">'.$html.'</div>';

$theme_file.= '<div style="width:80%;">';
foreach( $clicks['stats_data'] as $i => $click )
{
	// Get percentage
	$click_count = $click['count'] ? $click['count'] : 0;
	$impression_count = $impressions['stats_data'][$i]['count'] ? $impressions['stats_data'][$i]['count'] : 0;
	$impression_count = $click_count > $impression_count ? $impression_count = $click_count : $impression_count; // fix for clicks when impressions have been saved earlier.
	$cpers = $click_count ? round($click_count / $total_clicks * 100, 1) : 0;
	$ipers = $impression_count ? round($impression_count / $total_impressions * 100, 1) : 0;
	$dif = $click_count && $impression_count ? round(($impression_count - $click_count) / $impression_count * 100) : 0;
	$ctr = $click_count && $impression_count ? $click_count / $impression_count * 100 : 0;
	
	$iw = $ipers+30;
	$cw = $cpers/2;
	
	$label = $data['rid'] == 4 ? date('H:i',$click['label']) : $click['label'];
	
	$theme_file.= '<div style="float:left; height:20px; width:10%;">'.$label.'</div>';
	$theme_file.= '<div style="float:left; height:20px; width:68%; background:#EFEFEF; margin:0 2% 2px 0;">';
		
		$theme_file.= $impression_count ? '<div style="position:absolute; text-align:right; height:20px; padding:0 5px 0 0; font-size:10px; min-width:10px; max-width:98%; width:'.$iw.'%; background:#77b7c5;">'.$impression_count.'</div>' : '';
		$theme_file.= $click_count ? '<div style="position:absolute; text-align:right; height:20px; padding:0 5px 0 0; font-size:10px; min-width:10px; max-width:98%; width:'.$cw.'%; background:#66b71a;">'.$click_count.'</div>' : '';	
		
	$theme_file.= '</div>';
	$theme_file.= $click_count || $impression_count ? '<div style="float:left; height:20px; width:20%; font-size:12px; color:#999;">ctr: '.number_format($ctr, 2, '.', '').'%</div>' : '';
	$theme_file.= '<div style="clear:both;"></div>';
}
	$theme_file.= '<div style="clear:both;"></div>';
$theme_file.= '</div>';
?>