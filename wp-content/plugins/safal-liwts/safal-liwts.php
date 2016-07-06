<?php
/**
* Plugin Name: Safal - LIWTS Customization
* Plugin URI: http://www.safalweb.com
* Description: Plugin created for LIWTS by SafalWeb.
* Version: 1.0.0
* Author: Ankit Patel
* Author URI: http://www.safalweb.com
* License: GPL2
*/
if(!is_admin()){
	add_action( 'wp_print_scripts', 'enqueue_and_register_liwts_safal_scripts' );
	function enqueue_and_register_liwts_safal_scripts()
	{
		wp_enqueue_script( 'liwts-safal-script', plugin_dir_url( __FILE__ ) . '/js/scripts.js' );
		wp_localize_script( 'liwts-script', 'liwtsAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
	}
}
function homepagePopularPosts()
{
    $html          = '';
    $order_string  = '&orderby=comment_count';
    $posts         = 11;
    $popular_posts = new WP_Query('showposts=' . $posts . $order_string . '&order=DESC&ignore_sticky_posts=1&category__not_in=60');
    $html .= '<div class="popular-post-shortcode">';
    $html .= '<h3 class="pop-post-title shortcode-box-title">' . __("Popular Posts") . '</h3>';
    $html .= '<ul class="news-list">';
    $i = 0;
    if ($popular_posts->have_posts()):
        while ($popular_posts->have_posts()): 
            $post = $popular_posts->the_post();
            $postCats = get_the_category($post);
            $liClass = null; 
            if(++$i >= 5 && $i <= 7){
            	$liClass = "small-post-li";
            }
            $html .= '<li class="'.$liClass.'">';
            
            $html .= '<div class="image">';
            $html .= '<a href="' . get_the_permalink() . '">';
            $size = array( 415, 275, 'bfi_thumb' => true, 'crop' => true);
            if (has_post_thumbnail() && ($image = wp_get_attachment_image_src(get_post_thumbnail_id($post), $size)) && isset($image[0])){
            	$html .= '<img src="' . $image[0] . '" alt=""/>';
            }
            else{
            	$html .= '';
            }
            $html .= '</a>';
            $html .= '<div class="pop-post-cats">';
            foreach ($postCats as $postCat) {
            	$html .= '<a href="' . get_category_link($postCat->term_id) . '">' . $postCat->name . '</a>';
			}
            $html .= '</div>';
            $html .= '</div>';
            
            $html .= '<div class="post-holder">';
            $html .= '<div class="fusion-meta">';
            $html .= get_the_time('j F \a\t g:i');
            $html .= '</div>';
            $html .= '<div class="post-title"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></div>';
            $html .= '<div class="post-desc">' . the_excerpt_max_charlength(get_the_excerpt(), 100) . '</div>';
            $html .= '</div>';
            $html .= '</li>';
        endwhile;
        wp_reset_postdata();
    else:
        $html .= '<li>' . _e('No posts have been published yet.', 'Avada') . '</li>';
    endif;
    $html .= '</ul>';
    $html .= '</div>';
    return $html;
}
add_shortcode('homepage_popular_posts', 'homepagePopularPosts');

function homepageReviews()
{
	$html = '<div class="strain-review-shortcode">';
	$html .= '<h3 class="strain-review-title shortcode-box-title">'.__("Strain Reviews").'</h3>';
	$html .= '<ul id="strain-review-home">';
	wp_reset_query();
	$postArgs = array('post_type'=>'post','cat'=>'60');
	$posts = get_posts($postArgs);
	foreach($posts as $post):
		setup_postdata($post);
		$html .= '<li>';
		$size = array( 250, 250, 'bfi_thumb' => true, 'crop' => true);
		if (has_post_thumbnail($post) && ($image = wp_get_attachment_image_src(get_post_thumbnail_id($post), $size)) && isset($image[0])){
			$html .= '<span class="review-img"><a href="'.get_the_permalink($post).'"><img src="'.$image[0].'" alt=""/></a></span>';
		}
		else{
			$html .= '<span class="review-img"></span>';
		}
		
		$html .= '<span class="review-ratings"><img src="'.get_stylesheet_directory_uri().'/images/review-stars.png" alt=""/></span>';
		$html .= '<span class="review-title"><a href="'.get_the_permalink($post).'">'.$post->post_title.'</a></span>';
		$html .= '<span class="review-description">' . the_excerpt_max_charlength(get_the_excerpt(), 85) . '</span>';
		$html .= '</li>';;
	endforeach;
	wp_reset_query();
	$html .= '</ul>';
	$html .= '</div>';
	return $html;
}
add_shortcode( 'homepage_reviews', 'homepageReviews' );


function getLoungeNews($args)
{
	$html = "<div class='lounge-news-container'><div class='lounge-news-section'>";
	$dateFormate = "F dS, Y";
	$html .= getLoungeNewsList("LATEST NEWS",date($dateFormate));
	$html .= "</div>";
	$html .= "<div class='lounge-news-section'>";
	$dateBefore = date($dateFormate);
	$dateAfter = date($dateFormate,strtotime(date("Y-m-d")." -1 day"));
	$dateAfterTimeStamp = strtotime(date("Y-m-d")." -2 day");
	$html .= getLoungeNewsList("YESTERDAY NEWS",$dateAfter,$dateBefore);
	$html .= "</div>";
	$html .= "<div class='loungemore-btn'>";
	$html .= "<a href='#' id='lounge-loadmore' data-previousday='".$dateAfterTimeStamp."'>".__('Load More')."</a>";
	$html .= "</div></div>";
	return $html;
}
add_shortcode( 'letestnews', 'getLoungeNews' );

function getLoungeNewsList($title="LATEST NEWS", $dateAfter, $dateBefore = null){
	$html = "";
	$postArgs = array('post_type' => 'post', 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'DESC');
	if($dateBefore == null){
		$postArgs['date_query'] = array('after'=>$dateAfter,'inclusive' => true);
	}
	else{
		$postArgs['date_query'] = array('after'=>$dateAfter,'before'=>$dateBefore,'inclusive' => true);
	}
	$posts = get_posts($postArgs);
	if(count($posts)):
		$html .= "<div class='news-section-container'>";
		$html .= "<div><h1 class='news-section-title'>".$title."</h1>";
		$html .= "<span><a href='#' class='news-section-prev'><i class='fa fa-angle-left'></i></a><a href='#' class='news-section-next'><i class='fa fa-angle-right'></i></a></span>";
		$html .= "</div>";
		$html .= "<ul>";
		$i = 0;
		$_collectionSize = count($posts);
		foreach($posts as $post):
			setup_postdata($post);
			if ($i++%5==0):
				$html .= "<li>";
			endif;
			$html .= "<span class='news-post-container'>";
			$html .= "<span class='news-post-left'><h2 class='news-post-title'>".$post->post_title."</h2>";
			$html .= "<span class='news-post-description'>".the_excerpt_max_charlength(get_the_excerpt(),260)."</span></span>";
			$html .= "<span class='news-post-right'><span class='news-post-time fusion-meta'>".get_the_time('j F \a\t g:i',$post->ID)."</span></span>";
			$html .= "</span>";
			if ($i%5==0 || $i==$_collectionSize):
				$html .= "</li>";
			endif;
		
		endforeach;
		wp_reset_query();
		$html .= "</ul>";
		$html .= "</div>";
	endif;
	return $html;
}
add_action( 'wp_ajax_loadprevlounge', 'getPreviousLoungeNewsList' );
add_action( 'wp_ajax_nopriv_loadprevlounge', 'getPreviousLoungeNewsList' );
function getPreviousLoungeNewsList(){
	$respose = array();
	$html = "";
	if(isset($_POST['previousday']) && ($prevDay = $_POST['previousday']) != null){
		$dateFormate = "F dS, Y";
		$dateAfter = date($dateFormate,$prevDay); 
		$dateBefore = date($dateFormate,strtotime(date("Y-m-d",$prevDay)." +1 day"));
		$prevDay = strtotime(date("Y-m-d",$prevDay)." -1 day");
		$day = date("l",strtotime(date("Y-m-d",$prevDay)));
		$html .= getLoungeNewsList($day." NEWS",$dateAfter,$dateBefore);
	}
	
	$respose = array("html"=>$html,"prevday"=>$prevDay);
	echo json_encode($respose);
	die();
}