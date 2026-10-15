<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Lists;

use neto737\HestiaCP\Command\ListCommand;

class Database extends ListCommand {

	private string $username;

	private string $database;

	public function __construct(string $username, string $database) {
		$this->username = $username;
		$this->database = $database;
	}

	public function getName(): string {
		return 'v-list-database';
	}

	public function process(): array {
		return $this->convertDetail(parent::process());
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->username,
			self::ARG_2 => $this->database,
			self::ARG_3 => self::FORMAT_JSON
		];
	}
}
