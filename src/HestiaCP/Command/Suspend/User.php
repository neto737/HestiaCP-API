<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Suspend;

use neto737\HestiaCP\Command\ProcessCommand;

class User extends ProcessCommand {

	private string $user;

	private bool $restart;

	public function __construct(string $user, bool $restart = false) {
		$this->user = $user;
		$this->restart = $restart;
	}

	public function getName(): string {
		return 'v-suspend-user';
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->convertBool($this->restart)
		];
	}
}
