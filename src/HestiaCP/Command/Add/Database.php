<?php

namespace heliocg\HestiaCP\Command\Add;

use heliocg\HestiaCP\Command\ProcessCommand;

class Database extends ProcessCommand {

    /** @var string */
    private $user;

    /** @var string */
    private $database;

    /** @var string */
    private $dbuser;

    /** @var string */
    private $password;

    public function __construct(string $user, string $database, string $dbuser, string $password)
    {
        $this->user = $user;
        $this->database = $database;
        $this->dbuser = $dbuser;
        $this->password = $password;
    }

    public function getName(): string {
      return 'v-add-database';
    }

    public function getRequestParams(): array {
      return [
        self::ARG_1 => $this->user,
        self::ARG_2 => $this->database,
        self::ARG_3 => $this->dbuser,
        self::ARG_4 => $this->password,
      ];
    }
}