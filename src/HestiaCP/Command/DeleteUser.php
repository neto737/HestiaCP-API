<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

class DeleteUser extends ProcessCommand {

	public function __construct(
		private readonly string $user
	) {}

	public function getName(): string {
		return 'v-delete-user';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user]);
	}
}
