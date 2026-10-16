<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Add;

use neto737\HestiaCP\Command\ProcessCommand;

class MailDomain extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly bool $antispam = true,
		private readonly bool $antivirus = true,
		private readonly bool $dkim = true
	) {}

	public function getName(): string {
		return 'v-add-mail-domain';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->convertBool($this->antispam),
			$this->convertBool($this->antivirus),
			$this->convertBool($this->dkim)
		]);
	}
}
