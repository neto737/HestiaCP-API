<?php

namespace neto737\HestiaCP\Command\Add;

class WebDomain extends ProcessCommand {

	/** @var string */
	private $user;

	/** @var string */
	private $domain;

	/** @var string|null */
	private $ip;

	/** @var string|null */
	private $aliases;

	/** @var string|null */
	private $proxyExtensions;

	/** @var bool */
	private $restart;

	public function __construct(string $user, string $domain, string $ip = null, string $aliases = null, string $proxyExtensions = null, bool $restart = false) {
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
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->domain,
			self::ARG_3 => $this->ip ?: '',
			self::ARG_4 => $this->convertBool($this->restart),
			self::ARG_5 => $this->aliases ?: 'none',
			self::ARG_6 => $this->proxyExtensions ?: ''
		];
	}
}
