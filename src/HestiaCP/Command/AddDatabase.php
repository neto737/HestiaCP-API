<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

class AddDatabase extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $database,
		private readonly string $dbuser,
		private readonly string $dbpass,
		private readonly string $type = 'mysql'
	) {}

	public function getName(): string {
		return 'v-add-database';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->database,
			$this->dbuser,
			$this->dbpass,
			$this->type
		]);
	}
}
