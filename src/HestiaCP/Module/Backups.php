<?php

namespace neto737\HestiaCP\Module;

use Nette\Utils\ArrayHash;
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
     * @return string
     * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
     */
    public function backup(bool $notify = true): string {
        return $this->client->send(new BackupUser($this->user, $notify));
    }

    /**
     * This function deletes user backup.
     * 
     * @param string    $backup
     * @return bool
     * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
     */
    public function delete(string $backup): bool {
        return $this->client->send(new DeleteUserBackup($this->user, $backup));
    }

    /**
     * This function for deleting backup exclusion
     * 
     * @return bool
     * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
     */
    public function deleteExclusions(): bool {
        return $this->client->send(new DeleteUserBackupExclusions($this->user));
    }

    /**
     * This function for obtaining the list of available user backups.
     * 
     * @return array
     * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
     */
    public function listBackups(): array {
        return $this->client->send(new UserBackups($this->user));
    }

    /**
     * This function of obtaining the list of backup parameters.
     * 
     * @param string    $backup
     * @return ArrayHash
     * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
     */
    public function listBackup(string $backup): ArrayHash {
        return $this->client->send(new ListsUserBackup($this->user, $backup));
    }

    /**
     * This function for obtaining the backup exclusion list
     * 
     * @return array
     * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
     */
    public function listBackupExclusions(): array {
        return $this->client->send(new ListsUserBackupExclusions($this->user));
    }
}