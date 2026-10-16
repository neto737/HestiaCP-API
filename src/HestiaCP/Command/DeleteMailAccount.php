<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

class DeleteMailAccount extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $account
	) {}

	public function getName(): string {
		return 'v-delete-mail-account';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->domain, $this->account]);
	}
}
