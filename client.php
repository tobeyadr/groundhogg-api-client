<?php

namespace GroundhoggApi;

class Client {

	const NAME_SPACE = 'gh/v4';

	protected $public_key;
	protected $api_token;
	protected $use_app_pass;
	protected $home_url = '';

	protected $services = [];

	public function __construct( $home_url = '', $public_key_or_username = '', $api_token_or_app_pass = '', $use_app_pass = '' ) {

		$this->public_key   = $public_key_or_username;
		$this->api_token    = $api_token_or_app_pass;
		$this->use_app_pass = $use_app_pass;

		$this->home_url = $home_url;
	}

	/**
	 * Make a request to the Groundhogg API
	 *
	 * @param string $route
	 * @param mixed  $params
	 * @param string $method
	 *
	 * @return array|bool|object|\WP_Error
	 */
	public function request( $route, $params = null, $method = 'GET' ) {

		$route = trailingslashit( trailingslashit( $this->home_url ) . self::NAME_SPACE ) . $route;

		$headers = $this->use_app_pass ? [
			'Authorization' => 'Basic ' . base64_encode( $this->public_key . ':' . $this->api_token )
		] : [
			'gh-token'      => $this->api_token,
			'gh-public-key' => $this->public_key
		];

		return remote_post_json( $route, $params, $method, $headers );
	}

	/**
	 *
	 */
	public function contacts() {

	}

}