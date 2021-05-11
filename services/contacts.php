<?php

namespace GroundhoggApi\Services;

class Contacts extends Base_Object_With_Meta_Service {

	public function __construct( $client ) {
		parent::__construct( $client, 'contacts' );
	}

}