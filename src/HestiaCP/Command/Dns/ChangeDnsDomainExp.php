<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Dns;

use neto737\HestiaCP\Command\ProcessCommand;

class ChangeDnsDomainExp extends ProcessCommand {

	/**
	 * @param string $exp Expiration date, YYYY-MM-DD
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $exp
	) {}

	public function getName(): string {
		return 'v-change-dns-domain-exp';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->domain, $this->exp]);
	}
}
