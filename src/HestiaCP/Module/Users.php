<?php

namespace heliocg\HestiaCP\Module;

use Nette\Utils\ArrayHash;
use heliocg\HestiaCP\Command\Add\User as AddUser;
use heliocg\HestiaCP\Command\Change\UserPassword;
use heliocg\HestiaCP\Command\Suspend\User as SuspendUser;
use heliocg\HestiaCP\Command\Unsuspend\User  as UnsuspendUser;
use heliocg\HestiaCP\Command\Delete\User as DeleteUser;
use heliocg\HestiaCP\Command\Lists\User;
use heliocg\HestiaCP\Command\Lists\Users as ListsUsers;
use heliocg\HestiaCP\Command\Backup\Users as BackupUsers;

class Users extends Module {

	/**
	 * The function to obtain the list of all system users.
	 * 
	 * @return array
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function list(): array {
		return $this->client->send(new ListsUsers);
	}

	/**
	 * The function to obtain user parameters.
	 * 
	 * @param string $user
	 * @return ArrayHash
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function detail(string $user): ArrayHash {
		return $this->client->send(new User($user));
	}

	/**
	 * The function changes user's password and updates RKEY value.
	 * 
	 * @param string $user
	 * @param string $password
	 * @return bool
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function changePassword(string $user, string $password): bool {
		return $this->client->send(new UserPassword($user, $password));
	}

	/**
	 * The function creates new user account.
	 * 
	 * @param string 	  $user
	 * @param string 	  $password
	 * @param string 	  $email
	 * @param string|null $package
	 * @return bool
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function add(string $user, string $password, string $email, string $package = null): bool {
		return $this->client->send(new AddUser($user, $password, $email, $package));
	}

	/**
	 * The function suspends a certain user and all his objects.
	 * 
	 * @param string $user
	 * @param bool 	 $restart
	 * @return bool
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function suspend(string $user, bool $restart = false): bool {
		return $this->client->send(new SuspendUser($user, $restart));
	}

	/**
	 * The function unsuspends user and all his objects.
	 * 
	 * @param string $user
	 * @param bool $restart
	 * @return bool
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function unsuspend(string $user, bool $restart = false): bool {
		return $this->client->send(new UnsuspendUser($user, $restart));
	}

	/**
	 * This function deletes a certain user and all his resources such as domains, databases, cron jobs, etc.
	 * 
	 * @param string $user
	 * @return bool
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function delete(string $user): bool {
		return $this->client->send(new DeleteUser($user));
	}

	/**
	 * The function backups all system users.
	 * 
	 * @return string
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function backup(): string {
		return $this->client->send(new BackupUsers);
	}
}
