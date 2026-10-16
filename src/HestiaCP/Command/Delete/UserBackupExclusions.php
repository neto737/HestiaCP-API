<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Delete;

use neto737\HestiaCP\Command\ProcessCommand;

class UserBackupExclusions extends ProcessCommand {

	private string $user;

	private ?string $system = null;

	/**
	 * @param string      $user
	 * @param string|null $system Exclusion type to delete (WEB, DNS, MAIL, DB, CRON or USER). Deletes all of them when not set.
	 */
	public function __construct(string $user, ?string $system = null) {
		$this->user = $user;
		$this->system = $system;
	}

	public function getName(): string {
		return 'v-delete-user-backup-exclusions';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->system]);
	}
}
