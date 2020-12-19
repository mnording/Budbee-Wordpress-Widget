<?php
/**
 *
 * Main File of plugin
 *
 * @category Plugins
 * @package  BudbeeWooWidget
 * @author   Mnording10
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 */

/**
 * Plugin Name:       Budbee Widget
 * Description:       Render a widget anywhere on your site to display budbee alternatives
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * Author:            Mnording10
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       budbee-widget-plugin
 * Domain Path:       /languages
 */

/**
 * Requiring necessary files
 */
require 'class-budbeewooapiroutes.php';
require 'class-masterofrequests.php';
require 'class-budbeewidgetsettings.php';
/**
 * Undocumented class
 */
class BudbeeWooWidget {

	/**
	 * Undocumented variable
	 *
	 * @var BudbeeWooApiRoutes
	 */
	private $api_routes;

	/**
	 * Budbee Settings
	 *
	 * @var BudbeeWidgetSettings
	 */
	private $budbee_settings;
	/**
	 * Undocumented function
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_api_routes' ) );
		add_filter( 'query_vars', array( $this, 'add_budbee_query' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'budbee_woo_enqueue' ) );
		add_action( 'plugins_loaded', array( $this, 'init_all' ) );
	}
	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function init_all() {
		$this->budbee_settings = new BudbeeWidgetSettings();
		$this->api_routes      = new BudbeeWooApiRoutes( $this->budbee_settings->get_api_key(), $this->budbee_settings->get_api_secret(), $this->budbee_settings->get_max_distance_from_box() );
		add_filter( $this->budbee_settings->get_placement_hook(), array( $this, 'generate_widget' ) );
		load_plugin_textdomain( 'budbee-widget-plugin', false, basename( dirname( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function budbee_woo_enqueue() {
		wp_enqueue_style( 'dashicons' );
		wp_register_style( 'budbee-widget-plugin', plugins_url( 'css/budbee-widget.css', __FILE__ ), null, '1.0.0' );
		wp_enqueue_style( 'budbee-widget-plugin' );
		wp_enqueue_script(
			'ajax-script',
			plugins_url( '/js/budbee-woo.js', __FILE__ ),
			array( 'jquery' ),
			'1.0.0',
			true
		);
	}
	/**
	 * Generate the actual widget in the frontend
	 *
	 * @param String $content Existing content to be rendered in the hook.
	 * @return void
	 */
	public function generate_widget( $content ) {
		$nonce    = wp_create_nonce( 'wp_rest' );
		$content .= '<div id="budbee-widget-container">';
		$content .= '<h1>' . $this->budbee_settings->get_widget_title() . '</h1>';
		$content .= '<span style="float:left;">' . __( 'At our store you can get deliveries to your door, to a pickup-point or to a pickup-box. Enter your postalcode below to get personalized options', 'budbee-widget-plugin' ) . '</span>';
		$content .= '<input type="text" onkeypress="budbee_check_key(event)" id="budbee-postal-value" name="budbee-postal" placeholder="' . __( 'Enter your postalcode', 'budbee-widget-plugin' ) . '">';
		$content .= '<input type="hidden" value="' . $nonce . '"  id="budbee-postal-nonce">';
		$content .= '<button class="button alt" onclick="budbee_check_alternatives()">' . __( 'See alternatives', 'budbee-widget-plugin' ) . '</button>';
		$content .= '<ul>';
		$content .= '<li id="budbee-home-response" style="display:none" class="dashicons-before dashicons-yes">' . __( 'Homedelivery', 'budbee-widget-plugin' ) . '<img src="' . plugins_url( 'img/home-full-logo-medium.png', __FILE__ ) . '"/></li>';
		$content .= '<li id="budbee-box-response" style="display:none" class="dashicons-before dashicons-yes">' . __( 'PickupBox', 'budbee-widget-plugin' ) . '<img src="' . plugins_url( 'img/box-full-logo-medium.png', __FILE__ ) . '"/></li>';
		$content .= '<li id="budbee-fallback-text" style="display:none" class="dashicons-before dashicons-yes">' . $this->budbee_settings->get_fallback_text() . '</li>';
		$content .= '</ul>';
		$content .= '</div>';
		echo $content;
	}
	/**
	 * Add budbee query to searchable query params
	 *
	 * @param Array $qvars Existing query variables.
	 * @return Array new query variables
	 */
	public function add_budbee_query( $qvars ) {
		$qvars[] = 'budbeepostal';
		return $qvars;
	}
	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function register_api_routes() {
		$this->api_routes->register_check_postal_code_route();
		$this->api_routes->register_check_home_delivery_route();
	}

}
new BudbeeWooWidget();
