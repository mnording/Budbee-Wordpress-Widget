<?php
/**
 * PHP version 7.5
 *
 * @category Plugins
 * @package  BudbeeWooWidget
 * @author   Mnording10
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 */

/**
 * Master of Requests
 *
 * Handles all requests towards the Budbee API
 */
class MasterOfRequests {
	/**
	 * Budbee API Key
	 *
	 * @var String  The Api key that has been provided by Budbee.
	 */
	private $key;
	/**
	 * Budbee API Secret
	 *
	 * @var String Api Secret that has been provided by Budbee.
	 */
	private $secret;
	/**
	 * Undocumented function
	 *
	 * @param String $key The Api key that has been provided by Budbee.
	 * @param String $secret The Api secret that has been provided by Budbee.
	 */
	public function __construct( $key, $secret ) {
		$this->key    = $key;
		$this->secret = $secret;
	}
	/**
	 * Helper function for getting the correct auth
	 *
	 * @return String The base64 encoded string
	 */
	private function get_auth() {
		return base64_encode( $this->key . ':' . $this->secret );
	}
	/**
	 * Make a GET request towards the Budbee API
	 *
	 * @param String $url the url to request.
	 * @return Array the response from the request
	 */
	public function make_get_request( $url ) {
		$response = wp_remote_get(
			$url,
			array(
				'headers' => array(
					'Authorization' => 'Basic ' . $this->get_auth(),
				),
			)
		);
		$res      = $response;
		return $res;
	}
}
