<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Delete;

use neto737\HestiaCP\Command\ProcessCommand;

class UserBackup extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $backup
	) {}

	public function getName(): string {
		return 'v-delete-user-backup';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->backup]);
	}
}
