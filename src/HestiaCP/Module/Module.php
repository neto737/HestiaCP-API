<?php

namespace neto737\HestiaCP\Module;

use neto737\HestiaCP\Client;

abstract class Module implements IModule {

	/** @var Client */
	protected $client;

	public function __construct(Client $client) {
		$this->client = $client;
	}
}
