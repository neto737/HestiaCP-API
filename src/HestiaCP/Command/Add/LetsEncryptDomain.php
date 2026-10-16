<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Add;

use neto737\HestiaCP\Command\ProcessCommand;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;

class LetsEncryptDomain extends ProcessCommand {

	public const KEY_ALGO_ECDSA_256 = 'ecdsa-256';
	public const KEY_ALGO_ECDSA_384 = 'ecdsa-384';
	public const KEY_ALGO_RSA = 'rsa';

	private const KEY_ALGOS = [self::KEY_ALGO_ECDSA_256, self::KEY_ALGO_ECDSA_384, self::KEY_ALGO_RSA];

	private string $user;

	private string $domain;

	private ?string $aliases = null;

	private bool $mail;

	private ?string $keyAlgo = null;

	/**
	 * @param string      $user
	 * @param string      $domain
	 * @param string|null $aliases
	 * @param bool        $mail    Issue the certificate for the mail domain instead of the web domain
	 * @param string|null $keyAlgo One of the KEY_ALGO_* constants. HestiaCP uses ecdsa-256 when it isn't set.
	 *                             Needs a HestiaCP version newer than 1.10.5.
	 * @throws InvalidArgumentException
	 */
	public function __construct(string $user, string $domain, ?string $aliases = null, bool $mail = false, ?string $keyAlgo = null) {
		if ($keyAlgo !== null && !in_array($keyAlgo, self::KEY_ALGOS, true)) {
			throw new InvalidArgumentException("Invalid key algorithm '$keyAlgo', expected one of: " . implode(', ', self::KEY_ALGOS));
		}

		$this->user = $user;
		$this->domain = $domain;
		$this->aliases = $aliases;
		$this->mail = $mail;
		$this->keyAlgo = $keyAlgo;
	}

	public function getName(): string {
		return 'v-add-letsencrypt-domain';
	}

	public function getTimeout(): ?float {
		return 90;
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->aliases,
			$this->convertBool($this->mail),
			$this->keyAlgo
		]);
	}
}
