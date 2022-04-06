<?php

namespace neto737\HestiaCP\Module;

use neto737\HestiaCP\Client;

use neto737\HestiaCP\Command\Add\Database as AddDatabase;
use neto737\HestiaCP\Command\Delete\Database as DeleteDatabase;

class Databases extends Module {

    /** @var string */
	private $user;

    public function __construct(Client $client, string $user) {
		parent::__construct($client);
		$this->user = $user;
	}

    /**
     * The function is used for creating a new database.
     * 
     * @param string    $database
     * @param string    $dbuser
     * @param string    $dbpass
     * @param string    $type
     * @return bool
     * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
     */
    public function add(string $database, string $dbuser, string $dbpass, string $type = 'mysql'): bool {
        return $this->client->send(new AddDatabase($this->user, $database, $dbuser, $dbpass, $type));
    }

    /**
     * The function is used for delete a specified database.
     * 
     * @param string    $database
     * @return bool
     * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
     */
    public function delete(string $database) {
        return $this->client->send(new DeleteDatabase($this->user, $database));
    }
}