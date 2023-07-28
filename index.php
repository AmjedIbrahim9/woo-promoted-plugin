<?php
/**
 * Plugin Name: Woocommerce Featured Product
 * Version: 1.0.0
 * Description: Display a Featured Product on All Pages
 * Author: Amjed Ibrahim
 * Tested up to: 6.2
 *
 * @package WOOFP
 */

namespace WOOFP;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'WOOFP_PLUGIN_FILE' ) ) {
	define( 'WOOFP_PLUGIN_FILE', __FILE__ );
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Initialize the Woocommerce Featured Product plugin.
 */
function WOOFP_init() {
	$main_instance = new Main();
	$main_instance->init_hooks();
}

add_action( 'plugins_loaded', 'WOOFP\WOOFP_init' );
