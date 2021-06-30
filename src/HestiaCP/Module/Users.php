<?php

namespace neto737\HestiaCP\Module;

use Nette\Utils\ArrayHash;
use neto737\HestiaCP\Command\Add\User as AddUser;
use neto737\HestiaCP\Command\Change\UserPassword;
use neto737\HestiaCP\Command\Suspend\User as SuspendUser;
use neto737\HestiaCP\Command\Unsuspend\User  as UnsuspendUser;
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
	 * @param string 	  $user
	 * @param string 	  $password
	 * @param string 	  $email
	 * @param string|null $package
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function add(string $user, string $password, string $email, string $package = null): bool {
		return $this->client->send(new AddUser($user, $password, $email, $package));
	}

	/**
	 * @param string $user
	 * @param bool 	 $restart
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function suspend(string $user, bool $restart = false): bool {
		return $this->client->send(new SuspendUser($user, $restart));
	}

	/**
	 * @param string $user
	 * @param bool $restart
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function unsuspend(string $user, bool $restart = false): bool {
		return $this->client->send(new UnsuspendUser($user, $restart));
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
