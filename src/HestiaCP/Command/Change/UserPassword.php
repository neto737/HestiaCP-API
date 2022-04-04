<?php

namespace heliocg\HestiaCP\Command\Change;

use heliocg\HestiaCP\Command\ProcessCommand;

class UserPassword extends ProcessCommand {

	/** @var string */
	private $user;

	/** @var string */
	private $password;

	public function __construct(string $user, string $password) {
		$this->user = $user;
		$this->password = $password;
	}

	public function getName(): string {
		return 'v-change-user-password';
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->password
		];
	}
}
