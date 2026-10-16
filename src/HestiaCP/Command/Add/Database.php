<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Add;

use neto737\HestiaCP\Command\ProcessCommand;

class Database extends ProcessCommand {

	private string $user;

	private string $database;

	private string $dbuser;

	private string $dbpass;

	private string $type;

	public function __construct(string $user, string $database, string $dbuser, string $dbpass, string $type = 'mysql') {
		$this->user = $user;
		$this->database = $database;
		$this->dbuser = $dbuser;
		$this->dbpass = $dbpass;
		$this->type = $type;
	}

	public function getName(): string {
		return 'v-add-database';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->database,
			$this->dbuser,
			$this->dbpass,
			$this->type
		]);
	}
}
