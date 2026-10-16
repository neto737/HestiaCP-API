<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Add;

use neto737\HestiaCP\Command\ProcessCommand;

class MailAccount extends ProcessCommand {

	private string $user;

	private string $domain;

	private string $account;

	private string $password;

	public function __construct(string $user, string $domain, string $account, string $password) {
		$this->user = $user;
		$this->domain = $domain;
		$this->account = $account;
		$this->password = $password;
	}

	public function getName(): string {
		return 'v-add-mail-account';
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
