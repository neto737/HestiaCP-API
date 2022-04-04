<?php

namespace heliocg\HestiaCP\Module;

use heliocg\HestiaCP\Client;

abstract class Module implements IModule {

	/** @var Client */
	protected $client;

	public function __construct(Client $client) {
		$this->client = $client;
	}
}
