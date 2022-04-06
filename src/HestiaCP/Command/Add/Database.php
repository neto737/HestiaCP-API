<?php

namespace neto737\HestiaCP\Command\Add;

use neto737\HestiaCP\Command\ProcessCommand;

class Database extends ProcessCommand {

	/** @var string */
	private $user;

	/** @var string */
	private $database;

	/** @var string */
	private $dbuser;

	/** @var string */
	private $dbpass;

    /** @var string */
    private $type;

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
		return [
			self::ARG_1 => $this->user,
			self::ARG_2 => $this->database,
			self::ARG_3 => $this->dbuser,
			self::ARG_4 => $this->dbpass,
            self::ARG_5 => $this->type
		];
	}
}
