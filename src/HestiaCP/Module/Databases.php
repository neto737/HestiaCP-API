<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Module;

use neto737\HestiaCP\Client;
use neto737\HestiaCP\Command\Database\AddDatabase;
use neto737\HestiaCP\Command\Database\ChangeDatabasePassword;
use neto737\HestiaCP\Command\Database\DeleteDatabase;
use neto737\HestiaCP\Command\Database\DeleteDatabases;
use neto737\HestiaCP\Command\Database\ListDatabase;
use neto737\HestiaCP\Command\Database\ListDatabases;
use neto737\HestiaCP\Command\Database\SuspendDatabase;
use neto737\HestiaCP\Command\Database\UnsuspendDatabase;

class Databases extends Module {

	private string $user;

	public function __construct(Client $client, string $user) {
		parent::__construct($client);
		$this->user = $user;
	}

	/**
	 * This function is used for creating a new database.
	 *
	 * @param string    $database
	 * @param string    $dbuser
	 * @param string    $dbpass
	 * @param string    $type
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function add(string $database, string $dbuser, #[\SensitiveParameter] string $dbpass, string $type = 'mysql'): bool {
		return $this->client->send(new AddDatabase($this->user, $database, $dbuser, $dbpass, $type));
	}

	/**
	 * This function changes the password of a database's user.
	 *
	 * @param string    $database
	 * @param string    $dbpass
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changePassword(string $database, #[\SensitiveParameter] string $dbpass): bool {
		return $this->client->send(new ChangeDatabasePassword($this->user, $database, $dbpass));
	}

	/**
	 * This function suspends a database.
	 *
	 * @param string    $database
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function suspend(string $database): bool {
		return $this->client->send(new SuspendDatabase($this->user, $database));
	}

	/**
	 * This function unsuspends a database.
	 *
	 * @param string    $database
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function unsuspend(string $database): bool {
		return $this->client->send(new UnsuspendDatabase($this->user, $database));
	}

	/**
	 * This function is used for delete a specified database.
	 *
	 * @param string    $database
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function delete(string $database): bool {
		return $this->client->send(new DeleteDatabase($this->user, $database));
	}

	/**
	 * This function deletes all user databases.
	 *
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDatabases(): bool {
		return $this->client->send(new DeleteDatabases($this->user));
	}

	/**
	 * This function for obtaining of all database's parameters.
	 *
	 * @param string    $database
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listDatabase(string $database): array {
		return $this->client->send(new ListDatabase($this->user, $database));
	}

	/**
	 * This function for obtaining the list of all user's databases.
	 *
	 * @return array<array<mixed>>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listDatabases(): array {
		return $this->client->send(new ListDatabases($this->user));
	}
}
