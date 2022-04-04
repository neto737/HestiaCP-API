<?php

use heliocg\HestiaCP\Command\Lists\User;
use heliocg\HestiaCP\Command\Lists\Users;
use heliocg\HestiaCP\Command\Lists\WebDomains;
use heliocg\HestiaCP\Command\Lists\MailDomains;
use heliocg\HestiaCP\Command\Lists\MailDomainDkim;
use heliocg\HestiaCP\Command\Lists\MailDomainDkimDns;
use heliocg\HestiaCP\Command\Lists\MailAccounts;
use heliocg\HestiaCP\Command\Lists\UserBackups;
use heliocg\HestiaCP\Command\Lists\UserBackup;
use heliocg\HestiaCP\Command\Lists\UserBackupExclusions;

require_once __DIR__ . '/../vendor/autoload.php';

class commandListTest extends \PHPUnit\Framework\TestCase {

	public function testUser(): void {
		$command = new User('admin');
		$this->assertSame('v-list-user', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertFalse($command->needReturnCode());

		$this->expectException(TypeError::class);
		$command->process();
	}

	public function testUsers(): void {
		$command = new Users();
		$this->assertSame('v-list-users', $command->getName());
		$this->assertSame(['arg1' => 'json'], $command->getRequestParams());

		$this->expectException(TypeError::class);
		$command->process();
	}

	public function testWebDomains(): void {
		$command = new WebDomains('admin');
		$this->assertSame('v-list-web-domains', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->expectException(TypeError::class);
		$command->process();
	}

	public function testMailDomains(): void {
		$command = new MailDomains('admin');
		$this->assertSame('v-list-mail-domains', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());
	}

	public function testMailDomainDkim(): void {
		$command = new MailDomainDkim('admin', 'domain');
		$this->assertSame('v-list-mail-domain-dkim', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'domain', 'arg3' => 'json'], $command->getRequestParams());
		$this->assertSame('', $command->getResponseText());
	}

	public function testMailDomainDkimDns(): void {
		$command = new MailDomainDkimDns('admin', 'domain');
		$this->assertSame('v-list-mail-domain-dkim-dns', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'domain', 'arg3' => 'json'], $command->getRequestParams());
		$this->assertSame('', $command->getResponseText());
	}

	public function testMailAccounts(): void {
		$command = new MailAccounts('admin', 'domain');
		$this->assertSame('v-list-mail-accounts', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'domain', 'arg3' => 'json'], $command->getRequestParams());
	}

	public function testUserBackups(): void {
		$command = new UserBackups('admin');
		$this->assertSame('v-list-user-backups', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());
	}

	public function testUserBackup(): void {
		$command = new UserBackup('admin', 'admin.2021-10-07_05-15-09.tar');
		$this->assertSame('v-list-user-backup', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'admin.2021-10-07_05-15-09.tar', 'arg3' => 'json'], $command->getRequestParams());
	}

	public function testUserBackupExclusions(): void {
		$command = new UserBackupExclusions('admin');
		$this->assertSame('v-list-user-backup-exclusions', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());
	}
}
