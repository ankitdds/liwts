<?php get_header(); ?>
	<?php
	$subResult = identifyPostSubContentPage();
	$subContent = null;
		
	if($subResult['vparam'] != 0 && isset($subResult['sub_index'])){
		$subContents = $subResult['sub_contents'];
		if(isset($subContents[$subResult['sub_index']])){
			$subContent = $subContents[$subResult['sub_index']];
		}
	} 
	?>
	<div id="content" <?php Avada()->layout->add_style( 'content_style' ); ?>>
		<?php if( ( ! Avada()->settings->get( 'blog_pn_nav' ) && get_post_meta($post->ID, 'pyre_post_pagination', true) != 'no' ) ||
				  ( Avada()->settings->get( 'blog_pn_nav' ) && get_post_meta($post->ID, 'pyre_post_pagination', true) == 'yes' ) ): ?>
		<?php /*?><div class="single-navigation clearfix">
			<?php previous_post_link('%link', __('Previous', 'Avada')); ?>
			<?php next_post_link('%link', __('Next', 'Avada')); ?>
		</div><?php */?>
		<?php endif; ?>
		<?php while( have_posts() ): the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
        	<?php $categories = get_the_category( $post->ID ); //echo '<pre>'; print_r($categories); ?>
            <ul class="post-categoriestop">
            	<li><a rel="category tag" href="<?php echo get_category_link($categories[0]->cat_ID); ?>"><?php echo $categories[0]->name; ?></a></li>
            </ul>
        	<?php if(Avada()->settings->get( 'blog_post_title' )): ?>
				<?php echo avada_render_post_title( $post->ID, FALSE, '', '2' ); ?>
			<?php elseif( ! Avada()->settings->get( 'disable_date_rich_snippet_pages' ) ): ?>
				<span class="entry-title" style="display: none;"><?php the_title(); ?></span>
			<?php endif; ?>
			<div class="sub-post-title">
				<?php if(Avada()->settings->get( 'blog_post_title' )): ?>
	        		<?php if($subContent != null && isset($subContent['title']) && $subContent['title'] != null){ ?>
	                  	<h2 class="sub-title subpost-sub-title"><?php 
						$subTitle = $subContent['title'];
						if($subResult['show_numbers'] == true){
							$subPostNumber = (int) $subResult['sub_index'];
							
							if($subResult['count_down_up'] == "down"){
								$subPostNumber = (int) count($subContents) - $subPostNumber;
							}
							else{
								$subPostNumber = $subPostNumber+1;
							}
							$subTitle = $subPostNumber.". ".$subTitle;
						}
						else{
							$subTitle = str_replace("%number%",'',$subTitle);
						}
						echo $subTitle;
						?></h2>
					<?php } ?>
				<?php elseif( ! Avada()->settings->get( 'disable_date_rich_snippet_pages' ) ): ?>
					<?php if($subContent != null && isset($subContent['title']) && $subContent['title'] != null){ ?>
	                  	<span class="entry-title subpost-sub-title"><?php 
						$subTitle = $subContent['title'];
						if($subResult['show_numbers'] == true){
							$subPostNumber = (int) $subResult['sub_index'];
							
							if($subResult['count_down_up'] == "down"){
								$subPostNumber = (int) count($subContents) - $subPostNumber;
							}
							else{
								$subPostNumber = $subPostNumber+1;
							}
							$subTitle = $subPostNumber.". ".$subTitle;
						}
						else{
							$subTitle = str_replace("%number%",'',$subTitle);
						}
						echo $subTitle;
						?></span>
					<?php } ?>
				<?php endif; ?>
			</div>
            <div class="time-comment"><div class="fusion-meta"><?php echo get_the_time('j F \a\t g:i'); ?></div><div class="comment-bg"><?php echo get_comments_number(); ?></div></div>
			<div class="post-content">
				<?php 
				if($subContent != null && isset($subContent['content']) && $subContent['content'] != null):
					echo $subContent['content'];
				else:
					the_content();
				endif; 
				?>
				<?php avada_link_pages(); ?>
			</div>
            <?php
			$full_image = '';
			if( ! post_password_required($post->ID) ): // 1
				if($subContent != null && isset($subContent['image']) && $subContent['image'] != null): ?>
					<div class="sub-post-img">
						<a href="<?php echo $subContent['image']?>" data-rel="iLightbox[gallery<?php the_ID(); ?>]" data-title="<?php echo get_post_field('post_title', get_post_thumbnail_id()); ?>" data-caption="<?php echo get_post_field('post_excerpt', get_post_thumbnail_id()); ?>"><img src="<?php echo $subContent['image']?>" alt="<?php the_title(); ?>"/></a>
					</div>
				<?php else:
					if(Avada()->settings->get( 'featured_images_single' )): // 2
						if( avada_number_of_featured_images() > 0 || get_post_meta( $post->ID, 'pyre_video', true ) ): // 3
						?>
						<div class="fusion-flexslider flexslider fusion-flexslider-loading post-slideshow fusion-post-slideshow">
							<ul class="slides">
								<?php if(get_post_meta($post->ID, 'pyre_video', true)): ?>
								<li>
									<div class="full-video">
										<?php echo get_post_meta($post->ID, 'pyre_video', true); ?>
									</div>
								</li>
								<?php endif; ?>
								<?php if( has_post_thumbnail() && get_post_meta( $post->ID, 'pyre_show_first_featured_image', true ) != 'yes' ): ?>
								<?php $attachment_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); ?>
								<?php $full_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); ?>
								<?php $attachment_data = wp_get_attachment_metadata(get_post_thumbnail_id()); ?>
								<li>
									<?php if( ! Avada()->settings->get( 'status_lightbox' ) && ! Avada()->settings->get( 'status_lightbox_single' ) ): ?>
									<a href="<?php echo $full_image[0]; ?>" data-rel="iLightbox[gallery<?php the_ID(); ?>]" title="<?php echo get_post_field('post_excerpt', get_post_thumbnail_id()); ?>" data-title="<?php echo get_post_field('post_title', get_post_thumbnail_id()); ?>" data-caption="<?php echo get_post_field('post_excerpt', get_post_thumbnail_id()); ?>"><img src="<?php echo $attachment_image[0]; ?>" alt="<?php echo get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true); ?>" /></a>
									<?php else: ?>
									<img src="<?php echo $attachment_image[0]; ?>" alt="<?php echo get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true); ?>" />
									<?php endif; ?>
								</li>
								<?php endif; ?>
								<?php
								$i = 2;
								while($i <= Avada()->settings->get( 'posts_slideshow_number' )):
								$attachment_new_id = kd_mfi_get_featured_image_id('featured-image-'.$i, 'post');
								if($attachment_new_id):
								?>
								<?php $attachment_image = wp_get_attachment_image_src($attachment_new_id, 'full'); ?>
								<?php $full_image = wp_get_attachment_image_src($attachment_new_id, 'full'); ?>
								<?php $attachment_data = wp_get_attachment_metadata($attachment_new_id); ?>
								<li>
									<?php if( ! Avada()->settings->get( 'status_lightbox' ) && ! Avada()->settings->get( 'status_lightbox_single' ) ): ?>
									<a href="<?php echo $full_image[0]; ?>" data-rel="iLightbox[gallery<?php the_ID(); ?>]" title="<?php echo get_post_field('post_excerpt', $attachment_new_id); ?>" data-title="<?php echo get_post_field( 'post_title', $attachment_new_id ); ?>" data-caption="<?php echo get_post_field('post_excerpt', $attachment_new_id ); ?>"><img src="<?php echo $attachment_image[0]; ?>" alt="<?php echo get_post_meta($attachment_new_id, '_wp_attachment_image_alt', true); ?>" /></a>
									<?php else: ?>
									<img src="<?php echo $attachment_image[0]; ?>" alt="<?php echo get_post_meta($attachment_new_id, '_wp_attachment_image_alt', true); ?>" />
									<?php endif; ?>
								</li>
								<?php endif; $i++; endwhile; ?>
							</ul>
						</div>
						<?php endif; // 3 ?>
					<?php endif; // 3 ?>
				<?php endif; // 2 ?>
			<?php endif; // 1 ?>
            <?php the_category(); ?>
            <ul class="social">
             <li><a class="facebook" onClick="window.open('http://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php the_title(); ?>&amp;p[url]=<?php echo get_permalink();?>&amp;&p[images][0]=<?php echo $full_image;?>', 'sharer', 'toolbar=0,status=0,width=548,height=325');" target="_parent" href="javascript: void(0)"><i class="fa fa-facebook" aria-hidden="true"></i><span>Share</span></a></li>
             <li><a class="tweetbu" href="javascript: void(0)" target="_parent" onclick="window.open('http://twitter.com/home?status=<?php echo get_permalink();?>', 'sharer', 'toolbar=0,status=0,width=548,height=325');" ><i class="fa fa-twitter" aria-hidden="true"></i><span>Tweet</span></a></li>
             <li><a class="googleplus" href="javascript: void(0)" target="_parent" onclick="window.open('https://plus.google.com/share?url=<?php echo get_permalink();?>', 'sharer', 'toolbar=0,status=0,width=548,height=325');" ><i class="fa fa-google-plus" aria-hidden="true"></i><span>Share</span></a></li>
            </ul>
            <?php echo custom_content_after_post();?>
			<?php if( ! post_password_required($post->ID) ): ?>
			<?php //echo avada_render_post_metadata( 'single' ); ?>
			<?php //avada_render_social_sharing(); ?>
            
			
			
	            <!-- Related Post -->
				<?php /*?><?php
						echo avada_render_related_posts();
				?><?php */?>

				<?php if( ( Avada()->settings->get( 'blog_comments' ) && get_post_meta($post->ID, 'pyre_post_comments', true ) != 'no' ) ||
						  ( ! Avada()->settings->get( 'blog_comments' ) && get_post_meta($post->ID, 'pyre_post_comments', true) == 'yes' ) ): ?>
					<?php
					wp_reset_query();
					comments_template();
					?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
	</div>
	<?php do_action( 'fusion_after_content' ); ?>
<?php get_footer();

// Omit closing PHP tag to avoid "Headers already sent" issues.
