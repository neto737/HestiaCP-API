<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Dns;

use neto737\HestiaCP\Command\ProcessCommand;

class AddDnsRecord extends ProcessCommand {

	/**
	 * @param string    $record   Record name, e.g. www, or @ for the domain itself
	 * @param string    $type     Record type, e.g. A, AAAA, CNAME, MX, TXT, SRV, CAA
	 * @param int|null  $priority Priority of an MX or SRV record, or null for HestiaCP's default (10)
	 * @param int|null  $id       Record ID, or null for the next free ID
	 * @param bool|null $restart  Restart the DNS server, or null for HestiaCP's default
	 * @param int|null  $ttl      TTL in seconds, or null for the domain's TTL
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $record,
		private readonly string $type,
		private readonly string $value,
		private readonly ?int $priority = null,
		private readonly ?int $id = null,
		private readonly ?bool $restart = null,
		private readonly ?int $ttl = null
	) {}

	public function getName(): string {
		return 'v-add-dns-record';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->record,
			$this->type,
			$this->value,
			$this->priority === null ? null : (string) $this->priority,
			$this->id === null ? null : (string) $this->id,
			$this->restart === null ? null : $this->convertBool($this->restart),
			$this->ttl === null ? null : (string) $this->ttl
		]);
	}
}
