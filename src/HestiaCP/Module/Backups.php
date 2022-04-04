<?php

namespace heliocg\HestiaCP\Module;

use Nette\Utils\ArrayHash;
use heliocg\HestiaCP\Client;

use heliocg\HestiaCP\Command\Backup\User as BackupUser;
use heliocg\HestiaCP\Command\Delete\UserBackup as DeleteUserBackup;
use heliocg\HestiaCP\Command\Delete\UserBackupExclusions as DeleteUserBackupExclusions;
use heliocg\HestiaCP\Command\Lists\UserBackup as ListsUserBackup;
use heliocg\HestiaCP\Command\Lists\UserBackups;
use heliocg\HestiaCP\Command\Lists\UserBackupExclusions as ListsUserBackupExclusions;

class Backups extends Module {

    /** @var string */
	private $user;

    public function __construct(Client $client, string $user) {
		parent::__construct($client);
		$this->user = $user;
	}

    /**
     * The call is used for backing up user with all its domains and databases.
     * 
     * @param bool  $notify
     * @return string
     * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
     */
    public function backup(bool $notify = true): string {
        return $this->client->send(new BackupUser($this->user, $notify));
    }

    /**
     * The function deletes user backup.
     * 
     * @param string    $backup
     * @return bool
     * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
     */
    public function delete(string $backup): bool {
        return $this->client->send(new DeleteUserBackup($this->user, $backup));
    }

    /**
     * The function for deleting backup exclusion
     * 
     * @return bool
     * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
     */
    public function deleteExclusions() {
        return $this->client->send(new DeleteUserBackupExclusions($this->user));
    }

    /**
     * The function for obtaining the list of available user backups.
     * 
     * @return array
     * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
     */
    public function listBackups(): array {
        return $this->client->send(new UserBackups($this->user));
    }

    /**
     * The function of obtaining the list of backup parameters.
     * 
     * @param string    $backup
     * @return ArrayHash
     * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
     */
    public function listBackup(string $backup): ArrayHash {
        return $this->client->send(new ListsUserBackup($this->user, $backup));
    }

    /**
     * The function for obtaining the backup exclusion list
     * 
     * @return array
     * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
     */
    public function listBackupExclusions(): array {
        return $this->client->send(new ListsUserBackupExclusions($this->user));
    }
}