<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

class ListDatabase extends ListCommand {

	public function __construct(
		private readonly string $username,
		private readonly string $database
	) {}

	public function getName(): string {
		return 'v-list-database';
	}

	public function process(): array {
		return $this->convertDetail(parent::process());
	}

	public function getRequestParams(): array {
		return $this->positional([$this->username, $this->database, self::FORMAT_JSON]);
	}
}
