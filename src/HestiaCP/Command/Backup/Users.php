<?php

namespace neto737\HestiaCP\Command\Backup;

use neto737\HestiaCP\Command\ProcessCommand;

class Users extends ProcessCommand {

	public function getName(): string {
		return 'v-backup-users';
	}

	public function getTimeout(): ?float {
		// the backup runs synchronously and can take a long time
		return 0;
	}

	public function getRequestParams(): array {
		return [];
	}
}
