<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Authorization;

class Credentials {

	private const MODE_ACCESS_KEY = 'access_key';

	private const MODE_LEGACY_HASH = 'legacy_hash';

	private const MODE_PASSWORD = 'password';

	private string $mode;

	private ?string $user = null;

	private ?string $password = null;

	private ?string $api_key = null;

	private ?string $access_key = null;

	private ?string $secret_key = null;

	/**
	 * @param string $user		Username or Api Key
	 * @param string $password	Password (must be null if using API Key)
	 */
	public function __construct(string $user, ?string $password = null) {
		if (is_null($password)) {
			$this->init(self::MODE_LEGACY_HASH, $user);
		} else {
			$this->init(self::MODE_PASSWORD, $user, $password);
		}
	}

	/**
	 * Access key authentication (HestiaCP 1.6+)
	 */
	public static function accessKey(string $accessKey, string $secretKey): self {
		return self::create(self::MODE_ACCESS_KEY, $accessKey, $secretKey);
	}

	/**
	 * Legacy API key, sent as `hash`
	 */
	public static function legacyHash(string $hash): self {
		return self::create(self::MODE_LEGACY_HASH, $hash);
	}

	/**
	 * Username and password (HestiaCP only accepts the admin/root user)
	 */
	public static function password(string $user, string $password): self {
		return self::create(self::MODE_PASSWORD, $user, $password);
	}

	/**
	 * Builds an instance in the given mode without going through the public constructor,
	 * which guesses the mode from a null password. Becomes the private constructor once
	 * the public one is removed.
	 */
	private static function create(string $mode, string $first, ?string $second = null): self {
		$credentials = (new \ReflectionClass(self::class))->newInstanceWithoutConstructor();
		$credentials->init($mode, $first, $second);
		return $credentials;
	}

	/**
	 * Sets the mode and its fields, and clears the fields of the other modes
	 */
	private function init(string $mode, string $first, ?string $second = null): void {
		$this->mode = $mode;
		$this->user = $mode === self::MODE_PASSWORD ? $first : null;
		$this->password = $mode === self::MODE_PASSWORD ? $second : null;
		$this->api_key = $mode === self::MODE_LEGACY_HASH ? $first : null;
		$this->access_key = $mode === self::MODE_ACCESS_KEY ? $first : null;
		$this->secret_key = $mode === self::MODE_ACCESS_KEY ? $second : null;
	}

	public function setAccessKey(string $accessKey, string $secretKey): static {
		$this->init(self::MODE_ACCESS_KEY, $accessKey, $secretKey);
		return $this;
	}

	public function getAccessKey(): ?string {
		return $this->access_key;
	}

	public function getSecretKey(): ?string {
		return $this->secret_key;
	}

	public function setApiKey(string $key): static {
		$this->init(self::MODE_LEGACY_HASH, $key);
		return $this;
	}

	public function getApiKey(): ?string {
		return $this->api_key;
	}

	public function setUser(string $user): static {
		$this->user = $user;
		return $this;
	}

	public function getUser(): ?string {
		return $this->user;
	}

	public function setPassword(string $password): static {
		$this->password = $password;
		return $this;
	}

	public function getPassword(): ?string {
		return $this->password;
	}

	/**
	 * @return array<string, string|null>
	 */
	public function getRequestParams(): array {
		return match ($this->mode) {
			self::MODE_ACCESS_KEY => [
				'access_key' => $this->getAccessKey(),
				'secret_key' => $this->getSecretKey()
			],
			self::MODE_LEGACY_HASH => [
				'hash' => $this->getApiKey()
			],
			default => [
				'user' => $this->getUser(),
				'password' => $this->getPassword()
			]
		};
	}
}
