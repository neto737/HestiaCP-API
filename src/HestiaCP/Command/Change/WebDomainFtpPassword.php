<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Change;

use neto737\HestiaCP\Command\ProcessCommand;

class WebDomainFtpPassword extends ProcessCommand {

	private string $user;

	private string $domain;

	private string $ftpUser;

	private string $ftpPassword;

	public function __construct(string $user, string $domain, string $ftpUser, string $ftpPassword) {
		$this->user = $user;
		$this->domain = $domain;
		$this->ftpUser = $ftpUser;
		$this->ftpPassword = $ftpPassword;
	}

	public function getName(): string {
		return 'v-change-web-domain-ftp-password';
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->domain,
			self::ARG_3 => $this->ftpUser,
			self::ARG_4 => $this->ftpPassword,
		];
	}
}
