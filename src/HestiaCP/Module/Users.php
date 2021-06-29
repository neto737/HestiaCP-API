<?php

namespace neto737\HestiaCP\Module;

use Nette\Utils\ArrayHash;
use neto737\HestiaCP\Command\Add\User as AddUser;
use neto737\HestiaCP\Command\Change\UserPassword;
use neto737\HestiaCP\Command\Delete\User as DeleteUser;
use neto737\HestiaCP\Command\Lists\User;
use neto737\HestiaCP\Command\Lists\Users as ListsUsers;

class Users extends Module {

	/**
	 * @return ArrayHash[]
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function list(): array {
		return $this->client->send(new ListsUsers());
	}

	/**
	 * @param string $user
	 * @return ArrayHash
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function detail(string $user): ArrayHash {
		return $this->client->send(new User($user));
	}

	/**
	 * @param string $user
	 * @param string $password
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function changePassword(string $user, string $password): bool {
		return $this->client->send(new UserPassword($user, $password));
	}

	/**
	 * @param string $user
	 * @param string $password
	 * @param string $email
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function add(string $user, string $password, string $email): bool {
		return $this->client->send(new AddUser($user, $password, $email));
	}

	/**
	 * @param string $user
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function delete(string $user): bool {
		return $this->client->send(new DeleteUser($user));
	}
}
