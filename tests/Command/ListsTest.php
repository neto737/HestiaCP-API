<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Command\User\ListUser;
use neto737\HestiaCP\Command\User\ListUsers;
use neto737\HestiaCP\Command\Web\ListWebDomains;
use neto737\HestiaCP\Command\Mail\ListMailDomains;
use neto737\HestiaCP\Command\Mail\ListMailDomainDkim;
use neto737\HestiaCP\Command\Mail\ListMailDomainDkimDns;
use neto737\HestiaCP\Command\Mail\ListMailAccounts;
use neto737\HestiaCP\Command\Backup\ListUserBackups;
use neto737\HestiaCP\Command\Backup\ListUserBackup;
use neto737\HestiaCP\Command\Backup\ListUserBackupExclusions;
use neto737\HestiaCP\Command\Database\ListDatabase;
use neto737\HestiaCP\Command\Database\ListDatabases;
use neto737\HestiaCP\Command\User\ListUserStats;
use neto737\HestiaCP\Command\Package\ListUserPackage;
use neto737\HestiaCP\Command\Package\ListUserPackages;

class ListsTest extends \PHPUnit\Framework\TestCase {

	public function testUser(): void {
		$command = new ListUser('admin');
		$this->assertSame('v-list-user', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertFalse($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testUsers(): void {
		$command = new ListUsers();
		$this->assertSame('v-list-users', $command->getName());
		$this->assertSame(['arg1' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testWebDomains(): void {
		$command = new ListWebDomains('admin');
		$this->assertSame('v-list-web-domains', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testMailDomains(): void {
		$command = new ListMailDomains('admin');
		$this->assertSame('v-list-mail-domains', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testMailDomainDkim(): void {
		$command = new ListMailDomainDkim('admin', 'domain');
		$this->assertSame('v-list-mail-domain-dkim', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'domain', 'arg3' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testMailDomainDkimDns(): void {
		$command = new ListMailDomainDkimDns('admin', 'domain');
		$this->assertSame('v-list-mail-domain-dkim-dns', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'domain', 'arg3' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testMailAccounts(): void {
		$command = new ListMailAccounts('admin', 'domain');
		$this->assertSame('v-list-mail-accounts', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'domain', 'arg3' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testUserBackups(): void {
		$command = new ListUserBackups('admin');
		$this->assertSame('v-list-user-backups', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testUserBackup(): void {
		$command = new ListUserBackup('admin', 'admin.2021-10-07_05-15-09.tar');
		$this->assertSame('v-list-user-backup', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'admin.2021-10-07_05-15-09.tar', 'arg3' => 'json'], $command->getRequestParams());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testUserBackupExclusions(): void {
		$command = new ListUserBackupExclusions('admin');
		$this->assertSame('v-list-user-backup-exclusions', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testListDatabase(): void {
		$command = new ListDatabase('admin', 'test');
		$this->assertSame('v-list-database', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'test', 'arg3' => 'json'], $command->getRequestParams());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testListDatabases(): void {
		$command = new ListDatabases('admin');
		$this->assertSame('v-list-databases', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testUserStats(): void {
		$command = new ListUserStats('admin');
		$this->assertSame('v-list-user-stats', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testUserPackages(): void {
		$command = new ListUserPackages();
		$this->assertSame('v-list-user-packages', $command->getName());
		$this->assertSame(['arg1' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testUserPackage(): void {
		$command = new ListUserPackage('default');
		$this->assertSame('v-list-user-package', $command->getName());
		$this->assertSame(['arg1' => 'default', 'arg2' => 'json'], $command->getRequestParams());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}
}
