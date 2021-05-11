<?php

namespace GroundhoggApi;

/**
 * Get a variable from an array or default if it doesn't exist.
 *
 * @param        $array
 * @param string $key
 * @param bool   $default
 *
 * @return mixed
 */
function get_array_var( $array, $key = '', $default = false ) {
	if ( isset_not_empty( $array, $key ) ) {
		if ( is_object( $array ) ) {
			return $array->$key;
		} else if ( is_array( $array ) ) {
			return $array[ $key ];
		}
	}

	return $default;
}

/**
 * Return if a value in an array isset and is not empty
 *
 * @param $array
 * @param $key
 *
 * @return bool
 */
function isset_not_empty( $array, $key = '' ) {
	if ( is_object( $array ) ) {
		return isset( $array->$key ) && ! empty( $array->$key );
	} else if ( is_array( $array ) ) {
		return isset( $array[ $key ] ) && ! empty( $array[ $key ] );
	}

	return false;
}

/**
 * If the JSON is your typical error response
 *
 * @param $json
 *
 * @return bool
 */
function is_json_error( $json ) {
	return isset_not_empty( $json, 'code' ) && isset_not_empty( $json, 'message' ) && get_array_var( $json, 'code' ) !== 'success';
}

/**
 * Convert JSON to a WP_Error
 *
 * @param $json
 *
 * @return bool|\WP_Error
 */
function get_json_error( $json ) {
	if ( is_json_error( $json ) ) {
		return new \WP_Error( get_array_var( $json, 'code' ), get_array_var( $json, 'message' ), get_array_var( $json, 'data' ) );
	}

	return false;
}


/**
 * Remote post json content
 * Glorified wp_remote_post wrapper
 *
 * @param string $url
 * @param array  $body
 * @param string $method
 * @param array  $headers
 * @param bool   $as_array
 *
 * @return array|bool|\WP_Error|object
 */
function remote_post_json( $url = '', $body = [], $method = 'POST', $headers = [], $as_array = false ) {
	$method = strtoupper( $method );

	if ( ! isset_not_empty( $headers, 'Content-type' ) && $method !== 'GET' ) {
		$headers['Content-type'] = sprintf( 'application/json; charset=%s', get_bloginfo( 'charset' ) );
	}

	switch ( $method ) {
		case 'POST':
		case 'PUT':
		case 'PATCH':
		case 'DELETE':
			$body = is_array( $body ) ? wp_json_encode( $body ) : $body;
			break;
	}

	$args = [
		'method'      => $method,
		'headers'     => $headers,
		'body'        => $body,
		'data_format' => 'body',
		'sslverify'   => true,
		'user-agent'  => 'Groundhogg: ' . home_url()

	];

	if ( $method === 'GET' ) {
		$response = wp_remote_get( $url, $args );
	} else {
		$response = wp_remote_post( $url, $args );
	}

	if ( ! $response ) {
		return new \WP_Error( 'unknown_error', sprintf( 'Failed to initialize remote %s.', $method ), $response );
	}

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$json = json_decode( wp_remote_retrieve_body( $response ), $as_array );

	if ( ! $json ) {
		return new \WP_Error( 'unknown_error', sprintf( 'Failed to initialize remote %s.', $method ), wp_remote_retrieve_body( $response ) );
	}

	if ( is_json_error( $json ) ) {
		$error = get_json_error( $json );

		$data = (array) $error->get_error_data();

		$data['url']     = $url;
		$data['method']  = $method;
		$data['headers'] = $headers;
		$data['body']    = json_decode( $body );

		$error->add_data( $data );

		return $error;
	}

	return $json;
}

/**
 * Whether the array is using string keys
 *
 * @param $arr
 *
 * @return bool
 */
function has_string_keys( $arr ) {
	foreach ( $arr as $key => $value ) {
		if ( is_string( $key ) ) {
			return true;
		}
	}

	return false;
}