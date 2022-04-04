<?php

use heliocg\HestiaCP\Command\Change\UserPassword;
use heliocg\HestiaCP\Command\Change\MailAccountPassword;
use heliocg\HestiaCP\Command\Change\WebDomainFtpPassword;
use heliocg\HestiaCP\Command\Change\WebDomainFtpPath;

require_once __DIR__ . '/../vendor/autoload.php';

class commandChangeTest extends \PHPUnit\Framework\TestCase {

	public function testUserPassword(): void {
		$command = new UserPassword('admin', 'pass');
		$this->assertSame('v-change-user-password', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'pass'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());
	}

	public function testMailAccountPassword(): void {
		$command = new MailAccountPassword('admin', 'domain', 'account', 'pass');
		$this->assertSame('v-change-mail-account-password', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain',
			'arg3' => 'account',
			'arg4' => 'pass'
		], $command->getRequestParams());
	}

	public function testWebDomainFtpPassword(): void {
		$command = new WebDomainFtpPassword('admin', 'domain', 'account', 'pass');
		$this->assertSame('v-change-web-domain-ftp-password', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain',
			'arg3' => 'account',
			'arg4' => 'pass'
		], $command->getRequestParams());
	}

	public function testWebDomainFtpPath(): void {
		$command = new WebDomainFtpPath('admin', 'domain', 'account', 'path');
		$this->assertSame('v-change-web-domain-ftp-path', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain',
			'arg3' => 'account',
			'arg4' => 'path'
		], $command->getRequestParams());
	}
}
