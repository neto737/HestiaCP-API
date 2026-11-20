<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Live;

use neto737\HestiaCP\Command\ResponseCode;
use neto737\HestiaCP\Exceptions\ProcessException;

class BackupsLiveTest extends LiveTestCase {

	public function testExclusions(): void {
		$backup = static::$client->getModuleBackup(self::user());

		$this->assertTrue($backup->updateExclusions(['WEB' => [self::domain('web') . ':public_html/cache'], 'DB' => ['*'], 'CRON' => ['*']]));
		$exclusions = $backup->listBackupExclusions();
		$this->assertSame(['public_html/cache'], array_values($exclusions['WEB']));
		$this->assertArrayHasKey('*', $exclusions['DB']);

		$this->assertTrue($backup->deleteExclusions('DB'));
		$this->assertSame([], $backup->listBackupExclusions()['DB']);
		$this->assertTrue($backup->deleteExclusions());
		$this->assertSame([], $backup->listBackupExclusions()['WEB']);

		// HestiaCP checks the entries itself
		$this->expectException(ProcessException::class);
		$this->expectExceptionCode(ResponseCode::E_INVALID);
		$backup->updateExclusions(['CRON' => ['job1']]);
	}

	public function testBackupLifecycle(): void {
		$backup = static::$client->getModuleBackup(self::user());

		$before = $backup->listBackups();
		$this->assertSame([], $before);
		$this->assertTrue($backup->backup(false));

		$backups = $backup->listBackups();
		$this->assertCount(1, $backups);
		$name = (string) array_key_first($backups);
		$this->assertArrayHasKey('SIZE', $backup->listBackup($name));

		$this->assertTrue($backup->delete($name));
		$this->assertSame([], $backup->listBackups());
	}
}
