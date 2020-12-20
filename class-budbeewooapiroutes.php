<?php
/**
 * PHP version 7.2
 *
 * @category Plugins
 * @package  BudbeeWooWidget
 * @author   Name <email@email.com>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link     http://url.com
 */

/**
 * Undocumented class
 */
class BudbeeWooApiRoutes {

	/**
	 * Connector
	 *
	 * @var [type]
	 */
	private $master_of_requests;
	/**
	 * Max distance in meters that should be applied to boxes
	 *
	 * @var Integer
	 */
	private $max_distance;
	/**
	 * Constructor of the class
	 *
	 * @param String $key The API Key provided by Budbee.
	 * @param String $secret The API Secret provided by Budbee.
	 * @param String $maxdistance The max distance allowed for boxes.
	 */
	public function __construct( $key, $secret, $maxdistance ) {
		$this->master_of_requests = new MasterOfRequests( $key, $secret );
		$this->max_distance       = $maxdistance;

	}
	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function register_check_postal_code_route() {
		register_rest_route(
			'budbee/v1',
			'/postalcode/(?P<postal>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'validate_postal_code_box' ),
				'permission_callback' => '__return_true',
			),
		);
	}
	/**
	 * Creating the route for checking home delivery capabilities
	 *
	 * @return void
	 */
	public function register_check_home_delivery_route() {
		register_rest_route(
			'budbee/v1',
			'/homedelivery/(?P<postal>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'validate_postal_code_home' ),
				'permission_callback' => '__return_true',
			),
		);
	}
	/**
	 * Undocumented function
	 *
	 * @param Array] $data the request data.
	 * @return WP_REST_Response
	 */
	public function validate_postal_code_box( $data ) {
		$this->verify_payload();

		if ( $data['postal'] ) {

			$postal = wp_unslash( $data['postal'] );

			$res = $this->master_of_requests->make_get_request( 'https://api.budbee.com/boxes/postalcodes/validate/SE/' . $postal );

			if ( ! $this->verify_response( $res ) ) {
				return new WP_Error( 'no_box_delivery', 'No Boxes', array( 'status' => 404 ) );
			}
			$res = json_decode( $res['body'] );

			if ( count( $res->lockers ) === 0 || $res->lockers[0]->distance > $this->max_distance ) {
				return new WP_Error( 'no_box_delivery', 'No Boxes', array( 'status' => 404 ) );
			}
			$res      = array_slice( $res->lockers, 0, 5 );
			$response = new WP_REST_Response( $res );
			$response->set_headers( array( 'Cache-Control' => 'max-age=36000' ) );

			return $response;
		}

	}
	/**
	 * Check if a given postalcode supports home deliveries
	 *
	 * @param Array $data The request data.
	 * @return WP_REST_Response
	 */
	public function validate_postal_code_home( $data ) {
		$this->verify_payload();

		if ( $data['postal'] ) {

			$postal = wp_unslash( $data['postal'] );

			$res = $this->master_of_requests->make_get_request( 'https://api.budbee.com/postalcodes/validate/SE/' . $postal );
			if ( ! $this->verify_response( $res ) ) {
				return new WP_Error( 'no_home_delivery', 'HomeDelivery Not Supported', array( 'status' => 404 ) );
			}
			$response = new WP_REST_Response( array( 'message' => 'Delivery Supported' ) );
			$response->set_headers( array( 'Cache-Control' => 'max-age=36000' ) );
			return $response;
		}

	}
	/**
	 * Verify that the response was 200 as expected
	 *
	 * @param Array $res the response from the wp_remote_get.
	 * @return Boolean True if the response was 200
	 */
	private function verify_response( $res ) {
		if ( 200 !== $res['response']['code'] ) {
			return false;
		}
		return true;
	}

	/**
	 * Verify the payload from the FE
	 *
	 * @return void
	 */
	private function verify_payload() {
		$nonce = get_query_var( '_wpnonce' );
		if ( wp_verify_nonce( $nonce, 'budbee-widget-nonce' ) ) {
			die( esc_textarea( __( 'Security check', 'budbee-widget-plugin' ) ) );
		}
	}
}
