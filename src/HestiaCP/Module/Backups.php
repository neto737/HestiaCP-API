<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Module;

use neto737\HestiaCP\Client;
use neto737\HestiaCP\Command\Backup\BackupUser;
use neto737\HestiaCP\Command\Backup\DeleteUserBackup;
use neto737\HestiaCP\Command\Backup\DeleteUserBackupExclusions;
use neto737\HestiaCP\Command\Backup\ListUserBackup;
use neto737\HestiaCP\Command\Backup\ListUserBackups;
use neto737\HestiaCP\Command\Backup\ListUserBackupExclusions;
use neto737\HestiaCP\Command\Backup\UpdateUserBackupExclusions;
use neto737\HestiaCP\Command\MakeTmpFile;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;

class Backups extends Module {

	private const array EXCLUSION_TYPES = ['WEB', 'DNS', 'MAIL', 'DB', 'CRON', 'USER'];

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
	 * Replaces the backup exclusion list. Types left out are cleared.
	 *
	 * Entries per type: WEB 'domain' or 'domain:path', MAIL 'domain' or 'domain:account', DNS a domain,
	 * DB a database name, USER a directory name, and '*' for everything of that type (the only entry CRON accepts).
	 *
	 * It sends two requests: v-make-tmp-file writes the list to /tmp on the server, where
	 * v-update-user-backup-exclusions reads it.
	 *
	 * @param array<string, list<string>> $exclusions e.g. ['WEB' => ['example.com:public_html/cache'], 'DB' => ['*']]
	 * @return bool
	 * @throws InvalidArgumentException
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function updateExclusions(array $exclusions): bool {
		foreach (array_keys($exclusions) as $type) {
			if (!in_array($type, self::EXCLUSION_TYPES, true)) {
				throw new InvalidArgumentException("Unknown exclusion type '$type', expected WEB, DNS, MAIL, DB, CRON or USER");
			}
		}

		$lines = [];
		foreach (self::EXCLUSION_TYPES as $type) {
			$entries = $exclusions[$type] ?? [];
			foreach ($entries as $entry) {
				// the list holds one TYPE='entry,entry' line per type
				if ($entry === '' || preg_match("~[',\s]~", $entry)) {
					throw new InvalidArgumentException("Invalid $type exclusion '$entry', an entry can't be empty or contain a quote, a comma or whitespace");
				}
			}
			$lines[] = $type . "='" . implode(',', $entries) . "'";
		}

		$file = new MakeTmpFile('hestiacp-api-backup-exclusions-' . $this->user, implode("\n", $lines));
		$this->client->send($file);

		return $this->client->send(new UpdateUserBackupExclusions($this->user, $file->getPath()));
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
