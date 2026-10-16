<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Lists;

use neto737\HestiaCP\Command\ListCommand;

class UserBackup extends ListCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $backup
	) {}

	public function getName(): string {
		return 'v-list-user-backup';
	}

	public function process(): array {
		return $this->convertDetail(parent::process());
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->backup, self::FORMAT_JSON]);
	}
}
