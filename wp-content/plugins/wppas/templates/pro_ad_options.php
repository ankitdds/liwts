<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if(isset($_POST['1']))
	{
		update_option( 'wpproads_custom_css', $_POST['wpproads_custom_css'] );
	}
}


$custom_css = get_option('wpproads_custom_css', '');
?>

<div class="wrap">

	<div>
        <h2><?php echo _e('Ad Options','wpproads'); ?></h2>
     
        <table id="tuna_tab_customization">
            <tr>
            	<td valign="top">
                    <div id="tuna_tab_left">
                        <ul>
                            
                            <li><a class="focused" data-target="general-settings" title=""><?php _e('General Settings', 'wpproads'); ?></a></li>
                            <!--<li><a class="" data-target="options" title=""><?php _e('', 'wpproads'); ?></a></li>-->
                        </ul>					
                    </div>
                </td>
                <td width="100%"  valign="top">
                    <div id="tuna_tab_right">
                        <p id="tuna_tab_arrow" style="top:4px"></p>
                        <div class="customization_right_in">
                            
                            
                            <div id="general-settings" style="" class="nfer">
                                <h3 style="margin-bottom:10px" ><?php _e('General Settings', 'wpproads'); ?></h3>
                                <em class="hr_line"></em>
                                <form action="" method="post" enctype="multipart/form-data">
                                    <div class="tuna_meta metabox-holder">
                        
                                        <div class="postbox nobg">
                                            <div class="inside">
                                                <table class="form-table">
                                                    <tbody>
                                                        
                                                        <tr>
                                                            <th scope="row">
                                                                <?php _e('Custom CSS', 'wpproads'); ?>
                                                                <span class="description"><?php _e('If you need to customize some style for the Ads plugin you can add the custom CSS here.','wpproads'); ?></span>
                                                            </th>
                                                            <td>
                                                                <textarea id="wpproads_custom_css" class="ivc_input" style="height:100px; margin:10px 0 0 0;" name="wpproads_custom_css"><?php echo $custom_css; ?></textarea>
                                                                <span class="description"><?php _e('','wpproads'); ?></span>
                                                            </td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- end .postbox -->
                                    </div>
                                    <!-- end .tuna_meta -->
                                            
                                    
                                    <div class="btn_container_with_menu" style="margin-top:40px;">
                                        <input type="submit" value="<?php _e('Save General Settings', 'wpproads'); ?>" class="button-primary" name="1" />
                                    </div>
                                </form>
                            </div>
                            <!-- end #general-settings -->
                            
                            
                            
                            
                            
                            
                            
                        </div>
                        <!-- end .customization_right_in -->
                    </div>
                    <!-- end #tuna_tab_right -->
                </td>
            </tr>
        </table>
        
        
        
	</div> <!-- end #pro_ivc -->
</div> <!-- end .wrap -->

<script type='text/javascript'>
jQuery(document).ready(function($) {
    
    // switching between tabs
	$('#tuna_tab_left').find('a').click(function(){
		
		var nfer_id = $(this).data('target');
		
		$('.nfer').hide();
		$('#'+nfer_id).show();
		
		change_tab_position($(this));
		
		//window.location.hash = nfer_id;
		return false;
		
	});
	// position of the arrow
	function change_tab_position(obj){

		// class switch
		$('#tuna_tab_left').find('a').removeClass('focused');
		obj.addClass('focused');

		var menu_position = obj.position();
		$('#tuna_tab_arrow').css({'top':(menu_position.top+3)+'px'}).show();
	}
    
});
</script>


