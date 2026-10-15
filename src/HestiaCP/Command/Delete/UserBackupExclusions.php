<?php

namespace neto737\HestiaCP\Command\Delete;

use neto737\HestiaCP\Command\ProcessCommand;

class UserBackupExclusions extends ProcessCommand {

	/** @var string */
	private $user;

	/**
	 * TODO:
	 * Added system parameter as in the HestiaCP docs
	 * https://hestiacp.com/docs/introduction/getting-started#v-delete-user-backup-exclusions
	 */

	public function __construct(string $user) {
		$this->user = $user;
	}

	public function getName(): string {
		return 'v-delete-user-backup-exclusions';
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user
		];
	}
}
