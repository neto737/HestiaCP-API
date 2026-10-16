<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use neto737\HestiaCP\Command;

/**
 * The verb-namespace names from before the rename keep working until 3.0.
 */
class DeprecatedNamesTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @return iterable<string, array{class-string, class-string}>
	 */
	public static function names(): iterable {
		yield 'Add\Database' => [Command\Add\Database::class, Command\AddDatabase::class];
		yield 'Add\LetsEncryptDomain' => [Command\Add\LetsEncryptDomain::class, Command\AddLetsEncryptDomain::class];
		yield 'Add\MailAccount' => [Command\Add\MailAccount::class, Command\AddMailAccount::class];
		yield 'Add\MailDomain' => [Command\Add\MailDomain::class, Command\AddMailDomain::class];
		yield 'Add\User' => [Command\Add\User::class, Command\AddUser::class];
		yield 'Add\WebDomain' => [Command\Add\WebDomain::class, Command\AddWebDomain::class];
		yield 'Add\WebDomainFtp' => [Command\Add\WebDomainFtp::class, Command\AddWebDomainFtp::class];
		yield 'Backup\User' => [Command\Backup\User::class, Command\BackupUser::class];
		yield 'Backup\Users' => [Command\Backup\Users::class, Command\BackupUsers::class];
		yield 'Change\MailAccountPassword' => [Command\Change\MailAccountPassword::class, Command\ChangeMailAccountPassword::class];
		yield 'Change\UserPassword' => [Command\Change\UserPassword::class, Command\ChangeUserPassword::class];
		yield 'Change\WebDomainFtpPassword' => [Command\Change\WebDomainFtpPassword::class, Command\ChangeWebDomainFtpPassword::class];
		yield 'Change\WebDomainFtpPath' => [Command\Change\WebDomainFtpPath::class, Command\ChangeWebDomainFtpPath::class];
		yield 'Delete\Database' => [Command\Delete\Database::class, Command\DeleteDatabase::class];
		yield 'Delete\Databases' => [Command\Delete\Databases::class, Command\DeleteDatabases::class];
		yield 'Delete\LetsEncryptDomain' => [Command\Delete\LetsEncryptDomain::class, Command\DeleteLetsEncryptDomain::class];
		yield 'Delete\MailAccount' => [Command\Delete\MailAccount::class, Command\DeleteMailAccount::class];
		yield 'Delete\MailDomain' => [Command\Delete\MailDomain::class, Command\DeleteMailDomain::class];
		yield 'Delete\User' => [Command\Delete\User::class, Command\DeleteUser::class];
		yield 'Delete\UserBackup' => [Command\Delete\UserBackup::class, Command\DeleteUserBackup::class];
		yield 'Delete\UserBackupExclusions' => [Command\Delete\UserBackupExclusions::class, Command\DeleteUserBackupExclusions::class];
		yield 'Delete\WebDomain' => [Command\Delete\WebDomain::class, Command\DeleteWebDomain::class];
		yield 'Delete\WebDomainFtp' => [Command\Delete\WebDomainFtp::class, Command\DeleteWebDomainFtp::class];
		yield 'Lists\Database' => [Command\Lists\Database::class, Command\ListDatabase::class];
		yield 'Lists\Databases' => [Command\Lists\Databases::class, Command\ListDatabases::class];
		yield 'Lists\MailAccounts' => [Command\Lists\MailAccounts::class, Command\ListMailAccounts::class];
		yield 'Lists\MailDomainDkim' => [Command\Lists\MailDomainDkim::class, Command\ListMailDomainDkim::class];
		yield 'Lists\MailDomainDkimDns' => [Command\Lists\MailDomainDkimDns::class, Command\ListMailDomainDkimDns::class];
		yield 'Lists\MailDomains' => [Command\Lists\MailDomains::class, Command\ListMailDomains::class];
		yield 'Lists\User' => [Command\Lists\User::class, Command\ListUser::class];
		yield 'Lists\UserBackup' => [Command\Lists\UserBackup::class, Command\ListUserBackup::class];
		yield 'Lists\UserBackupExclusions' => [Command\Lists\UserBackupExclusions::class, Command\ListUserBackupExclusions::class];
		yield 'Lists\UserBackups' => [Command\Lists\UserBackups::class, Command\ListUserBackups::class];
		yield 'Lists\Users' => [Command\Lists\Users::class, Command\ListUsers::class];
		yield 'Lists\WebDomains' => [Command\Lists\WebDomains::class, Command\ListWebDomains::class];
		yield 'Suspend\MailAccount' => [Command\Suspend\MailAccount::class, Command\SuspendMailAccount::class];
		yield 'Suspend\MailDomain' => [Command\Suspend\MailDomain::class, Command\SuspendMailDomain::class];
		yield 'Suspend\User' => [Command\Suspend\User::class, Command\SuspendUser::class];
		yield 'Suspend\WebDomain' => [Command\Suspend\WebDomain::class, Command\SuspendWebDomain::class];
		yield 'Unsuspend\MailAccount' => [Command\Unsuspend\MailAccount::class, Command\UnsuspendMailAccount::class];
		yield 'Unsuspend\MailDomain' => [Command\Unsuspend\MailDomain::class, Command\UnsuspendMailDomain::class];
		yield 'Unsuspend\User' => [Command\Unsuspend\User::class, Command\UnsuspendUser::class];
		yield 'Unsuspend\WebDomain' => [Command\Unsuspend\WebDomain::class, Command\UnsuspendWebDomain::class];
	}

	#[\PHPUnit\Framework\Attributes\DataProvider('names')]
	public function testOldNameExtendsNewName(string $old, string $new): void {
		$this->assertTrue(is_subclass_of($old, $new));
	}
}
