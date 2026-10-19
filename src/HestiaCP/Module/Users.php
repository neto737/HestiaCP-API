<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Module;

use neto737\HestiaCP\Command\User\AddUser;
use neto737\HestiaCP\Command\User\ChangeUserContact;
use neto737\HestiaCP\Command\User\ChangeUserPackage;
use neto737\HestiaCP\Command\User\ChangeUserPassword;
use neto737\HestiaCP\Command\User\SuspendUser;
use neto737\HestiaCP\Command\User\UnsuspendUser;
use neto737\HestiaCP\Command\User\DeleteUser;
use neto737\HestiaCP\Command\User\ListUser;
use neto737\HestiaCP\Command\User\ListUsers;
use neto737\HestiaCP\Command\User\ListUserStats;
use neto737\HestiaCP\Command\User\BackupUsers;

class Users extends Module {

	/**
	 * This function to obtain the list of all system users.
	 *
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function list(): array {
		return $this->client->send(new ListUsers());
	}

	/**
	 * This function to obtain user parameters.
	 *
	 * @param string $user
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function detail(string $user): array {
		return $this->client->send(new ListUser($user));
	}

	/**
	 * This function obtains the monthly usage statistics of a user, keyed by date.
	 *
	 * @param string $user
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function stats(string $user): array {
		return $this->client->send(new ListUserStats($user));
	}

	/**
	 * This function changes user's password and updates RKEY value.
	 *
	 * @param string $user
	 * @param string $password
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changePassword(string $user, #[\SensitiveParameter] string $password): bool {
		return $this->client->send(new ChangeUserPassword($user, $password));
	}

	/**
	 * This function changes user's contact email.
	 *
	 * @param string $user
	 * @param string $email
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeContact(string $user, string $email): bool {
		return $this->client->send(new ChangeUserContact($user, $email));
	}

	/**
	 * This function changes user's hosting package.
	 *
	 * @param string $user
	 * @param string $package
	 * @param bool   $force   Change the package even if the user's current usage exceeds its limits
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changePackage(string $user, string $package, bool $force = false): bool {
		return $this->client->send(new ChangeUserPackage($user, $package, $force));
	}

	/**
	 * This function creates new user account.
	 *
	 * @param string 	  $user
	 * @param string 	  $password
	 * @param string 	  $email
	 * @param string|null $package
	 * @param string|null $name
	 * @param string|null $lastname
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function add(string $user, #[\SensitiveParameter] string $password, string $email, ?string $package = null, ?string $name = null, ?string $lastname = null): bool {
		return $this->client->send(new AddUser($user, $password, $email, $package, $name, $lastname));
	}

	/**
	 * This function suspends a certain user and all his objects.
	 *
	 * @param string $user
	 * @param bool 	 $restart
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function suspend(string $user, bool $restart = false): bool {
		return $this->client->send(new SuspendUser($user, $restart));
	}

	/**
	 * This function unsuspends user and all his objects.
	 *
	 * @param string $user
	 * @param bool $restart
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function unsuspend(string $user, bool $restart = false): bool {
		return $this->client->send(new UnsuspendUser($user, $restart));
	}

	/**
	 * This function deletes a certain user and all his resources such as domains, databases, cron jobs, etc.
	 *
	 * @param string $user
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function delete(string $user): bool {
		return $this->client->send(new DeleteUser($user));
	}

	/**
	 * This function backups all system users.
	 *
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function backup(): bool {
		return $this->client->send(new BackupUsers());
	}
}
