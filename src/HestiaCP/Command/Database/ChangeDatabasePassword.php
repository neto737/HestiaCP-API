<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Database;

use neto737\HestiaCP\Command\ProcessCommand;

class ChangeDatabasePassword extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $database,
		#[\SensitiveParameter] private readonly string $dbpass
	) {}

	public function getName(): string {
		return 'v-change-database-password';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->database,
			$this->dbpass
		]);
	}
}
