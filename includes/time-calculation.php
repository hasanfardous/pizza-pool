<?php
// Time table function
function is_shop_open() {
    // Set the timezone
    date_default_timezone_set('Asia/Dhaka');

    $thursday_start_time    = mktime('16', '00', '00', date('m'), date('d'), date('Y')); // 16h
    $start_time             = mktime('12', '00', '00', date('m'), date('d'), date('Y')); // 12h
    $end_time               = mktime('22', '00', '00', date('m'), date('d'), date('Y')); // 22h
    $now_time               = time();
    $allowed_days           = in_array( date('N'), array(4, 5, 6) ); // Thursday, Friday and Saturday are open

    return $allowed_days && (date('N') == 4 && $now_time >= $thursday_start_time || ((date('N') == 5 || date('N') == 6) && $now_time >= $start_time)) && $now_time <= $end_time ? true : false;
}

// Make the store purchasable on our specified time only
add_filter( 'woocommerce_variation_is_purchasable', 'pp_shop_closed_disable_purchases' );
add_filter( 'woocommerce_is_purchasable', 'pp_shop_closed_disable_purchases' );
function pp_shop_closed_disable_purchases( $purchasable ) {
    return is_shop_open() ? $purchasable : false;
}

// Throw the message
add_action( 'woocommerce_check_cart_items', 'pp_shop_open_allow_checkout' );
add_action( 'woocommerce_checkout_process', 'pp_shop_open_allow_checkout' );
function pp_shop_open_allow_checkout() {
    if ( ! is_shop_open() ) {
        wc_add_notice( __("The PizzaPOOL online store is currently closed. You can view products, but purchases are not allowed."), 'pizza-pool' );
    }
}

// Throw the notice
add_action( 'template_redirect', 'pp_shop_is_closed_notice' );
function pp_shop_is_closed_notice(){
    if ( ! ( is_cart() || is_checkout() ) && ! is_shop_open() ) {
        wc_add_notice( sprintf( '<span class="shop-closed">%s</span>',
            esc_html__('The PizzaPOOL online store is currently closed. You can view products. But purchases are allowed between 16.00-22.00 on Thursday also 12.00-22.00 on Friday and Saturday.', 'pizza-pool' )
        ), 'notice' );
    }
}