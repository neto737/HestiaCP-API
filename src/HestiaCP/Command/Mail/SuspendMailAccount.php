<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Mail;

use neto737\HestiaCP\Command\ProcessCommand;

class SuspendMailAccount extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $account
	) {}

	public function getName(): string {
		return 'v-suspend-mail-account';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->domain, $this->account]);
	}
}
