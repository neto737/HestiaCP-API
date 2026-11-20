<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Web;

use neto737\HestiaCP\Command\ProcessCommand;

class AddWebDomainHttpAuth extends ProcessCommand {

	/**
	 * @param bool|null $restart Restart the web server, or null for HestiaCP's default
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $authUser,
		#[\SensitiveParameter] private readonly string $authPassword,
		private readonly ?bool $restart = null
	) {}

	public function getName(): string {
		return 'v-add-web-domain-httpauth';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->authUser,
			$this->authPassword,
			$this->restart === null ? null : $this->convertBool($this->restart)
		]);
	}
}
