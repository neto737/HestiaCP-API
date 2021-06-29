<?php
require_once __DIR__ . '/../vendor/autoload.php';

class commandDeleteTest extends \PHPUnit\Framework\TestCase
{

	public function testUser(): void
	{
		$command = new \neto737\HestiaCP\Command\Delete\User('admin');
		$this->assertSame('v-delete-user', $command->getName());
		$this->assertSame(['arg1' => 'admin'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());
	}

	public function testWebDomain(): void
	{
		$command = new \neto737\HestiaCP\Command\Delete\WebDomain('admin', 'domain.com');
		$this->assertSame('v-delete-web-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com'
		], $command->getRequestParams());
	}

	public function testWebDomainFtp(): void
	{
		$command = new \neto737\HestiaCP\Command\Delete\WebDomainFtp('admin', 'domain.com', 'account');
		$this->assertSame('v-delete-web-domain-ftp', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account'
		], $command->getRequestParams());
	}

	public function testLEDomain(): void
	{
		$command = new \neto737\HestiaCP\Command\Delete\LetsEncryptDomain('admin', 'domain.com');
		$this->assertSame('v-delete-letsencrypt-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'no'
		], $command->getRequestParams());
	}

	public function testMailAccount(): void
	{
		$command = new \neto737\HestiaCP\Command\Delete\MailAccount('admin', 'domain.com', 'account');
		$this->assertSame('v-delete-mail-account', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account'
		], $command->getRequestParams());
	}

	public function testMailDomain(): void
	{
		$command = new \neto737\HestiaCP\Command\Delete\MailDomain('admin', 'domain.com');
		$this->assertSame('v-delete-mail-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com'
		], $command->getRequestParams());
	}


}
