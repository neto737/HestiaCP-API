<?php

namespace neto737\HestiaCP\Authorization;

class Credentials {

	/** @var string */
	private $user;

	/** @var string */
	private $password;

	/** @var string */
	private $api_key;

	/**
	 * @param string $user		Username or Api Key
	 * @param string @password	Password (must be null if using API Key)
	 */
	public function __construct(string $user, string $password = null) {
		if (is_null($password)) {
			$this->setApiKey($user);
		} else {
			$this->setUser($user);
			$this->setPassword($password);
		}
	}

	public function setApiKey(string $key) {
		$this->api_key = $key;
		return $this;
	}

	public function getApiKey(): string {
		return $this->api_key;
	}

	public function setUser(string $user) {
		$this->user = $user;
		return $this;
	}

	public function getUser(): string {
		return $this->user;
	}

	public function setPassword(string $password) {
		$this->password = $password;
		return $this;
	}

	public function getPassword(): string {
		return $this->password;
	}

	public function getRequestParams(): array {
		if ($this->api_key) {
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
