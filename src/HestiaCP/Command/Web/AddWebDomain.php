<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Web;

use neto737\HestiaCP\Command\ProcessCommand;

class AddWebDomain extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly ?string $ip = null,
		private readonly ?string $aliases = null,
		private readonly ?string $proxyExtensions = null,
		private readonly bool $restart = false
	) {}

	public function getName(): string {
		return 'v-add-web-domain';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->ip,
			$this->convertBool($this->restart),
			$this->aliases ?: 'none',
			$this->proxyExtensions
		]);
	}
}
