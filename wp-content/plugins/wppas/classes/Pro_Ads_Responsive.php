<?php
class Pro_Ads_Responsive {
	
	
	
	
	public function device_sizes()
	{
		$device_sizes = array(
			array(
				'prefix' => '',
				'type' => 'desktop',
				'name' => __('Desktop','wpproads'),
				'desc' => __('Adzone size when viewed from a desktop.','wpproads'),
				'size' => array(1025)
			), 
			array(
				'prefix' => '_tablet_portrait',
				'type' => 'tablet_portrait',
				'name' => __('Tablet','wpproads'),
				'desc' => __('Adzone size when viewed from a tablet in portrait mode.','wpproads'),
				'size' => array(540, 768)
			), 
			array(
				'prefix' => '_phone_portrait',
				'type' => 'phone_portrait',
				'name' => __('Phone','wpproads'),
				'desc' => __('Adzone size when viewed from a phone in portrait mode.','wpproads'),
				'size' => array(240, 425)
			)
		);
		
		return $device_sizes;	
	}
	
	
	
	
	
	
	public function get_device_type()
	{
		global $pro_ads_browser;
		
		if( $pro_ads_browser->isMobile() )
		{
			$device = array( 'device' => 'phone', 'type' => 'phone_portrait', 'prefix' => '_phone_portrait');
		}
		elseif( $pro_ads_browser->isTablet() )
		{
			$device =  array( 'device' => 'tablet', 'type' => 'tablet_portrait', 'prefix' => '_tablet_portrait' );
		}
		else
		{
			$device =  array( 'device' => 'desktop', 'type' => 'desktop', 'prefix' => '' );
		}
		
		return $device;
	}
	
	
	
	
	
	public function check_available_adzone_sizes( $id )
	{
		$sizes = array();
		
		foreach( $this->device_sizes() as $device_size )
		{
			$size = get_post_meta( $id, '_adzone_size'.$device_size['prefix'], true );
			$resp = get_post_meta( $id, '_adzone_responsive'.$device_size['prefix'], true );
			
			if( !empty($size) || !empty($resp) )
			{
				$sizes[] = array(
					'type' => $device_size['type'],
					'size' => $size
				);
			}
		}
		
		return $sizes;
	}
}
?>