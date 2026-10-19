<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Command\User\DeleteUser;
use neto737\HestiaCP\Command\Web\DeleteWebDomain;
use neto737\HestiaCP\Command\Web\DeleteWebDomainFtp;
use neto737\HestiaCP\Command\Web\DeleteLetsEncryptDomain;
use neto737\HestiaCP\Command\Mail\DeleteMailAccount;
use neto737\HestiaCP\Command\Mail\DeleteMailDomain;
use neto737\HestiaCP\Command\Backup\DeleteUserBackup;
use neto737\HestiaCP\Command\Backup\DeleteUserBackupExclusions;
use neto737\HestiaCP\Command\Database\DeleteDatabase;
use neto737\HestiaCP\Command\Database\DeleteDatabases;

class DeleteTest extends \PHPUnit\Framework\TestCase {

	public function testUser(): void {
		$command = new DeleteUser('admin');
		$this->assertSame('v-delete-user', $command->getName());
		$this->assertSame(['arg1' => 'admin'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testWebDomain(): void {
		$command = new DeleteWebDomain('admin', 'domain.com');
		$this->assertSame('v-delete-web-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testWebDomainFtp(): void {
		$command = new DeleteWebDomainFtp('admin', 'domain.com', 'account');
		$this->assertSame('v-delete-web-domain-ftp', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testLEDomain(): void {
		$command = new DeleteLetsEncryptDomain('admin', 'domain.com');
		$this->assertSame('v-delete-letsencrypt-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'no'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testMailAccount(): void {
		$command = new DeleteMailAccount('admin', 'domain.com', 'account');
		$this->assertSame('v-delete-mail-account', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testMailDomain(): void {
		$command = new DeleteMailDomain('admin', 'domain.com');
		$this->assertSame('v-delete-mail-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testUserBackup(): void {
		$command = new DeleteUserBackup('admin', 'admin.2021-10-07_05-15-09.tar');
		$this->assertSame('v-delete-user-backup', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'admin.2021-10-07_05-15-09.tar'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testUserBackupExclusions(): void {
		$command = new DeleteUserBackupExclusions('admin');
		$this->assertSame('v-delete-user-backup-exclusions', $command->getName());
		$this->assertSame([
			'arg1' => 'admin'
		], $command->getRequestParams());

		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'WEB'
		], (new DeleteUserBackupExclusions('admin', 'WEB'))->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testDatabase(): void {
		$command = new DeleteDatabase('admin', 'admin_test');
		$this->assertSame('v-delete-database', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'admin_test'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testDatabases(): void {
		$command = new DeleteDatabases('admin');
		$this->assertSame('v-delete-databases', $command->getName());
		$this->assertSame([
			'arg1' => 'admin'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}
}
