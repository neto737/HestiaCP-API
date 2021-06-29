<?php

namespace neto737\HestiaCP\Command\Add;

class User extends ProcessCommand {

	/** @var string */
	private $user;

	/** @var string */
	private $password;

	/** @var string */
	private $email;

	/** @var string */
	private $package;

	public function __construct(string $user, string $password, string $email, string $package = 'default') {
		$this->user = $user;
		$this->password = $password;
		$this->email = $email;
		$this->package = $package;
	}

	public function getName(): string {
		return 'v-add-user';
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->password,
			self::ARG_3 => $this->email,
			self::ARG_4 => $this->package
		];
	}
}
