<?php

namespace neto737\HestiaCP\Command\Change;

use neto737\HestiaCP\Command\Add\ProcessCommand;

class MailAccountPassword extends ProcessCommand {

	/** @var string */
	private $user;

	/** @var string */
	private $domain;

	/** @var string */
	private $account;

	/** @var string */
	private $password;

	public function __construct(string $user, string $domain, string $account, string $password) {
		$this->user = $user;
		$this->domain = $domain;
		$this->account = $account;
		$this->password = $password;
	}

	public function getName(): string {
		return 'v-change-mail-account-password';
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->domain,
			self::ARG_3 => $this->account,
			self::ARG_4 => $this->password,
		];
	}
}
