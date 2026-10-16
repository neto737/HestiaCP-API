<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\User;

use neto737\HestiaCP\Command\ProcessCommand;

class ChangeUserContact extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $email
	) {}

	public function getName(): string {
		return 'v-change-user-contact';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->email]);
	}
}
