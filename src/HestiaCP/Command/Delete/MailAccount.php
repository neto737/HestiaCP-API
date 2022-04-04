<?php

namespace heliocg\HestiaCP\Command\Delete;

use heliocg\HestiaCP\Command\ProcessCommand;

class MailAccount extends ProcessCommand {

	/** @var string */
	private $user;

	/** @var string */
	private $domain;

	/** @var string */
	private $account;

	public function __construct(string $user, string $domain, string $account) {
		$this->user = $user;
		$this->domain = $domain;
		$this->account = $account;
	}

	public function getName(): string {
		return 'v-delete-mail-account';
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->domain,
			self::ARG_3 => $this->account
		];
	}
}
