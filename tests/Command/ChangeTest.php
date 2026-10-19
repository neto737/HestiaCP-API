<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Command\User\ChangeUserPassword;
use neto737\HestiaCP\Command\Mail\ChangeMailAccountPassword;
use neto737\HestiaCP\Command\Web\ChangeWebDomainFtpPassword;
use neto737\HestiaCP\Command\Web\ChangeWebDomainFtpPath;
use neto737\HestiaCP\Command\Database\ChangeDatabasePassword;
use neto737\HestiaCP\Command\User\ChangeUserContact;
use neto737\HestiaCP\Command\User\ChangeUserPackage;

class ChangeTest extends \PHPUnit\Framework\TestCase {

	public function testUserPassword(): void {
		$command = new ChangeUserPassword('admin', 'pass');
		$this->assertSame('v-change-user-password', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'pass'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testMailAccountPassword(): void {
		$command = new ChangeMailAccountPassword('admin', 'domain', 'account', 'pass');
		$this->assertSame('v-change-mail-account-password', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain',
			'arg3' => 'account',
			'arg4' => 'pass'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testWebDomainFtpPassword(): void {
		$command = new ChangeWebDomainFtpPassword('admin', 'domain', 'account', 'pass');
		$this->assertSame('v-change-web-domain-ftp-password', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain',
			'arg3' => 'account',
			'arg4' => 'pass'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testWebDomainFtpPath(): void {
		$command = new ChangeWebDomainFtpPath('admin', 'domain', 'account', 'path');
		$this->assertSame('v-change-web-domain-ftp-path', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain',
			'arg3' => 'account',
			'arg4' => 'path'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testDatabasePassword(): void {
		$command = new ChangeDatabasePassword('admin', 'admin_db', 'pass');
		$this->assertSame('v-change-database-password', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'admin_db',
			'arg3' => 'pass'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testUserContact(): void {
		$command = new ChangeUserContact('admin', 'admin@example.com');
		$this->assertSame('v-change-user-contact', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'admin@example.com'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testUserPackage(): void {
		$command = new ChangeUserPackage('admin', 'gold');
		$this->assertSame('v-change-user-package', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'gold',
			'arg3' => 'no'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}
}
