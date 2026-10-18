<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Web;

use neto737\HestiaCP\Command\ProcessCommand;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;

class AddWebDomainSsl extends ProcessCommand {

	/** HTTPS serves the same directory as HTTP (public_html) */
	public const SSL_HOME_SAME = 'same';
	/** HTTPS serves its own directory (public_shtml) */
	public const SSL_HOME_SINGLE = 'single';

	private const SSL_HOMES = [self::SSL_HOME_SAME, self::SSL_HOME_SINGLE];

	/**
	 * @param string      $sslDir  Directory on the server holding <domain>.crt, <domain>.key and optionally <domain>.ca
	 * @param string|null $sslHome One of the SSL_HOME_* constants. HestiaCP uses same when it isn't set.
	 * @param bool|null   $restart Restart the web server, or null for HestiaCP's default
	 * @throws InvalidArgumentException
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $sslDir,
		private readonly ?string $sslHome = null,
		private readonly ?bool $restart = null
	) {
		if ($sslHome !== null && !in_array($sslHome, self::SSL_HOMES, true)) {
			throw new InvalidArgumentException("Invalid SSL home '$sslHome', expected one of: " . implode(', ', self::SSL_HOMES));
		}
	}

	public function getName(): string {
		return 'v-add-web-domain-ssl';
	}

	public function getRequestParams(): array {
		// HestiaCP stores an empty SSL home as is, so send its default when restart comes after it
		$sslHome = $this->sslHome ?? ($this->restart === null ? null : self::SSL_HOME_SAME);

		return $this->positional([
			$this->user,
			$this->domain,
			$this->sslDir,
			$sslHome,
			$this->restart === null ? null : $this->convertBool($this->restart)
		]);
	}
}
