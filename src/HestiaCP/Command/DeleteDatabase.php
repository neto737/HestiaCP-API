<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

class DeleteDatabase extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $database
	) {}

	public function getName(): string {
		return 'v-delete-database';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->database]);
	}
}
