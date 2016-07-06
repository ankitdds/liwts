<?php
if(!is_admin()){
	show_admin_bar( false );
	define( 'BFITHUMB_UPLOAD_DIR', 'resizedimages' );
	include_once 'BFI_Thumb.php';
	add_action('wp_enqueue_scripts', 'enqueue_and_register_liwts_scripts', 999);
	function enqueue_and_register_liwts_scripts()
	{
		wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
		wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css' );
		wp_enqueue_style( 'child-custom-style', get_stylesheet_directory_uri() . '/custom.css' );
		
		wp_dequeue_script('devicepx');
		wp_enqueue_script( 'owl-script', get_stylesheet_directory_uri() . '/js/owl.carousel.min.js' );
		wp_enqueue_script( 'liwts-script', get_stylesheet_directory_uri() . '/js/script.js' );
	}
} 

function the_excerpt_max_charlength($excerpt, $charlength) {
	$charlength++;

	if ( mb_strlen( $excerpt ) > $charlength ) {
		$subex = mb_substr( $excerpt, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );		$excerpt = "";
		if ( $excut < 0 ) {
			$excerpt .= mb_substr( $subex, 0, $excut );
		} else {
			$excerpt .= $subex;
		}
		$excerpt .= "...";
		return $excerpt;
	} else {
		return $excerpt;
	}
}
add_action( 'widgets_init', 'register_liwts_widgets' );

function register_liwts_widgets(){
	register_widget( 'LIWTS_Trending_Posts' );
	register_widget( 'LIWTS_Latest_Posts' );
	 register_sidebar(array(
	   'id' => 'shop_sidebar',
	   'name' => __( 'Shop Sidebar', 'liwts' ),
	   'description' => __( 'Shop Sidebar Widget', 'liwts' ),
	   'before_widget' => '<div id="%1$s" class="widget %2$s">',
	   'after_widget' => '</div>',
	   'before_title' => '<h4 class="widgettitle">',
	   'after_title' => '</h4>',
	 ));
	 
	 register_sidebar(array(
	 		'id' => '404_sidebar',
	 		'name' => __( '404 Sidebar', 'liwts' ),
	 		'description' => __( '404 Sidebar Widget', 'liwts' ),
	 		'before_widget' => '<div id="%1$s" class="widget %2$s">',
	 		'after_widget' => '</div>',
	 		'before_title' => '<h4 class="widgettitle">',
	 		'after_title' => '</h4>',
	 ));
}
add_action( 'widgets_init', 'register_liwts_widgets' );

function liwtsListCategories($args = null)
{
	$catArgs = array();
	$catArgs['hide_empty'] = 0;
	$categories = get_categories($catArgs);
	
	$sortorders = $finalcatgories = array();
	foreach ($categories as $key => $category) {
		$sortorders[$key]  = get_field('sort_order', $category->taxonomy . '_' . $category->term_id);
		$finalcatgories[$key] = $category;
	}
	array_multisort($sortorders, SORT_ASC, $finalcatgories, SORT_ASC, $categories);
	
	$i=0;
	$html = "";
	$html .= "<div class='catdisplay'>";
	foreach ($categories as $cat) {
		//return print_r($cat);
		$displayincat = get_field('display_on_category_page',$cat->taxonomy.'_'.$cat->term_id);
		if($displayincat != 1){ continue; }
		if($i++ % 2 == 0):
			$html .= "<ul>";
		endif;
		$html .= "<li><div class='catblock'>";
		$image = get_field('category_image',$cat->taxonomy.'_'.$cat->term_id);
		//$link = get_field('link', $image['ID']);
		if($image != null): 
			$html .= "<a href=".get_category_link($cat->term_id)."><img src=".$image." alt=".$cat->name." /></a>";
		endif;
		$html .= "<span><a href=".get_category_link($cat->term_id).">".$cat->name."</a></span>";
		$html .= "</div></li>";		
		//$cat->name;
		//return print_r($cat);
		
		if($i % 2 == 0 || $i == count($categories)):
			$html .= "</ul>";
		endif;
	}
	$html .= "</div>";
	return $html;
}
add_shortcode('liwtslistcategories','liwtsListCategories',0);

