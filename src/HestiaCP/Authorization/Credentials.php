<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Authorization;

class Credentials {

	private const MODE_ACCESS_KEY = 'access_key';

	private const MODE_LEGACY_HASH = 'legacy_hash';

	private const MODE_PASSWORD = 'password';

	private function __construct(
		private readonly string $mode,
		private readonly ?string $user = null,
		private readonly ?string $password = null,
		private readonly ?string $api_key = null,
		private readonly ?string $access_key = null,
		private readonly ?string $secret_key = null
	) {}

	/**
	 * Access key authentication (HestiaCP 1.6+)
	 */
	public static function accessKey(string $accessKey, string $secretKey): self {
		return new self(self::MODE_ACCESS_KEY, access_key: $accessKey, secret_key: $secretKey);
	}

	/**
	 * Legacy API key, sent as `hash`
	 */
	public static function legacyHash(string $hash): self {
		return new self(self::MODE_LEGACY_HASH, api_key: $hash);
	}

	/**
	 * Username and password (HestiaCP only accepts the admin/root user)
	 */
	public static function password(string $user, string $password): self {
		return new self(self::MODE_PASSWORD, user: $user, password: $password);
	}

	public function getAccessKey(): ?string {
		return $this->access_key;
	}

	public function getSecretKey(): ?string {
		return $this->secret_key;
	}

	public function getApiKey(): ?string {
		return $this->api_key;
	}

	public function getUser(): ?string {
		return $this->user;
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
