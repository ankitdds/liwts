<?php
header("Content-type: text/css; charset: UTF-8");

$container_id = '#wpproads_dashboard';
$container_class = '.wpproads';
$color = '#abdb6d';
$dark_color = '#85B747';
$header_background_image = '../../images/headerbg.png';
?>

/**
 * Header
 */

<?php echo $container_id; ?>.ad_dashboard .adheader {
	background-color: <?php echo $color; ?>;
    <?php echo !empty($header_background_image) ? 'background-image: url("'.$header_background_image.'");' : ''; ?>
    box-shadow: 0px 1px 0px <?php echo $dark_color; ?> inset, 0px 1px 0px rgba(0, 0, 0, 0.08);
}

<?php echo $container_id; ?>.ad_dashboard .menu {
	background-color: <?php echo $color; ?>;
    box-shadow: 0px 1px 0px <?php echo $dark_color; ?>  inset, 0px 1px 0px rgba(0, 0, 0, 0.08);
}

<?php echo $container_id; ?> .main_button {
	border-bottom-color: <?php echo $color; ?>;
}

<?php echo $container_id; ?> .main_button:hover {
	border-bottom-color: <?php echo $dark_color; ?>;
}


/**
 * Tabs
 */
<?php echo $container_id; ?> #tuna_tab_left ul a.focused{
	border-left:3px solid <?php echo $color; ?> !important;
}

<?php echo $container_id; ?> #tuna_tab_left ul a:hover{
	border-left: 3px solid <?php echo $dark_color; ?>;
}



/**
 * Sidebar Links
 */
<?php echo $container_id; ?>.ad_dashboard .sidebar .snipr_info_btn {
	color: <?php echo $color; ?>;
}
<?php echo $container_id; ?>.ad_dashboard .sidebar .snipr_info_btn:hover {
	color: <?php echo $dark_color; ?>;
}



/**
 * Default
 */
<?php echo $container_id; ?> a.plus {
	border-right: 1px solid <?php echo $dark_color; ?>;
}