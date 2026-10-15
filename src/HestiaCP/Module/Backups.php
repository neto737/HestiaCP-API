<?php

namespace neto737\HestiaCP\Module;

use neto737\HestiaCP\Client;
use neto737\HestiaCP\Command\Backup\User as BackupUser;
use neto737\HestiaCP\Command\Delete\UserBackup as DeleteUserBackup;
use neto737\HestiaCP\Command\Delete\UserBackupExclusions as DeleteUserBackupExclusions;
use neto737\HestiaCP\Command\Lists\UserBackup as ListsUserBackup;
use neto737\HestiaCP\Command\Lists\UserBackups;
use neto737\HestiaCP\Command\Lists\UserBackupExclusions as ListsUserBackupExclusions;

class Backups extends Module {

	/** @var string */
	private $user;

	public function __construct(Client $client, string $user) {
		parent::__construct($client);
		$this->user = $user;
	}

	/**
	 * This call is used for backing up user with all its domains and databases.
	 *
	 * @param bool  $notify
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function backup(bool $notify = true): bool {
		return $this->client->send(new BackupUser($this->user, $notify));
	}

	/**
	 * This function deletes user backup.
	 *
	 * @param string    $backup
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function delete(string $backup): bool {
		return $this->client->send(new DeleteUserBackup($this->user, $backup));
	}

	/**
	 * This function for deleting backup exclusion
	 *
	 * @param string|null $system Exclusion type to delete (WEB, DNS, MAIL, DB, CRON or USER). Deletes all of them when not set.
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteExclusions(?string $system = null): bool {
		return $this->client->send(new DeleteUserBackupExclusions($this->user, $system));
	}

	/**
	 * This function for obtaining the list of available user backups.
	 *
	 * @return array
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listBackups(): array {
		return $this->client->send(new UserBackups($this->user));
	}

	/**
	 * This function of obtaining the list of backup parameters.
	 *
	 * @param string    $backup
	 * @return array
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listBackup(string $backup): array {
		return $this->client->send(new ListsUserBackup($this->user, $backup));
	}

	/**
	 * This function for obtaining the backup exclusion list
	 *
	 * @return array
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listBackupExclusions(): array {
		return $this->client->send(new ListsUserBackupExclusions($this->user));
	}
}
