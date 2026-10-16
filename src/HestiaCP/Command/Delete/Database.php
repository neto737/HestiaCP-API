<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Delete;

use neto737\HestiaCP\Command\ProcessCommand;

class Database extends ProcessCommand {

	private string $user;

	private string $database;

	public function __construct(string $user, string $database) {
		$this->user = $user;
		$this->database = $database;
	}

	public function getName(): string {
		return 'v-delete-database';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->database]);
	}
}
