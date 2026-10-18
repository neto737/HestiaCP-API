<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Web;

use neto737\HestiaCP\Command\ProcessCommand;

class ChangeWebDomainBackendTpl extends ProcessCommand {

	/**
	 * @param bool|null $restart Restart the web server, or null for HestiaCP's default
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $template,
		private readonly ?bool $restart = null
	) {}

	public function getName(): string {
		return 'v-change-web-domain-backend-tpl';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->template,
			$this->restart === null ? null : $this->convertBool($this->restart)
		]);
	}
}
