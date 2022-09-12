<?php
// Check the user orders
function ppp_is_user_first_order() {	
	global $wpdb;
	$current_user_id = get_current_user_id();
	$result = $wpdb->get_results( "SELECT ID FROM $wpdb->posts 
		WHERE post_type = 'shop_order'
		AND post_status = 'wc-completed'
		AND post_author = $current_user_id
	");

	if ( count( $result ) > 0 ) {
		return true;
	} else {
		return false;
	}
}


// WC Cart Calculation Functions
add_action('woocommerce_cart_calculate_fees', function( $cart_obj ){
	global $woocommerce;

	// Get cart subtotal
	$cart_total = $woocommerce->cart->get_subtotal();

	// Looping the cart items
	foreach ( $cart_obj->get_cart_contents() as $cart_item ) {
		// Get the product variation title
		$variation_title = isset($cart_item['variation']['attribute_pa_order-type']) ? $cart_item['variation']['attribute_pa_order-type'] : '';

		// Do the operation conditionally
		if ( 'dine-in' == $variation_title ) {
			$percentage_10 = 0.10;		// 10% service charge
			$service_charge = $cart_total * $percentage_10;	// Calculating the service charge
			$woocommerce->cart->add_fee( esc_html('Service Charge 10% (Dine-in)', 'pizza-pool'), $service_charge );

			if ( ! ppp_is_user_first_order() ) {
				$percentage_40 = 0.40;		// 40% service charge
				$discount = ($cart_total + $service_charge) * $percentage_40;	// Calculating the discount
				$woocommerce->cart->add_fee( esc_html('Discount 40% (First order)', 'pizza-pool'), '-'.$discount );
			}
		} else {
			if ( ! ppp_is_user_first_order() ) {
				$percentage_40 = 0.40;		// 40% service charge
				$discount = $cart_total * $percentage_40;	// Calculating the discount
				$woocommerce->cart->add_fee( esc_html('Discount 40% (First order)', 'pizza-pool'), '-'.$discount );
			}
		}
	}
});