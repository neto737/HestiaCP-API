<?php

use neto737\HestiaCP\Command\Backup\Users;
use neto737\HestiaCP\Command\Backup\User;

require_once __DIR__ . '/../vendor/autoload.php';

class commandBackupTest extends \PHPUnit\Framework\TestCase {

    public function testBackupUsers(): void {
        $command = new Users;
        $this->assertSame('v-backup-users', $command->getName());
        $this->assertSame(['arg1' => 'json'], $command->getRequestParams());

		$this->expectException(TypeError::class);
		$command->process();
    }

    public function testBackupUser(): void {
        $command = new User('admin');
        $this->assertSame('v-backup-user', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'yes'], $command->getRequestParams());

        $this->expectException(TypeError::class);
		$command->process();
    }
}
