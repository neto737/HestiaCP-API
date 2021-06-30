<?php

use neto737\HestiaCP\Command\Lists\User;
use neto737\HestiaCP\Command\Lists\Users;
use neto737\HestiaCP\Command\Lists\WebDomains;
use neto737\HestiaCP\Command\Lists\MailDomains;
use neto737\HestiaCP\Command\Lists\MailDomainDkim;
use neto737\HestiaCP\Command\Lists\MailDomainDkimDns;
use neto737\HestiaCP\Command\Lists\MailAccounts;

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
}
