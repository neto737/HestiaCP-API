<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Module;

use neto737\HestiaCP\Command\Package\ListUserPackage;
use neto737\HestiaCP\Command\Package\ListUserPackages;

class Packages extends Module {

	/**
	 * This function obtains the list of all hosting packages.
	 *
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function list(): array {
		return $this->client->send(new ListUserPackages());
	}

	/**
	 * This function obtains the parameters of a hosting package.
	 *
	 * @param string $package
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function detail(string $package): array {
		return $this->client->send(new ListUserPackage($package));
	}
}
