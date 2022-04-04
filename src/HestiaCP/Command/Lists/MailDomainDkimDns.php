<?php

namespace heliocg\HestiaCP\Command\Lists;

use heliocg\HestiaCP\Command\ListCommand;

class MailDomainDkimDns extends ListCommand {

	/** @var string */
	private $user;

	/** @var string */
	private $domain;

	public function __construct(string $user, string $domain) {
		$this->user = $user;
		$this->domain = $domain;
	}

	public function getName(): string {
		return 'v-list-mail-domain-dkim-dns';
	}

	public function getResponseText(): string {
		// bug, issue 1620 ( https://github.com/serghey-rodin/vesta/issues/1620 )
		return preg_replace('~\\n~', '', parent::getResponseText());
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->domain,
			self::ARG_3 => self::FORMAT_JSON
		];
	}
}
