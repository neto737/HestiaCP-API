<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Web;

use neto737\HestiaCP\Command\ProcessCommand;

class DeleteWebDomainProxy extends ProcessCommand {

	/**
	 * @param bool|null $restart Restart the proxy server, or null for HestiaCP's default
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly ?bool $restart = null
	) {}

	public function getName(): string {
		return 'v-delete-web-domain-proxy';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->restart === null ? null : $this->convertBool($this->restart)
		]);
	}
}
