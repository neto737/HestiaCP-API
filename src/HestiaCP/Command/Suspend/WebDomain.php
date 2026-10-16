<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Suspend;

use neto737\HestiaCP\Command\ProcessCommand;

class WebDomain extends ProcessCommand {

	private string $user;

	private string $domain;

	private bool $restart;

	public function __construct(string $user, string $domain, bool $restart = false) {
		$this->user = $user;
		$this->domain = $domain;
		$this->restart = $restart;
	}

	public function getName(): string {
		return 'v-suspend-web-domain';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->domain, $this->convertBool($this->restart)]);
	}
}
