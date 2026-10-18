<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Mail;

use neto737\HestiaCP\Command\ProcessCommand;

class AddMailAccountAutoreply extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $account,
		private readonly string $message
	) {}

	public function getName(): string {
		return 'v-add-mail-account-autoreply';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->account,
			$this->message
		]);
	}
}
