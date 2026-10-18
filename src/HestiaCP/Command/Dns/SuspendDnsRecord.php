<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Dns;

use neto737\HestiaCP\Command\ProcessCommand;

class SuspendDnsRecord extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly int $id
	) {}

	public function getName(): string {
		return 'v-suspend-dns-record';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->domain, (string) $this->id]);
	}
}
