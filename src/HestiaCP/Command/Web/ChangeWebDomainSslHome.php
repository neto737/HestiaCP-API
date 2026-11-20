<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Web;

use neto737\HestiaCP\Command\ProcessCommand;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;

class ChangeWebDomainSslHome extends ProcessCommand {

	private const array SSL_HOMES = [AddWebDomainSsl::SSL_HOME_SAME, AddWebDomainSsl::SSL_HOME_SINGLE];

	/**
	 * @param string    $sslHome One of the AddWebDomainSsl::SSL_HOME_* constants
	 * @param bool|null $restart Restart the web server, or null for HestiaCP's default
	 * @throws InvalidArgumentException
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $sslHome,
		private readonly ?bool $restart = null
	) {
		if (!in_array($sslHome, self::SSL_HOMES, true)) {
			throw new InvalidArgumentException("Invalid SSL home '$sslHome', expected one of: " . implode(', ', self::SSL_HOMES));
		}
	}

	public function getName(): string {
		return 'v-change-web-domain-sslhome';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->sslHome,
			$this->restart === null ? null : $this->convertBool($this->restart)
		]);
	}
}
