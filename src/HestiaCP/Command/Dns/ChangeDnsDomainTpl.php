<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Dns;

use neto737\HestiaCP\Command\ProcessCommand;

class ChangeDnsDomainTpl extends ProcessCommand {

	/**
	 * @param bool|null $restart Restart the DNS server, or null for HestiaCP's default
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $template,
		private readonly ?bool $restart = null
	) {}

	public function getName(): string {
		return 'v-change-dns-domain-tpl';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->template,
			$this->restart === null ? null : $this->convertBool($this->restart)
		]);
	}
}
