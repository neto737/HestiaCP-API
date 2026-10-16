<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Web;

use neto737\HestiaCP\Command\ProcessCommand;

class ChangeWebDomainFtpPassword extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $ftpUser,
		private readonly string $ftpPassword
	) {}

	public function getName(): string {
		return 'v-change-web-domain-ftp-password';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->ftpUser,
			$this->ftpPassword
		]);
	}
}
