<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

class ListUsers extends ListCommand {

	public function getName(): string {
		return 'v-list-users';
	}

	public function getRequestParams(): array {
		return $this->positional([self::FORMAT_JSON]);
	}
}
