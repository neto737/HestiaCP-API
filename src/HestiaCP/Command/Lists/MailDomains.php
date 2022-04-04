<?php

namespace heliocg\HestiaCP\Command\Lists;

use heliocg\HestiaCP\Command\ListCommand;

class MailDomains extends ListCommand {

	/** @var string */
	private $user;

	public function __construct(string $user) {
		$this->user = $user;
	}

	public function getName(): string {
		return 'v-list-mail-domains';
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => self::FORMAT_JSON
		];
	}
}
