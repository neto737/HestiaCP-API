<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Web;

use neto737\HestiaCP\Command\ProcessCommand;

class AddWebDomainRedirect extends ProcessCommand {

	/**
	 * @param string    $redirect Target, e.g. www.example.com or https://example.org/path
	 * @param int       $code     HTTP status code of the redirect, e.g. 301 or 302
	 * @param bool|null $restart  Restart the web server, or null for HestiaCP's default
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $redirect,
		private readonly int $code = 301,
		private readonly ?bool $restart = null
	) {}

	public function getName(): string {
		return 'v-add-web-domain-redirect';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->redirect,
			(string) $this->code,
			$this->restart === null ? null : $this->convertBool($this->restart)
		]);
	}
}
