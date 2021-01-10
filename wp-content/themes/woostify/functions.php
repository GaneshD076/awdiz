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
//new order RestAPI Start
add_action( 'woocommerce_new_order', 'create_invoice_for_wc_order',  1, 1  );
function create_invoice_for_wc_order( $order_id ) {
   include_once('api.php');// gettind curl function page
$order_data=array();
$billingAddress=array();
$shippingAddress=array();
$items=array();
$query = new WC_Order_Query( array(
      'p'         => $order_id, 
		'post_type' => 'shop_order'
) );
$orders = $query->get_orders();
 foreach ($orders as $or) {
$order_data['id']=$or->id;
$order_data['customerOrderNumber']=$or->id;
$order = wc_get_order( $order_data['id']);
    foreach( $order->get_items( 'shipping' ) as $item_id => $item ){
    $order_data['shipMethod']= $item->get_method_title();
    }
$data=$or->data;
$billing=$data['billing'];
$shipping=$data['shipping'];

$payment_method=$data['payment_method'];
if($payment_method=="cod"){$order_data['isCOD']="1";}else{$order_data['isCOD']="0";}//to know paymentType is COD or not.
$date_created=$data['date_created'];
$order_data['orderDate']=get_the_date( 'Y-m-d H:i:s', $or->id);;
$order_data['paymentType']=$payment_method;
$order_data['taxAmount']=$data['total_tax'];
$order_data['shipChargeAmount']=$data['shipping_total'];
$order_data['orderTotal']=$data['total'];
$fname=$billing['first_name'];
$lname=$billing['last_name'];
$billingAddress['customerName']=$fname.' '.$lname;
$billingAddress['addressLine1']=$billing['address_1'];
$billingAddress['addressLine2']=$billing['address_2'];
$billingAddress['city']=$billing['city'];
$billingAddress['state']=$billing['state'];
$billingAddress['postalCode']=$billing['postcode'];
$billingAddress['countryName']=$billing['country'];
$billingAddress['email']=$billing['email'];
$billingAddress['contactPhone']=$billing['phone'];
$order_data['billingAddress']=$billingAddress;
$fname=$shipping['first_name'];
$lname=$shipping['last_name'];
$shippingAddress['customerName']=$fname.' '.$lname;
$shippingAddress['addressLine1']=$shipping['address_1'];
$shippingAddress['addressLine2']=$shipping['address_2'];
$shippingAddress['city']=$shipping['city'];
$shippingAddress['state']=$shipping['state'];
$shippingAddress['postalCode']=$shipping['postcode'];
$shippingAddress['countryName']=$shipping['country'];
$order_data['shippingAddress']=$shippingAddress;
$i=1;
foreach ($order->get_items() as $item_key => $item ):// to get item details 
   $product      = $item->get_product();
   $item_data    = $item->get_data();
$items['lineItemSequenceNumber']=$i;
$items['itemID']=$product->id;
$items['productName']=$item_data['name'];
$items['quantity']=$item_data['quantity'];
$productprice = new WC_Product($product->id);
$items['customerPrice']=$product->get_price();
$items['subtotal']=$item_data['subtotal'];
$items['lineItemTotal']=$item_data['total'];
$items['taxAmount']=$item_data['total_tax'];
$image=wp_get_attachment_image_src(get_post_thumbnail_id($product->id),'single-post-thumbnail');//product image link
$items['productUrl']=get_permalink( $product->id );// product link
$items['productImageUrl']=$image[0];
$items['sku']=$product->get_sku();
$order_data['items'][]=$items;
$i++;
endforeach;
}
$endpoint="http://localhost/out/awdiz/api/";
callAPI('POST', $endpoint, json_encode($order_data), $headers );//calling curl function
}
//new order RestAPI End