<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Change;

use neto737\HestiaCP\Command\ProcessCommand;

class UserPassword extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $password
	) {}

	public function getName(): string {
		return 'v-change-user-password';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->password]);
	}
}
