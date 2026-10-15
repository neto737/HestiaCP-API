<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Module;

use neto737\HestiaCP\Client;

abstract class Module implements IModule {

	protected Client $client;

	public function __construct(Client $client) {
		$this->client = $client;
	}
}
