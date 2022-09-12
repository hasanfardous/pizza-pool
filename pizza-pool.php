<?php

/**
 * Plugin Name:       Pizza Pool
 * Plugin URI:        https://me.hasanfardous.com
 * Description:       The Pizza Pool plugin made on a specefic requirement with Woocommerce store. Basically, It'll add a 10% service charge field on the cart page when the product 'Order Type' is 'Dine-in' also user will get a 40% discount for the first order only. The plugin makes the store purchasable between 16.00-22.00 on Thursday also 12.00-22.00 on Friday and Saturday during the 'Asia/Dhaka' timezone.
 * Version:           1.0.0
 * Requires at least: 5.5
 * Requires PHP:      7.2
 * Author:            Hasanfardous
 * Author URI:        https://me.hasanfardous.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pizza-pool
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Current plugin version
define( 'PIZZA_POOL', '1.0.0' );

function pp_load_textdomain() {
	load_plugin_textdomain( 'pizza-pool', false, dirname( __FILE__ ) . "/languages" );
}

add_action( "plugins_loaded", "pp_load_textdomain" );

// Enqueue Front-end scripts
add_action( 'wp_enqueue_scripts', 'pp_enqueue_scripts', 99 );
function pp_enqueue_scripts() {
	// Styles
	wp_enqueue_style( 'pp-styles', plugins_url( 'assets/css/styles.css', __FILE__ ), '', time() );
}

// Check if the Woocommerce is activated
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
// Including plugin files
require plugin_dir_path( __FILE__ ) . 'includes/cart-calculations.php';
require plugin_dir_path( __FILE__ ) . 'includes/time-calculation.php';
}

/**
 * The code that runs during plugin activation.
 */
register_activation_hook( __FILE__, 'pp_create_db_table' );
if ( ! function_exists( 'pp_create_db_table' ) ) {
	function pp_create_db_table() {
		// Saving our plugin current version
		add_option( "pizza_pool_version", PIZZA_POOL );
	}
}
?>