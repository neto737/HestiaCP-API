<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Backup;

use neto737\HestiaCP\Command\ProcessCommand;

class UpdateUserBackupExclusions extends ProcessCommand {

	/**
	 * @param string $user
	 * @param string $file Path on the server of the exclusion list, one TYPE='entry,entry' line per type,
	 *                     e.g. written with MakeTmpFile
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $file
	) {}

	public function getName(): string {
		return 'v-update-user-backup-exclusions';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->file]);
	}
}
