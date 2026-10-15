<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use neto737\HestiaCP\Command\Backup\Users;
use neto737\HestiaCP\Command\Backup\User;

class BackupTest extends \PHPUnit\Framework\TestCase {

	public function testBackupUsers(): void {
		$command = new Users();
		$this->assertSame('v-backup-users', $command->getName());
		$this->assertSame([], $command->getRequestParams());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
	}

	public function testBackupUser(): void {
		$command = new User('admin');
		$this->assertSame('v-backup-user', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'yes'], $command->getRequestParams());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
	}
}
