<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Command\Cron\AddCronJob;
use neto737\HestiaCP\Command\Cron\ChangeCronJob;
use neto737\HestiaCP\Command\Cron\DeleteCronJob;
use neto737\HestiaCP\Command\Cron\ListCronJob;
use neto737\HestiaCP\Command\Cron\ListCronJobs;
use neto737\HestiaCP\Command\Cron\SuspendCronJob;
use neto737\HestiaCP\Command\Cron\UnsuspendCronJob;

class CronTest extends \PHPUnit\Framework\TestCase {

	public function testListCronJobs(): void {
		$command = new ListCronJobs('admin');
		$this->assertSame('v-list-cron-jobs', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertFalse($command->needReturnCode());
		$this->assertSame([], $command->process(new Response()));
	}

	public function testListCronJob(): void {
		$command = new ListCronJob('admin', 7);
		$this->assertSame('v-list-cron-job', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => '7', 'arg3' => 'json'], $command->getRequestParams());

		$this->assertFalse($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testAddCronJob(): void {
		$command = new AddCronJob('admin', '0', '3', '*', '*', '1-5', '/usr/bin/php cron.php');
		$this->assertSame('v-add-cron-job', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => '0',
			'arg3' => '3',
			'arg4' => '*',
			'arg5' => '*',
			'arg6' => '1-5',
			'arg7' => '/usr/bin/php cron.php'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testAddCronJobWithJobAndRestart(): void {
		$command = new AddCronJob('admin', '0', '3', '*', '*', '*', 'uptime', 12, false);
		$this->assertSame(['arg8' => '12', 'arg9' => 'no'], array_slice($command->getRequestParams(), 7));
	}

	public function testAddCronJobRestartWithoutJob(): void {
		// an empty job ID makes HestiaCP pick the next free one
		$command = new AddCronJob('admin', '0', '3', '*', '*', '*', 'uptime', null, true);
		$this->assertSame(['arg8' => '', 'arg9' => 'yes'], array_slice($command->getRequestParams(), 7));
	}

	public function testChangeCronJob(): void {
		$command = new ChangeCronJob('admin', 7, '*/5', '*', '*', '*', '*', 'uptime');
		$this->assertSame('v-change-cron-job', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => '7',
			'arg3' => '*/5',
			'arg4' => '*',
			'arg5' => '*',
			'arg6' => '*',
			'arg7' => '*',
			'arg8' => 'uptime'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testDeleteCronJob(): void {
		$command = new DeleteCronJob('admin', 7);
		$this->assertSame('v-delete-cron-job', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => '7'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testSuspendCronJob(): void {
		$command = new SuspendCronJob('admin', 7);
		$this->assertSame('v-suspend-cron-job', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => '7'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testUnsuspendCronJob(): void {
		$command = new UnsuspendCronJob('admin', 7);
		$this->assertSame('v-unsuspend-cron-job', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => '7'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testRestartIsSentWhenSet(): void {
		$this->assertSame(['arg1' => 'admin', 'arg2' => '7', 'arg3' => 'yes'], (new DeleteCronJob('admin', 7, true))->getRequestParams());
		$this->assertSame(['arg1' => 'admin', 'arg2' => '7', 'arg3' => 'no'], (new SuspendCronJob('admin', 7, false))->getRequestParams());
		$this->assertSame(['arg1' => 'admin', 'arg2' => '7', 'arg3' => 'no'], (new UnsuspendCronJob('admin', 7, false))->getRequestParams());
	}
}
