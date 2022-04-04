<?php

namespace heliocg\HestiaCP\Command\Delete;

use heliocg\HestiaCP\Command\ProcessCommand;

class WebDomainFtp extends ProcessCommand {

	/** @var string */
	private $user;

	/** @var string */
	private $domain;

	/** @var string */
	private $ftpUser;

	public function __construct(string $user, string $domain, string $ftpUser) {
		$this->user = $user;
		$this->domain = $domain;
		$this->ftpUser = $ftpUser;
	}

	public function getName(): string {
		return 'v-delete-web-domain-ftp';
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->domain,
			self::ARG_3 => $this->ftpUser
		];
	}
}
