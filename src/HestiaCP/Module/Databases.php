<?php

namespace neto737\HestiaCP\Module;

use neto737\HestiaCP\Client;
use neto737\HestiaCP\Command\Add\Database;
use neto737\HestiaCP\Module\Module;

class Databases extends Module {

    /** @var string */
	private $user;

	public function __construct(Client $client, string $user) {
		parent::__construct($client);
		$this->user = $user;
	}

    public function addDatabase(string $database, string $dbuser, string $password){
        return $this->client->send(new Database($this->user, $database, $dbuser, $password));
    }
}