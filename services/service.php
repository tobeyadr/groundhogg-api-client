<?php

namespace GroundhoggApi\Services;

use GroundhoggApi\Client;

abstract class Service {

	const DELETE = 'DELETE';
	const UPDATE = 'PATCH';
	const CREATE = 'POST';

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * Service constructor.
	 *
	 * @param $client Client
	 */
	public function __construct( $client ) {
		$this->client = $client;
	}

	/**
	 * @return Client
	 */
	public function get_client(){
		return $this->client;
	}

}