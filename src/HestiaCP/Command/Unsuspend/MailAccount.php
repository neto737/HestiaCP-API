<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Unsuspend;

use neto737\HestiaCP\Command\ProcessCommand;

class MailAccount extends ProcessCommand {

	private string $user;

	private string $domain;

	private string $account;

	public function __construct(string $user, string $domain, string $account) {
		$this->user = $user;
		$this->domain = $domain;
		$this->account = $account;
	}

	public function getName(): string {
		return 'v-unsuspend-mail-account';
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->domain,
			self::ARG_3 => $this->account
		];
	}
}
