<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Web;

use neto737\HestiaCP\Command\ProcessCommand;

class AddWebDomainFtp extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $ftpUser,
		private readonly string $ftpPassword,
		private readonly ?string $ftpPath = null
	) {}

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
