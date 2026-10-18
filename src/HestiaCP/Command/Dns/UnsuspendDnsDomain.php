<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Dns;

use neto737\HestiaCP\Command\ProcessCommand;

class UnsuspendDnsDomain extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain
	) {}

	public function getName(): string {
		return 'v-unsuspend-dns-domain';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->domain]);
	}
}
