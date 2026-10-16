<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Module;

use neto737\HestiaCP\Client;
use neto737\HestiaCP\Command\BackupUser;
use neto737\HestiaCP\Command\DeleteUserBackup;
use neto737\HestiaCP\Command\DeleteUserBackupExclusions;
use neto737\HestiaCP\Command\ListUserBackup;
use neto737\HestiaCP\Command\ListUserBackups;
use neto737\HestiaCP\Command\ListUserBackupExclusions;

class Backups extends Module {

	private string $user;

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
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listBackups(): array {
		return $this->client->send(new ListUserBackups($this->user));
	}

	/**
	 * This function of obtaining the list of backup parameters.
	 *
	 * @param string    $backup
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listBackup(string $backup): array {
		return $this->client->send(new ListUserBackup($this->user, $backup));
	}

	/**
	 * This function for obtaining the backup exclusion list
	 *
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listBackupExclusions(): array {
		return $this->client->send(new ListUserBackupExclusions($this->user));
	}
}
