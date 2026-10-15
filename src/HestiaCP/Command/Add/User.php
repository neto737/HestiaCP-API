<?php

namespace neto737\HestiaCP\Command\Add;

use neto737\HestiaCP\Command\ProcessCommand;

class User extends ProcessCommand {

	/** @var string */
	private $user;

	/** @var string */
	private $password;

	/** @var string */
	private $email;

	/** @var string|null */
	private $package;

	/** @var string|null */
	private $name;

	/** @var string|null */
	private $lastname;

	public function __construct(string $user, string $password, string $email, ?string $package = null, ?string $name = null, ?string $lastname = null) {
		$this->user = $user;
		$this->password = $password;
		$this->email = $email;
		$this->package = $package;
		$this->name = $name;
		$this->lastname = $lastname;
	}

	public function getName(): string {
		return 'v-add-user';
	}

	public function getRequestParams(): array {
		// HestiaCP passes the arguments by position, so every argument before the last one set has to be sent
		$params = [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->password,
			self::ARG_3 => $this->email,
			self::ARG_4 => $this->package ?? 'default'
		];

		if ($this->name !== null || $this->lastname !== null) {
			$params[self::ARG_5] = $this->name ?? '';
		}

		if ($this->lastname !== null) {
			$params[self::ARG_6] = $this->lastname;
		}

		return $params;
	}
}
