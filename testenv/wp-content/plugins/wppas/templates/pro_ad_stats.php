<?php
global $pro_ads_statistics, $pro_ads_main;
?>

<div class="wrap">

<?php 
$wpproads_enable_stats = get_option('wpproads_enable_stats', 0);
if($wpproads_enable_stats)
{
	echo '<div class="bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div>';
	echo '<div class="pro_ad_stats_graph">';
		$group = isset($_GET['group']) ? $_GET['group'] : '';
		$group_id = isset( $_GET['group_id']) ? $_GET['group_id'] : '';
		echo $pro_ads_statistics->pro_ad_show_statistics(array('rid'  => 4, 'range' => 'day', 'day' => $pro_ads_main->time_by_timezone('d'), 'group' => $group, 'group_id' => $group_id)); 
	echo '</div>';
}
else
{
	?>
    <h2><?php _e('Statistics are disabled','wpproads'); ?></h2>
	<p><?php _e('You can enable statistics on the AD Dashboard under <em>General Settings</em>','wpproads'); ?></p>
    <?php
}
?>


</div>
<!-- end wrap -->