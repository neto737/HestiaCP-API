<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Unsuspend;

use neto737\HestiaCP\Command\ProcessCommand;

class User extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly bool $restart = false
	) {}

	public function getName(): string {
		return 'v-unsuspend-user';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->convertBool($this->restart)]);
	}
}
