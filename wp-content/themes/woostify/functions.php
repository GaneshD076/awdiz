<?php
/**
 * Woostify
 *
 * @package woostify
 */

// Define constants.
define( 'WOOSTIFY_VERSION', '1.8.0' );
define( 'WOOSTIFY_PRO_MIN_VERSION', '1.4.6' );
define( 'WOOSTIFY_THEME_DIR', get_template_directory() . '/' );
define( 'WOOSTIFY_THEME_URI', get_template_directory_uri() . '/' );

// Woostify functions, hooks.
require_once WOOSTIFY_THEME_DIR . 'inc/woostify-functions.php';
require_once WOOSTIFY_THEME_DIR . 'inc/woostify-template-hooks.php';
require_once WOOSTIFY_THEME_DIR . 'inc/woostify-template-builder.php';
require_once WOOSTIFY_THEME_DIR . 'inc/woostify-template-functions.php';

// Woostify generate css.
require_once WOOSTIFY_THEME_DIR . 'inc/customizer/class-woostify-fonts-helpers.php';
require_once WOOSTIFY_THEME_DIR . 'inc/customizer/class-woostify-get-css.php';

// Woostify customizer.
require_once WOOSTIFY_THEME_DIR . 'inc/class-woostify.php';
require_once WOOSTIFY_THEME_DIR . 'inc/customizer/class-woostify-customizer.php';

// Woostify woocommerce.
if ( woostify_is_woocommerce_activated() ) {
	require_once WOOSTIFY_THEME_DIR . 'inc/woocommerce/class-woostify-woocommerce.php';
	require_once WOOSTIFY_THEME_DIR . 'inc/woocommerce/class-woostify-adjacent-products.php';
	require_once WOOSTIFY_THEME_DIR . 'inc/woocommerce/woostify-woocommerce-template-functions.php';
	require_once WOOSTIFY_THEME_DIR . 'inc/woocommerce/woostify-woocommerce-archive-product-functions.php';
	require_once WOOSTIFY_THEME_DIR . 'inc/woocommerce/woostify-woocommerce-single-product-functions.php';
}

// Woostify admin.
if ( is_admin() ) {
	require_once WOOSTIFY_THEME_DIR . 'inc/admin/class-woostify-admin.php';
	require_once WOOSTIFY_THEME_DIR . 'inc/admin/class-woostify-meta-boxes.php';
}

// Compatibility.
require_once WOOSTIFY_THEME_DIR . 'inc/compatibility/class-woostify-divi-builder.php';

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 */
function myshortcode_posts( $att ) {
	 $categories=$att['categories'];
   	 $limit=$att['limit'];
 	 $order=$att['orderby'];
 	 if($categories==null){$cat="ALL";}else{$cat=$categories;}
   	 $return_string ='<h4>'.$cat .'</h4><br>';
	   query_posts(array('post_type' => 'post','post_status' => 'publish','orderby' => 'post_date', 'order' => $order , 'posts_per_page' => $limit,'category_name'=>$categories));
	   if (have_posts()) :
	      while (have_posts()) : the_post();
	         $return_string .= '<article class="post-16 post type-post status-publish format-standard hentry category-editorial">

				<div class="loop-post-inner">
					<header class="entry-header">
		<h2 class="entry-header-item alpha entry-title"><a href="'.get_permalink().'" rel="bookmark" >'.get_the_title().'</a></h2></header></div>
		
</article>';
	      endwhile;
	   endif;
	

	   wp_reset_query();
	   return $return_string;
}
add_shortcode( 'myshortcode', 'myshortcode_posts' );