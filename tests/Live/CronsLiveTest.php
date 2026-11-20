<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Live;

use neto737\HestiaCP\Command\ResponseCode;
use neto737\HestiaCP\Exceptions\ProcessException;

class CronsLiveTest extends LiveTestCase {

	public function testJobLifecycle(): void {
		$cron = static::$client->getModuleCron(self::user());

		$before = $cron->listJobs();
		$this->assertSame([], $before);
		$this->assertTrue($cron->addJob('0', '3', '*', '*', '1', 'true', 42));
		$this->assertSame(['3', '1', 'true'], array_values(array_intersect_key($cron->listJob(42), array_flip(['HOUR', 'WDAY', 'CMD']))));

		$this->assertTrue($cron->changeJob(42, '15', '4', '*', '*', '*', 'echo changed'));
		$this->assertSame('echo changed', $cron->listJob(42)['CMD']);

		$this->assertTrue($cron->suspendJob(42));
		$this->assertSame('yes', $cron->listJobs()['42']['SUSPENDED']);
		$this->assertTrue($cron->unsuspendJob(42));
		$this->assertTrue($cron->deleteJob(42));

		$this->expectException(ProcessException::class);
		$this->expectExceptionCode(ResponseCode::E_NOTEXIST);
		$cron->listJob(42);
	}

	public function testInvalidJob(): void {
		$this->expectException(ProcessException::class);
		$this->expectExceptionCode(ResponseCode::E_INVALID);
		static::$client->getModuleCron(self::user())->addJob('99', '*', '*', '*', '*', 'true');
	}
}
