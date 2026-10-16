<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Backup;

use neto737\HestiaCP\Command\ProcessCommand;

class User extends ProcessCommand {

	private string $user;

	private bool $notify;

	public function __construct(string $user, bool $notify = true) {
		$this->user = $user;
		$this->notify = $notify;
	}

	public function getName(): string {
		return 'v-backup-user';
	}

	public function getTimeout(): ?float {
		// the backup runs synchronously and can take a long time
		return 0;
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->convertBool($this->notify)]);
	}
}
