<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Backup;

use neto737\HestiaCP\Command\ProcessCommand;

class DeleteUserBackupExclusions extends ProcessCommand {

	/**
	 * @param string      $user
	 * @param string|null $system Exclusion type to delete (WEB, DNS, MAIL, DB, CRON or USER). Deletes all of them when not set.
	 */
	public function __construct(
		private readonly string $user,
		private readonly ?string $system = null
	) {}

	public function getName(): string {
		return 'v-delete-user-backup-exclusions';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->system]);
	}
}
