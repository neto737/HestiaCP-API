<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Command\User\BackupUsers;
use neto737\HestiaCP\Command\Backup\BackupUser;

class BackupTest extends \PHPUnit\Framework\TestCase {

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
}
