<?php

use neto737\HestiaCP\Command\Add\User;
use neto737\HestiaCP\Command\Add\WebDomain;
use neto737\HestiaCP\Command\Add\WebDomainFtp;
use neto737\HestiaCP\Command\Add\LetsEncryptDomain;
use neto737\HestiaCP\Command\Add\MailAccount;
use neto737\HestiaCP\Command\Add\MailDomain;
use neto737\HestiaCP\Command\Add\Database;

require_once __DIR__ . '/../vendor/autoload.php';

class commandAddTest extends \PHPUnit\Framework\TestCase {

	public function testUser(): void {
		$command = new User('admin', 'pass', 'mail@domain.com');
		$this->assertSame('v-add-user', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'pass', 'arg3' => 'mail@domain.com', 'arg4' => null], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\InvalidResponseException::class);
		$command->process();
	}

	public function testWebDomain(): void {
		$command = new WebDomain('admin', 'domain.com');
		$this->assertSame('v-add-web-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => '',
			'arg4' => 'no',
			'arg5' => 'none',
			'arg6' => ''
		], $command->getRequestParams());
	}

	public function testWebDomainFtp(): void {
		$command = new WebDomainFtp('admin', 'domain.com', 'account', 'pass');
		$this->assertSame('v-add-web-domain-ftp', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account',
			'arg4' => 'pass',
			'arg5' => ''
		], $command->getRequestParams());
	}

	public function testLEDomain(): void {
		$command = new LetsEncryptDomain('admin', 'domain.com');
		$this->assertSame('v-add-letsencrypt-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => '',
			'arg4' => 'no'
		], $command->getRequestParams());
	}

	public function testMailAccount(): void {
		$command = new MailAccount('admin', 'domain.com', 'account', 'pass');
		$this->assertSame('v-add-mail-account', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account',
			'arg4' => 'pass'
		], $command->getRequestParams());
	}

	public function testMailDomain(): void {
		$command = new MailDomain('admin', 'domain.com');
		$this->assertSame('v-add-mail-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'yes',
			'arg4' => 'yes',
			'arg5' => 'yes'
		], $command->getRequestParams());
	}

	public function testDatabase(): void {
		$command = new Database('admin', 'database', 'user', 'pass', 'mysql');
		$this->assertSame('v-add-database', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'database',
			'arg3' => 'user',
			'arg4' => 'pass',
			'arg5' => 'mysql'
		], $command->getRequestParams());
	}
}
