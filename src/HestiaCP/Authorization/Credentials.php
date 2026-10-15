<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Authorization;

class Credentials {

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
			$this->setApiKey($user);
		} else {
			$this->setUser($user);
			$this->setPassword($password);
		}
	}

	/**
	 * Access key authentication (HestiaCP 1.6+)
	 */
	public static function accessKey(string $accessKey, string $secretKey): self {
		return (new self(''))->setAccessKey($accessKey, $secretKey);
	}

	/**
	 * Legacy API key, sent as `hash`
	 */
	public static function legacyHash(string $hash): self {
		return new self($hash);
	}

	/**
	 * Username and password (HestiaCP only accepts the admin/root user)
	 */
	public static function password(string $user, string $password): self {
		return new self($user, $password);
	}

	public function setAccessKey(string $accessKey, string $secretKey): static {
		$this->access_key = $accessKey;
		$this->secret_key = $secretKey;
		$this->api_key = null;
		return $this;
	}

	public function getAccessKey(): ?string {
		return $this->access_key;
	}

	public function getSecretKey(): ?string {
		return $this->secret_key;
	}

	public function setApiKey(string $key): static {
		$this->api_key = $key;
		$this->access_key = null;
		$this->secret_key = null;
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
		if ($this->access_key !== null) {
			return [
				'access_key' => $this->getAccessKey(),
				'secret_key' => $this->getSecretKey()
			];
		}

		if ($this->api_key !== null) {
			return [
				'hash' => $this->getApiKey()
			];
		}

		return [
			'user' => $this->getUser(),
			'password' => $this->getPassword()
		];
	}
}
