<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Package;

use neto737\HestiaCP\Command\ProcessCommand;

class CopyUserPackage extends ProcessCommand {

	public function __construct(
		private readonly string $package,
		private readonly string $newPackage
	) {}

	public function getName(): string {
		return 'v-copy-user-package';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->package, $this->newPackage]);
	}
}
