<?php

namespace neto737\HestiaCP\Command\Delete;

use neto737\HestiaCP\Command\ProcessCommand;

class User extends ProcessCommand {

	/** @var string */
	private $user;

	public function __construct(string $user) {
		$this->user = $user;
	}

	public function getName(): string {
		return 'v-delete-user';
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user
		];
	}
}
