<?php
require_once __DIR__ . '/../vendor/autoload.php';

class commandAddTest extends \PHPUnit\Framework\TestCase
{

	public function testUser(): void
	{
		$command = new \neto737\HestiaCP\Command\Add\User('admin', 'pass', 'mail@domain.com');
		$this->assertSame('v-add-user', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'pass', 'arg3' => 'mail@domain.com'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\InvalidResponseException::class);
		$command->process();
	}

	public function testWebDomain(): void
	{
		$command = new \neto737\HestiaCP\Command\Add\WebDomain('admin', 'domain.com');
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

	public function testWebDomainFtp(): void
	{
		$command = new \neto737\HestiaCP\Command\Add\WebDomainFtp('admin', 'domain.com', 'account', 'pass');
		$this->assertSame('v-add-web-domain-ftp', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account',
			'arg4' => 'pass',
			'arg5' => ''
		], $command->getRequestParams());
	}

	public function testLEDomain(): void
	{
		$command = new \neto737\HestiaCP\Command\Add\LetsEncryptDomain('admin', 'domain.com');
		$this->assertSame('v-add-letsencrypt-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => '',
			'arg4' => 'no'
		], $command->getRequestParams());
	}

	public function testMailAccount(): void
	{
		$command = new \neto737\HestiaCP\Command\Add\MailAccount('admin', 'domain.com', 'account', 'pass');
		$this->assertSame('v-add-mail-account', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account',
			'arg4' => 'pass'
		], $command->getRequestParams());
	}

	public function testMailDomain(): void
	{
		$command = new \neto737\HestiaCP\Command\Add\MailDomain('admin', 'domain.com');
		$this->assertSame('v-add-mail-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'yes',
			'arg4' => 'yes',
			'arg5' => 'yes'
		], $command->getRequestParams());
	}


}
