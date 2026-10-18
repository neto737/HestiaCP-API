<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Mail;

use neto737\HestiaCP\Command\ListCommand;

class ListMailAccount extends ListCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $account
	) {}

	public function getName(): string {
		return 'v-list-mail-account';
	}

	public function process(): array {
		return $this->convertDetail(parent::process());
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->domain, $this->account, self::FORMAT_JSON]);
	}
}
