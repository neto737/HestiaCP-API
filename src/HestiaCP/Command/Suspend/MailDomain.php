<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Suspend;

use neto737\HestiaCP\Command\ProcessCommand;

class MailDomain extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain
	) {}

	public function getName(): string {
		return 'v-suspend-mail-domain';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->domain]);
	}
}
