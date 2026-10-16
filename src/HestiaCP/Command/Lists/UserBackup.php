<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Lists;

use neto737\HestiaCP\Command\ListCommand;

class UserBackup extends ListCommand {

	private string $user;

	private string $backup;

	public function __construct(string $user, string $backup) {
		$this->user = $user;
		$this->backup = $backup;
	}

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
