<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\User;

use neto737\HestiaCP\Command\ListCommand;

class ListUserStats extends ListCommand {

	public function __construct(
		private readonly string $user
	) {}

	public function getName(): string {
		return 'v-list-user-stats';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, self::FORMAT_JSON]);
	}
}
