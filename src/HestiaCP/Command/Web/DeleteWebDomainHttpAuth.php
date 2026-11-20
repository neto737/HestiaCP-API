<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Web;

use neto737\HestiaCP\Command\ProcessCommand;

class DeleteWebDomainHttpAuth extends ProcessCommand {

	/**
	 * @param bool|null $restart Restart the service, or null for HestiaCP's default
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $authUser,
		private readonly ?bool $restart = null
	) {}

	public function getName(): string {
		return 'v-delete-web-domain-httpauth';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->authUser,
			$this->restart === null ? null : $this->convertBool($this->restart)
		]);
	}
}
