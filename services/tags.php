<?php

namespace GroundhoggApi\Services;

class Tags extends Base_Object_With_Meta_Service {

	public function __construct( $client ) {
		parent::__construct( $client, 'tags' );
	}

}