<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Database;

use neto737\HestiaCP\Command\ProcessCommand;

class DeleteDatabases extends ProcessCommand {

	public function __construct(
		private readonly string $user
	) {}

	public function getName(): string {
		return 'v-delete-databases';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user]);
	}
}
