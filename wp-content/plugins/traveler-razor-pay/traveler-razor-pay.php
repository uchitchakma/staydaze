<?php
/**
Plugin Name: Traveler Razor Pay
Plugin URI: https://shinetheme.com/
Description: This plugin is used for Traveler Theme
Version: 1.4
Author: ShineTheme
Author URI: https://shinetheme.com/
License: GPLv2 or later
Text Domain: traveler-razor-pay
 */
if ( ! function_exists( 'add_action' ) ) {
	echo __( 'Hi there!  I\'m just a plugin, not much I can do when called directly.', 'vina_stripe' );
	exit;
}
define( 'ST_RAZOR_VERSION', '1.0.0' );
define( 'ST_RAZOR_MINIMUM_WP_VERSION', '5.0' );
define( 'ST_RAZOR_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'ST_RAZOR_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'ST_RAZOR_DELETE_LIMIT', 100000 );
define( 'ST_RAZOR_FOLDER_PLUGIN', 'traveler-razor-pay' );

require_once __DIR__ . '/razorpay-sdk/Razorpay.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors;

class ST_RazorPay {

	public $table_name = 'st_order_item_meta';
	public function __construct() {
		add_action( 'init', [ $this, '_pluginLoader' ], 20 );
		add_action( 'plugin_loaded', [ $this, 'razor_init' ] );
	}
	public function razor_init() {
		load_plugin_textdomain( 'traveler-razor-pay', false, basename( __DIR__ ) . '/languages' );
	}
	public function loadTemplate( $name, $data = null ) {
		if ( is_array( $data ) ) {
			extract( $data );
		}
		$template = ST_RAZOR_PLUGIN_PATH . 'views/' . $name . '.php';
		if ( is_file( $template ) ) {
			$templateCustom = locate_template( ST_RAZOR_FOLDER_PLUGIN . '/views/' . $name . '.php' );
			if ( is_file( $templateCustom ) ) {
				$template = $templateCustom;
			}
			ob_start();
			require $template;
			$html = ob_get_clean();
			return $html;
		}
	}
	public function _pluginLoader() {
		require_once ST_RAZOR_PLUGIN_PATH . 'inc/razor.php';
		require_once ST_RAZOR_PLUGIN_PATH . 'inc/process.php';
		require_once ST_RAZOR_PLUGIN_PATH . 'inc/process-package.php';
	}
	public static function get_inst() {
		static $instance;
		if ( is_null( $instance ) ) {
			$instance = new self();
		}
		return $instance;
	}
}
ST_RazorPay::get_inst();
