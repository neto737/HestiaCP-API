<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Web;

use neto737\HestiaCP\Command\ProcessCommand;

class AddWebDomainAlias extends ProcessCommand {

	/**
	 * @param string    $aliases Comma-separated
	 * @param bool|null $restart Restart the web server, or null for HestiaCP's default
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $aliases,
		private readonly ?bool $restart = null
	) {}

	public function getName(): string {
		return 'v-add-web-domain-alias';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->aliases,
			$this->restart === null ? null : $this->convertBool($this->restart)
		]);
	}
}
