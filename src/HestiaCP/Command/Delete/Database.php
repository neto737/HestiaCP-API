<?php

namespace neto737\HestiaCP\Command\Delete;

use neto737\HestiaCP\Command\ProcessCommand;

class Database extends ProcessCommand {

	/** @var string */
	private $user;

	/** @var string */
	private $database;

	public function __construct(string $user, string $database) {
		$this->user = $user;
		$this->database = $database;
	}

	public function getName(): string {
		return 'v-delete-database';
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->database
		];
	}
}
