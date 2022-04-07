<?php

namespace neto737\HestiaCP\Command\Lists;

use Nette\Utils\ArrayHash;
use neto737\HestiaCP\Command\ListCommand;

class Databases extends ListCommand {

	/** @var string */
	private $username;

	public function __construct(string $username) {
		$this->username = $username;
	}

	public function getName(): string {
		return 'v-list-databases';
	}

	public function process(): ArrayHash {
		return $this->convertDetail(parent::process());
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->username,
			self::ARG_2 => self::FORMAT_JSON
		];
	}
}
