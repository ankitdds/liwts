<?php
/**
 * Prevent w3tc caching for banners.
 */
!defined('DONOTCACHEPAGE') ? define('DONOTCACHEPAGE', true) : '';


/* ----------------------------------------------------------------
 * Create adzone widgets
 * ---------------------------------------------------------------- */
class Wppas_Custom_Widgets extends WP_Widget 
{
	public function __construct(){
	//function Wppas_Custom_Widgets() {
		$widget_ops = array('classname' => 'wppaszone', 'description' => __( 'Display your ads.','wpproads') );
		parent::__construct('Wppas_Custom_Widgets', '<img src="'.WP_ADS_URL.'images/banner_icon_20.png">'.__('Display your ads.','wpproads'), $widget_ops);
	}
	
	function widget($args,$instance) 
	{
		global $pro_ads_adzones, $pro_ads_multisite;
		
		extract($args);
		
		if( !empty( $instance['adzone_id'] ))
		{
			echo $before_widget;
				echo do_shortcode("[pro_ad_display_adzone id=".$instance['adzone_id']."]");
			echo $after_widget;
		}
		elseif( !empty( $instance['pas_shortcode'] ))
		{
			echo $before_widget;
				echo do_shortcode($instance['pas_shortcode']);
			echo $after_widget;
		}
	}
	
	
	function update($new_instance,$old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['adzone_id'] = $new_instance['adzone_id'];
		$instance['pas_shortcode'] = $new_instance['pas_shortcode'];

		return $instance;
	}

	function form($instance) 
	{	
		global $pro_ads_adzones, $pro_ads_multisite;
		
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'adzone_id' => '', 'pas_shortcode' => ''));
		?>
        <p>
        	<label for="<?php echo $this->get_field_id('adzone_id'); ?>"><?php _e('Select your adzone:', 'wpproads'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'adzone_id' ); ?>" name="<?php echo $this->get_field_name( 'adzone_id' ); ?>" style="width:100%;">
            	
                <option value=""><?php _e('Select an Adzone', 'wpproads'); ?></option>
                
            	<?php
				$adzones = $pro_ads_adzones->get_adzones();
				/***
				 * Multisite ___________________________________________________________________ */
				$pro_ads_multisite->wpproads_wpmu_load_from_main_start();
				foreach( $adzones as $i => $adzone )
				{
					?>
					<option value="<?php echo $adzone->ID; ?>" <?php if ( $adzone->ID == $instance['adzone_id'] ) echo 'selected="selected"'; ?>>
						<?php echo get_the_title($adzone->ID); ?>
                    </option>
                    <?php
				}
				/***
				 * Multisite ___________________________________________________________________ */
				$pro_ads_multisite->wpproads_wpmu_load_from_main_stop();
				?>
				
			</select>
		</p>
        <hr />
        <p>
        	<label for="<?php echo $this->get_field_id('pas_shortcode'); ?>"><?php _e('Or add the adzone shortcode:', 'wpproads'); ?></label><br />
            <textarea id="<?php echo $this->get_field_id( 'pas_shortcode' ); ?>" name="<?php echo $this->get_field_name( 'pas_shortcode' ); ?>" style="font-size:10px; width:100%;"><?php echo $instance['pas_shortcode']; ?></textarea>
		</p>
		<?php
	}
}








/* ----------------------------------------------------------------
 * Create adzone widgets
 * ---------------------------------------------------------------- */
class Pro_Ads_Ad_Slot extends WP_Widget 
{
	public function __construct(){
	//function Pro_Ads_Ad_Slot() {
		$widget_ops = array('classname' => 'passlot_wgt', 'description' => __( 'AD Slot.','wpproads') );
		parent::__construct('Pro_Ads_Ad_Slot', '<img src="'.WP_ADS_URL.'images/banner_icon_20.png">'.__('Pro Ads - AD Slot.','wpproads'), $widget_ops);
		//$this->WP_Widget('Pro_Ads_Ad_Slot', '<img src="'.WP_ADS_URL.'images/banner_icon_20.png">'.__('Pro Ads - AD Slot.','wpproads'), $widget_ops);
	}
	
	function widget($args,$instance) 
	{
		global $pro_ads_adzones;
		extract($args);
		
		if( !empty( $instance['screen'] ))
		{
			echo $before_widget;
			echo do_shortcode('[ad_slot screen="'.$instance['screen'].'" id="'.$instance['slot_id'].'"]');
			echo $after_widget;
		}
	}
	
	
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['screen'] = strip_tags($new_instance['screen']);
		$instance['slot_id'] = strip_tags($new_instance['slot_id']);

		return $instance;
	}

	function form($instance) {
		
		global $pro_ads_responsive;
		
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'screen' => 'desktop'));
		$instance = wp_parse_args( (array) $instance, array( 'slot_id' => ''));
		?>
        <p>
        	<label for="<?php echo $this->get_field_id('screen'); ?>"><?php _e('Select the ad slot device type:'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'screen' ); ?>" name="<?php echo $this->get_field_name( 'screen' ); ?>" width="20%">
            	
                <?php
				foreach( $pro_ads_responsive->device_sizes() as $device_size )
				{
					?>
            		<option value="<?php echo $device_size['type']; ?>" <?php if ( $instance['screen'] == $device_size['type'] ) echo 'selected="selected"'; ?>><?php echo $device_size['name'] ?></option>
                    <?php
				}
				?>
                    
			</select>
		</p>
        <p>
        	<label for="<?php echo $this->get_field_id('slot_id'); ?>"><?php _e('Select a unique ID to link banners:', 'wpproads'); ?></label> 
            <input id="<?php echo $this->get_field_id( 'slot_id' ); ?>" name="<?php echo $this->get_field_name( 'slot_id' ); ?>" value="<?php echo $instance['slot_id']; ?>" />
        </p>
		<?php
	}
}
?>