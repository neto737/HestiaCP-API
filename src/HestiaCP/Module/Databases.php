<?php

namespace heliocg\HestiaCP\Module;

use heliocg\HestiaCP\Client;
use heliocg\HestiaCP\Command\Add\Database;
use heliocg\HestiaCP\Module\Module;

class Databases extends Module {

    /** @var string */
	private $user;

	public function __construct(Client $client, string $user) {
		parent::__construct($client);
		$this->user = $user;
	}

    public function addDatabase(string $database, string $dbuser, string $password): bool{
        return $this->client->send(new Database($this->user, $database, $dbuser, $password));
    }
}