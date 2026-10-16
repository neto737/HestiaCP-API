<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Web;

use neto737\HestiaCP\Command\ProcessCommand;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;

class AddWebDomainProxy extends ProcessCommand {

	/**
	 * @param string|null $template   Proxy template, or null for the user's package default
	 * @param string|null $extensions File extensions served by the proxy, comma-separated, or null for HestiaCP's default list
	 * @param bool|null   $restart    Restart the proxy server, or null for HestiaCP's default
	 * @throws InvalidArgumentException
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly ?string $template = null,
		private readonly ?string $extensions = null,
		private readonly ?bool $restart = null
	) {
		if ($extensions === null && $restart !== null) {
			throw new InvalidArgumentException('Pass the extensions when you set restart: HestiaCP reads an empty extensions argument as no extensions');
		}
	}

	public function getName(): string {
		return 'v-add-web-domain-proxy';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->template,
			$this->extensions,
			$this->restart === null ? null : $this->convertBool($this->restart)
		]);
	}
}
