<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Command\Backup\BackupUser;
use neto737\HestiaCP\Command\Backup\DeleteUserBackup;
use neto737\HestiaCP\Command\Backup\DeleteUserBackupExclusions;
use neto737\HestiaCP\Command\Backup\ListUserBackup;
use neto737\HestiaCP\Command\Backup\ListUserBackupExclusions;
use neto737\HestiaCP\Command\Backup\ListUserBackups;
use neto737\HestiaCP\Command\User\BackupUsers;

class BackupTest extends \PHPUnit\Framework\TestCase {

	public function testListUserBackups(): void {
		$command = new ListUserBackups('admin');
		$this->assertSame('v-list-user-backups', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testListUserBackup(): void {
		$command = new ListUserBackup('admin', 'admin.2021-10-07_05-15-09.tar');
		$this->assertSame('v-list-user-backup', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'admin.2021-10-07_05-15-09.tar', 'arg3' => 'json'], $command->getRequestParams());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testListUserBackupExclusions(): void {
		$command = new ListUserBackupExclusions('admin');
		$this->assertSame('v-list-user-backup-exclusions', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testBackupUsers(): void {
		$command = new BackupUsers();
		$this->assertSame('v-backup-users', $command->getName());
		$this->assertSame([], $command->getRequestParams());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testBackupUser(): void {
		$command = new BackupUser('admin');
		$this->assertSame('v-backup-user', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'yes'], $command->getRequestParams());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testDeleteUserBackup(): void {
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

	public function testDeleteUserBackupExclusions(): void {
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
}
