<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\User;

use neto737\HestiaCP\Command\ProcessCommand;

class ChangeUserRole extends ProcessCommand {

	/**
	 * @param string    $role user, admin or dns-cluster
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $role
	) {}

	public function getName(): string {
		return 'v-change-user-role';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->role]);
	}
}
