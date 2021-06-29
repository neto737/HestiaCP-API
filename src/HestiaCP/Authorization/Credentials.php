<?php

namespace neto737\HestiaCP\Authorization;

class Credentials {

	/** @var string */
	private $user;

	/** @var string */
	private $password;

	public function __construct(string $user, string $password) {
		$this->setUser($user);
		$this->setPassword($password);
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
		return [
			'user' => $this->getUser(),
			'password' => $this->getPassword()
		];
	}
}
