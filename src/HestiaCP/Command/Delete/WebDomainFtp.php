<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Delete;

use neto737\HestiaCP\Command\ProcessCommand;

class WebDomainFtp extends ProcessCommand {

	private string $user;

	private string $domain;

	private string $ftpUser;

	public function __construct(string $user, string $domain, string $ftpUser) {
		$this->user = $user;
		$this->domain = $domain;
		$this->ftpUser = $ftpUser;
	}

	public function getName(): string {
		return 'v-delete-web-domain-ftp';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->domain, $this->ftpUser]);
	}
}
