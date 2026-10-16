<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Delete;

use neto737\HestiaCP\Command\ProcessCommand;

class Databases extends ProcessCommand {

	private string $user;

	public function __construct(string $user) {
		$this->user = $user;
	}

	public function getName(): string {
		return 'v-delete-databases';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user]);
	}
}
