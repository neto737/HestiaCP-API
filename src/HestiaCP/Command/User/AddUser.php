<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\User;

use neto737\HestiaCP\Command\ProcessCommand;

class AddUser extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		#[\SensitiveParameter] private readonly string $password,
		private readonly string $email,
		private readonly ?string $package = null,
		private readonly ?string $name = null,
		private readonly ?string $lastname = null
	) {}

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
