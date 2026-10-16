<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Add;

use neto737\HestiaCP\Command\ProcessCommand;

class WebDomain extends ProcessCommand {

	private string $user;

	private string $domain;

	private ?string $ip = null;

	private ?string $aliases = null;

	private ?string $proxyExtensions = null;

	private bool $restart;

	public function __construct(string $user, string $domain, ?string $ip = null, ?string $aliases = null, ?string $proxyExtensions = null, bool $restart = false) {
		$this->user = $user;
		$this->domain = $domain;
		$this->ip = $ip;
		$this->aliases = $aliases;
		$this->proxyExtensions = $proxyExtensions;
		$this->restart = $restart;
	}

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
