<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

class ChangeWebDomainFtpPath extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $ftpUser,
		private readonly string $ftpPath
	) {}

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