add_action( 'after_setup_theme', 'override_avada_functionality' );
function override_avada_functionality() {
	add_action( 'woocommerce_before_shop_loop', 'liwts_woocommerce_catalog_ordering', 30 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	remove_action( 'woocommerce_before_shop_loop', 'avada_woocommerce_catalog_ordering', 30 );
}

function liwts_woocommerce_catalog_ordering() {

		if ( isset( $_SERVER['QUERY_STRING'] ) ) {
			parse_str( $_SERVER['QUERY_STRING'], $params );

			$query_string = '?' . $_SERVER['QUERY_STRING'];
		} else {
			$query_string = '';
		}

		// replace it with theme option
		if ( Avada()->settings->get( 'woo_items' ) ) {
			$per_page = Avada()->settings->get( 'woo_items' );
		} else {
			$per_page = 12;
		}

		$pob = ! empty( $params['product_orderby'] ) ? $params['product_orderby'] : 'default';

		if ( ! empty( $params['product_order'] ) ) {
			$po = $params['product_order'];
		} else {
			switch ( $pob ) {
				case 'date':
					$po = 'desc';
					break;
				case 'price':
					$po = 'asc';
					break;
				case 'popularity':
					$po = 'asc';
					break;
				case 'rating':
					$po = 'desc';
					break;
				case 'name':
					$po = 'asc';
					break;
				case 'default':
					$po = 'asc';
					break;
			}
		}

		switch ( $pob ) {
			case 'date':
				$order_string = __( 'Date', 'Avada' );
				break;
			case 'price':
				$order_string = __( 'Price', 'Avada' );
				break;
			case 'popularity':
				$order_string = __( 'Popularity', 'Avada' );
				break;
			case 'rating':
				$order_string = __( 'Rating', 'Avada' );
				break;
			case 'name':
				$order_string = __( 'Name', 'Avada' );
				break;
			case 'default':
				$order_string = __( 'Default Order', 'Avada' );
				break;
		}

		$pc = ! empty( $params['product_count'] ) ? $params['product_count'] : $per_page;
		$breadCrumbs = new Fusion_Breadcrumbs();
		
		ob_start();
		$breadCrumbs->get_breadcrumbs();
		$breadCrumbHtml = ob_get_clean();
		
		
		$html = '';
		$html .= '<div class="catalog-ordering clearfix">';
		$html .= '<div class="breadcrumb-container">'.$breadCrumbHtml.'</div>';
		$html .= '<div class="orderby-order-container">';

		$html .= '<ul class="orderby">';
		$html .= '<li class="' . ( ( $pob == 'name' ) ? 'current' : '' ) . '"><a class="currentdir-'.(($po == "desc")? "asc" : "desc").'" href="' . fusion_add_url_parameter(fusion_add_url_parameter( $query_string, 'product_orderby', 'name' ), 'product_order', ($po == "desc")? "asc" : "desc" ) . '"><strong>' . __( 'Name', 'Avada' ) . '</strong></a></li>';
		$html .= '<li class="' . ( ( $pob == 'price' ) ? 'current' : '' ) . '"><a class="currentdir-'.(($po == "desc")? "asc" : "desc").'" href="' . fusion_add_url_parameter(fusion_add_url_parameter( $query_string, 'product_orderby', 'price' ), 'product_order', ($po == "desc")? "asc" : "desc" ) . '"><strong>' . __( 'Price', 'Avada' ) . '</strong></a></li>';
		$html .= '<li class="' . ( ( $pob == 'date' ) ? 'current' : '' ) . '"><a class="currentdir-'.(($po == "desc")? "asc" : "desc").'" href="' . fusion_add_url_parameter(fusion_add_url_parameter( $query_string, 'product_orderby', 'date' ), 'product_order', ($po == "desc")? "asc" : "desc" ) . '"><strong>' . __( 'Date', 'Avada' ) . '</strong></a></li>';
		$html .= '<li class="' . ( ( $pob == 'popularity' ) ? 'current' : '' ) . '"><a class="currentdir-'.(($po == "desc")? "asc" : "desc").'" href="' . fusion_add_url_parameter(fusion_add_url_parameter( $query_string, 'product_orderby', 'popularity' ), 'product_order', ($po == "desc")? "asc" : "desc" ) . '"><strong>' . __( 'Popularity', 'Avada' ) . '</strong></a></li>';
		$html .= '<li class="' . ( ( $pob == 'rating' ) ? 'current' : '' ) . '"><a class="currentdir-'.(($po == "desc")? "asc" : "desc").'" href="' . fusion_add_url_parameter(fusion_add_url_parameter( $query_string, 'product_orderby', 'rating' ), 'product_order', ($po == "desc")? "asc" : "desc" ) . '"><strong>' . __( 'Rating', 'Avada' ) . '</strong></a></li>';
		$html .= '</ul>';


		/* $html .= '<ul class="order">';
		if ( $po == 'desc' ):
			$html .= '<li class="desc"><a aria-haspopup="true" href="' . fusion_add_url_parameter( $query_string, 'product_order', 'asc' ) . '"><i class="fusion-icon-arrow-down2 icomoon-up"></i></a></li>';
		endif;
		if ( $po == 'asc' ):
			$html .= '<li class="asc"><a aria-haspopup="true" href="' . fusion_add_url_parameter( $query_string, 'product_order', 'desc' ) . '"><i class="fusion-icon-arrow-down2"></i></a></li>';
		endif;
		$html .= '</ul>'; */

		$html .= '</div>';

		/* $html .= '<ul class="sort-count order-dropdown">';
		$html .= '<li>';
		$html .= '<span class="current-li"><a aria-haspopup="true">' . __( 'Show', 'Avada' ) . ' <strong>' . $per_page . ' ' . __( ' Products', 'Avada' ) . '</strong></a></span>';
		$html .= '<ul>';
		$html .= '<li class="' . ( ( $pc == $per_page ) ? 'current' : '' ) . '"><a href="' . fusion_add_url_parameter( $query_string, 'product_count', $per_page ) . '">' . __( 'Show', 'Avada' ) . ' <strong>' . $per_page . ' ' . __( 'Products', 'Avada' ) . '</strong></a></li>';
		$html .= '<li class="' . ( ( $pc == $per_page * 2 ) ? 'current' : '' ) . '"><a href="' . fusion_add_url_parameter( $query_string, 'product_count', $per_page * 2 ) . '">' . __( 'Show', 'Avada' ) . ' <strong>' . ( $per_page * 2 ) . ' ' . __( 'Products', 'Avada' ) . '</strong></a></li>';
		$html .= '<li class="' . ( ( $pc == $per_page * 3 ) ? 'current' : '' ) . '"><a href="' . fusion_add_url_parameter( $query_string, 'product_count', $per_page * 3 ) . '">' . __( 'Show', 'Avada' ) . ' <strong>' . ( $per_page * 3 ) . ' ' . __( 'Products', 'Avada' ) . '</strong></a></li>';
		$html .= '</ul>';
		$html .= '</li>';
		$html .= '</ul>'; */

		$woocommerce_toggle_grid_list = Avada()->settings->get( 'woocommerce_toggle_grid_list' );
		$product_view = 'grid';
		if ( isset( $_SERVER['QUERY_STRING'] ) ) {
			parse_str( $_SERVER['QUERY_STRING'], $params );
			if( isset($params[ 'product_view' ] ) ){
				$product_view = $params[ 'product_view' ];
			}
		}

		if( $woocommerce_toggle_grid_list ){
			$html .= '<ul class="fusion-grid-list-view">';

			if ( $product_view == 'grid' ) {
				$html .= '<li class="fusion-grid-view-li active-view">';
			} else {
				$html .= '<li class="fusion-grid-view-li">';
			}
			$html .= '<a class="fusion-grid-view" aria-haspopup="true" href="' . fusion_add_url_parameter( $query_string, 'product_view', 'grid' ) . '"><i class="fusion-icon-grid icomoon-grid"></i></a>';
			$html .= '</li>';

			if ( $product_view == 'list' ) {
				$html .= '<li class="fusion-list-view-li active-view">';
			} else {
				$html .= '<li class="fusion-list-view-li">';
			}
			$html .= '<a class="fusion-list-view" aria-haspopup="true" href="' . fusion_add_url_parameter( $query_string, 'product_view', 'list' ) . '"><i class="fusion-icon-list icomoon-list"></i></a>';
			$html .= '</li>';
			$html .= '</ul>';
		}

		$html .= '</div>';

		echo $html;
	}

function liwtsListForum($args = null)
{
	$args = array(
		'post_type' => 'forum',
	);
	$query1 = new WP_Query( $args );
	$html .= '<ul id="forums">';
	while ( $query1->have_posts() ) {
		$query1->the_post();
		$html .= '<li>'; 
		$html .= '<h2><a href="'.get_the_permalink().'">'.get_the_title().'</a></h2>'; 
		//$html .= get_the_forum_thumbnail();
		$content = get_the_content();
		$trimmed_content = wp_trim_words( $content, 200, '<br/><a href="'. get_the_permalink() .'">Read More</a>' );
		$html .= '<p class="forumcontent">'.$trimmed_content.'</p>';
		$html .= '</li>';
	}
	$html .= '</ul>';
	return $html;
}
add_shortcode('liwtslistforum','liwtsListForum');


function avada_nav_woo_cart( $position = 'main' ) {
	global $woocommerce;

	if( $position == 'main' ) {
		$is_enabled = fusion_get_theme_option( 'woocommerce_cart_link_main_nav' );
		$main_cart_class = 'fusion-main-menu-cart';
		$cart_link_active_class = 'fusion-main-menu-icon fusion-main-menu-icon-active';
		$cart_link_active_text = '';

		if( Avada()->settings->get( 'woocommerce_cart_counter') ) {
			$cart_link_active_text = '<span class="fusion-widget-cart-number">' . $woocommerce->cart->get_cart_contents_count() . '</span>';
			$main_cart_class .= ' fusion-widget-cart-counter';
		}

		if( ! Avada()->settings->get( 'woocommerce_cart_counter') && $woocommerce->cart->get_cart_contents_count() ) {
			$main_cart_class .= ' fusion-active-cart-icons';
		}

		$cart_link_inactive_class = 'fusion-main-menu-icon';
		$cart_link_inactive_text = '';
	} else if( $position ='secondary' ) {
		$is_enabled = fusion_get_theme_option( 'woocommerce_cart_link_top_nav' );
		$main_cart_class = 'fusion-secondary-menu-cart';
		$cart_link_active_class = 'fusion-secondary-menu-icon';
		$cart_link_active_text = sprintf('<span class="fusion-woo-cart-separator">%s</span>', $woocommerce->cart->get_cart_contents_count(), __('', 'Avada' ),wc_price( $woocommerce->cart->subtotal ) );
		$cart_link_inactive_class = $cart_link_active_class;
		$cart_link_inactive_text = __( '', 'Avada' );
	}

	if( class_exists( 'WooCommerce' ) && $is_enabled ) {
		$woo_cart_page_link = get_permalink( get_option( 'woocommerce_cart_page_id' ) );

		$items = sprintf( '<li class="fusion-custom-menu-item fusion-menu-cart %s">', $main_cart_class );
		if( $woocommerce->cart->get_cart_contents_count() ) {
			$checkout_link = get_permalink( get_option('woocommerce_checkout_page_id') );

			$items .= sprintf( '<a class="%s" href="%s">%s</a>', $cart_link_active_class, $woo_cart_page_link, $cart_link_active_text );

			$items .= '<div class="fusion-custom-menu-item-contents fusion-menu-cart-items">';
			foreach( $woocommerce->cart->cart_contents as $cart_item ) {
				$product_link = get_permalink( $cart_item['product_id'] );
				$thumbnail_id = ( $cart_item['variation_id'] && has_post_thumbnail( $cart_item['variation_id'] )  ) ? $cart_item['variation_id'] : $cart_item['product_id'];
				$items .= '<div class="fusion-menu-cart-item">';
				$items .= sprintf( '<a href="%s">', $product_link );
				$items .= get_the_post_thumbnail( $thumbnail_id, 'recent-works-thumbnail' );
				$items .= '<div class="fusion-menu-cart-item-details">';
				$items .= sprintf( '<span class="fusion-menu-cart-item-title">%s</span>', $cart_item['data']->post->post_title );
				$items .= sprintf( '<span class="fusion-menu-cart-item-quantity">%s x %s</span>', $cart_item['quantity'], $woocommerce->cart->get_product_subtotal( $cart_item['data'], 1 ) );
				$items .= '</div>';
				$items .= '</a>';
				$items .= '</div>';
			}
			$items .= '<div class="fusion-menu-cart-checkout">';
			$items .= sprintf( '<div class="fusion-menu-cart-link"><a href="%s">%s</a></div>', $woo_cart_page_link, __('View Cart', 'Avada') );
			$items .= sprintf( '<div class="fusion-menu-cart-checkout-link"><a href="%s">%s</a></div>', $checkout_link, __('Checkout', 'Avada') );
			$items .= '</div>';
			$items .= '</div>';
		} else {
			$items .= sprintf( '<a class="%s" href="%s">%s</a>', $cart_link_inactive_class, $woo_cart_page_link, $cart_link_inactive_text );
		}
		$items .= '</li>';

		return $items;
	}
}

remove_action('pre_comment_on_post', 'dsq_pre_comment_on_post');
add_action( 'the_post' , 'block_disqus');
function block_disqus() {
	if ( get_post_type() == 'product' ){
		remove_filter('comments_template', 'dsq_comments_template');
	}
}
class LIWTS_Trending_Posts extends WP_Widget
{
	function __construct()
	{
		parent::__construct('LIWTS_Trending_Posts',__('LIWTS Trending Posts', 'liwts_trending_posts'),array('description' => __('LIWTS Trending Posts', 'liwts_trending_posts')));
	}
	public function widget($args, $instance)
	{
		echo $args['before_widget'];

		$html = '<h3 class="trend-post-title widget-box-title">' . $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'] . '</h3>';

		$html .= '<ul class="trending-posts-widget">';
		$order_string  = '&orderby=rand()';
		$posts         = 2;
		$popular_posts = new WP_Query('showposts=' . $posts . $order_string . '&order=DESC&ignore_sticky_posts=1&category__not_in=60');
		if ($popular_posts->have_posts()):
		while ($popular_posts->have_posts()):
		$post = $popular_posts->the_post();
		$postCats = get_the_category($post);
		$html .= '<li>';
		$size = array( 330, 250, 'bfi_thumb' => true, 'crop' => true);
		if (has_post_thumbnail() && ($image = wp_get_attachment_image_src(get_post_thumbnail_id($post), $size)) && isset($image[0])):
		$html .= '<div class="image">';
		$html .= '<a href="' . get_the_permalink() . '"><img src="' . $image[0] . '" alt=""/></a>';
		$html .= '</div>';
		endif;
		$html .= '<div class="post-holder">';
		$html .= '<div class="post-cats">';
		foreach ($postCats as $postCat) {
			$html .= '<a href="' . get_category_link($postCat->term_id) . '">' . $postCat->name . '</a>';
		}
		$html .= '</div>';
		$html .= '<div class="post-title"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></div>';
		$html .= '</div>';
		$html .= '</li>';
		endwhile;
		wp_reset_postdata();
		else:
		$html .= '<li>' . _e('No posts have been published yet.', 'Avada') . '</li>';
		endif;
		$html .= '</ul>';
		/*$html .= '<div class="more-post-btn more-trending-post-widget"><a href="#" class="more-trending-post-btn">MORE</a></div>';*/
		echo $html;
		echo $args['after_widget'];
	}
	public function form($instance)
	{
		// Defaults
		$instance = wp_parse_args(( array ) $instance, array(
				'title' => ''
		));
		$title    = sanitize_text_field($instance['title']);
		?>
<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:');?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title) ?>" />
</p>

<?php
    }
}

class LIWTS_Latest_Posts extends WP_Widget
{
	function __construct()
	{
		parent::__construct('LIWTS_Latest_Posts',__('LIWTS Latest Posts', 'liwts_latest_posts'),array('description' => __('LIWTS Latest Posts', 'liwts_latest_posts')));
	}
	public function widget($args, $instance)
	{
		echo $args['before_widget'];

		$html = '<h3 class="latest-post-title widget-box-title">' . $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'] . '</h3>';

		$html .= '<ul class="latest-posts-widget">';
		$order_string  = '&orderby=date';
		$posts         = 4;
		$popular_posts = new WP_Query('showposts=' . $posts . $order_string . '&order=DESC&ignore_sticky_posts=1&category__not_in=60');
		if ($popular_posts->have_posts()):
		while ($popular_posts->have_posts()):
		$post = $popular_posts->the_post();
		$postCats = get_the_category($post);
		$html .= '<li>';
		$size = array( 250, 250, 'bfi_thumb' => true, 'crop' => true);
		if (has_post_thumbnail() && ($image = wp_get_attachment_image_src(get_post_thumbnail_id($post), $size)) && isset($image[0])):
		$html .= '<div class="image">';
		$html .= '<a href="' . get_the_permalink() . '"><img src="' . $image[0] . '" alt=""/></a>';
		$html .= '</div>';
		endif;
		$html .= '<div class="post-holder">';
		$html .= '<div class="post-title"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></div>';
		$html .= '<div class="fusion-meta">';
		$html .= get_the_time('j F \a\t g:i');
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</li>';
		endwhile;
		wp_reset_postdata();
		else:
		$html .= '<li>' . _e('No posts have been published yet.', 'Avada') . '</li>';
		endif;
		$html .= '</ul>';
		//$html .= '<div class="more-post-btn more-latest-post-widget"><a href="#" class="more-latest-post-btn">MORE</a></div>';
		echo $html;
		echo $args['after_widget'];
	}
	public function form($instance)
	{
		// Defaults
		$instance = wp_parse_args(( array ) $instance, array(
				'title' => ''
		));
		$title    = sanitize_text_field($instance['title']);
		?>
<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:');?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title) ?>" />
</p>

<?php
    }
}

function identifyPostSubContentPage()
{
	global $post;
	$result = array();
	if(is_singular('post') && $post->post_type == "post"){
		$previousParam = $nextParam = 0;
		$vParam = (get_query_var( 'v' ) != null)? (int) get_query_var( 'v' ) : 0;
		$currentSubIndex = 0;

		if(($subContents = get_field('post_sub_contents',$post->ID)) && ($totalSubCount = count($subContents)) > 0){
				
			$nextParam = $vParam + 1;
			$previousParam = $vParam - 1;
			$currentSubIndex = $vParam - 1;

			if($vParam >= $totalSubCount){
				$nextParam = 0;
			}
			if($vParam == 0){
				$previousParam = 0;
			}
				
			$show_numbers = get_field('show_numbers',$post->ID);
			$count_down_up = get_field('count_down_up',$post->ID);
			
			$result = array("vparam" => $vParam,"sub_index" => $currentSubIndex,"nextparam" => $nextParam,"prevparam" => $previousParam,'sub_contents'=>$subContents,'post_url'=>get_permalink($post),'show_numbers'=>$show_numbers,'count_down_up'=>$count_down_up);
		}
	}
	return $result;
}

function blogPostContent($content)
{
	$subResult = identifyPostSubContentPage();

	$content .= '';

	if(!empty($subResult) && count($subResult)){

		$content .= '<div class="post-next-show">';

		if($subResult['prevparam'] >= 0 && $subResult['vparam'] != 0){
			$prevParamsLink = "v/".$subResult['prevparam'];
			if($subResult['prevparam'] == 0){
				$prevParamsLink = null;
			}
			$content .= '<a href="'.get_permalink($post->ID).$prevParamsLink.'" class="prevlink">< Previous</a>';
		}
		if($subResult['nextparam'] > 0){
			$nextText = (get_query_var( 'v' ) == null)? "View List Now" : "Next";
			$content .= '<a href="'.get_permalink($post->ID).'v/'.$subResult['nextparam'].'" class="nextlink">'.$nextText.' ></a>';
		}
		$content .= '</div>';
	}
	return $content;
}

function custom_content_after_post($content = null){
	if (is_single()) {
		global $post;
		if(is_singular('post') && $post->post_type == "post"){
			$content = blogPostContent($content);
		}
	}
	return $content;
}
#add_filter( "the_content", "custom_content_after_post", 8);

add_action( 'init', 'casino_rewrite_add_rewrites' );
function casino_rewrite_add_rewrites()
{
	add_rewrite_endpoint( 'v', EP_PERMALINK );
}

add_action("woocommerce_before_single_product_summary","productpage_breadcrumbs");
function productpage_breadcrumbs()
{
	$breadCrumbs = new Fusion_Breadcrumbs();
	$breadCrumbs->get_breadcrumbs();
}
?>