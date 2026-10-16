<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

class ChangeMailAccountPassword extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $account,
		private readonly string $password
	) {}

	public function getName(): string {
		return 'v-change-mail-account-password';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->account,
			$this->password
		]);
	}
}
