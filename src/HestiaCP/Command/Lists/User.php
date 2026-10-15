<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Lists;

use neto737\HestiaCP\Command\ListCommand;

class User extends ListCommand {

	private string $username;

	public function __construct(string $username) {
		$this->username = $username;
	}

	public function getName(): string {
		return 'v-list-user';
	}

	public function process(): array {
		return $this->convertDetail(parent::process());
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->username,
			self::ARG_2 => self::FORMAT_JSON
		];
	}
}
