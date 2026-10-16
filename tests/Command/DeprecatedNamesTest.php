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
		yield 'Add\Database' => [Command\Add\Database::class, Command\Database\AddDatabase::class];
		yield 'Add\LetsEncryptDomain' => [Command\Add\LetsEncryptDomain::class, Command\Web\AddLetsEncryptDomain::class];
		yield 'Add\MailAccount' => [Command\Add\MailAccount::class, Command\Mail\AddMailAccount::class];
		yield 'Add\MailDomain' => [Command\Add\MailDomain::class, Command\Mail\AddMailDomain::class];
		yield 'Add\User' => [Command\Add\User::class, Command\User\AddUser::class];
		yield 'Add\WebDomain' => [Command\Add\WebDomain::class, Command\Web\AddWebDomain::class];
		yield 'Add\WebDomainFtp' => [Command\Add\WebDomainFtp::class, Command\Web\AddWebDomainFtp::class];
		yield 'Backup\User' => [Command\Backup\User::class, Command\Backup\BackupUser::class];
		yield 'Backup\Users' => [Command\Backup\Users::class, Command\User\BackupUsers::class];
		yield 'Change\MailAccountPassword' => [Command\Change\MailAccountPassword::class, Command\Mail\ChangeMailAccountPassword::class];
		yield 'Change\UserPassword' => [Command\Change\UserPassword::class, Command\User\ChangeUserPassword::class];
		yield 'Change\WebDomainFtpPassword' => [Command\Change\WebDomainFtpPassword::class, Command\Web\ChangeWebDomainFtpPassword::class];
		yield 'Change\WebDomainFtpPath' => [Command\Change\WebDomainFtpPath::class, Command\Web\ChangeWebDomainFtpPath::class];
		yield 'Delete\Database' => [Command\Delete\Database::class, Command\Database\DeleteDatabase::class];
		yield 'Delete\Databases' => [Command\Delete\Databases::class, Command\Database\DeleteDatabases::class];
		yield 'Delete\LetsEncryptDomain' => [Command\Delete\LetsEncryptDomain::class, Command\Web\DeleteLetsEncryptDomain::class];
		yield 'Delete\MailAccount' => [Command\Delete\MailAccount::class, Command\Mail\DeleteMailAccount::class];
		yield 'Delete\MailDomain' => [Command\Delete\MailDomain::class, Command\Mail\DeleteMailDomain::class];
		yield 'Delete\User' => [Command\Delete\User::class, Command\User\DeleteUser::class];
		yield 'Delete\UserBackup' => [Command\Delete\UserBackup::class, Command\Backup\DeleteUserBackup::class];
		yield 'Delete\UserBackupExclusions' => [Command\Delete\UserBackupExclusions::class, Command\Backup\DeleteUserBackupExclusions::class];
		yield 'Delete\WebDomain' => [Command\Delete\WebDomain::class, Command\Web\DeleteWebDomain::class];
		yield 'Delete\WebDomainFtp' => [Command\Delete\WebDomainFtp::class, Command\Web\DeleteWebDomainFtp::class];
		yield 'Lists\Database' => [Command\Lists\Database::class, Command\Database\ListDatabase::class];
		yield 'Lists\Databases' => [Command\Lists\Databases::class, Command\Database\ListDatabases::class];
		yield 'Lists\MailAccounts' => [Command\Lists\MailAccounts::class, Command\Mail\ListMailAccounts::class];
		yield 'Lists\MailDomainDkim' => [Command\Lists\MailDomainDkim::class, Command\Mail\ListMailDomainDkim::class];
		yield 'Lists\MailDomainDkimDns' => [Command\Lists\MailDomainDkimDns::class, Command\Mail\ListMailDomainDkimDns::class];
		yield 'Lists\MailDomains' => [Command\Lists\MailDomains::class, Command\Mail\ListMailDomains::class];
		yield 'Lists\User' => [Command\Lists\User::class, Command\User\ListUser::class];
		yield 'Lists\UserBackup' => [Command\Lists\UserBackup::class, Command\Backup\ListUserBackup::class];
		yield 'Lists\UserBackupExclusions' => [Command\Lists\UserBackupExclusions::class, Command\Backup\ListUserBackupExclusions::class];
		yield 'Lists\UserBackups' => [Command\Lists\UserBackups::class, Command\Backup\ListUserBackups::class];
		yield 'Lists\Users' => [Command\Lists\Users::class, Command\User\ListUsers::class];
		yield 'Lists\WebDomains' => [Command\Lists\WebDomains::class, Command\Web\ListWebDomains::class];
		yield 'Suspend\MailAccount' => [Command\Suspend\MailAccount::class, Command\Mail\SuspendMailAccount::class];
		yield 'Suspend\MailDomain' => [Command\Suspend\MailDomain::class, Command\Mail\SuspendMailDomain::class];
		yield 'Suspend\User' => [Command\Suspend\User::class, Command\User\SuspendUser::class];
		yield 'Suspend\WebDomain' => [Command\Suspend\WebDomain::class, Command\Web\SuspendWebDomain::class];
		yield 'Unsuspend\MailAccount' => [Command\Unsuspend\MailAccount::class, Command\Mail\UnsuspendMailAccount::class];
		yield 'Unsuspend\MailDomain' => [Command\Unsuspend\MailDomain::class, Command\Mail\UnsuspendMailDomain::class];
		yield 'Unsuspend\User' => [Command\Unsuspend\User::class, Command\User\UnsuspendUser::class];
		yield 'Unsuspend\WebDomain' => [Command\Unsuspend\WebDomain::class, Command\Web\UnsuspendWebDomain::class];
	}

	#[\PHPUnit\Framework\Attributes\DataProvider('names')]
	public function testOldNameExtendsNewName(string $old, string $new): void {
		$this->assertTrue(is_subclass_of($old, $new));
	}
}
