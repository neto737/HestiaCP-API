<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

class UnsuspendMailDomain extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain
	) {}

	public function getName(): string {
		return 'v-unsuspend-mail-domain';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->domain]);
	}
}
