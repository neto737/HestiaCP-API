<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

class ListUserBackupExclusions extends ListCommand {

	public function __construct(
		private readonly string $user
	) {}

	public function getName(): string {
		return 'v-list-user-backup-exclusions';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, self::FORMAT_JSON]);
	}
}
