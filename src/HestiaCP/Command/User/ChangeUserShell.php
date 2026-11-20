<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\User;

use neto737\HestiaCP\Command\ProcessCommand;

class ChangeUserShell extends ProcessCommand {

	/**
	 * @param string    $shell e.g. nologin, bash or sh
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $shell
	) {}

	public function getName(): string {
		return 'v-change-user-shell';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->shell]);
	}
}
