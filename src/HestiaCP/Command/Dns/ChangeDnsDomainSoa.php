<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Dns;

use neto737\HestiaCP\Command\ProcessCommand;

class ChangeDnsDomainSoa extends ProcessCommand {

	/**
	 * @param string    $soa     Primary nameserver for the SOA record
	 * @param bool|null $restart Restart the DNS server, or null for HestiaCP's default
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $soa,
		private readonly ?bool $restart = null
	) {}

	public function getName(): string {
		return 'v-change-dns-domain-soa';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->soa,
			$this->restart === null ? null : $this->convertBool($this->restart)
		]);
	}
}
