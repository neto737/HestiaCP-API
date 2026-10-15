<?php

namespace neto737\HestiaCP\Command\Delete;

use neto737\HestiaCP\Command\ProcessCommand;

class UserBackupExclusions extends ProcessCommand {

	/** @var string */
	private $user;

	/** @var string|null */
	private $system;

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
		$params = [
			self::ARG_1 => $this->user
		];

		if ($this->system !== null) {
			$params[self::ARG_2] = $this->system;
		}

		return $params;
	}
}
