<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Web;

use neto737\HestiaCP\Command\ProcessCommand;

class DeleteWebDomainAlias extends ProcessCommand {

	/**
	 * @param bool|null $restart Restart the web server, or null for HestiaCP's default
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $alias,
		private readonly ?bool $restart = null
	) {}

	public function getName(): string {
		return 'v-delete-web-domain-alias';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->alias,
			$this->restart === null ? null : $this->convertBool($this->restart)
		]);
	}
}
