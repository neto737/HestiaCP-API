<?php

use heliocg\HestiaCP\Command\Delete\User;
use heliocg\HestiaCP\Command\Delete\WebDomain;
use heliocg\HestiaCP\Command\Delete\WebDomainFtp;
use heliocg\HestiaCP\Command\Delete\LetsEncryptDomain;
use heliocg\HestiaCP\Command\Delete\MailAccount;
use heliocg\HestiaCP\Command\Delete\MailDomain;
use heliocg\HestiaCP\Command\Delete\UserBackup;
use heliocg\HestiaCP\Command\Delete\UserBackupExclusions;

require_once __DIR__ . '/../vendor/autoload.php';

class commandDeleteTest extends \PHPUnit\Framework\TestCase {

	public function testUser(): void {
		$command = new User('admin');
		$this->assertSame('v-delete-user', $command->getName());
		$this->assertSame(['arg1' => 'admin'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());
	}

	public function testWebDomain(): void {
		$command = new WebDomain('admin', 'domain.com');
		$this->assertSame('v-delete-web-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com'
		], $command->getRequestParams());
	}

	public function testWebDomainFtp(): void {
		$command = new WebDomainFtp('admin', 'domain.com', 'account');
		$this->assertSame('v-delete-web-domain-ftp', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account'
		], $command->getRequestParams());
	}

	public function testLEDomain(): void {
		$command = new LetsEncryptDomain('admin', 'domain.com');
		$this->assertSame('v-delete-letsencrypt-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'no'
		], $command->getRequestParams());
	}

	public function testMailAccount(): void {
		$command = new MailAccount('admin', 'domain.com', 'account');
		$this->assertSame('v-delete-mail-account', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account'
		], $command->getRequestParams());
	}

	public function testMailDomain(): void {
		$command = new MailDomain('admin', 'domain.com');
		$this->assertSame('v-delete-mail-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com'
		], $command->getRequestParams());
	}

	public function testUserBackup(): void {
		$command = new UserBackup('admin', 'admin.2021-10-07_05-15-09.tar');
		$this->assertSame('v-delete-user-backup', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'admin.2021-10-07_05-15-09.tar'
		], $command->getRequestParams());
	}

	public function testUserBackupExclusions(): void {
		$command = new UserBackupExclusions('admin');
		$this->assertSame('v-delete-user-backup-exclusions', $command->getName());
		$this->assertSame([
			'arg1' => 'admin'
		], $command->getRequestParams());
	}
}
