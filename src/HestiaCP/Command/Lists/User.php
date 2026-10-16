<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Lists;

use neto737\HestiaCP\Command\ListCommand;

class User extends ListCommand {

	public function __construct(
		private readonly string $username
	) {}

	public function getName(): string {
		return 'v-list-user';
	}

	public function process(): array {
		return $this->convertDetail(parent::process());
	}

	public function getRequestParams(): array {
		return $this->positional([$this->username, self::FORMAT_JSON]);
	}
}
