<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Add;

use neto737\HestiaCP\Command\ProcessCommand;

class User extends ProcessCommand {

	private string $user;

	private string $password;

	private string $email;

	private ?string $package = null;

	private ?string $name = null;

	private ?string $lastname = null;

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
		return $this->positional([
			$this->user,
			$this->password,
			$this->email,
			$this->package ?? 'default',
			$this->name,
			$this->lastname
		]);
	}
}
