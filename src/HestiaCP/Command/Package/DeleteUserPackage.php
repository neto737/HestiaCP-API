<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Package;

use neto737\HestiaCP\Command\ProcessCommand;

class DeleteUserPackage extends ProcessCommand {

	public function __construct(
		private readonly string $package
	) {}

	public function getName(): string {
		return 'v-delete-user-package';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->package]);
	}
}
