<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Delete;

use neto737\HestiaCP\Command\ProcessCommand;

class MailDomain extends ProcessCommand {

	private string $user;

	private string $domain;

	public function __construct(string $user, string $domain) {
		$this->user = $user;
		$this->domain = $domain;
	}

	public function getName(): string {
		return 'v-delete-mail-domain';
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->domain
		];
	}
}
