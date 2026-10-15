<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Delete;

use neto737\HestiaCP\Command\ProcessCommand;

class UserBackup extends ProcessCommand {

	private string $user;

	private string $backup;

	public function __construct(string $user, string $backup) {
		$this->user = $user;
		$this->backup = $backup;
	}

	public function getName(): string {
		return 'v-delete-user-backup';
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->backup
		];
	}
}
