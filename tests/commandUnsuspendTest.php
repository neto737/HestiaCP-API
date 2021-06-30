<?php

use neto737\HestiaCP\Command\Unsuspend\User;
use neto737\HestiaCP\Command\Unsuspend\WebDomain;
use neto737\HestiaCP\Command\Unsuspend\MailAccount;
use neto737\HestiaCP\Command\Unsuspend\MailDomain;

require_once __DIR__ . '/../vendor/autoload.php';

class commandUnsuspendTest extends \PHPUnit\Framework\TestCase {

	public function testUser(): void {
		$command = new User('admin');
		$this->assertSame('v-unsuspend-user', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'no'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());
	}

	public function testWebDomain(): void {
		$command = new WebDomain('admin', 'domain.com');
		$this->assertSame('v-unsuspend-web-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
            'arg3' => 'no'
		], $command->getRequestParams());
	}

	public function testMailAccount(): void {
		$command = new MailAccount('admin', 'domain.com', 'account');
		$this->assertSame('v-unsuspend-mail-account', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account'
		], $command->getRequestParams());
	}

	public function testMailDomain(): void {
		$command = new MailDomain('admin', 'domain.com');
		$this->assertSame('v-unsuspend-mail-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com'
		], $command->getRequestParams());
	}
}
