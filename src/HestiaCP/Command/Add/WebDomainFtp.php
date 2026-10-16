<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Add;

use neto737\HestiaCP\Command\ProcessCommand;

class WebDomainFtp extends ProcessCommand {

	private string $user;

	private string $domain;

	private string $ftpUser;

	private string $ftpPassword;

	private ?string $ftpPath = null;

	public function __construct(string $user, string $domain, string $ftpUser, string $ftpPassword, ?string $ftpPath = null) {
		$this->user = $user;
		$this->domain = $domain;
		$this->ftpUser = $ftpUser;
		$this->ftpPassword = $ftpPassword;
		$this->ftpPath = $ftpPath;
	}

	public function getName(): string {
		return 'v-add-web-domain-ftp';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->ftpUser,
			$this->ftpPassword,
			$this->ftpPath
		]);
	}
}
