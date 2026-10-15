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
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->domain,
			self::ARG_3 => $this->convertBool($this->antispam),
			self::ARG_4 => $this->convertBool($this->antivirus),
			self::ARG_5 => $this->convertBool($this->dkim)
		];
	}
}
