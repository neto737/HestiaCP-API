<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Change;

use neto737\HestiaCP\Command\ProcessCommand;

class WebDomainFtpPath extends ProcessCommand {

	private string $user;

	private string $domain;

	private string $ftpUser;

	private string $ftpPath;

	public function __construct(string $user, string $domain, string $ftpUser, string $ftpPath) {
		$this->user = $user;
		$this->domain = $domain;
		$this->ftpUser = $ftpUser;
		$this->ftpPath = $ftpPath;
	}

	public function getName(): string {
		return 'v-change-web-domain-ftp-path';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->ftpUser,
			$this->ftpPath
		]);
	}
}
