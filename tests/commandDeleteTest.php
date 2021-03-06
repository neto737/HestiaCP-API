<?php

use neto737\HestiaCP\Command\Delete\User;
use neto737\HestiaCP\Command\Delete\WebDomain;
use neto737\HestiaCP\Command\Delete\WebDomainFtp;
use neto737\HestiaCP\Command\Delete\LetsEncryptDomain;
use neto737\HestiaCP\Command\Delete\MailAccount;
use neto737\HestiaCP\Command\Delete\MailDomain;
use neto737\HestiaCP\Command\Delete\UserBackup;
use neto737\HestiaCP\Command\Delete\UserBackupExclusions;
use neto737\HestiaCP\Command\Delete\Database;
use neto737\HestiaCP\Command\Delete\Databases;

require_once __DIR__ . '/../vendor/autoload.php';

class commandDeleteTest extends \PHPUnit\Framework\TestCase {

	public function testUser(): void {
		$command = new User('admin');
		$this->assertSame('v-delete-user', $command->getName());
		$this->assertSame(['arg1' => 'admin'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\InvalidResponseException::class);
		$command->process();
	}

	public function testWebDomain(): void {
		$command = new WebDomain('admin', 'domain.com');
		$this->assertSame('v-delete-web-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\InvalidResponseException::class);
		$command->process();
	}

	public function testWebDomainFtp(): void {
		$command = new WebDomainFtp('admin', 'domain.com', 'account');
		$this->assertSame('v-delete-web-domain-ftp', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\InvalidResponseException::class);
		$command->process();
	}

	public function testLEDomain(): void {
		$command = new LetsEncryptDomain('admin', 'domain.com');
		$this->assertSame('v-delete-letsencrypt-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'no'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\InvalidResponseException::class);
		$command->process();
	}

	public function testMailAccount(): void {
		$command = new MailAccount('admin', 'domain.com', 'account');
		$this->assertSame('v-delete-mail-account', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\InvalidResponseException::class);
		$command->process();
	}

	public function testMailDomain(): void {
		$command = new MailDomain('admin', 'domain.com');
		$this->assertSame('v-delete-mail-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\InvalidResponseException::class);
		$command->process();
	}

	public function testUserBackup(): void {
		$command = new UserBackup('admin', 'admin.2021-10-07_05-15-09.tar');
		$this->assertSame('v-delete-user-backup', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'admin.2021-10-07_05-15-09.tar'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\InvalidResponseException::class);
		$command->process();
	}

	public function testUserBackupExclusions(): void {
		$command = new UserBackupExclusions('admin');
		$this->assertSame('v-delete-user-backup-exclusions', $command->getName());
		$this->assertSame([
			'arg1' => 'admin'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\InvalidResponseException::class);
		$command->process();
	}

	public function testDatabase(): void {
		$command = new Database('admin', 'admin_test');
		$this->assertSame('v-delete-database', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'admin_test'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\InvalidResponseException::class);
		$command->process();
	}

	public function testDatabases(): void {
		$command = new Databases('admin');
		$this->assertSame('v-delete-databases', $command->getName());
		$this->assertSame([
			'arg1' => 'admin'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\InvalidResponseException::class);
		$command->process();
	}
}
