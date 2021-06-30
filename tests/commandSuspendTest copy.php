<?php

use neto737\HestiaCP\Command\Suspend\User;
use neto737\HestiaCP\Command\Suspend\WebDomain;
use neto737\HestiaCP\Command\Suspend\MailAccount;
use neto737\HestiaCP\Command\Suspend\MailDomain;

require_once __DIR__ . '/../vendor/autoload.php';

class commandSuspendTest extends \PHPUnit\Framework\TestCase {

	public function testUser(): void {
		$command = new User('admin');
		$this->assertSame('v-suspend-user', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'no'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());
	}

	public function testWebDomain(): void {
		$command = new WebDomain('admin', 'domain.com');
		$this->assertSame('v-suspend-web-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
            'arg3' => 'no'
		], $command->getRequestParams());
	}

	public function testMailAccount(): void {
		$command = new MailAccount('admin', 'domain.com', 'account');
		$this->assertSame('v-suspend-mail-account', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account'
		], $command->getRequestParams());
	}

	public function testMailDomain(): void {
		$command = new MailDomain('admin', 'domain.com');
		$this->assertSame('v-suspend-mail-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com'
		], $command->getRequestParams());
	}
}
