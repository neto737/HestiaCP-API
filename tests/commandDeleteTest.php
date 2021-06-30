<?php

use neto737\HestiaCP\Command\Delete\User;
use neto737\HestiaCP\Command\Delete\WebDomain;
use neto737\HestiaCP\Command\Delete\WebDomainFtp;
use neto737\HestiaCP\Command\Delete\LetsEncryptDomain;
use neto737\HestiaCP\Command\Delete\MailAccount;
use neto737\HestiaCP\Command\Delete\MailDomain;

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
}
