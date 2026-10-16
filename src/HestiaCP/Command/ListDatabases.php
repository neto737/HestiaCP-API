<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

class ListDatabases extends ListCommand {

	public function __construct(
		private readonly string $username
	) {}

	public function getName(): string {
		return 'v-list-databases';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->username, self::FORMAT_JSON]);
	}
}
