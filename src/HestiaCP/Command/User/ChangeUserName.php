<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\User;

use neto737\HestiaCP\Command\ProcessCommand;

class ChangeUserName extends ProcessCommand {

	/**
	 * @param string      $name     First name
	 * @param string|null $lastname Last name
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $name,
		private readonly ?string $lastname = null
	) {}

	public function getName(): string {
		return 'v-change-user-name';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->name, $this->lastname]);
	}
}
