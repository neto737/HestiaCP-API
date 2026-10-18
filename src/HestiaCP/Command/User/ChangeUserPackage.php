<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\User;

use neto737\HestiaCP\Command\ProcessCommand;

class ChangeUserPackage extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $package,
		private readonly bool $force = false
	) {}

	public function getName(): string {
		return 'v-change-user-package';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->package,
			$this->convertBool($this->force)
		]);
	}
}
