<?php

namespace heliocg\HestiaCP\Command\Add;

use heliocg\HestiaCP\Command\ProcessCommand;

class LetsEncryptDomain extends ProcessCommand {

	/** @var string */
	private $user;

	/** @var string */
	private $domain;

	/** @var string|null */
	private $aliases;

	/** @var bool */
	private $restart;

	public function __construct(string $user, string $domain, string $aliases = null, bool $restart = false) {
		$this->user = $user;
		$this->domain = $domain;
		$this->aliases = $aliases;
		$this->restart = $restart;
	}

	public function getName(): string {
		return 'v-add-letsencrypt-domain';
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->domain,
			self::ARG_3 => $this->aliases ?: '',
			self::ARG_4 => $this->convertBool($this->restart),
		];
	}
}
