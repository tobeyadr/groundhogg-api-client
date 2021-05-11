<?php

namespace GroundhoggApi\Services;

use GroundhoggApi\Client;
use function GroundhoggApi\get_array_var;
use function GroundhoggApi\has_string_keys;

abstract class Base_Object_Service extends Service {

	/**
	 * The endpoint route
	 *
	 * @var string
	 */
	protected $route;

	public function __construct( $client, $route ) {

		$this->route = $route;

		parent::__construct( $client );
	}

	/**
	 * Create an object
	 *
	 * @param $params mixed
	 */
	public function create( $data ) {

		// Creating multiple objects at a time.
		if ( ! has_string_keys( $data ) ) {

			$response = $this->client->request( $this->route, $data, self::CREATE );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			return $response->items;
		}

		$response = $this->client->request( $this->route, [
			'data' => $data
		], self::CREATE );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return $response->item;
	}

	/**
	 * Fetches 1 or more objects from the API
	 *
	 * @param array $query_or_id
	 *
	 * @return array|bool|object|\WP_Error
	 */
	public function read( $query_or_id = [] ) {

		if ( is_numeric( $query_or_id ) ) {

			$response = $this->client->request( $this->route . '/' . $query_or_id );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			return $response->item;
		}

		$response = $this->client->request( $this->route, $query_or_id );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return $response->items;
	}

	/**
	 * Update a single or multiple objects
	 *
	 * @param mixed $query_or_id either a numeric ID or a db query
	 * @param array $data        associated array of what to update in the object
	 *
	 * @return object|array|\WP_Error
	 */
	public function update( $query_or_id, $data = [] ) {

		// Updating a single object
		if ( is_numeric( $query_or_id ) ) {

			$response = $this->client->request( $this->route . '/' . $query_or_id, [
				'data' => $data,
			], self::UPDATE );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			return $response->item;
		} // Updating multiple objects at a time with supplied IDs
		else if ( is_array( $query_or_id ) && ! has_string_keys( $query_or_id ) ) {
			$response = $this->client->request( $this->route, $query_or_id, self::UPDATE );
		} // Updating from a query
		else {
			$response = $this->client->request( $this->route, [
				'query' => $query_or_id,
				'data'  => $data
			], self::UPDATE );
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return $response->items;
	}

	/**
	 * Delete a multiple or a single object
	 *
	 * @param mixed $query_or_id
	 */
	public function delete( $query_or_id = false ) {

		// Use the object ID route to delete
		if ( is_numeric( $query_or_id ) ) {

			$response = $this->client->request( $this->route . '/' . $query_or_id, [], self::DELETE );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			return true;
		} // Pass object ids or other identifier as sequential array
		else if ( is_array( $query_or_id ) && ! has_string_keys( $query_or_id ) ) {
			$response = $this->client->request( $this->route . '/' . $query_or_id, [], self::DELETE );
		} // Use query to delete the objects instead
		else {
			$response = $this->client->request( $this->route, [
				'query' => $query_or_id,
			], self::DELETE );
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return $response->total_items;

	}

}