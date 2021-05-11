<?php

namespace GroundhoggApi\Services;

use GroundhoggApi\Client;
use function GroundhoggApi\get_array_var;
use function GroundhoggApi\has_string_keys;

abstract class Base_Object_With_Meta_Service extends Base_Object_Service {

	/**
	 * Create an object
	 *
	 * There 2 ways of creating an object
	 *
	 * 1. passing $data as and array of arrays with data and meta
	 *
	 * $data = [
	 *    [ data => [...data], meta => [...data] ],
	 *    [ data => [...data], meta => [...data] ],
	 *    [ data => [...data], meta => [...data] ],
	 *    ...
	 * ]
	 *
	 * 2. passing $data and $meta as normal arrays of key => value pairs
	 *
	 * $data/$meta = [
	 *    key => value,
	 *    key => value,
	 *    ...
	 * ]
	 *
	 * @param       $data
	 * @param array $meta
	 *
	 * @return array|object|\WP_Error
	 */
	public function create( $data, $meta = [] ) {

		// If the data is not associative assume we are creating multiple objects at the same time as array of arrays.
		if ( ! has_string_keys( $data ) ) {

			$response = $this->client->request( $this->route, $data, self::CREATE );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			return $response->items;
		}

		// Otherwise we pass meta and data as normal
		$response = $this->client->request( $this->route, [
			'data' => $data,
			'meta' => $meta,
		], self::CREATE );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return $response->item;
	}

	/**
	 * Update a single or multiple objects
	 *
	 * 3 ways of updating objects
	 *
	 * 1. Passing $query_or_id as and array of arrays with data and meta
	 *
	 * $query_or_id = [
	 *    [ ID => 1, data => [...args], meta => [...args] ],
	 *    [ ID => 2, data => [...args], meta => [...args] ],
	 *    [ ID => 3, data => [...args], meta => [...args] ],
	 *    ...
	 * ]
	 *
	 * 2. Passing $query_or_id as an associative array and $data and $meta as normal arrays of key => value pairs
	 *
	 * $query_or_id/$data/$meta = [
	 *    key => value,
	 *    key => value,
	 *    ...
	 * ]
	 *
	 * 3. Passing $query_or_id as an integer and $data and $meta as normal arrays of key => value pairs
	 *
	 * $query_or_id = integer
	 *
	 * $data/$meta = [
	 *    key => value,
	 *    key => value,
	 *    ...
	 * ]
	 *
	 * @param mixed $query_or_id either a numeric ID or a db query
	 * @param array $data        associated array of what to update in the object
	 * @param array $meta        associated array of what to meta update in the object
	 *
	 * @return object|array|\WP_Error
	 */
	public function update( $query_or_id, $data = [], $meta = [] ) {

		// If we get a numeric id we can set the route to the ID of the object
		if ( is_numeric( $query_or_id ) ) {

			$response = $this->client->request( $this->route . '/' . $query_or_id, [
				'data' => $data,
				'meta' => $meta,
			], self::UPDATE );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			return $response->item;

		} // If the data is not associative assume we are updating multiple objects at the same time as array of arrays.
		else if ( is_array( $query_or_id ) && ! has_string_keys( $query_or_id ) ) {
			$response = $this->client->request( $this->route, $data, self::UPDATE );
		} // Otherwise use a query
		else {
			$response = $this->client->request( $this->route, [
				'query' => $query_or_id,
				'data'  => $data,
				'meta'  => $meta,
			], self::UPDATE );
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return $response->items;
	}
}