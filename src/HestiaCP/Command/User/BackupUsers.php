<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\User;

use neto737\HestiaCP\Command\ProcessCommand;

class BackupUsers extends ProcessCommand {

	public function getName(): string {
		return 'v-backup-users';
	}

	#[\Override]
	public function getTimeout(): ?float {
		// the backup runs synchronously and can take a long time
		return 0;
	}

	public function getRequestParams(): array {
		return [];
	}
}
