<?php

// DEALS render
function render_products_gallery(){

  ob_start();

  $products = new WP_Query( array(
  	'post_type'     => 'product',
  	'orderby'       => 'date',
    'post_status'     => 'publish',
    // 'posts_per_page'=>  9,
  ));


  $orderby = 'name';
  $order = 'asc';
  $hide_empty = false ;
  $cat_args = array(
      'orderby'    => $orderby,
      'order'      => $order,
      'hide_empty' => $hide_empty,
  );

  $product_categories = get_terms( 'product_cat', $cat_args );
  $template_directory_uri_child = get_stylesheet_directory_uri();
  ?>

  <div class="tcf-multiple">
      <div id="tcf-product-category" class="tcf-select tcf-multiple filterby">
        <a class="btn-light-filter active-filter" data-category="all">Tous les articles</a>
        <?php
          foreach ($product_categories as $key => $category) {
              if (strpos( $category->slug, trim('non-classe') ) === false){
          ?>
          <a class="btn-light-filter" data-category="<?php echo $category->slug ?>">
            <img src="<?php echo $template_directory_uri_child ?>/assets/images/icons/<?php echo $category->slug ?>.svg">
            <text><?php echo $category->name ?></text></a>
        <?php }} ?>
    </div>
  </div>

  <?
  wp_reset_postdata();

  $html = ob_get_contents();
  ob_get_clean();

  return $html;
}

add_shortcode( 'products_gallery_grid', 'render_products_gallery' );



// DEALS render
function render_woo_info(){

  ob_start();
  ?>
  <a class="cart-customlocation" href="<?php echo wc_get_cart_url(); ?>"
    title="<?php _e( 'View your shopping cart' ); ?>">
    <?php echo sprintf ( _n( 'Panier (%d)', 'Panier (%d)', WC()->cart->get_cart_contents_count() ),
    WC()->cart->get_cart_contents_count() ); ?>
     <!-- - <?php echo WC()->cart->get_cart_total(); ?> -->
   </a>

  <?
  wp_reset_postdata();

  $html = ob_get_contents();
  ob_get_clean();

  return $html;
}
add_shortcode( 'menu_woo_info', 'render_woo_info' );


/**
 * Show cart contents / total Ajax
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment' );

function woocommerce_header_add_to_cart_fragment( $fragments ) {
	global $woocommerce;

	ob_start();
	?>
	<a class="cart-customlocation"
  href="<?php echo esc_url(wc_get_cart_url()); ?>"
  title="<?php _e('View your shopping cart', 'woothemes'); ?>">
  <?php echo sprintf(_n( 'Panier (%d)', 'Panier (%d)', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);?>
 </a>
	<?php
	$fragments['a.cart-customlocation'] = ob_get_clean();
	return $fragments;
}



// DEALS render
function render_woo_login(){

  ob_start();
  
	$loginbtn = '';
  if (is_user_logged_in()) { //change your theme registered menu name to suit - currently for DIVI theme
      $loginbtn .= '<a class="btn-light-menu" href="'. get_permalink( wc_get_page_id( 'myaccount' ) )  .'">Mon compte</a>';
      //the style is changing the theme's color once you are logged in
  }
  elseif (!is_user_logged_in() ) {//change your theme registered menu name to suit
      $loginbtn .= '<a class="btn-light-menu" href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '">Se connecter</a>';
  }
  echo $loginbtn;

  ?>
  <?
  wp_reset_postdata();

  $html = ob_get_contents();
  ob_get_clean();

  return $html;
}
add_shortcode( 'menu_woo_login', 'render_woo_login' );
