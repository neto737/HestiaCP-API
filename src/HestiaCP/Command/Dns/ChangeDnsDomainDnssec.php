<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Dns;

use neto737\HestiaCP\Command\ProcessCommand;

class ChangeDnsDomainDnssec extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly bool $status
	) {}

	public function getName(): string {
		return 'v-change-dns-domain-dnssec';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->domain, $this->convertBool($this->status)]);
	}
}
