<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Dns;

use neto737\HestiaCP\Command\ProcessCommand;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;

class AddDnsDomain extends ProcessCommand {

	/**
	 * @param list<string> $nameservers Two to eight nameservers, or none for the user's
	 * @param bool|null    $restart     Restart the DNS server, or null for HestiaCP's default
	 * @param bool|null    $dnssec      Turn on DNSSEC, or null for HestiaCP's default (off)
	 * @throws InvalidArgumentException
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $ip,
		private readonly array $nameservers = [],
		private readonly ?bool $restart = null,
		private readonly ?bool $dnssec = null
	) {
		// HestiaCP replaces fewer than two nameservers with the user's, so a single one would be dropped
		if (count($nameservers) === 1) {
			throw new InvalidArgumentException('Pass at least two nameservers, or none for the user\'s: HestiaCP ignores a single nameserver');
		}
		if (count($nameservers) > 8) {
			throw new InvalidArgumentException('HestiaCP takes at most 8 nameservers');
		}
	}

	public function getName(): string {
		return 'v-add-dns-domain';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->ip,
			...array_pad($this->nameservers, 8, null),
			$this->restart === null ? null : $this->convertBool($this->restart),
			$this->dnssec === null ? null : $this->convertBool($this->dnssec)
		]);
	}
}
