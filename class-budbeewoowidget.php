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
	public function init_all() {
		$this->budbee_settings = new BudbeeWidgetSettings();
		$this->api_routes      = new BudbeeWooApiRoutes( $this->budbee_settings->get_api_key(), $this->budbee_settings->get_api_secret() );
		add_filter( $this->budbee_settings->get_placement_hook(), array( $this, 'generate_widget' ) );

	}
	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function budbee_woo_enqueue() {
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
	 * @return String the new content.
	 */
	public function generate_widget( $content ) {
		$nonce    = wp_create_nonce( 'wp_rest' );
		$content .= '<div id="budbee-widget-container">';
		$content .= '<span>' . __( 'At our store you can get deliveries to your door, to a pickup-point or to a pickup-box. Enter your postalcode below to get personalized options' ) . '</span>';
		$content .= '<input type="text" onkeypress="budbee_check_key(event)" id="budbee-postal-value" name="budbee-postal" placeholder="' . __( 'Enter your postalcode', 'budbee-widget-plugin' ) . '">';
		$content .= '<input type="hidden" value="' . $nonce . '"  id="budbee-postal-nonce">';
		$content .= '<button onclick="budbee_check_alternatives()">' . __( 'See alternatives', 'budbee-widget-plugin' ) . '</button>';
		$content .= '<div id="budbee-home-response" style = "display:none">';
		$content .= '<span>' . __( 'You can get your package delivered to your door.', 'budbee-widget-plugin' ) . '</span>';
		$content .= '</div>';
		$content .= '<div id="budbee-box-response"  style="display:none">';
		$content .= '<span>' . __( 'You can get your package delivered to a Budbee box near you! Your closest alternatives are:', 'budbee-widget-plugin' ) . '</span>';
		$content .= '<ul id="budbee-box-response-list"></ul>';
		$content .= '</div>';
		$content .= '</div>';
		echo ( $content );
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
