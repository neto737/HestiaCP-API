<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Add;

use neto737\HestiaCP\Command\ProcessCommand;

class MailDomain extends ProcessCommand {

	private string $user;

	private string $domain;

	private bool $antispam;

	private bool $antivirus;

	private bool $dkim;

	public function __construct(string $user, string $domain, bool $antispam = true, bool $antivirus = true, bool $dkim = true) {
		$this->user = $user;
		$this->domain = $domain;
		$this->antispam = $antispam;
		$this->antivirus = $antivirus;
		$this->dkim = $dkim;
	}

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
